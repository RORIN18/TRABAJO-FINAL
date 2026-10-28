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
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
              $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete();
    $table->enum('tipo', ['parcial_1', 'parcial_2', 'parcial_3', 'examen_final']);
    $table->decimal('calificacion', 5, 2);
    $table->date('fecha_evaluacion')->nullable();
    $table->timestamps();

    $table->unique(['inscripcion_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
