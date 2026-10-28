<?php

namespace Tests\Feature;

use App\Models\{Carrera, Docente, Estudiante, Gestion, Inscripcion, Materia, Paralelo, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicRulesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_registration_form_only_shows_parallels_with_places_in_the_active_term(): void
    {
        $gestion = Gestion::factory()->create(['vigente' => true]);
        $disponible = $this->makeParallel($gestion, ['cupo' => 2]);
        $lleno = $this->makeParallel($gestion, ['cupo' => 1]);
        $inactivo = $this->makeParallel($gestion, ['activo' => false]);
        $gestionCerrada = Gestion::factory()->create(['vigente' => false]);
        $paraleloCerrado = $this->makeParallel($gestionCerrada);

        Inscripcion::create([
            'estudiante_id' => Estudiante::factory()->create(['carrera_id' => $lleno->materia->carrera_id])->id,
            'paralelo_id' => $lleno->id,
            'fecha_inscripcion' => now()->toDateString(),
            'estado' => 'inscrito',
        ]);

        $response = $this->actingAs($this->admin)->get('/inscripciones/create');

        $response->assertOk()
            ->assertSee($disponible->materia->nombre)
            ->assertDontSee($lleno->materia->nombre)
            ->assertDontSee($inactivo->materia->nombre)
            ->assertDontSee($paraleloCerrado->materia->nombre);
    }

    public function test_registration_rejects_a_parallel_that_became_full(): void
    {
        $gestion = Gestion::factory()->create(['vigente' => true]);
        $paralelo = $this->makeParallel($gestion, ['cupo' => 1]);

        Inscripcion::create([
            'estudiante_id' => Estudiante::factory()->create(['carrera_id' => $paralelo->materia->carrera_id])->id,
            'paralelo_id' => $paralelo->id,
            'fecha_inscripcion' => now()->toDateString(),
            'estado' => 'inscrito',
        ]);

        $estudiante = Estudiante::factory()->create(['carrera_id' => $paralelo->materia->carrera_id]);
        $this->actingAs($this->admin)
            ->post('/inscripciones', [
                'estudiante_id' => $estudiante->id,
                'paralelo_id' => $paralelo->id,
            ])
            ->assertSessionHasErrors('paralelo_id');

        $this->assertDatabaseCount('inscripciones', 1);
    }

    public function test_registration_reactivates_an_existing_withdrawn_enrollment(): void
    {
        $gestion = Gestion::factory()->create(['vigente' => true]);
        $paralelo = $this->makeParallel($gestion, ['cupo' => 1]);
        $estudiante = Estudiante::factory()->create(['carrera_id' => $paralelo->materia->carrera_id]);
        $inscripcion = Inscripcion::create([
            'estudiante_id' => $estudiante->id,
            'paralelo_id' => $paralelo->id,
            'fecha_inscripcion' => now()->subDays(10)->toDateString(),
            'estado' => 'no_inscrito',
        ]);

        $this->actingAs($this->admin)
            ->post('/inscripciones', [
                'estudiante_id' => $estudiante->id,
                'paralelo_id' => $paralelo->id,
            ])
            ->assertRedirect(route('inscripciones.index'));

        $this->assertDatabaseCount('inscripciones', 1);
        $this->assertDatabaseHas('inscripciones', [
            'id' => $inscripcion->id,
            'estado' => 'inscrito',
        ]);
        $this->assertSame(now()->toDateString(), $inscripcion->fresh()->fecha_inscripcion->toDateString());
    }

    public function test_available_teacher_list_excludes_teachers_with_a_time_conflict(): void
    {
        $gestion = Gestion::factory()->create(['vigente' => true]);
        $ocupado = Docente::factory()->create();
        $libre = Docente::factory()->create();
        $paralelo = $this->makeParallel($gestion, ['docente_id' => $ocupado->id]);

        $response = $this->actingAs($this->admin)->getJson(route('paralelos.docentes-disponibles', [
            'gestion_id' => $gestion->id,
            'turno' => 'mañana',
        ]));

        $response->assertOk()
            ->assertJsonMissing(['id' => $ocupado->id])
            ->assertJsonFragment(['id' => $libre->id, 'nombres' => $libre->nombres, 'apellidos' => $libre->apellidos]);

        $this->actingAs($this->admin)->getJson(route('paralelos.docentes-disponibles', [
            'gestion_id' => $gestion->id,
            'turno' => 'mañana',
            'paralelo_id' => $paralelo->id,
        ]))->assertJsonFragment(['id' => $ocupado->id]);
    }

    public function test_parallel_creation_rejects_a_teacher_with_an_overlapping_class(): void
    {
        $gestion = Gestion::factory()->create(['vigente' => true]);
        $docente = Docente::factory()->create();
        $this->makeParallel($gestion, ['docente_id' => $docente->id]);
        $materia = Materia::factory()->create();

        $this->actingAs($this->admin)
            ->post('/paralelos', [
                'materia_id' => $materia->id,
                'gestion_id' => $gestion->id,
                'docente_id' => $docente->id,
                'codigo' => 'B',
                'turno' => 'mañana',
                'aula' => 'B',
                'cupo' => 40,
                'tipo' => 'regular',
            ])
            ->assertSessionHasErrors('docente_id');
    }

    public function test_materia_cannot_be_created_with_a_prerequisite_from_another_career(): void
    {
        $carreraDestino = Carrera::factory()->create();
        $carreraAjena = Carrera::factory()->create();
        $prerrequisitoAjeno = Materia::factory()->create(['carrera_id' => $carreraAjena->id]);

        $this->actingAs($this->admin)
            ->post('/materias', [
                'carrera_id' => $carreraDestino->id,
                'codigo' => 'MAT-DESTINO',
                'nombre' => 'Materia de destino',
                'semestre' => 2,
                'activa' => 1,
                'prerrequisitos' => [$prerrequisitoAjeno->id],
            ])
            ->assertSessionHasErrors('prerrequisitos.0');

        $this->assertDatabaseMissing('materias', ['codigo' => 'MAT-DESTINO']);
    }

    public function test_student_update_rejects_an_email_used_by_another_student_and_allows_its_own(): void
    {
        $estudiante = Estudiante::factory()->create();
        $otroEstudiante = Estudiante::factory()->create();

        $this->actingAs($this->admin)
            ->put('/estudiantes/' . $estudiante->id, $this->studentUpdateData($estudiante, $otroEstudiante->correo))
            ->assertSessionHasErrors('correo');

        $this->actingAs($this->admin)
            ->put('/estudiantes/' . $estudiante->id, $this->studentUpdateData($estudiante, $estudiante->correo))
            ->assertRedirect(route('estudiantes.index'));
    }

    public function test_student_update_rejects_duplicate_student_code_and_identity_number(): void
    {
        $estudiante = Estudiante::factory()->create();
        $otroEstudiante = Estudiante::factory()->create();

        $this->actingAs($this->admin)
            ->put('/estudiantes/' . $estudiante->id, array_merge(
                $this->studentUpdateData($estudiante, $estudiante->correo),
                ['codigo' => $otroEstudiante->codigo],
            ))
            ->assertSessionHasErrors('codigo');

        $this->actingAs($this->admin)
            ->put('/estudiantes/' . $estudiante->id, array_merge(
                $this->studentUpdateData($estudiante, $estudiante->correo),
                ['ci' => $otroEstudiante->ci],
            ))
            ->assertSessionHasErrors('ci');
    }

    public function test_docente_update_rejects_an_email_used_by_another_docente_and_allows_its_own(): void
    {
        $docente = Docente::factory()->create();
        $otroDocente = Docente::factory()->create();

        $this->actingAs($this->admin)
            ->put('/docentes/' . $docente->id, [
                'nombres' => $docente->nombres,
                'apellidos' => $docente->apellidos,
                'correo' => $otroDocente->correo,
            ])
            ->assertSessionHasErrors('correo');

        $this->actingAs($this->admin)
            ->put('/docentes/' . $docente->id, [
                'nombres' => $docente->nombres,
                'apellidos' => $docente->apellidos,
                'correo' => $docente->correo,
            ])
            ->assertRedirect(route('docentes.index'));
    }

    public function test_carrera_update_rejects_duplicate_codes_and_allows_its_own_code(): void
    {
        $carrera = Carrera::factory()->create();
        $otraCarrera = Carrera::factory()->create();

        $this->actingAs($this->admin)
            ->put('/carreras/' . $carrera->id, $this->carreraUpdateData($carrera, $otraCarrera->codigo))
            ->assertSessionHasErrors('codigo');

        $this->actingAs($this->admin)
            ->put('/carreras/' . $carrera->id, $this->carreraUpdateData($carrera, $carrera->codigo))
            ->assertRedirect(route('carreras.index'));

        $this->actingAs($this->admin)
            ->put('/carreras/' . $carrera->id, array_merge(
                $this->carreraUpdateData($carrera, $carrera->codigo),
                ['nombre' => $otraCarrera->nombre],
            ))
            ->assertSessionHasErrors('nombre');
    }

    private function makeParallel(Gestion $gestion, array $attributes = []): Paralelo
    {
        return Paralelo::factory()->create(array_merge([
            'materia_id' => Materia::factory()->create()->id,
            'gestion_id' => $gestion->id,
            'docente_id' => Docente::factory()->create()->id,
            'codigo' => 'A',
            'turno' => 'mañana',
            'hora_inicio' => '07:30',
            'hora_fin' => '11:30',
            'aula' => 'A',
            'cupo' => 40,
            'activo' => true,
        ], $attributes));
    }

    private function studentUpdateData(Estudiante $estudiante, string $correo): array
    {
        return [
            'carrera_id' => $estudiante->carrera_id,
            'codigo' => $estudiante->codigo,
            'ci' => $estudiante->ci,
            'nombres' => $estudiante->nombres,
            'apellidos' => $estudiante->apellidos,
            'correo' => $correo,
        ];
    }

    private function carreraUpdateData(Carrera $carrera, string $codigo): array
    {
        return [
            'codigo' => $codigo,
            'nombre' => $carrera->nombre,
            'duracion_anios' => $carrera->duracion_anios,
            'activa' => 1,
        ];
    }
}
