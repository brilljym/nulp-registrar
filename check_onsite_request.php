<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OnsiteRequest;

$request = OnsiteRequest::where('ref_code', 'A003')->first();

if ($request) {
    echo 'Found onsite request: ID=' . $request->id . ', Status=' . $request->status . ', RefCode=' . $request->ref_code . PHP_EOL;
    echo 'Player ID: ' . ($request->player_id ?? 'null') . PHP_EOL;
} else {
    echo 'Onsite request A003 not found' . PHP_EOL;
}