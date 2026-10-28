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
        Schema::table('paralelos', function (Blueprint $table) {
            $table->string('turno')->nullable()->after('codigo');
            $table->time('hora_inicio')->nullable()->after('turno');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->string('tipo')->default('regular')->after('hora_fin'); // regular | especial
            $table->index(['docente_id', 'gestion_id', 'turno']);
            $table->index(['aula', 'gestion_id', 'turno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paralelos', function (Blueprint $table) {
            $table->dropIndex(['docente_id', 'gestion_id', 'turno']);
            $table->dropIndex(['aula', 'gestion_id', 'turno']);
            $table->dropColumn(['turno', 'hora_inicio', 'hora_fin', 'tipo']);
        });
    }
};
