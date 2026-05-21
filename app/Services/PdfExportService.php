<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PdfExportService
{
    /**
     * Create a simple PDF export for a chat.
     */
    public function exportChat(Chat $chat): Response
    {
        $lines = collect([
            'NoteGov AI DILG',
            'Chat Export',
            'Notebook: '.$chat->notebook->title,
            'Chat: '.$chat->title,
            'Generated: '.now()->format('F j, Y g:i A'),
            '',
        ]);

        foreach ($chat->messages as $message) {
            $lines->push(strtoupper($message->role).':');
            $lines->push(...$this->wrapText($message->content));
            $lines->push('');
        }

        $pdf = $this->buildPdf($lines->all());

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.Str::slug($chat->notebook->title.'-'.$chat->title).'.pdf"',
        ]);
    }

    /**
     * Wrap text for a monospaced PDF export.
     *
     * @return array<int, string>
     */
    protected function wrapText(string $text, int $width = 92): array
    {
        $wrapped = wordwrap(preg_replace('/\s+/', ' ', trim($text)) ?? trim($text), $width, "\n", true);

        return explode("\n", $wrapped);
    }

    /**
     * Build a minimal PDF document.
     *
     * @param  array<int, string>  $lines
     */
    protected function buildPdf(array $lines): string
    {
        $pages = [];
        $currentPage = [];
        $lineCounter = 0;

        foreach ($lines as $line) {
            $currentPage[] = $line;
            $lineCounter++;

            if ($lineCounter >= 42) {
                $pages[] = $currentPage;
                $currentPage = [];
                $lineCounter = 0;
            }
        }

        if ($currentPage !== []) {
            $pages[] = $currentPage;
        }

        $objects = [];
        $offsets = [];

        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';

        $pageObjectIds = [];
        $contentObjectIds = [];
        $nextId = 3;

        foreach ($pages as $pageLines) {
            $pageObjectIds[] = $nextId++;
            $contentObjectIds[] = $nextId++;
        }

        $kids = implode(' ', array_map(fn (int $id) => "{$id} 0 R", $pageObjectIds));
        $objects[] = "<< /Type /Pages /Kids [ {$kids} ] /Count ".count($pageObjectIds)." >>";

        foreach ($pages as $index => $pageLines) {
            $pageId = $pageObjectIds[$index];
            $contentId = $contentObjectIds[$index];
            $objects[$pageId - 1] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 ".($nextId)." 0 R >> >> /Contents {$contentId} 0 R >>";
            $stream = "BT\n/F1 10 Tf\n50 742 Td\n14 TL\n";

            foreach ($pageLines as $line) {
                $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
                $stream .= "({$escaped}) Tj\nT*\n";
            }

            $stream .= "ET";
            $objects[$contentId - 1] = "<< /Length ".strlen($stream)." >>\nstream\n{$stream}\nendstream";
        }

        $fontId = $nextId;
        $objects[$fontId - 1] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

        $pdf = "%PDF-1.4\n";

        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT)." 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xrefPosition}\n%%EOF";

        return $pdf;
    }
}
