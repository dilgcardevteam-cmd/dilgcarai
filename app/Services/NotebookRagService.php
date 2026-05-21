<?php

namespace App\Services;

use App\Models\AiEmbedding;
use App\Models\Notebook;
use App\Models\Source;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class NotebookRagService
{
    public function __construct(protected GeminiService $gemini) {}

    /**
     * Index a source into chunked embeddings.
     */
    public function syncSourceEmbeddings(Source $source): void
    {
        $source->embeddings()->delete();

        $chunks = $this->chunkSource($source);

        if ($chunks === []) {
            return;
        }

        $vectors = $this->gemini->embeddings(array_column($chunks, 'content'));

        foreach ($chunks as $index => $chunk) {
            AiEmbedding::create([
                'notebook_id' => $source->notebook_id,
                'source_id' => $source->id,
                'message_id' => null,
                'chunk_index' => $index,
                'content_hash' => sha1($chunk['content']),
                'embedding_model' => $vectors !== [] ? config('services.gemini.embedding_model', 'text-embedding-004') : null,
                'token_count' => $chunk['token_count'],
                'content' => $chunk['content'],
                'embedding' => $vectors[$index] ?? null,
                'metadata' => [
                    'source_name' => $source->name,
                    'type' => $source->type,
                    'page_number' => $chunk['page_number'],
                    'evidence_snippet' => $this->citationSnippet($chunk['content']),
                ],
            ]);
        }
    }

    /**
     * Build notebook context for an incoming prompt.
     *
     * @return array{context:string,citations:array<int, array<string, mixed>>,chunks:Collection<int, AiEmbedding>}
     */
    public function buildContext(Notebook $notebook, string $prompt, int $limit = 4, ?array $sourceIds = null): array
    {
        Log::info('Building RAG context', [
            'notebook_id' => $notebook->id,
            'prompt' => $prompt,
            'selected_source_ids' => $sourceIds,
        ]);

        $selectedSources = $notebook->sources()
            ->when(is_array($sourceIds), fn ($query) => $sourceIds === []
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('id', $sourceIds))
            ->latest('updated_at')
            ->get();

        if ($selectedSources->isEmpty()) {
            Log::warning('No sources found for RAG context', ['notebook_id' => $notebook->id]);
            return [
                'context' => '',
                'citations' => [],
                'chunks' => collect(),
                'sources_considered' => 0,
            ];
        }

        $chunks = $this->searchRelevantChunks($notebook, $prompt, $limit, $selectedSources->pluck('id')->all());

        if ($chunks->isEmpty()) {
            return [
                'context' => '',
                'citations' => [],
                'chunks' => collect(),
                'sources_considered' => $selectedSources->count(),
            ];
        }

        $context = $chunks
            ->map(function (AiEmbedding $chunk) {
                $source = $chunk->source;
                $page = (int) data_get($chunk->metadata, 'page_number', 1);

                return "[Page {$page} | Source {$source?->name} | Chunk {$chunk->chunk_index}]\n{$chunk->content}";
            })
            ->implode("\n\n---\n\n");

        $citations = $chunks
            ->map(fn (AiEmbedding $chunk) => [
                'ai_embedding_id' => $chunk->id,
                'source_id' => $chunk->source?->id,
                'source_name' => $chunk->source?->name ?? 'Uploaded source',
                'type' => $chunk->source?->type,
                'source_url' => $chunk->source?->source_url,
                'page' => (int) data_get($chunk->metadata, 'page_number', 1),
                'page_number' => (int) data_get($chunk->metadata, 'page_number', 1),
                'chunk_id' => (string) $chunk->chunk_index,
                'chunk_index' => $chunk->chunk_index,
                'evidence_snippet' => $this->citationSnippet($chunk->content),
                'text' => $this->citationSnippet($chunk->content),
                'score' => $chunk->getAttribute('relevance_score'),
            ])
            ->unique(fn (array $citation) => ($citation['source_id'] ?? '').':'.($citation['chunk_index'] ?? ''))
            ->values()
            ->all();

        Log::info('Generated RAG context preview', [
            'context_length' => Str::length($context),
            'context_preview' => Str::limit($context, 500),
        ]);

        return [
            'context' => $context,
            'citations' => $citations,
            'chunks' => $chunks,
            'sources_considered' => $selectedSources->count(),
        ];
    }

    /**
     * Find relevant chunks using lexical overlap as a lightweight fallback.
     *
     * @return Collection<int, AiEmbedding>
     */
    public function searchRelevantChunks(Notebook $notebook, string $prompt, int $limit = 4, ?array $sourceIds = null): Collection
    {
        $keywords = $this->extractSearchKeywords($prompt);

        if ($keywords->isEmpty()) {
            return collect();
        }

        return $notebook->embeddings()
            ->with('source')
            ->when(
                is_array($sourceIds) && $sourceIds !== [],
                fn ($query) => $query->whereIn('source_id', $sourceIds)
            )
            ->get()
            ->map(function (AiEmbedding $chunk) use ($keywords): AiEmbedding {
                $haystack = Str::lower($chunk->content);
                $score = $keywords->sum(fn (string $keyword) => substr_count($haystack, $keyword));
                $matchedTerms = $keywords->filter(fn (string $keyword) => str_contains($haystack, $keyword))->count();
                $chunk->setAttribute('relevance_score', $score);
                $chunk->setAttribute('matched_terms', $matchedTerms);

                return $chunk;
            })
            ->filter(fn (AiEmbedding $chunk) => ($chunk->getAttribute('relevance_score') ?? 0) > 0 && ($chunk->getAttribute('matched_terms') ?? 0) > 0)
            ->sortByDesc(fn (AiEmbedding $chunk) => $chunk->getAttribute('relevance_score'))
            ->take($limit)
            ->values();
    }

    protected function extractSearchKeywords(string $prompt): Collection
    {
        $stopWords = [
            'about', 'also', 'answer', 'based', 'briefly', 'could', 'does', 'explain',
            'from', 'give', 'how', 'information', 'main', 'meaning', 'mention',
            'overview', 'purpose', 'question', 'show', 'source', 'sources', 'state',
            'tell', 'that', 'their', 'there', 'these', 'this', 'what', 'when',
            'where', 'which', 'while', 'with', 'would', 'the', 'and', 'for', 'are',
            'was', 'were', 'has', 'have', 'had', 'into', 'its', 'why', 'who',
        ];

        return collect(Str::of($prompt)->lower()->replaceMatches('/[^a-z0-9\s]/', ' ')->explode(' '))
            ->map(fn (string $part) => trim($part))
            ->filter(fn (string $part) => Str::length($part) > 2 && ! in_array($part, $stopWords, true))
            ->unique()
            ->values();
    }

    /**
     * Split a text payload into chunks suitable for indexing.
     *
     * @return array<int, array{content:string,token_count:int}>
     */
    protected function chunkText(string $text, int $chunkSize = 1000): array
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text)) ?? trim($text);
        $chunks = [];

        foreach (str_split($normalized, $chunkSize) as $chunk) {
            $content = trim($chunk);

            if ($content === '') {
                continue;
            }

            $chunks[] = [
                'content' => $content,
                'token_count' => (int) ceil(str_word_count($content) * 1.35),
                'page_number' => 1,
            ];
        }

        return $chunks;
    }

    /**
     * @return array<int, array{content:string,token_count:int,page_number:int}>
     */
    protected function chunkSource(Source $source): array
    {
        if ($source->type !== 'pdf') {
            return $this->chunkText(trim($source->extracted_text ?: ($source->summary ?: '')));
        }

        $path = $source->storage_path ? Storage::disk($source->storage_disk)->path($source->storage_path) : null;

        if (! $path || ! is_file($path)) {
            return $this->chunkText(trim($source->extracted_text ?: ($source->summary ?: '')));
        }

        try {
            $pdf = (new Parser())->parseFile($path);
            $chunks = [];

            foreach ($pdf->getPages() as $index => $page) {
                $pageNumber = $index + 1;

                foreach ($this->chunkText($page->getText()) as $chunk) {
                    $chunk['page_number'] = $pageNumber;
                    $chunks[] = $chunk;
                }
            }

            return $chunks;
        } catch (\Throwable $exception) {
            Log::warning('Failed to build page-aware PDF chunks', [
                'source_id' => $source->id,
                'error' => $exception->getMessage(),
            ]);

            return $this->chunkText(trim($source->extracted_text ?: ($source->summary ?: '')));
        }
    }

    protected function citationSnippet(string $text): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text)) ?? trim($text);

        return Str::limit($normalized, 280);
    }
}
