<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Checking User-Registrar Relationships\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Check all registrars and their users
$registrars = App\Models\Registrar::all();

foreach($registrars as $registrar) {
    echo "Registrar ID: {$registrar->id}\n";
    echo "User ID: {$registrar->user_id}\n";
    echo "Window Number: {$registrar->window_number}\n";
    
    if ($registrar->user_id) {
        $user = App\Models\User::find($registrar->user_id);
        if ($user) {
            echo "User Found: {$user->name} (Email: {$user->email})\n";
            echo "User Role: " . ($user->role ? $user->role->name : 'No role') . "\n";
        } else {
            echo "❌ User ID {$registrar->user_id} not found in users table\n";
        }
    } else {
        echo "❌ No user_id assigned to this registrar\n";
    }
    echo "---\n";
}

echo "\n🔎 Checking if users exist that could be registrars:\n";
$users = App\Models\User::with('role')->get();
foreach($users as $user) {
    if ($user->role && strpos(strtolower($user->role->name), 'registrar') !== false) {
        echo "Potential Registrar User: {$user->name} (Role: {$user->role->name})\n";
    }
}