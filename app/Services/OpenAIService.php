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
    public function answer(string $prompt, string $context, array $citations = [], string $mode = 'qa'): array
    {
        if (! $this->isConfigured()) {
            return [
                'text' => $this->fallbackAnswer($prompt, $context, $mode),
                'citations' => $citations,
                'provider' => 'local-fallback',
            ];
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
                                'text' => 'You are NoteGov AI DILG, an AI governance notebook assistant.

GUIDELINES FOR ANSWERING:
- Give a direct and concise answer first
- Use clean formatting (bullet points, numbered lists, etc. when appropriate)
- Avoid repeating duplicated content
- Ignore unrelated extracted preview text, document headers, or metadata
- Answer only the requested question
- Do NOT repeat raw extracted text, file preview, metadata, or document headers unless specifically asked
- Do NOT include phrases like "Answer based on indexed notebook content"

Answer with clear government-ready language, cite source titles when possible, and keep your response grounded only in the provided notebook context.',
                            ]],
                        ],
                        [
                            'role' => 'user',
                            'content' => [[
                                'type' => 'input_text',
                                'text' => "Mode: {$mode}\n\nPrompt:\n{$prompt}\n\nNotebook context:\n{$context}",
                            ]],
                        ],
                    ],
                ])
                ->throw()
                ->json();

            return [
                'text' => $this->extractResponseText($response) ?: $this->fallbackAnswer($prompt, $context, $mode),
                'citations' => $citations,
                'provider' => 'openai',
            ];
        } catch (Throwable $exception) {
            report($exception);

            return [
                'text' => $this->fallbackAnswer($prompt, $context, $mode),
                'citations' => $citations,
                'provider' => 'local-fallback',
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
     * Build a deterministic fallback answer.
     */
    protected function fallbackAnswer(string $prompt, string $context, string $mode): string
    {
        $context = trim($context);

        if ($context === '') {
            return "This document couldn't be analyzed, but you can still continue chatting with NoteGov AI.";
        }

        return "NoteGov AI is currently unavailable. Please try again later.";
    }
}