<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitutoSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();

        DB::transaction(function () use ($timestamp): void {
            DB::table('carreras')->insertOrIgnore([
                'codigo' => 'SIS-2026',
                'nombre' => 'Sistemas Informáticos',
                'duracion_anios' => 3,
                'activa' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
            $carreraId = DB::table('carreras')->where('codigo', 'SIS-2026')->value('id');

            DB::table('gestiones')->insertOrIgnore([
                'codigo' => '2026-1',
                'fecha_inicio' => '2026-01-01',
                'fecha_fin' => '2026-06-30',
                'vigente' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            DB::table('docentes')->insertOrIgnore([
                'codigo' => 'DOC-01',
                'ci' => '1234567',
                'nombres' => 'Juan',
                'apellidos' => 'Pérez',
                'correo' => 'juan@illimani.edu.bo',
                'especialidad' => 'Sistemas',
                'activo' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            DB::table('materias')->insertOrIgnore([
                'carrera_id' => $carreraId,
                'codigo' => 'SIS-101',
                'nombre' => 'Programación I',
                'semestre' => 1,
                'creditos' => 5,
                'activa' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
            DB::table('materias')->insertOrIgnore([
                'carrera_id' => $carreraId,
                'codigo' => 'SIS-102',
                'nombre' => 'Programación II',
                'semestre' => 2,
                'creditos' => 5,
                'activa' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $materiaUno = DB::table('materias')->where('codigo', 'SIS-101')->value('id');
            $materiaDos = DB::table('materias')->where('codigo', 'SIS-102')->value('id');
            DB::table('materia_prerrequisitos')->insertOrIgnore([
                'materia_id' => $materiaDos,
                'prerrequisito_id' => $materiaUno,
            ]);
        });
    }
}
