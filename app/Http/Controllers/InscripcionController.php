<?php

namespace App\Http\Controllers;

use App\Models\{Estudiante, Inscripcion, Paralelo};
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class InscripcionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->input('estado') === 'no_inscrito') {
            $estudiantes = Estudiante::query()
                ->where('estado', 'activo')
                ->whereDoesntHave('inscripciones', fn ($query) => $query->where('estado', '!=', 'no_inscrito'))
                ->when($request->filled('q'), function ($query) use ($request) {
                    $termino = $request->input('q');
                    $query->where(function ($estudiantes) use ($termino) {
                        $estudiantes->where('nombres', 'like', '%' . $termino . '%')
                            ->orWhere('apellidos', 'like', '%' . $termino . '%')
                            ->orWhere('codigo', 'like', '%' . $termino . '%');
                    });
                })
                ->orderBy('apellidos')
                ->orderBy('nombres')
                ->paginate(15)
                ->withQueryString();

            if ($request->wantsJson()) {
                return $estudiantes;
            }

            return view('inscripciones.index', [
                'inscripciones' => $estudiantes,
                'mostrandoEstudiantesSinInscripcion' => true,
            ]);
        }

        $q = Inscripcion::with('estudiante', 'paralelo.materia');
        if ($request->filled('estado')) {
            $q->where('estado', $request->estado);
        }
        if ($request->filled('q')) {
            $q->whereHas('estudiante', fn ($qq) => $qq->where('nombres', 'like', '%' . $request->q . '%')->orWhere('apellidos', 'like', '%' . $request->q . '%'));
        }
        if ($request->filled('fecha')) {
            $q->whereDate('fecha_inscripcion', $request->fecha);
        }
        $inscripciones = $q->orderByDesc('fecha_inscripcion')->paginate(15)->withQueryString();
        if ($request->wantsJson()) {
            return $inscripciones;
        }

        return view('inscripciones.index', [
            'inscripciones' => $inscripciones,
            'mostrandoEstudiantesSinInscripcion' => false,
        ]);
    }

    public function create(Request $request)
    {
        $estudiantes = Estudiante::where('estado', 'activo')
            ->whereHas('carrera', fn ($query) => $query->where('activa', true))
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get(['id', 'codigo', 'nombres', 'apellidos']);

        $estudianteSeleccionado = $estudiantes->firstWhere('id', (int) $request->query('estudiante_id'));

        return view('inscripciones.create', compact('estudiantes', 'estudianteSeleccionado'));
    }

    public function paralelosDisponibles(Request $request)
    {
        $data = $request->validate([
            'estudiante_id' => ['nullable', Rule::exists('estudiantes', 'id')->where('estado', 'activo')],
        ]);

        $estudianteId = $data['estudiante_id'] ?? null;
        $estudiante = ! empty($estudianteId)
            ? Estudiante::with('carrera')->findOrFail($estudianteId)
            : null;
        if ($estudiante && ! $estudiante->carrera?->activa) {
            return response()->json([]);
        }

        $inscripciones = $estudiante
            ? Inscripcion::with(['paralelo', 'notas'])
                ->where('estudiante_id', $estudiante->id)
                ->where('estado', '!=', 'no_inscrito')
                ->get()
            : collect();

        $paralelos = Paralelo::with(['materia.prerrequisitos', 'docente', 'gestion'])
            ->withCount(['inscripciones as inscritos_count' => fn ($query) => $query->where('estado', '!=', 'no_inscrito')])
            ->where('activo', true)
            ->whereHas('materia', fn ($query) => $query->where('activa', true)
                ->when($estudiante, fn ($materias) => $materias->where('carrera_id', $estudiante->carrera_id))
                ->whereHas('carrera', fn ($career) => $career->where('activa', true)))
            ->whereHas('gestion', fn ($query) => $query->where('vigente', true))
            ->get()
            ->filter(fn (Paralelo $paralelo) => $paralelo->inscritos_count < $paralelo->cupo
                && (! $estudiante || $this->estudianteDisponibleParaParalelo($paralelo, $inscripciones)))
            ->sortBy(fn (Paralelo $paralelo) => mb_strtolower(($paralelo->materia?->nombre ?? '').' '.$paralelo->codigo))
            ->map(fn (Paralelo $paralelo) => [
                'id' => $paralelo->id,
                'materia' => $paralelo->materia?->nombre,
                'codigo' => $paralelo->codigo,
                'docente' => trim(($paralelo->docente?->nombres ?? '').' '.($paralelo->docente?->apellidos ?? '')),
                'horario' => $paralelo->horario,
                'cupos_libres' => max(0, $paralelo->cupo - $paralelo->inscritos_count),
            ])
            ->values();

        return response()->json($paralelos);
    }

    public function estudiantesDisponibles(Request $request)
    {
        $data = $request->validate([
            'paralelo_id' => 'required|exists:paralelos,id',
        ]);

        $paralelo = Paralelo::with(['materia.prerrequisitos', 'materia.carrera', 'gestion'])
            ->withCount(['inscripciones as inscritos_count' => fn ($query) => $query->where('estado', '!=', 'no_inscrito')])
            ->where('activo', true)
            ->whereHas('materia', fn ($query) => $query->where('activa', true)->whereHas('carrera', fn ($career) => $career->where('activa', true)))
            ->whereHas('gestion', fn ($query) => $query->where('vigente', true))
            ->findOrFail($data['paralelo_id']);

        if ($paralelo->inscritos_count >= $paralelo->cupo) {
            return response()->json([]);
        }

        $estudiantes = Estudiante::where('estado', 'activo')
            ->where('carrera_id', $paralelo->materia->carrera_id)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $inscripcionesPorEstudiante = Inscripcion::with(['paralelo', 'notas'])
            ->whereIn('estudiante_id', $estudiantes->pluck('id'))
            ->where('estado', '!=', 'no_inscrito')
            ->get()
            ->groupBy('estudiante_id');

        $disponibles = $estudiantes
            ->filter(fn (Estudiante $estudiante) => $this->estudianteDisponibleParaParalelo(
                $paralelo,
                $inscripcionesPorEstudiante->get($estudiante->id, collect()),
            ))
            ->map(fn (Estudiante $estudiante) => [
                'id' => $estudiante->id,
                'codigo' => $estudiante->codigo,
                'nombres' => $estudiante->nombres,
                'apellidos' => $estudiante->apellidos,
            ])
            ->values();

        return response()->json($disponibles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'estudiante_id' => ['required', Rule::exists('estudiantes', 'id')->where('estado', 'activo')],
            'paralelo_id' => 'required|exists:paralelos,id',
        ]);

        try {
            $ins = DB::transaction(function () use ($request) {
                $paralelo = Paralelo::withCount(['inscripciones as inscritos_count' => fn ($query) => $query->where('estado', '!=', 'no_inscrito')])
                    ->with('materia.prerrequisitos', 'materia.carrera', 'gestion')
                    ->lockForUpdate()
                    ->findOrFail($request->paralelo_id);

                if (! $paralelo->activo) {
                    throw ValidationException::withMessages(['paralelo_id' => 'Este paralelo no está activo.']);
                }

                if (! $paralelo->gestion?->vigente || ! $paralelo->materia?->activa || ! $paralelo->materia?->carrera?->activa) {
                    throw ValidationException::withMessages(['paralelo_id' => 'Este paralelo ya no está disponible.']);
                }

                $estudiante = Estudiante::findOrFail($request->estudiante_id);
                if ((int) $estudiante->carrera_id !== (int) $paralelo->materia->carrera_id) {
                    throw ValidationException::withMessages(['estudiante_id' => 'El estudiante solo puede inscribirse en materias de su carrera.']);
                }

                if ($paralelo->inscritos_count >= $paralelo->cupo) {
                    throw ValidationException::withMessages(['paralelo_id' => 'Cupo lleno']);
                }

                $inscripcionExistente = Inscripcion::where('estudiante_id', $request->estudiante_id)
                    ->where('paralelo_id', $paralelo->id)
                    ->lockForUpdate()
                    ->first();

                if ($inscripcionExistente && $inscripcionExistente->estado !== 'no_inscrito') {
                    throw ValidationException::withMessages(['paralelo_id' => 'El estudiante ya está inscrito en este paralelo']);
                }

                $prerrequisitos = $paralelo->materia?->prerrequisitos ?? collect();
                if ($prerrequisitos->isNotEmpty()) {
                    foreach ($prerrequisitos as $prerreq) {
                        $aprobado = Inscripcion::where('estudiante_id', $request->estudiante_id)
                            ->whereHas('paralelo', fn ($q) => $q->where('materia_id', $prerreq->id))
                            ->where('estado', '!=', 'no_inscrito')
                            ->whereHas('notas')
                            ->get()
                            ->some(fn ($insc) => $insc->calcularNotaFinal() !== null && $insc->calcularNotaFinal() >= 51);

                        if (! $aprobado) {
                            throw ValidationException::withMessages(['paralelo_id' => 'No cumple con el prerrequisito: ' . $prerreq->nombre . ' (se requiere nota >= 51)']);
                        }
                    }
                }

                $inscripcionesEstudiante = Inscripcion::with('paralelo')
                    ->where('estudiante_id', $request->estudiante_id)
                    ->where('estado', '!=', 'no_inscrito')
                    ->get();

                foreach ($inscripcionesEstudiante as $insc) {
                    $pActual = $insc->paralelo;
                    if (! $pActual) {
                        continue;
                    }
                    if ($pActual->gestion_id === $paralelo->gestion_id) {
                        if ($pActual->turno && $paralelo->turno && $pActual->turno === $paralelo->turno) {
                            throw ValidationException::withMessages(['paralelo_id' => 'Choque de horario: el estudiante ya está inscrito en otro paralelo con el mismo turno en esta gestión.']);
                        }
                        if ($pActual->hayCruceHorario($paralelo)) {
                            throw ValidationException::withMessages(['paralelo_id' => 'Choque de horario: el estudiante ya está inscrito en otro paralelo con cruce de horario en esta gestión.']);
                        }
                    }
                }

                if ($inscripcionExistente) {
                    $inscripcionExistente->update([
                        'fecha_inscripcion' => now()->toDateString(),
                        'estado' => 'inscrito',
                    ]);

                    return $inscripcionExistente->fresh();
                }

                return Inscripcion::create([
                    'estudiante_id' => $request->estudiante_id,
                    'paralelo_id' => $paralelo->id,
                    'fecha_inscripcion' => now(),
                    'estado' => 'inscrito',
                ]);
            });

            if ($request->wantsJson()) {
                return $ins;
            }

            return redirect()->route('inscripciones.index')->with('success', 'Inscrito correctamente');
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function registrarNota(Request $r, Inscripcion $inscripcion)
    {
        $r->validate(['tipo' => 'required|in:parcial_1,parcial_2,parcial_3,examen_final', 'calificacion' => 'required|numeric|between:0,100']);
        $nota = $inscripcion->notas()->updateOrCreate(['tipo' => $r->tipo], ['calificacion' => $r->calificacion, 'fecha_evaluacion' => now()]);

        $respuesta = ['nota' => $nota, 'nota_final' => $inscripcion->calcularNotaFinal()];
        if ($r->wantsJson()) {
            return response()->json($respuesta);
        }

        return redirect()->route('inscripciones.show', $inscripcion)
            ->with('success', 'Nota guardada. Nota final actual: '.$respuesta['nota_final']);
    }

    private function estudianteDisponibleParaParalelo(Paralelo $paralelo, $inscripciones): bool
    {
        $inscripciones = collect($inscripciones);

        foreach ($inscripciones as $inscripcion) {
            $paraleloActual = $inscripcion->paralelo;
            if (! $paraleloActual) {
                continue;
            }

            if ((int) $paraleloActual->id === (int) $paralelo->id) {
                return false;
            }

            if ((int) $paraleloActual->gestion_id === (int) $paralelo->gestion_id
                && (($paraleloActual->turno && $paralelo->turno && $paraleloActual->turno === $paralelo->turno)
                    || $paraleloActual->hayCruceHorario($paralelo))) {
                return false;
            }
        }

        foreach ($paralelo->materia?->prerrequisitos ?? collect() as $prerrequisito) {
            $aprobado = $inscripciones->contains(function (Inscripcion $inscripcion) use ($prerrequisito): bool {
                return (int) ($inscripcion->paralelo?->materia_id ?? 0) === (int) $prerrequisito->id
                    && $inscripcion->notas->isNotEmpty()
                    && $inscripcion->notas->avg('calificacion') >= 51;
            });

            if (! $aprobado) {
                return false;
            }
        }

        return true;
    }

    public function registrarAsistencia(Request $r, Inscripcion $inscripcion)
    {
        $r->validate(['fecha' => 'required|date', 'presente' => 'required|boolean']);

        $asistencia = $inscripcion->asistencias()->updateOrCreate(['fecha' => $r->fecha], ['presente' => $r->presente, 'observacion' => $r->observacion]);

        if ($r->wantsJson()) {
            return response()->json($asistencia);
        }

        return redirect()->route('inscripciones.show', $inscripcion)->with('success', 'Asistencia guardada correctamente.');
    }

    public function show(Inscripcion $inscripcion, Request $request)
    {
        $inscripcion->load('estudiante', 'paralelo.materia', 'notas', 'asistencias');
        if ($request->wantsJson()) {
            return $inscripcion;
        }

        return view('inscripciones.show', compact('inscripcion'));
    }

    public function update(Request $request, Inscripcion $inscripcion)
    {
        $data = $request->validate([
            'estado' => 'required|in:no_inscrito,concluido',
        ]);
        $inscripcion->update($data);

        return redirect()->route('inscripciones.index')->with('success', 'Estado de inscripción actualizado.');
    }

    public function destroy(Inscripcion $inscripcion)
    {
        $inscripcion->delete();

        return redirect()->route('inscripciones.index');
    }
}
