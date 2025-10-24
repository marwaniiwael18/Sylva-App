<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Donation;

echo "Checking donation ID 1...\n";

try {
    $donation = Donation::find(1);

    if (!$donation) {
        echo "Donation with ID 1 does not exist!\n";
        exit(1);
    }

    echo "Donation found:\n";
    echo "- ID: {$donation->id}\n";
    echo "- Amount: {$donation->amount}\n";
    echo "- Status: {$donation->payment_status}\n";
    echo "- User ID: {$donation->user_id}\n";
    echo "- Created: {$donation->created_at}\n";
    echo "- Can refund: " . ($donation->canRefund() ? 'YES' : 'NO') . "\n";

    // Check refund eligibility details
    $daysSinceCreation = $donation->created_at->diffInDays(now());
    echo "- Days since creation: $daysSinceCreation\n";

    $existingRefunds = $donation->refunds()->whereIn('status', ['pending', 'processing'])->count();
    echo "- Existing pending refunds: $existingRefunds\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}