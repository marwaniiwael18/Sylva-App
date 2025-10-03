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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2); // Donation amount
            $table->string('currency', 3)->default('TND'); // Currency
            $table->enum('type', ['tree_planting', 'maintenance', 'awareness']); // Donation type
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Donor user
            $table->unsignedBigInteger('event_id')->nullable(); // Related event (no foreign key constraint for now)
            $table->text('message')->nullable(); // Optional message
            $table->boolean('anonymous')->default(false); // Anonymous donation
            
            // Stripe integration fields
            $table->string('stripe_payment_intent_id')->nullable(); // Stripe PaymentIntent ID
            $table->string('stripe_charge_id')->nullable(); // Stripe Charge ID
            $table->enum('payment_status', ['pending', 'processing', 'succeeded', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable(); // Payment method type
            $table->timestamp('paid_at')->nullable(); // Payment completion timestamp
            
            // Refund fields
            $table->enum('refund_status', ['none', 'pending', 'processing', 'succeeded', 'failed'])->default('none');
            $table->decimal('refunded_amount', 10, 2)->nullable(); // Refunded amount
            $table->text('refund_reason')->nullable(); // Refund reason
            $table->timestamp('refunded_at')->nullable(); // Refund completion timestamp
            
            $table->timestamps();
            
            // Indexes for query optimization
            $table->index(['user_id', 'payment_status']);
            $table->index(['event_id']);
            $table->index(['type']);
            $table->index(['created_at']);
            $table->index(['payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};