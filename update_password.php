<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('email', 'admin@example.com')->first();
if ($user) {
    $user->password = bcrypt('AdminPass123!');
    $user->save();
    echo "Password updated successfully for user: " . $user->email . "\n";
} else {
    echo "User not found\n";
}