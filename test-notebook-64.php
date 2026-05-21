<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Notebook;
use App\Models\Source;
use App\Models\AiEmbedding;
use Illuminate\Support\Facades\DB;

echo "=== Checking Notebook 64 ===\n";
$notebook64 = Notebook::find(64);
if (!$notebook64) {
    echo "Notebook 64 not found!\n";
    exit;
}
echo "Notebook 64 title: " . $notebook64->title . "\n";
echo "Sources in Notebook 64:\n";
foreach ($notebook64->sources as $source) {
    echo "  - Source " . $source->id . ": " . $source->name . " (Status: " . $source->status . ")\n";
    $embeddingsCount = AiEmbedding::where('source_id', $source->id)->count();
    echo "    Embeddings: " . $embeddingsCount . "\n";
}

echo "\n=== Checking Queued Sources ===\n";
$queuedSources = Source::where('status', 'queued')->get();
echo "Queued sources: " . $queuedSources->count() . "\n";
foreach ($queuedSources as $source) {
    echo "  - Source " . $source->id . ": " . $source->name . " (Notebook ID: " . $source->notebook_id . ")\n";
}

echo "\n=== Checking Jobs Table ===\n";
$jobs = DB::table('jobs')->get();
echo "Pending jobs: " . $jobs->count() . "\n";
foreach ($jobs as $job) {
    echo "  - Job ID: " . $job->id . ", Queue: " . $job->queue . "\n";
}
