<?php

namespace App\Services;

use App\Jobs\ProcessSourceJob;
use App\Models\Notebook;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use ZipArchive;

class DocumentIngestionService
{
    protected const DEFAULT_MAX_EXTRACTED_TEXT_CHARS = 200_000;

    public function __construct(protected ActivityLogger $activityLogger, protected NotebookRagService $rag) {}

    /**
     * Create a new notebook source and queue it for processing.
     */
    public function createSource(Notebook $notebook, array $validated, ?UploadedFile $file, ?User $user): Source
    {
        $type = $validated['source_type'];
        $storagePath = $file?->store("notebooks/{$notebook->id}/sources", 'public');
        $sourceUrl = $validated['source_url'] ?? null;
        $name = $validated['title']
            ?? $file?->getClientOriginalName()
            ?? parse_url((string) $sourceUrl, PHP_URL_HOST)
            ?? Str::headline($type.' source');

        Log::info('Creating source', [
            'filename' => $file?->getClientOriginalName(),
            'file_size' => $file?->getSize(),
            'notebook_id' => $notebook->id,
            'type' => $type,
        ]);

        $comprehensiveMetadata = $this->scanFile($file, $type);

        $source = $notebook->sources()->create([
            'uploaded_by' => $user?->id,
            'type' => $type,
            'name' => $name,
            'original_name' => $file?->getClientOriginalName(),
            'storage_disk' => 'public',
            'storage_path' => $storagePath,
            'source_url' => $sourceUrl,
            'mime_type' => $file?->getClientMimeType(),
            'file_size' => $file?->getSize(),
            'status' => 'queued',
            'content_hash' => $file ? sha1_file($file->getRealPath()) : sha1((string) $sourceUrl),
            'metadata' => array_merge([
                'notes' => $validated['notes'] ?? null,
                'extension' => $file?->getClientOriginalExtension(),
            ], $comprehensiveMetadata),
        ]);

        ProcessSourceJob::dispatch($source);

        $this->activityLogger->log(
            $user,
            'source.uploaded',
            "Uploaded source {$source->name}.",
            $notebook,
            $source
        );

        return $source;
    }

    /**
     * Comprehensive file scanning and analysis
     *
     * @param UploadedFile|null $file
     * @param string $type
     * @return array<string, mixed>
     */
    protected function scanFile(?UploadedFile $file, string $type): array
    {
        $metadata = [
            'scan_completed_at' => now()->toIso8601String(),
            'scan_status' => 'completed',
        ];

        if (!$file) {
            return array_merge($metadata, [
                'is_file_upload' => false,
                'type' => $type,
            ]);
        }

        $filePath = $file->getRealPath();
        $fileSize = $file->getSize();
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getClientMimeType();

        $metadata = array_merge($metadata, [
            'is_file_upload' => true,
            'file_type_detected' => $type,
            'file_extension' => $extension,
            'mime_type' => $mimeType,
            'file_size_bytes' => $fileSize,
            'file_size_human' => $this->formatFileSize($fileSize),
            'file_hash_sha1' => sha1_file($filePath),
            'file_hash_md5' => md5_file($filePath),
        ]);

        $securityScan = $this->securityScan($file, $extension, $mimeType);
        $metadata = array_merge($metadata, [
            'security_scan' => $securityScan,
        ]);

        $formatSpecs = $this->analyzeFormatSpecs($file, $extension, $type);
        $metadata = array_merge($metadata, [
            'format_specifications' => $formatSpecs,
        ]);

        $contentStructure = $this->analyzeContentStructure($file, $type);
        $metadata = array_merge($metadata, [
            'content_structure' => $contentStructure,
        ]);

        $additionalAttributes = $this->extractAdditionalAttributes($file, $extension);
        $metadata = array_merge($metadata, $additionalAttributes);

        return $metadata;
    }

    /**
     * Security threat detection scan
     *
     * @param UploadedFile $file
     * @param string $extension
     * @param string $mimeType
     * @return array<string, mixed>
     */
    protected function securityScan(UploadedFile $file, string $extension, string $mimeType): array
    {
        $threats = [];
        $warnings = [];
        $safeExtensions = ['pdf', 'docx', 'doc', 'txt', 'xlsx', 'xls', 'pptx', 'ppt', 'csv', 'rtf'];
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'sh', 'php', 'js', 'vbs', 'scr', 'com', 'pif', 'application', 'gadget', 'msi', 'msp', 'mst', 'ps1', 'ps1xml', 'psc1', 'psc2', 'scf', 'lnk', 'inf', 'reg', 'ws', 'wsf', 'wsc', 'wsh'];

