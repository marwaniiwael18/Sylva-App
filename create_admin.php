<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $user = \App\Models\User::create([
        'name' => 'adminadmin',
        'email' => 'super@exemple.com',
        'password' => bcrypt('admin123!'),
        'is_admin' => true
    ]);

    echo "Admin user created successfully!\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Is Admin: " . ($user->is_admin ? 'Yes' : 'No') . "\n";
} catch (Exception $e) {
    echo "Error creating user: " . $e->getMessage() . "\n";
}