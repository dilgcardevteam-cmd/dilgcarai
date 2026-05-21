<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Notebook;

$notebooks = Notebook::take(4)->get();
foreach ($notebooks as $notebook) {
    $notebook->update(['is_featured' => true]);
    echo "Set " . $notebook->title . " as featured\n";
}

echo "\nDone!";
