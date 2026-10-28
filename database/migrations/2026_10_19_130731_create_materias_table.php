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
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
               $table->foreignId('carrera_id')->constrained('carreras')->cascadeOnDelete();
    $table->string('codigo', 20)->unique();
    $table->string('nombre', 120);
    $table->unsignedTinyInteger('semestre'); // Valores válidos: del 1 al 6
    $table->unsignedTinyInteger('creditos')->default(1);
    $table->text('descripcion')->nullable();
    $table->boolean('activa')->default(true);
    $table->timestamps();

    $table->unique(['carrera_id', 'nombre']);
    $table->index(['carrera_id', 'semestre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
