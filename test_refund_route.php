<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

echo "Testing refund route access...\n";

// Simulate a logged-in user
$user = \App\Models\User::find(1);
if (!$user) {
    echo "User not found!\n";
    exit(1);
}

Auth::login($user);
echo "Logged in as user: {$user->name} ({$user->email})\n";

// Check if the route exists
$routes = Route::getRoutes();
$refundRoute = null;

foreach ($routes as $route) {
    if ($route->getName() === 'donations.refund') {
        $refundRoute = $route;
        break;
    }
}

if (!$refundRoute) {
    echo "Refund route not found!\n";
    exit(1);
}

echo "Refund route found: {$refundRoute->uri()}\n";
echo "Route methods: " . implode(', ', $refundRoute->methods()) . "\n";
echo "Route middleware: " . implode(', ', $refundRoute->middleware()) . "\n";

// Test route accessibility
$donation = \App\Models\Donation::find(1);
if (!$donation) {
    echo "Donation not found!\n";
    exit(1);
}

echo "Donation found: ID {$donation->id}, User ID {$donation->user_id}\n";
echo "Current user ID: " . Auth::id() . "\n";
echo "Can refund: " . ($donation->canRefund() ? 'YES' : 'NO') . "\n";

echo "Route test completed.\n";