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
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['general', 'tree_care', 'environmental', 'events', 'suggestions']);
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('address', 500)->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['open', 'closed', 'resolved'])->default('open');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('views')->default(0);
            $table->integer('replies_count')->default(0);
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
            
            $table->index(['category', 'status']);
            $table->index(['created_at', 'priority']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forums');
    }
};
