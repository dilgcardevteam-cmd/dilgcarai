<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSourceRequest;
use App\Models\Notebook;
use App\Models\Source;
use App\Services\ActivityLogger;
use App\Services\DocumentIngestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SourceController extends Controller
{
    public function __construct(
        protected DocumentIngestionService $ingestion,
        protected ActivityLogger $activityLogger,
    ) {}

    /**
     * Store a newly uploaded source.
     */
    public function store(StoreSourceRequest $request, Notebook $notebook): RedirectResponse
    {
        $source = $this->ingestion->createSource(
            $notebook,
            $request->validated(),
            $request->file('upload_file'),
            null
        );

        return redirect()
            ->route('notebooks.show', $notebook)
            ->with('status', "Source {$source->name} uploaded and queued for indexing.");
    }

    /**
     * Preview the specified source.
     */
    public function show(Notebook $notebook, Source $source)
    {
        abort_unless($source->notebook_id === $notebook->id, 404);

        if (filled($source->source_url) && in_array($source->type, ['web', 'url', 'link'], true)) {
            return response()->redirectTo($source->source_url);
        }

        if (blank($source->storage_disk) || blank($source->storage_path)) {
            abort(404);
        }

        $disk = Storage::disk($source->storage_disk);

        if (!$disk->exists($source->storage_path)) {
            abort(404);
        }

        $path = $disk->path($source->storage_path);
        $headers = [];

        if (filled($source->mime_type)) {
            $headers['Content-Type'] = $source->mime_type;
        }

        return response()->file($path, $headers);
    }

    /**
     * Update the specified source in storage.
     */
    public function update(Request $request, Notebook $notebook, Source $source): RedirectResponse
    {
        abort_unless($source->notebook_id === $notebook->id, 404);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $oldName = $source->name;
        $source->update(['name' => $request->name]);

        $this->activityLogger->log(
            $request->user(),
            'source.updated',
            "Renamed source from \"{$oldName}\" to \"{$source->name}\".",
            $notebook,
            $source
        );

        return redirect()
            ->route('notebooks.show', $notebook)
            ->with('status', 'Source renamed.');
    }

    /**
     * Remove the specified source.
     */
    public function destroy(Request $request, Notebook $notebook, Source $source): RedirectResponse
    {
        abort_unless($source->notebook_id === $notebook->id, 404);

        $this->ingestion->deleteSource($source);

        $this->activityLogger->log(
            null,
            'source.deleted',
            "Deleted source {$source->name}.",
            $notebook,
            $source,
            [],
            $request->ip(),
            $request->userAgent()
        );

        $source->delete();

        return redirect()
            ->route('notebooks.show', $notebook)
            ->with('status', 'Source deleted.');
    }
}