        $extensionLower = strtolower($extension);

        if (in_array($extensionLower, $dangerousExtensions)) {
            $threats[] = "Potentially dangerous file extension detected: .{$extension}";
        }

        if (!in_array($extensionLower, array_merge($safeExtensions, $dangerousExtensions))) {
            $warnings[] = "Uncommon file extension: .{$extension}";
        }

        $fileSize = $file->getSize();
        $maxSize = 100 * 1024 * 1024;
        if ($fileSize > $maxSize) {
            $warnings[] = "Large file size detected (" . $this->formatFileSize($fileSize) . ")";
        }

        $filePath = $file->getRealPath();
        $content = file_get_contents($filePath);
        
        $suspiciousPatterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is' => 'Potential script tags found',
            '/eval\s*\(/i' => 'Potential eval() function found',
            '/base64_decode\s*\(/i' => 'Potential base64_decode() found',
            '/system\s*\(/i' => 'Potential system() function found',
            '/shell_exec\s*\(/i' => 'Potential shell_exec() function found',
            '/exec\s*\(/i' => 'Potential exec() function found',
        ];

        foreach ($suspiciousPatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $warnings[] = $description;
            }
        }

        return [
            'status' => empty($threats) ? 'clean' : 'flagged',
            'threats_found' => $threats,
            'warnings' => $warnings,
            'scan_timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Analyze format specifications
     *
     * @param UploadedFile $file
     * @param string $extension
     * @param string $type
     * @return array<string, mixed>
     */
    protected function analyzeFormatSpecs(UploadedFile $file, string $extension, string $type): array
    {
        $specs = [
            'type' => $type,
            'extension' => $extension,
        ];

        $filePath = $file->getRealPath();

        if ($extension === 'pdf') {
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($filePath);
                $details = $pdf->getDetails();
                
                $specs = array_merge($specs, [
                    'pdf_version' => $details['Producer'] ?? 'Unknown',
                    'page_count' => count($pdf->getPages()),
                    'title' => $details['Title'] ?? null,
                    'author' => $details['Author'] ?? null,
                    'subject' => $details['Subject'] ?? null,
                    'keywords' => $details['Keywords'] ?? null,
                    'creator' => $details['Creator'] ?? null,
                    'creation_date' => $details['CreationDate'] ?? null,
                    'modification_date' => $details['ModDate'] ?? null,
                ]);
            } catch (\Exception $e) {
                $specs['pdf_parse_error'] = $e->getMessage();
            }
        }

        return $specs;
    }

    /**
     * Analyze content structure
     *
     * @param UploadedFile $file
     * @param string $type
     * @return array<string, mixed>
     */
    protected function analyzeContentStructure(UploadedFile $file, string $type): array
    {
        $structure = [
            'type' => $type,
        ];

        $filePath = $file->getRealPath();
        $content = file_get_contents($filePath);

        $lineCount = substr_count($content, "\n") + 1;
        $wordCount = str_word_count($content);
        $charCount = strlen($content);

        $structure = array_merge($structure, [
            'line_count' => $lineCount,
            'word_count' => $wordCount,
            'character_count' => $charCount,
            'estimated_reading_time_minutes' => ceil($wordCount / 200),
        ]);

        $headingCount = preg_match_all('/^#+\s+/m', $content);
        if ($headingCount > 0) {
            $structure['headings_detected'] = $headingCount;
        }

        return $structure;
    }

    /**
     * Extract additional file attributes
     *
     * @param UploadedFile $file
     * @param string $extension
     * @return array<string, mixed>
     */
    protected function extractAdditionalAttributes(UploadedFile $file, string $extension): array
    {
        $attributes = [];
        $filePath = $file->getRealPath();

        $attributes['file_last_modified'] = date('c', filemtime($filePath));
        $attributes['file_is_readable'] = is_readable($filePath);
        $attributes['file_is_writable'] = is_writable($filePath);

        return $attributes;
    }

    /**
     * Format file size in human-readable format
     *
     * @param int $bytes
     * @return string
     */
    protected function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Extract content and metadata from a source.
     *
     * @return array{text:string,summary:string,metadata:array<string, mixed>}
     */
    public function extractContent(Source $source): array
    {
        $text = match ($source->type) {
            'txt', 'csv' => $this->extractFromTextFile($source),
            'docx', 'doc' => $this->extractFromDocx($source),
            'pdf' => $this->extractFromPdf($source),
            'url', 'youtube' => $this->extractFromUrl($source),
            'audio', 'video' => $this->extractFromMedia($source),
            'xlsx', 'xls', 'pptx', 'ppt' => 'File type ' . $source->type . ' support is coming soon!',
            default => '',
        };

        $normalized = trim(preg_replace('/\s+/', ' ', $text) ?? $text);
        $payload = $this->persistAndLimitExtractedText($source, $normalized);
        $limitedText = $payload['text'];

        Log::info('Extracted content from source', [
            'source_id' => $source->id,
            'extracted_text_length' => Str::length($limitedText),
            'first_500_chars' => Str::limit($limitedText, 500),
        ]);

        $summary = Str::limit($limitedText, 420);

        return [
            'text' => $limitedText,
            'summary' => $summary !== '' ? $summary : 'Source queued for deeper indexing.',
            'metadata' => array_filter(array_merge([
                'word_count' => str_word_count($limitedText),
                'character_count' => Str::length($limitedText),
            ], $payload['metadata'])),
        ];
    }

    /**
     * Delete a source from storage.
     */
    public function deleteSource(Source $source): void
    {
        if ($source->storage_path) {
            Storage::disk($source->storage_disk)->delete($source->storage_path);
        }
    }

    protected function extractFromTextFile(Source $source): string
    {
        $path = $source->storage_path ? Storage::disk($source->storage_disk)->path($source->storage_path) : null;

        return $path && is_file($path) ? (file_get_contents($path) ?: '') : '';
    }

    protected function extractFromDocx(Source $source): string
    {
        $path = $source->storage_path ? Storage::disk($source->storage_disk)->path($source->storage_path) : null;

        if (! $path || ! is_file($path)) {
            return '';
        }

        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            return '';
        }

        $content = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        return trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? strip_tags($content));
    }

    protected function extractFromPdf(Source $source): string
    {
        $path = $source->storage_path ? Storage::disk($source->storage_disk)->path($source->storage_path) : null;

        if (! $path || ! is_file($path)) {
            Log::warning('PDF file not found for extraction', ['source_id' => $source->id, 'path' => $path]);
            return '';
        }

        $maxBytes = (int) (config('notegov.sources.max_pdf_parse_bytes') ?? env('SOURCES_MAX_PDF_PARSE_BYTES') ?? 200 * 1024 * 1024);
        $fileSize = @filesize($path) ?: $source->file_size;

        if ($maxBytes > 0 && is_int($fileSize) && $fileSize > $maxBytes) {
            Log::warning('Skipping PDF extraction due to file size', [
                'source_id' => $source->id,
                'file_size' => $fileSize,
                'max_bytes' => $maxBytes,
            ]);

            return "PDF too large to parse (".number_format((float) $fileSize / 1024 / 1024, 1)." MB).";
        }

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();
            
            Log::info('PDF text extracted successfully', [
                'source_id' => $source->id,
                'text_length' => Str::length($text),
            ]);
            
            return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
        } catch (\Exception $e) {
            Log::error('Failed to extract PDF text', [
                'source_id' => $source->id,
                'error' => $e->getMessage(),
            ]);
            return '';
        }
    }

    /**
     * Persist full extracted text to local storage (when needed) and return a DB-safe payload.
     *
     * @return array{text:string,metadata:array<string,mixed>}
     */
    protected function persistAndLimitExtractedText(Source $source, string $text): array
    {
        $maxChars = (int) (config('notegov.sources.max_extracted_text_chars')
            ?? env('SOURCES_MAX_EXTRACTED_TEXT_CHARS')
            ?? self::DEFAULT_MAX_EXTRACTED_TEXT_CHARS);

        if ($maxChars <= 0 || $text === '') {
            return ['text' => $text, 'metadata' => []];
        }

        $length = Str::length($text);

        if ($length <= $maxChars) {
            return ['text' => $text, 'metadata' => ['extracted_text_full_length' => $length]];
        }

        $path = "notebooks/{$source->notebook_id}/sources/{$source->id}/extracted_text.txt";
        Storage::disk('local')->put($path, $text);

        return [
            'text' => Str::substr($text, 0, $maxChars),
            'metadata' => [
                'extracted_text_truncated' => true,
                'extracted_text_full_length' => $length,
                'extracted_text_path' => $path,
            ],
        ];
    }

    protected function extractFromUrl(Source $source): string
    {
        $notes = data_get($source->metadata, 'notes');

        return trim("Imported link: {$source->source_url}\n\n{$notes}");
    }

    protected function extractFromMedia(Source $source): string
    {
        $notes = data_get($source->metadata, 'notes');

        return trim("Media source {$source->name} is queued for transcription.\n\n{$notes}");
    }
}
