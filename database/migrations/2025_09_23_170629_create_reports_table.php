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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['tree_planting', 'maintenance', 'pollution', 'green_space_suggestion']);
            $table->enum('urgency', ['low', 'medium', 'high']);
            $table->enum('status', ['pending', 'validated', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address')->nullable();
            $table->json('images')->nullable(); // Store image URLs
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->text('validation_notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['latitude', 'longitude']);
            $table->index(['status', 'type']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
