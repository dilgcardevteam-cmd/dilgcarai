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

        $chunks = $this->searchRelevantChunks($notebook, $prompt, 15, $selectedSources->pluck('id')->all());
        $chunks = $this->rerankChunks($chunks, $prompt)->take($limit);

        if ($chunks->isEmpty()) {
            return [
                'context' => '',
                'citations' => [],
                'chunks' => collect(),
                'sources_considered' => $selectedSources->count(),
            ];
        }

        $context = $chunks
            ->map(function (AiEmbedding $chunk, $index) {
                $source = $chunk->source;
                $page = (int) data_get($chunk->metadata, 'page_number', 1);
                $sectionTitle = data_get($chunk->metadata, 'section_title');

                return "[Source " . ($index + 1) . " | {$source?->name}" . ($sectionTitle ? " | {$sectionTitle}" : '') . " | Page {$page}]\n{$chunk->content}";
            })
            ->implode("\n\n---\n\n");

        $citations = $chunks
            ->map(fn (AiEmbedding $chunk, $index) => [
                'citation_number' => $index + 1,
                'ai_embedding_id' => $chunk->id,
                'source_id' => $chunk->source?->id,
                'source_name' => $chunk->source?->name ?? 'Uploaded source',
                'type' => $chunk->source?->type,
                'source_url' => $chunk->source?->source_url,
                'page' => (int) data_get($chunk->metadata, 'page_number', 1),
                'page_number' => (int) data_get($chunk->metadata, 'page_number', 1),
                'section_title' => data_get($chunk->metadata, 'section_title'),
                'case_name' => data_get($chunk->metadata, 'case_name'),
                'chunk_id' => (string) $chunk->chunk_index,
                'chunk_index' => $chunk->chunk_index,
                'evidence_snippet' => $this->citationSnippet($chunk->content),
                'text' => $chunk->content,
                'score' => $chunk->getAttribute('relevance_score'),
            ])
            ->unique(fn (array $citation) => ($citation['source_id'] ?? '').':'.($citation['chunk_index'] ?? ''))
            ->values()
            ->all();

        Log::info('Generated RAG context with reranking', [
            'context_length' => Str::length($context),
            'context_preview' => Str::limit($context, 500),
            'citations_count' => count($citations),
        ]);

        return [
            'context' => $context,
            'citations' => $citations,
            'chunks' => $chunks,
            'sources_considered' => $selectedSources->count(),
        ];
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
