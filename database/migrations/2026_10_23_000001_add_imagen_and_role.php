<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('visitante')->after('email');
            $table->string('imagen')->nullable()->after('role');
        });
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('correo');
        });
        Schema::table('docentes', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('correo');
        });
        Schema::table('carreras', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('nombre');
        });
        Schema::table('materias', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('nombre');
        });
        Schema::table('paralelos', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('codigo');
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','imagen']);
        });
        Schema::table('estudiantes', function (Blueprint $table) { $table->dropColumn('imagen'); });
        Schema::table('docentes', function (Blueprint $table) { $table->dropColumn('imagen'); });
        Schema::table('carreras', function (Blueprint $table) { $table->dropColumn('imagen'); });
        Schema::table('materias', function (Blueprint $table) { $table->dropColumn('imagen'); });
        Schema::table('paralelos', function (Blueprint $table) { $table->dropColumn('imagen'); });
    }
};
