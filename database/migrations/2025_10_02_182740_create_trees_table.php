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
        Schema::create('trees', function (Blueprint $table) {
            $table->id();
            $table->string('species');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->date('planting_date');
            $table->enum('status', ['Planted', 'Not Yet', 'Sick', 'Dead'])->default('Not Yet');
            $table->enum('type', ['Fruit', 'Ornamental', 'Forest', 'Medicinal']);
            $table->foreignId('planted_by_user')->constrained('users');
            $table->json('images')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};
