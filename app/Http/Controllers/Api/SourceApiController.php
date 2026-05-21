<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSourceRequest;
use App\Models\Notebook;
use App\Services\DocumentIngestionService;
use Illuminate\Http\JsonResponse;

class SourceApiController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSourceRequest $request, Notebook $notebook, DocumentIngestionService $ingestion): JsonResponse
    {
        $source = $ingestion->createSource(
            $notebook,
            $request->validated(),
            $request->file('upload_file'),
            null
        );

        return response()->json([
            'message' => 'Source queued for indexing.',
            'source' => $source,
        ], 201);
    }
}
