<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('gestiones', function (Blueprint $table) {
            $table->id();
             $table->string('codigo', 20)->unique(); // Ej.: 2026-1
    $table->date('fecha_inicio');
    $table->date('fecha_fin');
    $table->boolean('vigente')->default(false);
    $table->timestamps();

    $table->index(['vigente', 'fecha_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestiones');
    }
};
