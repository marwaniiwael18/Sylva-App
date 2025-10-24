<?php

namespace App\Console\Commands;

use App\Models\Donation;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestRefund extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:refund {donation_id} {user_id} {reason}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test refund functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $donationId = $this->argument('donation_id');
        $userId = $this->argument('user_id');
        $reason = $this->argument('reason');

        $this->info("Testing refund for donation ID: $donationId, User ID: $userId");

        // Find the donation
        $donation = Donation::find($donationId);
        if (!$donation) {
            $this->error("Donation not found!");
            return 1;
        }

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            $this->error("User not found!");
            return 1;
        }

        // Authenticate the user
        Auth::login($user);
        $this->info("Logged in as: {$user->name} ({$user->email})");

        // Check donation details
        $this->info("Donation details:");
        $this->line("- ID: {$donation->id}");
        $this->line("- Amount: {$donation->amount}");
        $this->line("- Status: {$donation->payment_status}");
        $this->line("- User ID: {$donation->user_id}");
        $this->line("- Can refund: " . ($donation->canRefund() ? 'YES' : 'NO'));

        // Check authorization
        if ($donation->user_id !== Auth::id()) {
            $this->error("Authorization failed! Donation user: {$donation->user_id}, Auth user: " . Auth::id());
            return 1;
        }

        if (!$donation->canRefund()) {
            $this->error("Donation cannot be refunded!");
            return 1;
        }

        // Create refund
        try {
            $refund = Refund::create([
                'donation_id' => $donation->id,
                'processed_by' => null,
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'status' => 'pending',
                'reason' => $reason,
            ]);

            $this->info("Refund created successfully!");
            $this->line("- Refund ID: {$refund->id}");
            $this->line("- Status: {$refund->status}");
            $this->line("- Amount: {$refund->amount}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create refund: " . $e->getMessage());
            return 1;
        }
    }
}
