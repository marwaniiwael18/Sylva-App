<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->onDelete('cascade');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who processed the refund
            $table->decimal('amount', 10, 2); // Refund amount
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('reason')->nullable(); // Reason for refund
            $table->text('admin_notes')->nullable(); // Admin notes
            $table->string('stripe_refund_id')->nullable(); // Stripe refund ID
            $table->timestamp('processed_at')->nullable(); // When refund was processed
            $table->timestamp('completed_at')->nullable(); // When refund was completed
            $table->timestamps();

            // Indexes
            $table->index(['donation_id']);
            $table->index(['processed_by']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
