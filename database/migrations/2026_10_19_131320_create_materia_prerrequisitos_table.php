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
        Schema::create('materia_prerrequisitos', function (Blueprint $table) {
           $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
    $table->foreignId('prerrequisito_id')->constrained('materias')->cascadeOnDelete();

    $table->primary(['materia_id', 'prerrequisito_id']);
    $table->index('prerrequisito_id');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materia_prerrequisitos');
    }
};
