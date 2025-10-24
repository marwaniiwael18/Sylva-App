<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Refund;

echo "Checking refunds in database...\n";

try {
    $count = Refund::count();
    echo "Total refunds: $count\n";

    if ($count > 0) {
        $refunds = Refund::all();
        foreach ($refunds as $refund) {
            echo "ID: {$refund->id}, Donation: {$refund->donation_id}, Status: {$refund->status}, Amount: {$refund->amount}\n";
        }
    } else {
        echo "No refunds found in database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}