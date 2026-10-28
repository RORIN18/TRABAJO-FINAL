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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
               $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete();
    $table->date('fecha');
    $table->boolean('presente');
    $table->string('observacion', 255)->nullable();
    $table->timestamps();

    $table->unique(['inscripcion_id', 'fecha']);
    $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
