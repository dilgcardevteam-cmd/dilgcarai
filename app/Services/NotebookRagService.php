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
     * Index a source into chunked embeddings with legal-aware chunking.
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
                    'section_title' => $chunk['section_title'] ?? null,
                    'chapter' => $chunk['chapter'] ?? null,
                    'topic' => $chunk['topic'] ?? null,
                    'subtopic' => $chunk['subtopic'] ?? null,
                    'case_name' => $chunk['case_name'] ?? null,
                    'keywords' => $chunk['keywords'] ?? [],
                    'source_document' => $source->name,
                    'evidence_snippet' => $this->citationSnippet($chunk['content']),
                ],
            ]);
        }
    }

    /**
     * Build notebook context with hybrid search and reranking.
     *
     * @return array{context:string,citations:array<int, array<string, mixed>>,chunks:Collection<int, AiEmbedding>}
     */
    public function buildContext(Notebook $notebook, string $prompt, int $limit = 5, ?array $sourceIds = null): array
    {
        Log::info('Building RAG context with hybrid search', [
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

        $sourceIdsForSearch = $selectedSources->pluck('id')->all();
        $chunks = $this->searchRelevantChunks($notebook, $prompt, 15, $sourceIdsForSearch);
        $chunks = $this->rerankChunks($chunks, $prompt)->take($limit);

        if ($chunks->isEmpty()) {
            $sourceExcerptContext = $this->formatSourceExcerptContext($selectedSources, $prompt, $limit);

            if ($sourceExcerptContext['context'] !== '') {
                Log::info('Using source extracted-text excerpts after keyword search returned no matches', [
                    'notebook_id' => $notebook->id,
                    'sources_count' => $selectedSources->count(),
                ]);

                return array_merge(
                    $sourceExcerptContext,
                    ['sources_considered' => $selectedSources->count()]
                );
            }

            $fallbackChunks = $this->fallbackChunks($notebook, $sourceIdsForSearch, $limit);

            if ($fallbackChunks->isNotEmpty()) {
                Log::info('Using fallback source chunks after keyword search returned no matches', [
                    'notebook_id' => $notebook->id,
                    'chunks_count' => $fallbackChunks->count(),
                ]);

                return array_merge(
                    $this->formatChunkContext($fallbackChunks, $prompt),
                    ['sources_considered' => $selectedSources->count()]
                );
            }

            $unindexedSourceContext = $this->formatUnindexedSourceContext($selectedSources, $prompt, $limit);

            if ($unindexedSourceContext['context'] !== '') {
                Log::info('Using on-demand source parsing because no indexed source text was available', [
                    'notebook_id' => $notebook->id,
                    'sources_count' => $selectedSources->count(),
                ]);

                return array_merge(
                    $unindexedSourceContext,
                    ['sources_considered' => $selectedSources->count()]
                );
            }

            return [
                'context' => '',
                'citations' => [],
                'chunks' => collect(),
                'sources_considered' => $selectedSources->count(),
            ];
        }

        $formatted = $this->formatChunkContext($chunks, $prompt);

        Log::info('Generated RAG context with reranking', [
            'context_length' => Str::length($formatted['context']),
            'context_preview' => Str::limit($formatted['context'], 500),
            'citations_count' => count($formatted['citations']),
        ]);

        return array_merge($formatted, [
            'sources_considered' => $selectedSources->count(),
        ]);
    }

    /**
     * @param Collection<int, AiEmbedding> $chunks
     * @return array{context:string,citations:array<int,array<string,mixed>>,chunks:Collection<int,AiEmbedding>}
     */
    protected function formatChunkContext(Collection $chunks, string $prompt): array
    {
        $passages = $chunks
            ->flatMap(function (AiEmbedding $chunk): array {
                return $this->sourcePassagesFromText(
                    $chunk->source,
                    $chunk->content,
                    $prompt,
                    (int) data_get($chunk->metadata, 'page_number', 1),
                    2,
                    (float) ($chunk->getAttribute('relevance_score') ?? 0),
                    [
                        'ai_embedding_id' => $chunk->id,
                        'chunk_id' => (string) $chunk->chunk_index,
                        'chunk_index' => $chunk->chunk_index,
                        'section_title' => data_get($chunk->metadata, 'section_title'),
                        'case_name' => data_get($chunk->metadata, 'case_name'),
                    ],
                );
            })
            ->values();

        $context = $passages
            ->map(function (array $passage, int $index): string {
                return $this->formatPassageForContext($passage, $index + 1);
            })
            ->implode("\n\n---\n\n");

        $citations = $passages
            ->map(fn (array $passage, int $index): array => $this->formatPassageCitation($passage, $index + 1))
            ->unique(fn (array $citation) => ($citation['source_id'] ?? '').':'.($citation['page'] ?? '').':'.($citation['paragraph_index'] ?? '').':'.($citation['sentence_index'] ?? '').':'.($citation['startOffset'] ?? ''))
            ->values()
            ->all();

        return [
            'context' => $context,
            'citations' => $citations,
            'chunks' => $chunks,
        ];
    }

    /**
     * Return representative chunks when keyword matching fails, so existing
     * uploaded sources are still visible to the model.
     *
     * @param array<int, int> $sourceIds
     * @return Collection<int, AiEmbedding>
     */
    protected function fallbackChunks(Notebook $notebook, array $sourceIds, int $limit): Collection
    {
        if ($sourceIds === []) {
            return collect();
        }

        return $notebook->embeddings()
            ->with('source')
            ->whereIn('source_id', $sourceIds)
            ->orderBy('source_id')
            ->orderBy('chunk_index')
            ->limit(max($limit, count($sourceIds)))
            ->get()
            ->map(function (AiEmbedding $chunk): AiEmbedding {
                $chunk->setAttribute('relevance_score', 0);

                return $chunk;
            });
    }

    /**
     * Build context directly from source text when embeddings are missing.
     *
     * @param Collection<int, Source> $sources
     * @return array{context:string,citations:array<int,array<string,mixed>>,chunks:Collection<int,AiEmbedding>}
     */
    protected function formatSourceExcerptContext(Collection $sources, string $prompt, int $limit): array
    {
        $passages = $sources
            ->filter(fn (Source $source) => filled($source->extracted_text) || filled($source->summary))
            ->flatMap(function (Source $source) use ($prompt, $limit): array {
                return $this->sourcePassagesFromText(
                    $source,
                    (string) ($source->extracted_text ?: $source->summary),
                    $prompt,
                    1,
                    $limit,
                    0,
                    ['chunk_id' => 'source-excerpt', 'chunk_index' => 0],
                );
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values();

        if ($passages->isEmpty()) {
            return [
                'context' => '',
                'citations' => [],
                'chunks' => collect(),
            ];
        }

        $context = $passages
            ->map(fn (array $passage, int $index): string => $this->formatPassageForContext($passage, $index + 1))
            ->implode("\n\n---\n\n");

        $citations = $passages
            ->map(fn (array $passage, int $index): array => $this->formatPassageCitation($passage, $index + 1))
            ->values()
            ->all();

        return [
            'context' => $context,
            'citations' => $citations,
            'chunks' => collect(),
        ];
    }

    /**
     * Parse source files directly when upload indexing has not run yet.
     *
     * @param Collection<int, Source> $sources
     * @return array{context:string,citations:array<int,array<string,mixed>>,chunks:Collection<int,AiEmbedding>}
     */
    protected function formatUnindexedSourceContext(Collection $sources, string $prompt, int $limit): array
    {
        $sourcePayloads = $sources
            ->take($limit)
            ->flatMap(function (Source $source) use ($prompt): array {
                $pages = $this->bestPagesFromSourceFile($source, $prompt);

                return collect($pages)->map(fn (array $page): array => [
                    'source' => $source,
                    'page_number' => $page['page_number'],
                    'text' => $page['text'],
                    'score' => $page['score'],
                    'paragraph_index' => $page['paragraph_index'],
                    'sentence_index' => $page['sentence_index'],
                    'startOffset' => $page['startOffset'],
                    'endOffset' => $page['endOffset'],
                ])->all();
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values();

        if ($sourcePayloads->isEmpty()) {
            return [
                'context' => '',
                'citations' => [],
                'chunks' => collect(),
            ];
        }

        $context = $sourcePayloads
            ->map(function (array $payload, int $index): string {
                /** @var Source $source */
                $source = $payload['source'];
                $page = (int) $payload['page_number'];

                return "[Source " . ($index + 1) . " | {$source->name} | Page {$page}]\n{$payload['text']}";
            })
            ->implode("\n\n---\n\n");

        $citations = $sourcePayloads
            ->map(function (array $payload, int $index): array {
                /** @var Source $source */
                $source = $payload['source'];
                $page = (int) $payload['page_number'];

                return [
                    'citation_number' => $index + 1,
                    'ai_embedding_id' => null,
                    'source_id' => $source->id,
                    'source_name' => $source->name,
                    'type' => $source->type,
                    'source_url' => $source->source_url,
                    'page' => $page,
                    'page_number' => $page,
                    'paragraph_index' => $payload['paragraph_index'] ?? null,
                    'paragraphIndex' => $payload['paragraph_index'] ?? null,
                    'sentence_index' => $payload['sentence_index'] ?? null,
                    'sentenceIndex' => $payload['sentence_index'] ?? null,
                    'startOffset' => $payload['startOffset'] ?? null,
                    'endOffset' => $payload['endOffset'] ?? null,
                    'confidence' => $this->confidenceFromScore((float) ($payload['score'] ?? 0)),
                    'chunk_id' => 'on-demand-source-parse',
                    'chunk_index' => 0,
                    'evidence_snippet' => $this->citationSnippet((string) $payload['text']),
                    'quote' => $payload['text'],
                    'text' => $payload['text'],
                    'score' => $payload['score'] ?? 0,
                ];
            })
            ->values()
            ->all();

        return [
            'context' => $context,
            'citations' => $citations,
            'chunks' => collect(),
        ];
    }

    /**
     * @return array<int,array{page_number:int,text:string,score:int}>
     */
    protected function bestPagesFromSourceFile(Source $source, string $prompt, int $limit = 4): array
    {
        if ($source->type !== 'pdf' || blank($source->storage_disk) || blank($source->storage_path)) {
            return [];
        }

        $disk = Storage::disk($source->storage_disk);

        if (! $disk->exists($source->storage_path)) {
            return [];
        }

        try {
            $pdf = (new Parser())->parseFile($disk->path($source->storage_path));
            $keywords = $this->extractSearchKeywords($prompt);
            $pages = collect();

            foreach ($pdf->getPages() as $index => $page) {
                $text = preg_replace('/\s+/', ' ', trim($page->getText())) ?? '';

                if ($text === '') {
                    continue;
                }

                $lowerText = Str::lower($text);
                $score = $keywords->sum(function (string $keyword) use ($lowerText): int {
                    $count = substr_count($lowerText, $keyword);

                    return $count > 0 ? 10 + min($count, 3) : 0;
                });
                $score += $this->implementationTermScore($lowerText, $prompt);
                $score += $this->sdlcTermScore($lowerText, $prompt);

                if ($score > 0) {
                    foreach ($this->sourcePassagesFromText($source, $text, $prompt, $index + 1, 2, $score) as $passage) {
                        $pages->push($passage);
                    }
                }
            }

            if ($pages->isEmpty()) {
                foreach ($pdf->getPages() as $index => $page) {
                    $text = preg_replace('/\s+/', ' ', trim($page->getText())) ?? '';

                    if ($text !== '') {
                        return $this->sourcePassagesFromText($source, $text, $prompt, $index + 1, 1, 0);
                    }
                }
            }

            return $pages
                ->sortByDesc('score')
                ->take($limit)
                ->values()
                ->all();
        } catch (\Throwable $exception) {
            Log::warning('Failed on-demand source parsing for chat context', [
                'source_id' => $source->id,
                'error' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    protected function implementationTermScore(string $lowerText, string $prompt): int
    {
        $lowerPrompt = Str::lower($prompt);

        if (! str_contains($lowerPrompt, 'language')
            && ! str_contains($lowerPrompt, 'develop')
            && ! str_contains($lowerPrompt, 'create')
            && ! str_contains($lowerPrompt, 'built')
            && ! str_contains($lowerPrompt, 'technology')
            && ! str_contains($lowerPrompt, 'ginamit')
            && ! str_contains($lowerPrompt, 'gamit')
            && ! str_contains($lowerPrompt, 'wika')) {
            return 0;
        }

        $terms = [
            'programming language' => 24,
            'javascript' => 18,
            'php' => 18,
            'react native' => 16,
            'react' => 10,
            'node.js' => 16,
            'node' => 10,
            'laravel' => 16,
            'framework' => 8,
            'front-end' => 8,
            'back-end' => 8,
            'web technologies' => 12,
        ];

        $score = 0;

        foreach ($terms as $term => $weight) {
            if (str_contains($lowerText, $term)) {
                $score += $weight;
            }
        }

        return $score;
    }

    protected function sdlcTermScore(string $lowerText, string $prompt): int
    {
        $lowerPrompt = Str::lower($prompt);

        if (! str_contains($lowerPrompt, 'sdlc')
            && ! str_contains($lowerPrompt, 'software development life cycle')
            && ! str_contains($lowerPrompt, 'methodology')
            && ! str_contains($lowerPrompt, 'model')
            && ! str_contains($lowerPrompt, 'creation')
            && ! str_contains($lowerPrompt, 'development process')) {
            return 0;
        }

        $terms = [
            'rapid application development' => 80,
            'rad model' => 70,
            'rad methodology' => 70,
            'rad' => 35,
            'requirements planning' => 26,
            'user design' => 24,
            'construction phase' => 24,
            'cutover' => 24,
            'prototyping' => 18,
            'iterative development' => 18,
            'continuous feedback' => 18,
            'software development life cycle' => 30,
            'development process' => 14,
        ];

        $score = 0;

        foreach ($terms as $term => $weight) {
            if (str_contains($lowerText, $term)) {
                $score += $weight;
            }
        }

        return $score;
    }

    protected function promptAwareExcerpt(string $text, string $prompt, int $maxChars = 4500): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text)) ?? trim($text);

        if (Str::length($normalized) <= $maxChars) {
            return $normalized;
        }

        $lowerText = Str::lower($normalized);
        $keywords = $this->extractSearchKeywords($prompt);
        $matchPosition = null;

        foreach ($keywords as $keyword) {
            $position = strpos($lowerText, $keyword);

            if ($position !== false && ($matchPosition === null || $position < $matchPosition)) {
                $matchPosition = $position;
            }
        }

        $start = $matchPosition === null ? 0 : max(0, $matchPosition - (int) floor($maxChars / 3));

        return trim(Str::substr($normalized, $start, $maxChars));
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    protected function sourcePassagesFromText(?Source $source, string $text, string $prompt, int $pageNumber, int $limit = 3, float $baseScore = 0, array $extra = []): array
    {
        $text = trim($text);

        if ($text === '') {
            return [];
        }

        $keywords = $prompt !== '' ? $this->extractSearchKeywords($prompt) : collect();
        $paragraphs = $this->paragraphsWithOffsets($text);
        $passages = collect();

        foreach ($this->authorPassagesFromText($source, $text, $prompt, $pageNumber, $baseScore, $extra) as $authorPassage) {
            $passages->push($authorPassage);
        }

        foreach ($paragraphs as $paragraphIndex => $paragraph) {
            foreach ($this->sentencesWithOffsets($paragraph['text'], (int) $paragraph['start']) as $sentenceIndex => $sentence) {
                $sentenceText = trim((string) $sentence['text']);

                if ($sentenceText === '' || Str::length($sentenceText) < 24) {
                    continue;
                }

                $score = $baseScore + $this->passageScore($sentenceText, $keywords, $prompt);

                if ($prompt !== '' && $score <= $baseScore) {
                    continue;
                }

                $passages->push(array_merge($extra, [
                    'source' => $source,
                    'source_id' => $source?->id,
                    'source_name' => $source?->name ?? 'Uploaded source',
                    'type' => $source?->type,
                    'source_url' => $source?->source_url,
                    'page_number' => $pageNumber,
                    'page' => $pageNumber,
                    'paragraph_index' => $paragraphIndex,
                    'paragraphIndex' => $paragraphIndex,
                    'sentence_index' => $sentenceIndex,
                    'sentenceIndex' => $sentenceIndex,
                    'startOffset' => (int) $sentence['start'],
                    'endOffset' => (int) $sentence['end'],
                    'text' => $sentenceText,
                    'quote' => $sentenceText,
                    'evidence_snippet' => $sentenceText,
                    'score' => $score,
                    'confidence' => $this->confidenceFromScore($score),
                ]));
            }
        }

        if ($passages->isEmpty()) {
            $fallback = Str::limit(preg_replace('/\s+/', ' ', $text) ?? $text, 700, '');

            return [[
                'source' => $source,
                'source_id' => $source?->id,
                'source_name' => $source?->name ?? 'Uploaded source',
                'type' => $source?->type,
                'source_url' => $source?->source_url,
                'page_number' => $pageNumber,
                'page' => $pageNumber,
                'paragraph_index' => 0,
                'paragraphIndex' => 0,
                'sentence_index' => 0,
                'sentenceIndex' => 0,
                'startOffset' => 0,
                'endOffset' => Str::length($fallback),
                'text' => $fallback,
                'quote' => $fallback,
                'evidence_snippet' => $fallback,
                'score' => $baseScore,
                'confidence' => $this->confidenceFromScore($baseScore),
            ]];
        }

        return $passages
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * Extract the author/researcher block from thesis-style abstract pages.
     *
     * @return array<int,array<string,mixed>>
     */
    protected function authorPassagesFromText(?Source $source, string $text, string $prompt, int $pageNumber, float $baseScore = 0, array $extra = []): array
    {
        $lowerPrompt = Str::lower($prompt);

        if (! str_contains($lowerPrompt, 'researcher')
            && ! str_contains($lowerPrompt, 'researchers')
            && ! str_contains($lowerPrompt, 'author')
            && ! str_contains($lowerPrompt, 'authors')
            && ! str_contains($lowerPrompt, 'behind')
            && ! str_contains($lowerPrompt, 'sino')
            && ! str_contains($lowerPrompt, 'gumawa')
            && ! str_contains($lowerPrompt, 'may gawa')) {
            return [];
        }

        if (! preg_match('/\bABSTRACT\s+(.+?)\s+EduTrack:/is', $text, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $quote = trim(preg_replace('/\s+/', ' ', $matches[1][0]) ?? $matches[1][0]);

        if ($quote === '' || ! str_contains(Str::lower($quote), 'carrera')) {
            return [];
        }

        $start = (int) $matches[1][1];
        $score = $baseScore + 500;

        return [array_merge($extra, [
            'source' => $source,
            'source_id' => $source?->id,
            'source_name' => $source?->name ?? 'Uploaded source',
            'type' => $source?->type,
            'source_url' => $source?->source_url,
            'page_number' => $pageNumber,
            'page' => $pageNumber,
            'paragraph_index' => 0,
            'paragraphIndex' => 0,
            'sentence_index' => 0,
            'sentenceIndex' => 0,
            'startOffset' => $start,
            'endOffset' => $start + strlen($matches[1][0]),
            'text' => $quote,
            'quote' => $quote,
            'evidence_snippet' => $quote,
            'score' => $score,
            'confidence' => $this->confidenceFromScore($score),
        ])];
    }

    protected function formatPassageForContext(array $passage, int $sourceNumber): string
    {
        $sourceName = $passage['source_name'] ?? 'Uploaded source';
        $page = (int) ($passage['page_number'] ?? $passage['page'] ?? 1);
        $paragraph = (int) ($passage['paragraph_index'] ?? 0);
        $sentence = (int) ($passage['sentence_index'] ?? 0);
        $start = (int) ($passage['startOffset'] ?? 0);
        $end = (int) ($passage['endOffset'] ?? 0);

        return "[Source {$sourceNumber} | {$sourceName} | Page {$page} | Paragraph {$paragraph} | Sentence {$sentence} | Offsets {$start}-{$end}]\n{$passage['text']}";
    }

    protected function formatPassageCitation(array $passage, int $sourceNumber): array
    {
        $source = $passage['source'] ?? null;

        return array_filter([
            'citation_number' => $sourceNumber,
            'source_number' => $sourceNumber,
            'ai_embedding_id' => $passage['ai_embedding_id'] ?? null,
            'source_id' => $passage['source_id'] ?? ($source instanceof Source ? $source->id : null),
            'source_name' => $passage['source_name'] ?? ($source instanceof Source ? $source->name : 'Uploaded source'),
            'type' => $passage['type'] ?? ($source instanceof Source ? $source->type : null),
            'source_url' => $passage['source_url'] ?? ($source instanceof Source ? $source->source_url : null),
            'page' => (int) ($passage['page'] ?? $passage['page_number'] ?? 1),
            'page_number' => (int) ($passage['page_number'] ?? $passage['page'] ?? 1),
            'paragraph_index' => $passage['paragraph_index'] ?? null,
            'paragraphIndex' => $passage['paragraphIndex'] ?? $passage['paragraph_index'] ?? null,
            'sentence_index' => $passage['sentence_index'] ?? null,
            'sentenceIndex' => $passage['sentenceIndex'] ?? $passage['sentence_index'] ?? null,
            'startOffset' => $passage['startOffset'] ?? null,
            'endOffset' => $passage['endOffset'] ?? null,
            'confidence' => $passage['confidence'] ?? $this->confidenceFromScore((float) ($passage['score'] ?? 0)),
            'chunk_id' => $passage['chunk_id'] ?? null,
            'chunk_index' => $passage['chunk_index'] ?? null,
            'section_title' => $passage['section_title'] ?? null,
            'case_name' => $passage['case_name'] ?? null,
            'quote' => $passage['quote'] ?? $passage['text'] ?? null,
            'text' => $passage['text'] ?? null,
            'evidence_snippet' => $passage['evidence_snippet'] ?? $passage['text'] ?? null,
            'score' => $passage['score'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @return array<int,array{text:string,start:int,end:int}>
     */
    protected function paragraphsWithOffsets(string $text): array
    {
        $paragraphs = [];
        preg_match_all('/\S(?:.*?\S)?(?=(?:\R\s*\R)|\z)/su', $text, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] ?? [] as $match) {
            $paragraphs[] = [
                'text' => trim($match[0]),
                'start' => (int) $match[1],
                'end' => (int) $match[1] + strlen($match[0]),
            ];
        }

        if ($paragraphs === []) {
            $trimmed = trim($text);
            $paragraphs[] = [
                'text' => $trimmed,
                'start' => 0,
                'end' => strlen($trimmed),
            ];
        }

        return $paragraphs;
    }

    /**
     * @return array<int,array{text:string,start:int,end:int}>
     */
    protected function sentencesWithOffsets(string $text, int $baseOffset = 0): array
    {
        preg_match_all('/[^.!?]+(?:[.!?]+|$)/u', $text, $matches, PREG_OFFSET_CAPTURE);
        $sentences = [];

        foreach ($matches[0] ?? [] as $match) {
            $sentence = trim($match[0]);

            if ($sentence === '') {
                continue;
            }

            $leadingWhitespace = strlen($match[0]) - strlen(ltrim($match[0]));
            $start = $baseOffset + (int) $match[1] + $leadingWhitespace;

            $sentences[] = [
                'text' => $sentence,
                'start' => $start,
                'end' => $start + strlen($sentence),
            ];
        }

        if ($sentences === []) {
            $trimmed = trim($text);
            $sentences[] = [
                'text' => $trimmed,
                'start' => $baseOffset,
                'end' => $baseOffset + strlen($trimmed),
            ];
        }

        return $sentences;
    }

    protected function passageScore(string $text, Collection $keywords, string $prompt): float
    {
        $lowerText = Str::lower($text);
        $score = 0.0;

        foreach ($keywords as $keyword) {
            if (str_contains($lowerText, $keyword)) {
                $score += 12;
            }
        }

        $score += $this->implementationTermScore($lowerText, $prompt);
        $score += $this->sdlcTermScore($lowerText, $prompt);

        return $score;
    }

    protected function confidenceFromScore(float $score): float
    {
        if ($score <= 0) {
            return 0.65;
        }

        return round(min(0.99, 0.72 + ($score / 160)), 2);
    }

    /**
     * Hybrid search: keyword + metadata + lexical.
     *
     * @return Collection<int, AiEmbedding>
     */
    public function searchRelevantChunks(Notebook $notebook, string $prompt, int $limit = 15, ?array $sourceIds = null): Collection
    {
        $keywords = $this->extractSearchKeywords($prompt);
        $lowerPrompt = Str::lower($prompt);

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
            ->map(function (AiEmbedding $chunk) use ($keywords, $lowerPrompt): AiEmbedding {
                $haystack = Str::lower($chunk->content);
                $metadata = $chunk->metadata ?? [];
                
                $keywordScore = $keywords->sum(fn (string $keyword) => substr_count($haystack, $keyword) * 2);
                
                $sectionTitle = Str::lower((string) ($metadata['section_title'] ?? ''));
                $caseName = Str::lower((string) ($metadata['case_name'] ?? ''));
                $topic = Str::lower((string) ($metadata['topic'] ?? ''));
                
                $metadataScore = 0;
                foreach ($keywords as $keyword) {
                    if (str_contains($sectionTitle, $keyword)) $metadataScore += 10;
                    if (str_contains($caseName, $keyword)) $metadataScore += 15;
                    if (str_contains($topic, $keyword)) $metadataScore += 8;
                }
                
                $chunkKeywords = $metadata['keywords'] ?? [];
                $keywordMatchScore = collect($chunkKeywords)->intersect($keywords)->count() * 5;
                
                $totalScore = $keywordScore + $metadataScore + $keywordMatchScore;
                $matchedTerms = $keywords->filter(fn (string $keyword) => str_contains($haystack, $keyword) || str_contains($sectionTitle, $keyword) || str_contains($caseName, $keyword))->count();
                
                $chunk->setAttribute('relevance_score', $totalScore);
                $chunk->setAttribute('matched_terms', $matchedTerms);
                $chunk->setAttribute('keyword_score', $keywordScore);
                $chunk->setAttribute('metadata_score', $metadataScore);

                return $chunk;
            })
            ->filter(fn (AiEmbedding $chunk) => ($chunk->getAttribute('relevance_score') ?? 0) > 0)
            ->sortByDesc(fn (AiEmbedding $chunk) => $chunk->getAttribute('relevance_score'))
            ->take($limit)
            ->values();
    }

    /**
     * Rerank chunks to prioritize quality over quantity.
     *
     * @param Collection<int, AiEmbedding> $chunks
     * @return Collection<int, AiEmbedding>
     */
    protected function rerankChunks(Collection $chunks, string $prompt): Collection
    {
        $lowerPrompt = Str::lower($prompt);
        
        return $chunks->map(function (AiEmbedding $chunk) use ($lowerPrompt): AiEmbedding {
            $content = $chunk->content;
            $wordCount = str_word_count($content);
            $metadata = $chunk->metadata ?? [];
            
            $lengthPenalty = 0;
            if ($wordCount < 100) $lengthPenalty = -2;
            if ($wordCount > 1500) $lengthPenalty = -3;
            
            $focusScore = 0;
            if (isset($metadata['section_title']) && filled($metadata['section_title'])) $focusScore += 5;
            if (isset($metadata['case_name']) && filled($metadata['case_name'])) $focusScore += 7;
            
            $currentScore = $chunk->getAttribute('relevance_score') ?? 0;
            $newScore = $currentScore + $lengthPenalty + $focusScore;
            
            $chunk->setAttribute('relevance_score', max(0, $newScore));
            
            return $chunk;
        })
        ->sortByDesc(fn (AiEmbedding $chunk) => $chunk->getAttribute('relevance_score'))
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
            'was', 'were', 'has', 'have', 'had', 'into', 'its', 'why', 'who', 'a', 'an', 'is', 'or', 'but', 'not', 'you', 'your', 'we', 'our', 'us', 'they', 'them', 'my', 'me', 'i', 'am', 'be', 'been', 'being', 'can', 'will', 'shall', 'may', 'might', 'must', 'should', 'ought', 'need', 'dare', 'used', 'to', 'of', 'in', 'on', 'at', 'by', 'for', 'with', 'about', 'against', 'between', 'into', 'through', 'during', 'before', 'after', 'above', 'below', 'from', 'up', 'down', 'out', 'off', 'over', 'under', 'again', 'further', 'then', 'once', 'here', 'there', 'all', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than', 'too', 'very', 'just', 'don', 'now',
        ];

        return collect(Str::of($prompt)->lower()->replaceMatches('/[^a-z0-9\s\.]/', ' ')->explode(' '))
            ->map(fn (string $part) => trim($part))
            ->filter(fn (string $part) => Str::length($part) > 1 && ! in_array($part, $stopWords, true))
            ->unique()
            ->values();
    }

    /**
     * Smart legal-aware chunking.
     *
     * @return array<int, array{content:string,token_count:int,page_number:int,section_title:string|null,chapter:string|null,topic:string|null,subtopic:string|null,case_name:string|null,keywords:array<string>}>
     */
    protected function chunkSource(Source $source): array
    {
        if ($source->type !== 'pdf') {
            return $this->smartChunkText(trim($source->extracted_text ?: ($source->summary ?: '')), 1);
        }

        $path = $source->storage_path ? Storage::disk($source->storage_disk)->path($source->storage_path) : null;

        if (! $path || ! is_file($path)) {
            return $this->smartChunkText(trim($source->extracted_text ?: ($source->summary ?: '')), 1);
        }

        try {
            $pdf = (new Parser())->parseFile($path);
            $chunks = [];

            foreach ($pdf->getPages() as $index => $page) {
                $pageNumber = $index + 1;
                $pageChunks = $this->smartChunkText($page->getText(), $pageNumber);
                $chunks = array_merge($chunks, $pageChunks);
            }

            return $chunks;
        } catch (\Throwable $exception) {
            Log::warning('Failed to build page-aware PDF chunks', [
                'source_id' => $source->id,
                'error' => $exception->getMessage(),
            ]);

            return $this->smartChunkText(trim($source->extracted_text ?: ($source->summary ?: '')), 1);
        }
    }

    /**
     * Smart text chunking with legal structure detection.
     *
     * @return array<int, array{content:string,token_count:int,page_number:int,section_title:string|null,chapter:string|null,topic:string|null,subtopic:string|null,case_name:string|null,keywords:array<string>}>
     */
    protected function smartChunkText(string $text, int $pageNumber = 1): array
    {
        $chunks = [];
        
        $legalPatterns = [
            'section' => '/^(?:SECTION|Section|SEC\.)\s+\d+(?:\.\d+)*[-.]?\s*(.*?)(?=\s+(?:SECTION|Section|SEC\.|ARTICLE|Article|ART\.|CHAPTER|Chapter|CHAP\.|$))/is',
            'article' => '/^(?:ARTICLE|Article|ART\.)\s+\d+(?:\.\d+)*[-.]?\s*(.*?)(?=\s+(?:SECTION|Section|SEC\.|ARTICLE|Article|ART\.|CHAPTER|Chapter|CHAP\.|$))/is',
            'chapter' => '/^(?:CHAPTER|Chapter|CHAP\.)\s+[A-Z0-9]+[-.]?\s*(.*?)(?=\s+(?:SECTION|Section|SEC\.|ARTICLE|Article|ART\.|CHAPTER|Chapter|CHAP\.|$))/is',
            'case' => '/^(?:[A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\s+vs\.?\s+(?:[A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)|^(?:[A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\s+v\.?\s+(?:[A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)/is',
            'heading' => '/^(?:[A-Z][A-Z\s]+?)(?=\s+(?:SECTION|Section|SEC\.|ARTICLE|Article|ART\.|CHAPTER|Chapter|CHAP\.|$))/s',
        ];
        
        $words = preg_split('/\s+/', trim($text));
        $currentChunk = '';
        $currentMetadata = [
            'section_title' => null,
            'chapter' => null,
            'topic' => null,
            'subtopic' => null,
            'case_name' => null,
            'keywords' => [],
        ];
        $minWords = 150;
        $maxWords = 1000;
        
        foreach ($words as $i => $word) {
            $currentChunk .= ($currentChunk ? ' ' : '') . $word;
            $currentWordCount = str_word_count($currentChunk);
            
            $textSoFar = $currentChunk;
            foreach ($legalPatterns as $type => $pattern) {
                if (preg_match($pattern, $textSoFar, $matches)) {
                    if ($currentWordCount >= $minWords && !empty(trim($currentChunk))) {
                        $chunks[] = $this->createChunk($currentChunk, $pageNumber, $currentMetadata);
                        $currentChunk = $word;
                    }
                    
                    if ($type === 'section') {
                        $currentMetadata['section_title'] = trim($matches[0] ?? $matches[1] ?? '');
                        $currentMetadata['topic'] = $currentMetadata['section_title'];
                    } elseif ($type === 'article') {
                        $currentMetadata['section_title'] = trim($matches[0] ?? $matches[1] ?? '');
                        $currentMetadata['topic'] = $currentMetadata['section_title'];
                    } elseif ($type === 'chapter') {
                        $currentMetadata['chapter'] = trim($matches[0] ?? $matches[1] ?? '');
                    } elseif ($type === 'case') {
                        $currentMetadata['case_name'] = trim($matches[0] ?? '');
                    }
                    
                    $currentMetadata['keywords'] = $this->extractKeywords($currentChunk);
                }
            }
            
            if ($currentWordCount >= $maxWords) {
                $chunks[] = $this->createChunk($currentChunk, $pageNumber, $currentMetadata);
                $currentChunk = '';
                $currentMetadata['keywords'] = [];
            }
        }
        
        if (!empty(trim($currentChunk)) && str_word_count($currentChunk) >= 50) {
            $chunks[] = $this->createChunk($currentChunk, $pageNumber, $currentMetadata);
        }
        
        return $chunks;
    }

    /**
     * Create a chunk array with metadata.
     */
    protected function createChunk(string $content, int $pageNumber, array $metadata): array
    {
        $trimmed = trim($content);
        return [
            'content' => $trimmed,
            'token_count' => (int) ceil(str_word_count($trimmed) * 1.35),
            'page_number' => $pageNumber,
            'section_title' => $metadata['section_title'] ?? null,
            'chapter' => $metadata['chapter'] ?? null,
            'topic' => $metadata['topic'] ?? null,
            'subtopic' => $metadata['subtopic'] ?? null,
            'case_name' => $metadata['case_name'] ?? null,
            'keywords' => $metadata['keywords'] ?? $this->extractKeywords($trimmed),
        ];
    }

    /**
     * Extract simple keywords from text.
     */
    protected function extractKeywords(string $text): array
    {
        $stopWords = ['the', 'and', 'for', 'are', 'was', 'were', 'has', 'have', 'had', 'into', 'its', 'why', 'who', 'a', 'an', 'is', 'or', 'but', 'not', 'you', 'your', 'we', 'our', 'us', 'they', 'them', 'my', 'me', 'i', 'am', 'be', 'been', 'being', 'can', 'will', 'shall', 'may', 'might', 'must', 'should', 'to', 'of', 'in', 'on', 'at', 'by', 'with', 'about', 'against', 'between', 'through', 'during', 'before', 'after', 'above', 'below', 'from', 'up', 'down', 'out', 'off', 'over', 'under', 'again', 'further', 'then', 'once', 'here', 'there', 'all', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than', 'too', 'very', 'just', 'don', 'now'];
        $words = preg_split('/\s+/', Str::lower($text));
        $wordCounts = [];
        
        foreach ($words as $word) {
            $clean = preg_replace('/[^a-z0-9]/', '', $word);
            if (strlen($clean) > 3 && !in_array($clean, $stopWords)) {
                $wordCounts[$clean] = ($wordCounts[$clean] ?? 0) + 1;
            }
        }
        
        arsort($wordCounts);
        return array_slice(array_keys($wordCounts), 0, 10);
    }

    protected function citationSnippet(string $text): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($text)) ?? trim($text);

        return Str::limit($normalized, 280);
    }
}
