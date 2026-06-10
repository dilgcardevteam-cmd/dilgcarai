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
        $hasNotebookSources = $hasSourceContext || filled($retrievalNote);

        if (! $this->isConfigured()) {
            return [
                'text' => $this->fallbackAnswer($prompt, $context, $mode, $retrievalNote),
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
                                'text' => $hasSourceContext ? 'You are NoteGov AI, a source-grounded notebook assistant.

CRITICAL SOURCE RULES:
1. ALWAYS prioritize uploaded source chunks before any general knowledge.
2. NEVER answer from general knowledge when source chunks are provided.
3. Answer ONLY using the provided source chunks.
4. If the answer is not clearly found in the source chunks, say: "Based on uploaded sources, I could not find enough information to answer this question."
5. Start with the direct answer. Do not add a generic "Based on uploaded sources" prefix when the source answer is clear.
6. Cite source chunks inline as [1], [2], matching the source numbers in the provided context.
7. Only cite information that came from uploaded source chunks.
8. Do not hallucinate facts, laws, doctrines, or cases.
9. Treat related source wording as valid evidence. For example, if the user asks about "SDLC" and the source states the project used "Rapid Application Development (RAD)", answer that RAD is the SDLC/development model used.
10. Understand Taglish/Filipino questions. For example, "anong language ginamit namin?" means the user is asking which programming languages, frameworks, or technologies were used in the source.
11. Use NotebookLM-style formatting: concise paragraphs, **bold** key terms, and bullets for phases/processes/components.
12. Use sentence-level citations only. Cite the smallest source passage that fully supports the claim.
13. Never cite an entire page when the provided Source passage is a smaller exact quote.
14. Preserve citation metadata from the selected Source passage: page, paragraphIndex, sentenceIndex, exact text, startOffset, endOffset, and confidence.
15. Return valid JSON only. No markdown fences.

OUTPUT FORMAT:
{
  "answer": "string",
  "citations": [
    {
      "source_number": 1,
      "page": 27,
      "paragraphIndex": 0,
      "sentenceIndex": 2,
      "text": "exact sentence or sentences used as evidence",
      "startOffset": 1245,
      "endOffset": 1387,
      "confidence": 0.98
    }
  ]
}' : ($hasNotebookSources
                                    ? 'Uploaded sources exist, but no relevant source chunks were provided. Return exactly: Based on uploaded sources, I could not find enough information in the available source chunks to answer this question.'
                                    : 'No sources found, using general knowledge. You are NoteGov AI, a conversational AI assistant. Answer naturally and helpfully. Do not cite uploaded documents when none were provided.'),
                            ]],
                        ],
                        [
                            'role' => 'user',
                            'content' => [[
                                'type' => 'input_text',
                                'text' => $hasSourceContext
                                    ? "Retrieved PDF context chunks:\n{$context}\n\nQuestion:\n{$prompt}"
                                    : trim(($retrievalNote ? "Retrieval note: {$retrievalNote}\n\n" : '')."User message:\n{$prompt}"),
                            ]],
                        ],
                    ],
                ])
                ->throw()
                ->json();

            $rawText = $this->extractResponseText($response) ?: '';
            $structured = $hasSourceContext ? $this->extractStructuredAnswer($rawText) : ['answer' => $rawText, 'citations' => []];
            $answerCitations = $hasSourceContext ? $this->mergeCitationMetadata($structured['citations'], $citations) : [];
            $answerCitations = $hasSourceContext && $answerCitations === []
                ? $this->citationsFromInlineSourceMarkers($structured['answer'] ?: $rawText, $citations)
                : $answerCitations;

            return [
                'text' => $structured['answer'] ?: ($rawText ?: $this->fallbackAnswer($prompt, $context, $mode, $retrievalNote)),
                'citations' => $answerCitations,
                'provider' => 'openai',
                'used_sources' => $answerCitations !== [],
            ];
        } catch (Throwable $exception) {
            report($exception);

            return [
                'text' => $this->fallbackAnswer($prompt, $context, $mode, $retrievalNote),
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
                $sourceNumber = (int) ($citation['source_number'] ?? $citation['citation_number'] ?? 0);
                $page = (int) ($citation['page'] ?? $citation['page_number'] ?? 1);
                $text = trim((string) ($citation['text'] ?? ''));
                $source = $sourceNumber > 0
                    ? collect($availableCitations)->first(fn (array $available) => (int) ($available['citation_number'] ?? 0) === $sourceNumber)
                    : null;

                $source ??= collect($availableCitations)->first(function (array $available) use ($page, $text): bool {
                    if ((int) ($available['page'] ?? $available['page_number'] ?? 1) !== $page) {
                        return false;
                    }

                    $availableText = (string) ($available['text'] ?? $available['evidence_snippet'] ?? '');

                    return $text === '' || str_contains($availableText, $text) || str_contains($text, $availableText);
                }) ?? collect($availableCitations)->first(fn (array $available) => (int) ($available['page'] ?? $available['page_number'] ?? 1) === $page) ?? [];

                $hasMatchedSource = $source !== null && $source !== [];
                $trustedPage = $hasMatchedSource ? (int) ($source['page'] ?? $source['page_number'] ?? $page) : $page;
                $trustedText = $hasMatchedSource
                    ? ($source['quote'] ?? $source['text'] ?? $source['evidence_snippet'] ?? $text)
                    : $text;

                return array_filter(array_merge($source, [
                    'citation_number' => $sourceNumber ?: ($source['citation_number'] ?? null),
                    'page' => $trustedPage,
                    'page_number' => $trustedPage,
                    'paragraph_index' => $hasMatchedSource ? ($source['paragraph_index'] ?? null) : ($citation['paragraph_index'] ?? $citation['paragraphIndex'] ?? null),
                    'paragraphIndex' => $hasMatchedSource ? ($source['paragraphIndex'] ?? $source['paragraph_index'] ?? null) : ($citation['paragraphIndex'] ?? $citation['paragraph_index'] ?? null),
                    'sentence_index' => $hasMatchedSource ? ($source['sentence_index'] ?? null) : ($citation['sentence_index'] ?? $citation['sentenceIndex'] ?? null),
                    'sentenceIndex' => $hasMatchedSource ? ($source['sentenceIndex'] ?? $source['sentence_index'] ?? null) : ($citation['sentenceIndex'] ?? $citation['sentence_index'] ?? null),
                    'startOffset' => $hasMatchedSource ? ($source['startOffset'] ?? null) : ($citation['startOffset'] ?? $citation['start_offset'] ?? null),
                    'endOffset' => $hasMatchedSource ? ($source['endOffset'] ?? null) : ($citation['endOffset'] ?? $citation['end_offset'] ?? null),
                    'confidence' => $hasMatchedSource ? ($source['confidence'] ?? null) : ($citation['confidence'] ?? null),
                    'quote' => $trustedText,
                    'text' => $trustedText,
                    'evidence_snippet' => $trustedText,
                ]), fn ($value) => $value !== null && $value !== '');
            })
            ->filter(fn (array $citation) => filled($citation['text'] ?? null))
            ->values()
            ->all();
    }

    /**
     * @param array<int,array<string,mixed>> $availableCitations
     * @return array<int,array<string,mixed>>
     */
    protected function citationsFromInlineSourceMarkers(string $answer, array $availableCitations): array
    {
        preg_match_all('/\[(?:Source\s*)?(\d+)\]/i', $answer, $matches);
        $sourceNumbers = collect($matches[1] ?? [])
            ->map(fn (string $number): int => (int) $number)
            ->filter(fn (int $number): bool => $number > 0)
            ->unique()
            ->values();

        if ($sourceNumbers->isEmpty()) {
            return [];
        }

        return $sourceNumbers
            ->map(fn (int $sourceNumber): ?array => collect($availableCitations)
                ->first(fn (array $citation): bool => (int) ($citation['citation_number'] ?? 0) === $sourceNumber))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Build a deterministic fallback answer.
     */
    protected function fallbackAnswer(string $prompt, string $context, string $mode, ?string $retrievalNote = null): string
    {
        $context = trim($context);

        if ($context === '' && filled($retrievalNote)) {
            return 'Based on uploaded sources, I could not find enough information in the available source chunks to answer this question.';
        }

        if ($context === '') {
            return "No sources found, using general knowledge. Hello! I'm NoteGov AI. How can I help you today?";
        }

        return "NoteGov AI is currently unavailable. Please try again later.";
    }
}
