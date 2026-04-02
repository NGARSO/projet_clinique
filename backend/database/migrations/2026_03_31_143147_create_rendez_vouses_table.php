<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_heure');
            $table->enum('statut', ['EN_ATTENTE', 'CONFIRME', 'ANNULE', 'TERMINE'])->default('EN_ATTENTE');
            $table->string('motif');
            $table->text('notes')->nullable();

            // Relations exactes du prof
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};