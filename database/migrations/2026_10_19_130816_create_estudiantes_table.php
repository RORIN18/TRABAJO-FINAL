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
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
              $table->string('codigo', 20)->unique();
    $table->string('ci', 20)->unique();
    $table->string('nombres', 100);
    $table->string('apellidos', 100);
    $table->date('fecha_nacimiento')->nullable();
    $table->string('correo')->unique();
    $table->string('telefono', 30)->nullable();
    $table->string('estado', 20)->default('activo');
    $table->timestamps();

    $table->index(['apellidos', 'nombres']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
