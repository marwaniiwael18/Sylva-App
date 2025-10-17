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
        Schema::create('report_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('activity_type', ['comment', 'vote', 'reaction', 'share'])->default('comment');
            $table->text('content')->nullable(); // For comments
            $table->enum('reaction_type', ['like', 'love', 'support', 'concern'])->nullable(); // For reactions
            $table->integer('vote_value')->nullable(); // 1 for upvote, -1 for downvote
            $table->foreignId('parent_id')->nullable()->constrained('report_activities')->onDelete('cascade'); // For comment replies
            $table->boolean('is_pinned')->default(false); // Pinned comments by moderators
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['report_id', 'activity_type']);
            $table->index(['user_id', 'activity_type']);
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_activities');
    }
};
