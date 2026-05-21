<?php

namespace App\Jobs;

use App\Models\Source;
use App\Services\DocumentIngestionService;
use App\Services\NotebookRagService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Source $source) {}

    /**
     * Execute the job.
     */
    public function handle(DocumentIngestionService $ingestion, NotebookRagService $rag): void
    {
        $source = $this->source->fresh();

        if (! $source) {
            return;
        }

        try {
            $source->update(['status' => 'processing']);

            $payload = $ingestion->extractContent($source);

            $source->update([
                'status' => 'indexed',
                'summary' => $payload['summary'],
                'extracted_text' => $payload['text'],
                'metadata' => array_merge($source->metadata ?? [], $payload['metadata']),
                'indexed_at' => now(),
                'last_processed_at' => now(),
            ]);

            $rag->syncSourceEmbeddings($source);
        } catch (\Throwable $e) {
            Log::error('Failed processing source', [
                'source_id' => $source->id,
                'exception' => $e,
            ]);

            $source->update([
                'status' => 'failed',
                'metadata' => array_merge($source->metadata ?? [], [
                    'last_error' => $e->getMessage(),
                ]),
                'last_processed_at' => now(),
            ]);

            throw $e;
        }
    }
}
