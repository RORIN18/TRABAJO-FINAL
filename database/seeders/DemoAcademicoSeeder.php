<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DemoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $timestamp = now();
            $carreras = [
                ['codigo' => 'SIS-2026', 'nombre' => 'Sistemas Informáticos'],
                ['codigo' => 'ADM-2026', 'nombre' => 'Administración de Empresas'],
                ['codigo' => 'CON-2026', 'nombre' => 'Contaduría Pública'],
            ];

            foreach ($carreras as $carrera) {
                DB::table('carreras')->insertOrIgnore([
                        'codigo' => $carrera['codigo'],
                        'nombre' => $carrera['nombre'],
                        'duracion_anios' => 3,
                        'activa' => true,
                        'updated_at' => $timestamp,
                        'created_at' => $timestamp,
                    ]);
            }

            $carreraIds = DB::table('carreras')
                ->whereIn('codigo', array_column($carreras, 'codigo'))
                ->pluck('id', 'codigo');

            $gestion = DB::table('gestiones')->where('codigo', '2026-1')->first();
            if (! $gestion) {
                throw new RuntimeException('Primero ejecuta InstitutoSeeder para crear la gestión 2026-1.');
            }

            $materias = [
                ['codigo' => 'SIS-101', 'carrera' => 'SIS-2026', 'nombre' => 'Programación I'],
                ['codigo' => 'ADM-101', 'carrera' => 'ADM-2026', 'nombre' => 'Fundamentos de Administración'],
                ['codigo' => 'CON-101', 'carrera' => 'CON-2026', 'nombre' => 'Contabilidad Básica'],
            ];

            foreach ($materias as $materia) {
                DB::table('materias')->insertOrIgnore([
                        'codigo' => $materia['codigo'],
                        'carrera_id' => $carreraIds[$materia['carrera']],
                        'nombre' => $materia['nombre'],
                        'semestre' => 1,
                        'creditos' => 5,
                        'activa' => true,
                        'updated_at' => $timestamp,
                        'created_at' => $timestamp,
                    ]);
            }

            $materiaIds = DB::table('materias')
                ->whereIn('codigo', array_column($materias, 'codigo'))
                ->pluck('id', 'codigo');

            $turnos = config('academico.turnos');
            $secciones = ['A', 'B', 'C', 'D', 'E'];
            $paralelosPorMateria = [];
            $slot = 0;

            foreach ($materias as $materia) {
                foreach ($secciones as $seccion) {
                    $turnoIndex = intdiv($slot, count($secciones));
                    $turnoKey = array_keys($turnos)[$turnoIndex];
                    $turno = $turnos[$turnoKey];
                    $docenteNumero = $slot + 1;
                    $docenteCodigo = 'DOC-DEMO-' . str_pad((string) $docenteNumero, 2, '0', STR_PAD_LEFT);

                    DB::table('docentes')->insertOrIgnore([
                            'codigo' => $docenteCodigo,
                            'ci' => 'CI-DOC-' . str_pad((string) $docenteNumero, 6, '0', STR_PAD_LEFT),
                            'nombres' => 'Docente ' . $docenteNumero,
                            'apellidos' => 'Académico',
                            'correo' => strtolower($docenteCodigo) . '@illimani.edu.bo',
                            'especialidad' => $materia['nombre'],
                            'activo' => true,
                            'updated_at' => $timestamp,
                            'created_at' => $timestamp,
                        ]);

                    $docenteId = DB::table('docentes')->where('codigo', $docenteCodigo)->value('id');
                    $horario = $turno['label'] . ' ' . $turno['hora_inicio'] . ' - ' . $turno['hora_fin'];

                    DB::table('paralelos')->insertOrIgnore([
                            'materia_id' => $materiaIds[$materia['codigo']],
                            'gestion_id' => $gestion->id,
                            'codigo' => $seccion,
                            'docente_id' => $docenteId,
                            'turno' => $turnoKey,
                            'hora_inicio' => $turno['hora_inicio'],
                            'hora_fin' => $turno['hora_fin'],
                            'horario' => $horario,
                            'cupo' => 40,
                            'activo' => true,
                            'updated_at' => $timestamp,
                            'created_at' => $timestamp,
                        ]);

                    $paraleloId = DB::table('paralelos')
                        ->where('materia_id', $materiaIds[$materia['codigo']])
                        ->where('gestion_id', $gestion->id)
                        ->where('codigo', $seccion)
                        ->value('id');
                    $paralelosPorMateria[$materia['codigo']][] = $paraleloId;
                    $slot++;
                }
            }

            for ($numeroEstudiante = 1; $numeroEstudiante <= 500; $numeroEstudiante++) {
                $estudianteId = DB::table('estudiantes')->where('codigo', 'EST-' . $numeroEstudiante)->value('id');
                if (! $estudianteId) {
                    throw new RuntimeException('Falta crear el estudiante EST-' . $numeroEstudiante . '.');
                }

                $materiaIndex = $numeroEstudiante <= 167 ? 0 : ($numeroEstudiante <= 334 ? 1 : 2);
                $primerNumeroDeGrupo = [1, 168, 335][$materiaIndex];
                $indiceEnMateria = $numeroEstudiante - $primerNumeroDeGrupo;
                $seccionIndex = intdiv($indiceEnMateria, 40);
                $materia = $materias[$materiaIndex];
                $paraleloId = $paralelosPorMateria[$materia['codigo']][$seccionIndex];

                DB::table('estudiantes')->where('id', $estudianteId)->whereNull('carrera_id')->update([
                    'carrera_id' => $carreraIds[$materia['carrera']],
                    'updated_at' => $timestamp,
                ]);

                $carreraEstudianteId = DB::table('estudiantes')->where('id', $estudianteId)->value('carrera_id');
                if ((int) $carreraEstudianteId !== (int) $carreraIds[$materia['carrera']]) {
                    continue;
                }

                $yaTieneInscripcionActiva = DB::table('inscripciones')
                    ->where('estudiante_id', $estudianteId)
                    ->where('estado', '!=', 'no_inscrito')
                    ->exists();

                if ($yaTieneInscripcionActiva) {
                    continue;
                }

                DB::table('inscripciones')->insertOrIgnore([
                        'estudiante_id' => $estudianteId,
                        'paralelo_id' => $paraleloId,
                        'fecha_inscripcion' => $timestamp->toDateString(),
                        'estado' => 'inscrito',
                        'updated_at' => $timestamp,
                        'created_at' => $timestamp,
                    ]);
            }
        });
    }
}
