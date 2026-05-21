<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GeminiService
{
    /**
     * Determine if the Gemini credentials are configured.
     */
    public function isConfigured(): bool
    {
        return filled(config('services.gemini.api_key'));
    }

    /**
     * Generate a notebook-aware answer.
     *
     * @return array{text:string,citations:array<int, array<string, mixed>>,provider:string,used_sources:bool}
     */
    public function answer(string $prompt, string $context, array $citations = [], string $mode = 'qa', ?string $retrievalNote = null): array
    {
        Log::info('GeminiService: Generating answer', [
            'prompt' => $prompt,
            'context_length' => Str::length($context),
            'citations_count' => count($citations),
        ]);

        if (! $this->isConfigured()) {
            return [
                'text' => $this->fallbackAnswer($prompt, $context, $mode),
                'citations' => $citations,
                'provider' => 'local-fallback',
                'used_sources' => false,
            ];
        }

        $trimmedContext = trim($context);
        
        if (str_starts_with($trimmedContext, 'PDF too large to parse') || str_starts_with($trimmedContext, 'File type')) {
            Log::info('GeminiService: Context contains failed source messages, filtering them out and proceeding with normal AI chat', [
                'context_preview' => Str::limit($trimmedContext, 200),
            ]);
            $trimmedContext = '';
        }

        if ($trimmedContext === '') {
            try {
                $apiKey = config('services.gemini.api_key');
                $model = config('services.gemini.grounded_model', 'gemini-2.5-flash');
                
                $retrievalInstruction = filled($retrievalNote)
                    ? "\nRETRIEVAL NOTE:\n{$retrievalNote}\nIf useful, briefly mention that you could not find a direct reference in the uploaded source, then answer normally using general knowledge."
                    : '';

                $systemPrompt = <<<PROMPT
You are NoteGov AI, a general-purpose AI assistant with deep knowledge across many fields.
- Respond conversationally and naturally to the user's message
- Answer questions on any topic, from everyday conversations to technical/academic subjects
- Sources are optional enhancements only; you can chat normally even without them
- Keep responses friendly, helpful, comprehensive, and professional
- Follow all safety and ethical guidelines while answering legitimate questions
- Provide accurate, appropriate, and useful information in every response
- Never refuse only because uploaded sources do not contain the answer
- Focus on what the user is asking
{$retrievalInstruction}

USER MESSAGE:
{$prompt}
PROMPT;

                Log::info('GeminiService: Sending grounded web request to API (no notebook sources available)');
                
                $response = Http::baseUrl('https://generativelanguage.googleapis.com/v1beta')
                    ->timeout(120)
                    ->post("/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => $systemPrompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                        ],
                        'tools' => [
                            [
                                'google_search' => (object) [],
                            ],
                        ],
                    ]);

                Log::info('GeminiService: Received conversational raw response', [
                    'status' => $response->status(),
                    'raw_response' => $response->json(),
                ]);

                if ($response->status() === 429) {
                    $text = "You've exceeded your Gemini API free quota limit. Please try again tomorrow or upgrade your API plan.";
                } elseif ($response->failed()) {
                    $text = $this->friendlyFallbackAnswer($prompt);
                } else {
                    $responseJson = $response->json();
                    $text = $this->extractResponseText($responseJson) ?: $this->friendlyFallbackAnswer($prompt);
                }

                $groundingSources = isset($responseJson) ? $this->extractGroundingSources($responseJson) : [];
                $provider = $response->status() === 429
                    ? 'gemini-quota-error'
                    : ($groundingSources === [] ? 'gemini-conversational' : 'gemini-grounded-web');

                return [
                    'text' => $text,
                    'citations' => $groundingSources,
                    'provider' => $provider,
                    'used_sources' => $groundingSources !== [],
                ];
            } catch (Throwable $exception) {
                Log::error('GeminiService: Conversational API error', [
                    'error' => $exception->getMessage(),
                ]);
                
                return [
                    'text' => $this->friendlyFallbackAnswer($prompt),
                    'citations' => [],
                    'provider' => 'local-fallback',
                    'used_sources' => false,
                ];
            }
        }

        try {
            $apiKey = config('services.gemini.api_key');
            $model = config('services.gemini.chat_model', 'gemini-flash-latest');
            
            $systemPrompt = <<<PROMPT
You are NoteGov AI, a modern hybrid RAG + general AI assistant.

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
9. Do NOT include phrases like "Answer based on indexed notebook content".
10. Return valid JSON only. No markdown fences.

OUTPUT FORMAT:
{
  "answer": "string",
  "citations": [
    {
      "page": 27,
      "text": "matched text snippet"
    }
  ]
}

DOCUMENT CONTEXT:
{$trimmedContext}

QUESTION:
{$prompt}
PROMPT;

            Log::info('GeminiService: Sending request to API', [
                'prompt_preview' => Str::limit($systemPrompt, 500),
            ]);
            
            $response = Http::baseUrl('https://generativelanguage.googleapis.com/v1beta')
                ->timeout(120)
                ->post("/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemPrompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topK' => 40,
                        'topP' => 0.95,
                    ],
                ]);

            Log::info('GeminiService: Received raw response', [
                'status' => $response->status(),
                'raw_response' => $response->json(),
            ]);

            if ($response->status() === 429) {
                return [
                    'text' => "You've exceeded your Gemini API free quota limit. Please try again tomorrow or upgrade your API plan.",
                    'citations' => $citations,
                    'provider' => 'gemini-quota-error',
                    'used_sources' => false,
                ];
            }

            if ($response->failed()) {
                return [
                    'text' => $this->fallbackAnswer($prompt, $context, $mode),
                    'citations' => $citations,
                    'provider' => 'local-fallback',
                    'used_sources' => false,
                ];
            }

            $responseJson = $response->json();
            $text = $this->extractResponseText($responseJson) ?: '';
            $structured = $this->extractStructuredAnswer($text);
            $answerCitations = $this->mergeCitationMetadata($structured['citations'], $citations);
            $finalAnswer = $structured['answer'] ?: ($text ?: $this->fallbackAnswer($prompt, $trimmedContext, $mode));

            Log::info('GeminiService: Final formatted response ready', [
                'final_answer_text' => $finalAnswer,
            ]);

            return [
                'text' => $finalAnswer,
                'citations' => $answerCitations,
                'provider' => 'gemini',
                'used_sources' => $answerCitations !== [],
            ];
        } catch (Throwable $exception) {
            Log::error('GeminiService: API error', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
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
            $apiKey = config('services.gemini.api_key');
            $model = config('services.gemini.embedding_model', 'gemini-embedding-001');
            $embeddings = [];

            foreach ($chunks as $chunk) {
                $response = Http::baseUrl('https://generativelanguage.googleapis.com/v1beta')
                    ->timeout(60)
                    ->post("/models/{$model}:embedContent?key={$apiKey}", [
                        'content' => [
                            'parts' => [
                                ['text' => $chunk],
                            ],
                        ],
                    ])
                    ->throw()
                    ->json();

                $embedding = data_get($response, 'embedding.values', []);
                $embeddings[] = $embedding;
            }

            return $embeddings;
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
            $apiKey = config('services.gemini.api_key');
            $model = config('services.gemini.chat_model', 'gemini-flash-latest');
            
            $response = Http::baseUrl('https://generativelanguage.googleapis.com/v1beta')
                ->timeout(60)
                ->post("/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => trim("Summarize the provided notebook content for a government policy and operations audience. {$instructions}\n\nContent:\n" . Str::limit($content, 12000))],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
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
     * Attempt to extract response text from Gemini API payload.
     */
    protected function extractResponseText(array $response): ?string
    {
        $candidates = data_get($response, 'candidates', []);
        
        foreach ($candidates as $candidate) {
            $content = data_get($candidate, 'content', []);
            $parts = data_get($content, 'parts', []);
            
            foreach ($parts as $part) {
                $text = data_get($part, 'text');
                
                if (filled($text)) {
                    return trim($text);
                }
            }
        }

        return null;
    }

    /**
     * Extract Google Search grounding links from the Gemini response.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractGroundingSources(array $response): array
    {
        $chunks = data_get($response, 'candidates.0.groundingMetadata.groundingChunks', []);

        return collect($chunks)
            ->map(function (array $chunk): ?array {
                $url = data_get($chunk, 'web.uri');

                if (blank($url)) {
                    return null;
                }

                $title = data_get($chunk, 'web.title')
                    ?: parse_url((string) $url, PHP_URL_HOST)
                    ?: 'Web source';

                return [
                    'source_id' => null,
                    'source_name' => $title,
                    'type' => 'web',
                    'source_url' => $url,
                ];
            })
            ->filter()
            ->unique('source_url')
            ->values()
            ->all();
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
    protected function friendlyFallbackAnswer(string $prompt): string
    {
        $lowerPrompt = strtolower(trim($prompt));
        
        if (str_contains($lowerPrompt, 'hi') || str_contains($lowerPrompt, 'hello') || str_contains($lowerPrompt, 'hey')) {
            return "Hello! How can I help you today?";
        }
        
        if (str_contains($lowerPrompt, 'how are you')) {
            return "I'm doing well, thank you for asking! How can I assist you today?";
        }
        
        return "Hello! I'm NoteGov AI. How can I help you today?";
    }

    protected function fallbackAnswer(string $prompt, string $context, string $mode): string
    {
        $context = trim($context);

        if ($context === '') {
            return $this->friendlyFallbackAnswer($prompt);
        }

        return "Based on the uploaded sources, here is the relevant information:\n\n" . $context;
    }
}
