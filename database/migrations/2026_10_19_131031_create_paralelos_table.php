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
        Schema::create('paralelos', function (Blueprint $table) {
            $table->id();
             $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
    $table->foreignId('gestion_id')->constrained('gestiones')->cascadeOnDelete();
    $table->foreignId('docente_id')->constrained('docentes')->restrictOnDelete();
    $table->string('codigo', 10); // Ej.: A, B o NOCHE
    $table->string('horario', 120);
    $table->string('aula', 40)->nullable();
    $table->unsignedSmallInteger('cupo')->default(30);
    $table->boolean('activo')->default(true);
    $table->timestamps();

    $table->unique(['materia_id', 'gestion_id', 'codigo']);
    $table->index(['gestion_id', 'docente_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paralelos');
    }
};
