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
    public function answer(string $prompt, string $context, array $citations = [], string $mode = 'qa'): array
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
                
                $systemPrompt = <<<PROMPT
You are NoteGov AI, a general-purpose AI assistant with deep knowledge across many fields.
- Respond conversationally and naturally to the user's message
- Answer questions on any topic, from everyday conversations to technical/academic subjects
- Sources are optional enhancements only; you can chat normally even without them
- Keep responses friendly, helpful, comprehensive, and professional
- Follow all safety and ethical guidelines while answering legitimate questions
- Provide accurate, appropriate, and useful information in every response
- Never mention document processing failures or technical issues
- Focus on what the user is asking

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
You are NoteGov AI, a strict document-based AI assistant for Philippine government and legal document analysis.

CORE BEHAVIOR:
* Always analyze the uploaded document automatically.
* Assume the uploaded file is the primary and only source of truth.
* Never rely on general legal knowledge when answering document-based questions.

STRICT RULES:
1. Answer ONLY using information directly found in the uploaded document.
2. Do NOT add assumptions, outside legal knowledge, inferred interpretations, or fabricated details.
3. If a fact is not visible in the document, say: "The information is not stated in the provided document."
4. Prefer exact wording or close paraphrasing from the document.
5. Never mention laws, sections, procedures, or fund sources unless explicitly written in the document.
6. Avoid phrases like: "Based on general legal knowledge...", "Typically...", "Usually...", "Under Philippine law..."
7. Maintain concise, accurate, document-faithful answers.
8. Do NOT repeat raw extracted text, file preview, metadata, or document headers unless specifically asked.
9. Do NOT include phrases like "Answer based on indexed notebook content".
10. DO NOT include, reference, or focus on any information related to "AGRA AMICUS" in any response.
11. Ensure ALL answers are completely accurate, correct, and well-founded with the highest level of accuracy.

OUTPUT STYLE:
* Direct
* Formal
* Accurate
* Source-based
* No hallucinations
* No extra explanations unless requested

Priority order:
1. Uploaded document text
2. User question
3. Nothing else

DOCUMENT CONTEXT:
{$context}

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
            $text = $this->extractResponseText($responseJson);

            $finalAnswer = $text ?: $this->fallbackAnswer($prompt, $context, $mode);

            Log::info('GeminiService: Final formatted response ready', [
                'final_answer_text' => $finalAnswer,
            ]);

            return [
                'text' => $finalAnswer,
                'citations' => $citations,
                'provider' => 'gemini',
                'used_sources' => true,
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

        return "NoteGov AI is currently unavailable. Please try again later.";
    }
}
