<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$downloads = \App\Models\Download::all();
foreach ($downloads as $d) {
    echo $d->id . ' | ' . substr($d->title, 0, 40) . ' | ' . $d->quality . ' | ' . $d->status . ' | ' . $d->progress . '%' . PHP_EOL;
}
echo 'Total: ' . $downloads->count() . PHP_EOL;
