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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Titre de l'événement
            $table->text('description'); // Description détaillée
            $table->dateTime('date'); // Date et heure de l'événement
            $table->string('location'); // GPS ou adresse
            $table->enum('type', ['Tree Planting', 'Maintenance', 'Awareness', 'Workshop']); // Type d'événement
            $table->enum('status', ['active', 'cancelled', 'completed', 'draft'])->default('active'); // Statut
            $table->foreignId('organized_by_user_id')->constrained('users')->onDelete('cascade'); // Organisateur
            $table->integer('max_participants')->nullable(); // Nombre max de participants
            $table->integer('current_participants')->default(0); // Participants actuels
            $table->timestamps();

            // Indexes pour optimiser les requêtes
            $table->index(['date', 'status']);
            $table->index(['type']);
            $table->index(['organized_by_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
