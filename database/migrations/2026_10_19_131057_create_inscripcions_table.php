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
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
               $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
    $table->foreignId('paralelo_id')->constrained('paralelos')->restrictOnDelete();
    $table->date('fecha_inscripcion');
    $table->string('estado', 20)->default('inscrito'); // inscrito, retirado, concluido
    $table->timestamps();

    $table->unique(['estudiante_id', 'paralelo_id']);
    $table->index(['estado', 'fecha_inscripcion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
