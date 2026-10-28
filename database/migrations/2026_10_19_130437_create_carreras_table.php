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
        Schema::create('carreras', function (Blueprint $table) {
            $table->id();
               $table->string('codigo', 20)->unique();
    $table->string('nombre', 120)->unique();
    $table->unsignedTinyInteger('duracion_anios')->default(3);
    $table->text('descripcion')->nullable();
    $table->boolean('activa')->default(true);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carreras');
    }
};
