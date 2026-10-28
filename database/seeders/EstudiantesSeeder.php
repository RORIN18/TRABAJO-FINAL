<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstudiantesSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();

        for ($i = 1; $i <= 500; $i++) {
            DB::table('estudiantes')->insertOrIgnore([
                    'codigo' => 'EST-' . $i,
                    'ci' => 'CI-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                    'nombres' => 'Estudiante ' . $i,
                    'apellidos' => 'Apellido ' . $i,
                    'correo' => "est$i@illimani.edu.bo",
                    'estado' => 'activo',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
        }
    }
}
