<?php

use App\Http\Controllers\Api\NotebookApiController;
use App\Http\Controllers\Api\NotebookChatApiController;
use App\Http\Controllers\Api\SourceApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    Route::get('/notebooks', [NotebookApiController::class, 'index'])->name('notebooks.index');
    Route::get('/notebooks/{notebook}', [NotebookApiController::class, 'show'])->name('notebooks.show');

    Route::post('/notebooks/{notebook}/sources', [SourceApiController::class, 'store'])
        ->middleware('throttle:uploads')
        ->name('notebooks.sources.store');

    Route::post('/notebooks/{notebook}/chats/{chat}/messages', [NotebookChatApiController::class, 'store'])
        ->middleware('throttle:ai-chat')
        ->name('notebooks.chats.messages.store');
});
