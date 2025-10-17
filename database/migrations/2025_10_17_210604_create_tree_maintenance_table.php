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
        Schema::create('tree_care', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tree_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('activity_type', ['watering', 'pruning', 'fertilizing', 'disease_treatment', 'inspection', 'other']);
            $table->text('notes')->nullable();
            $table->json('images')->nullable();
            $table->date('performed_at');
            $table->enum('condition_after', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tree_care');
    }
};
