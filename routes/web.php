<?php

use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotebookChatController;
use App\Http\Controllers\NotebookController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SystemSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->isAdmin() ? 'dashboard' : 'notebooks.index');
    }
    return view('welcome');
})->name('home');
Route::view('/terms-of-service', 'legal.terms')->name('terms');
Route::view('/privacy-policy', 'legal.privacy')->name('privacy');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/analytics', AdminAnalyticsController::class)->name('analytics');
    
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/notebooks/create-quick', [NotebookController::class, 'createQuick'])->name('notebooks.create.quick');
    Route::resource('notebooks', NotebookController::class);

    Route::post('/notebooks/{notebook}/sources', [SourceController::class, 'store'])
        ->middleware('throttle:uploads')
        ->name('notebooks.sources.store');
    Route::get('/notebooks/{notebook}/sources/{source}', [SourceController::class, 'show'])
        ->name('notebooks.sources.show');
    Route::patch('/notebooks/{notebook}/sources/{source}', [SourceController::class, 'update'])
        ->name('notebooks.sources.update');
    Route::delete('/notebooks/{notebook}/sources/{source}', [SourceController::class, 'destroy'])
        ->name('notebooks.sources.destroy');

    Route::get('/notebooks/{notebook}/chats/{chat}/export', [NotebookChatController::class, 'export'])
        ->name('notebooks.chats.export');

    Route::post('/notebooks/{notebook}/members', [\App\Http\Controllers\NotebookMemberController::class, 'store'])
        ->name('notebooks.members.store');
    Route::delete('/notebooks/{notebook}/members/{member}', [\App\Http\Controllers\NotebookMemberController::class, 'destroy'])
        ->name('notebooks.members.destroy');
    Route::post('/notebooks/{notebook}/duplicate', [\App\Http\Controllers\NotebookController::class, 'duplicate'])->name('notebooks.duplicate');
    Route::post('/notebooks/bulk', [\App\Http\Controllers\NotebookController::class, 'bulkActions'])->name('notebooks.bulk');
    Route::post('/notebooks/{notebook}/pin', [\App\Http\Controllers\NotebookController::class, 'pin'])->name('notebooks.pin');
    Route::delete('/notebooks/{notebook}/pin', [\App\Http\Controllers\NotebookController::class, 'unpin'])->name('notebooks.unpin');
    Route::patch('/notebooks/{notebook}/visibility', [\App\Http\Controllers\NotebookController::class, 'updateVisibility'])->name('notebooks.visibility.update');
    
    Route::post('/shelves', [\App\Http\Controllers\ShelfController::class, 'store'])->name('shelves.store');
    Route::patch('/shelves/{shelf}', [\App\Http\Controllers\ShelfController::class, 'update'])->name('shelves.update');
    Route::delete('/shelves/{shelf}', [\App\Http\Controllers\ShelfController::class, 'destroy'])->name('shelves.destroy');
    Route::post('/shelves/{shelf}/notebooks', [\App\Http\Controllers\ShelfController::class, 'addNotebook'])->name('shelves.notebooks.add');
    Route::delete('/shelves/{shelf}/notebooks/{notebook}', [\App\Http\Controllers\ShelfController::class, 'removeNotebook'])->name('shelves.notebooks.remove');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [SystemSettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/import-psgc', [SystemSettingsController::class, 'importPsgc'])->name('settings.import-psgc');
        Route::get('/featured-notebooks', [App\Http\Controllers\FeaturedNotebookController::class, 'index'])->name('featured-notebooks.index');
        Route::post('/featured-notebooks/{notebook}/add', [App\Http\Controllers\FeaturedNotebookController::class, 'add'])->name('featured-notebooks.add');
        Route::delete('/featured-notebooks/{notebook}/remove', [App\Http\Controllers\FeaturedNotebookController::class, 'remove'])->name('featured-notebooks.remove');
        Route::patch('/featured-notebooks/reorder', [App\Http\Controllers\FeaturedNotebookController::class, 'reorder'])->name('featured-notebooks.reorder');
    });
});
