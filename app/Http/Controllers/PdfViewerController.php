<?php

namespace App\Http\Controllers;

use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfViewerController extends Controller
{
    public function show(Request $request, Source $source)
    {
        $page = $request->integer('page', 1);
        $chunkIndex = $request->integer('chunk', null);
        $highlightText = $request->string('highlight', '')->toString();
        $paragraphIndex = $request->integer('paragraph', null);
        $sentenceIndex = $request->integer('sentence', null);
        $startOffset = $request->integer('start', null);
        $endOffset = $request->integer('end', null);

        return view('pdf-viewer.show', [
            'source' => $source,
            'page' => $page,
            'chunkIndex' => $chunkIndex,
            'highlightText' => $highlightText,
            'paragraphIndex' => $paragraphIndex,
            'sentenceIndex' => $sentenceIndex,
            'startOffset' => $startOffset,
            'endOffset' => $endOffset,
        ]);
    }

    public function stream(Source $source)
    {
        abort_unless($source->type === 'pdf', 404);

        if (blank($source->storage_disk) || blank($source->storage_path)) {
            abort(404);
        }

        $disk = Storage::disk($source->storage_disk);

        if (!$disk->exists($source->storage_path)) {
            abort(404);
        }

        $path = $disk->path($source->storage_path);
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $source->name . '"',
        ];

        return response()->file($path, $headers);
    }
}
