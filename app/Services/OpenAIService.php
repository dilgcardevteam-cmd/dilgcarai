<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class OpenAIService
{
    /**
     * Determine if the OpenAI credentials are configured.
     */
    public function isConfigured(): bool
    {
        return filled(config('services.openai.api_key'));
    }

    /**
     * Generate a notebook-aware answer.
     *
     * @return array{text:string,citations:array<int, array<string, mixed>>,provider:string}
     */
    public function answer(string $prompt, string $context, array $citations = [], string $mode = 'qa', ?string $retrievalNote = null): array
    {
        $context = trim($context);
        $hasSourceContext = $context !== '' && $citations !== [];

        if (! $this->isConfigured()) {
            return [
                'text' => $this->fallbackAnswer($prompt, $context, $mode),
                'citations' => $citations,
                'provider' => 'local-fallback',
                'used_sources' => false,
            ];
        }

        try {
            $response = Http::baseUrl(rtrim(config('services.openai.base_url', 'https://api.openai.com/v1'), '/'))
                ->withToken(config('services.openai.api_key'))
                ->timeout(60)
                ->post('/responses', [
                    'model' => config('services.openai.chat_model', 'gpt-4.1-mini'),
                    'temperature' => $hasSourceContext ? 0.1 : 0.7,
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => [[
                                'type' => 'input_text',
                                'text' => $hasSourceContext ? 'You are NoteGov AI, a modern hybrid RAG + general AI assistant.

Your job is to help the user intelligently while using uploaded source chunks as optional supporting evidence.

RESPONSE PRIORITIES:
1. Helpful response
2. Source grounding when relevant context exists
3. Natural conversation
4. Retrieval augmentation
5. General AI fallback intelligence

RULES:
1. Use the retrieved source chunks when they directly help answer the question.
2. Do not invent facts and present them as being from the uploaded source.
3. If the retrieved chunks do not fully answer the question, do NOT refuse. Answer using general knowledge and clearly separate it from source-backed information.
4. Never use a bare document-not-found refusal as the whole answer.
5. You may say: "I could not find this specifically in the uploaded source, but based on general knowledge..."
6. Include page/snippet citations only for claims supported by retrieved chunks.
7. If no citation supports a claim, do not attach a source citation to that claim.
8. Use professional, concise, intelligent wording.
9. Return valid JSON only. No markdown fences.

OUTPUT FORMAT:
{
  "answer": "string",
  "citations": [
    {
      "page": 27,
      "text": "matched text snippet"
    }
  ]
}' : 'You are NoteGov AI, a conversational AI assistant. Answer naturally and helpfully. Uploaded sources are optional context only. Never refuse only because retrieval found no source match.',
                            ]],
                        ],
                        [
                            'role' => 'user',
                            'content' => [[
                                'type' => 'input_text',
                                'text' => $hasSourceContext
                                    ? "Retrieved PDF context chunks:\n{$context}\n\nQuestion:\n{$prompt}"
                                    : trim(($retrievalNote ? "Retrieval note: {$retrievalNote}\nIf useful, briefly mention that you could not find a direct reference in the uploaded source, then answer normally using general knowledge.\n\n" : '')."User message:\n{$prompt}"),
                            ]],
                        ],
                    ],
                ])
                ->throw()
                ->json();

            $rawText = $this->extractResponseText($response) ?: '';
            $structured = $hasSourceContext ? $this->extractStructuredAnswer($rawText) : ['answer' => $rawText, 'citations' => []];
            $answerCitations = $hasSourceContext ? $this->mergeCitationMetadata($structured['citations'], $citations) : [];

            return [
                'text' => $structured['answer'] ?: ($rawText ?: $this->fallbackAnswer($prompt, $context, $mode)),
                'citations' => $answerCitations,
                'provider' => 'openai',
                'used_sources' => $answerCitations !== [],
            ];
        } catch (Throwable $exception) {
            report($exception);

            return [
                'text' => $this->fallbackAnswer($prompt, $context, $mode),
                'citations' => $citations,
                'provider' => 'local-fallback',
                'used_sources' => false,
            ];
        }
    }

    /**
     * Generate embeddings for a set of chunks.
     *
     * @param  array<int, string>  $chunks
     * @return array<int, array<int, float>>
     */
    public function embeddings(array $chunks): array
    {
        if (! $this->isConfigured() || $chunks === []) {
            return [];
        }

        try {
            $response = Http::baseUrl(rtrim(config('services.openai.base_url', 'https://api.openai.com/v1'), '/'))
                ->withToken(config('services.openai.api_key'))
                ->timeout(60)
                ->post('/embeddings', [
                    'model' => config('services.openai.embedding_model', 'text-embedding-3-small'),
                    'input' => $chunks,
                ])
                ->throw()
                ->json('data', []);

            return collect($response)
                ->map(fn (array $row) => $row['embedding'] ?? [])
                ->all();
        } catch (Throwable $exception) {
            report($exception);

            return [];
        }
    }

    /**
     * Summarize a content block.
     */
    public function summarize(string $content, string $instructions = ''): string
    {
        $content = trim($content);

        if ($content === '') {
            return 'No source content has been indexed yet.';
        }

        if (! $this->isConfigured()) {
            return Str::limit(preg_replace('/\s+/', ' ', $content) ?? $content, 420);
        }

        try {
            $response = Http::baseUrl(rtrim(config('services.openai.base_url', 'https://api.openai.com/v1'), '/'))
                ->withToken(config('services.openai.api_key'))
                ->timeout(60)
                ->post('/responses', [
                    'model' => config('services.openai.chat_model', 'gpt-4.1-mini'),
                    'temperature' => 0.2,
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => [[
                                'type' => 'input_text',
                                'text' => trim("Summarize the provided notebook content for a government policy and operations audience. {$instructions}"),
                            ]],
                        ],
                        [
                            'role' => 'user',
                            'content' => [[
                                'type' => 'input_text',
                                'text' => Str::limit($content, 12000),
                            ]],
                        ],
                    ],
                ])
                ->throw()
                ->json();

            return $this->extractResponseText($response) ?: Str::limit($content, 420);
        } catch (Throwable $exception) {
            report($exception);

            return Str::limit($content, 420);
        }
    }

    /**
     * Attempt to extract response text from the Responses API payload.
     */
    protected function extractResponseText(array $response): ?string
    {
        $output = data_get($response, 'output', []);

        foreach ($output as $item) {
            foreach (($item['content'] ?? []) as $contentItem) {
                $text = $contentItem['text'] ?? null;

                if (filled($text)) {
                    return trim($text);
                }
            }
        }

        return data_get($response, 'output_text');
    }

    /**
     * @return array{answer:string,citations:array<int,array<string,mixed>>}
     */
    protected function extractStructuredAnswer(string $text): array
    {
        $decoded = json_decode(trim($text), true);

        if (! is_array($decoded)) {
            preg_match('/\{.*\}/s', $text, $matches);
            $decoded = isset($matches[0]) ? json_decode($matches[0], true) : null;
        }

        if (! is_array($decoded)) {
            return ['answer' => trim($text), 'citations' => []];
        }

        return [
            'answer' => trim((string) ($decoded['answer'] ?? '')),
            'citations' => is_array($decoded['citations'] ?? null) ? $decoded['citations'] : [],
        ];
    }

    /**
     * @param array<int,array<string,mixed>> $modelCitations
     * @param array<int,array<string,mixed>> $availableCitations
     * @return array<int,array<string,mixed>>
     */
    protected function mergeCitationMetadata(array $modelCitations, array $availableCitations): array
    {
        if ($modelCitations === []) {
            return [];
        }

        return collect($modelCitations)
            ->map(function (array $citation) use ($availableCitations): array {
                $page = (int) ($citation['page'] ?? $citation['page_number'] ?? 1);
                $text = trim((string) ($citation['text'] ?? ''));
                $source = collect($availableCitations)->first(function (array $available) use ($page, $text): bool {
                    if ((int) ($available['page'] ?? $available['page_number'] ?? 1) !== $page) {
                        return false;
                    }

                    $availableText = (string) ($available['text'] ?? $available['evidence_snippet'] ?? '');

                    return $text === '' || str_contains($availableText, $text) || str_contains($text, $availableText);
                }) ?? collect($availableCitations)->first(fn (array $available) => (int) ($available['page'] ?? $available['page_number'] ?? 1) === $page) ?? [];

                return array_filter(array_merge($source, [
                    'page' => $page,
                    'page_number' => $page,
                    'text' => $text ?: ($source['text'] ?? $source['evidence_snippet'] ?? null),
                    'evidence_snippet' => $text ?: ($source['evidence_snippet'] ?? $source['text'] ?? null),
                ]), fn ($value) => $value !== null && $value !== '');
            })
            ->filter(fn (array $citation) => filled($citation['text'] ?? null))
            ->values()
            ->all();
    }

    /**
     * Build a deterministic fallback answer.
     */
    protected function fallbackAnswer(string $prompt, string $context, string $mode): string
    {
        $context = trim($context);

        if ($context === '') {
            return "Hello! I'm NoteGov AI. How can I help you today?";
        }

        return "NoteGov AI is currently unavailable. Please try again later.";
    }
}
