<?php

namespace App\Http\Controllers;

use App\Models\{Paralelo, Gestion, Materia, Docente};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParaleloController extends Controller
{
    public function index()
    {
        $paralelos = Paralelo::with('materia', 'gestion', 'docente')
            ->withCount('inscripciones')
            ->get()
            ->sortBy(fn (Paralelo $paralelo) => mb_strtolower(($paralelo->materia?->nombre ?? '').' '.$paralelo->codigo))
            ->values();
        return view('paralelos.index', compact('paralelos'));
    }

    public function create()
    {
        return view('paralelos.create', [
            'materias' => Materia::where('activa', true)
                ->whereHas('carrera', fn ($query) => $query->where('activa', true))
                ->orderBy('nombre')
                ->get(),
            'gestiones' => Gestion::where('vigente', 1)->get(),
            'turnos' => config('academico.turnos'),
            'paralelos' => config('academico.paralelos'),
        ]);
    }

    public function store(Request $r)
    {
        $r->validate([
            'materia_id' => 'required|exists:materias,id',
            'gestion_id' => 'required|exists:gestiones,id',
            'docente_id' => 'required|exists:docentes,id,activo,1',
            'codigo' => [
                'required',
                'string',
                Rule::in(config('academico.paralelos')),
                Rule::unique('paralelos', 'codigo')->where(fn ($query) => $query
                    ->where('materia_id', $r->materia_id)
                    ->where('gestion_id', $r->gestion_id)),
            ],
            'turno' => 'required|string|in:mañana,medio_dia,tarde,noche',
            'cupo' => 'required|integer|min:1',
        ]);

        $materiaDisponible = Materia::where('id', $r->materia_id)
            ->where('activa', true)
            ->whereHas('carrera', fn ($query) => $query->where('activa', true))
            ->exists();

        if (! $materiaDisponible) {
            return back()->withErrors(['materia_id' => 'No se pueden crear paralelos para materias inactivas o de carreras inactivas.'])->withInput();
        }

        $g = Gestion::findOrFail($r->gestion_id);
        if (! $g->vigente) {
            return back()->withErrors(['gestion_id' => 'Solo se puede crear paralelos en gestión vigente'])->withInput();
        }

        $turnos = config('academico.turnos');
        $turno = $r->turno;
        $horaInicio = $turnos[$turno]['hora_inicio'] ?? null;
        $horaFin = $turnos[$turno]['hora_fin'] ?? null;

        // Evitar choque de docente en mismo turno/gestion
        $paraleloPropuesto = new Paralelo([
            'gestion_id' => (int) $r->gestion_id,
            'turno' => $turno,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
        ]);

        $codigoOcupado = Paralelo::where('gestion_id', $r->gestion_id)
            ->where('codigo', $r->codigo)
            ->where('activo', true)
            ->get()
            ->contains(fn (Paralelo $existente) => $paraleloPropuesto->hayCruceHorario($existente));

        if ($codigoOcupado) {
            return back()->withErrors(['codigo' => 'Ese paralelo ya tiene una clase asignada en ese horario.'])->withInput();
        }

        $cruceDocente = $this->docenteTieneCruce($paraleloPropuesto, (int) $r->docente_id);

        if ($cruceDocente) {
            return back()->withErrors(['docente_id' => 'El docente ya tiene un paralelo asignado en este turno para la misma gestión.'])->withInput();
        }

        Paralelo::create([
            'materia_id' => $r->materia_id,
            'gestion_id' => $r->gestion_id,
            'docente_id' => $r->docente_id,
            'codigo' => $r->codigo,
            'turno' => $turno,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'horario' => $turnos[$turno]['label'].' '.$horaInicio.' - '.$horaFin,
            'cupo' => $r->cupo,
            'activo' => true,
        ]);

        return redirect()->route('paralelos.index')->with('success', 'Paralelo creado');
    }

    public function show(Paralelo $paralelo)
    {
        return view('paralelos.show', ['paralelo' => $paralelo->load('materia', 'docente', 'inscripciones.estudiante')]);
    }

    public function edit(Paralelo $paralelo)
    {
        return view('paralelos.edit', [
            'paralelo' => $paralelo,
            'materias' => Materia::where('activa', true)
                ->whereHas('carrera', fn ($query) => $query->where('activa', true))
                ->orderBy('nombre')
                ->get(),
            'gestiones' => Gestion::orderByDesc('fecha_inicio')->get(),
            'turnos' => config('academico.turnos'),
            'paralelos' => config('academico.paralelos'),
        ]);
    }

    public function update(Request $r, Paralelo $paralelo)
    {
        $r->validate([
            'materia_id' => 'required|exists:materias,id',
            'gestion_id' => 'required|exists:gestiones,id',
            'docente_id' => 'required|exists:docentes,id,activo,1',
            'codigo' => [
                'required',
                'string',
                Rule::in(config('academico.paralelos')),
                Rule::unique('paralelos', 'codigo')->where(fn ($query) => $query
                    ->where('materia_id', $r->materia_id)
                    ->where('gestion_id', $r->gestion_id))
                    ->ignore($paralelo->id),
            ],
            'turno' => 'required|string|in:mañana,medio_dia,tarde,noche',
            'cupo' => 'required|integer|min:1',
        ]);

        $materiaDisponible = Materia::where('id', $r->materia_id)
            ->where('activa', true)
            ->whereHas('carrera', fn ($query) => $query->where('activa', true))
            ->exists();

        if (! $materiaDisponible) {
            return back()->withErrors(['materia_id' => 'No se pueden asignar paralelos a materias inactivas o de carreras inactivas.'])->withInput();
        }

        $turnos = config('academico.turnos');
        $turno = $r->turno;
        $horaInicio = $turnos[$turno]['hora_inicio'] ?? null;
        $horaFin = $turnos[$turno]['hora_fin'] ?? null;

        // Evitar choque de docente
        $paraleloPropuesto = new Paralelo([
            'gestion_id' => (int) $r->gestion_id,
            'turno' => $turno,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
        ]);

        $codigoOcupado = Paralelo::where('gestion_id', $r->gestion_id)
            ->where('codigo', $r->codigo)
            ->where('activo', true)
            ->where('id', '!=', $paralelo->id)
            ->get()
            ->contains(fn (Paralelo $existente) => $paraleloPropuesto->hayCruceHorario($existente));

        if ($codigoOcupado) {
            return back()->withErrors(['codigo' => 'Ese paralelo ya tiene una clase asignada en ese horario.'])->withInput();
        }

        $cruceDocente = $this->docenteTieneCruce($paraleloPropuesto, (int) $r->docente_id, $paralelo->id);

        if ($cruceDocente) {
            return back()->withErrors(['docente_id' => 'El docente ya tiene un paralelo asignado en este turno para la misma gestión.'])->withInput();
        }

        $paralelo->update([
            'materia_id' => $r->materia_id,
            'gestion_id' => $r->gestion_id,
            'docente_id' => $r->docente_id,
            'codigo' => $r->codigo,
            'turno' => $turno,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'horario' => $turnos[$turno]['label'].' '.$horaInicio.' - '.$horaFin,
            'cupo' => $r->cupo,
            'activo' => $r->boolean('activo'),
        ]);

        return redirect()->route('paralelos.index')->with('success', 'Actualizado');
    }

    public function docentesDisponibles(Request $request)
    {
        $data = $request->validate([
            'gestion_id' => 'required|exists:gestiones,id',
            'turno' => 'required|in:mañana,medio_dia,tarde,noche',
            'paralelo_id' => 'nullable|exists:paralelos,id',
        ]);

        $turno = config('academico.turnos')[$data['turno']];
        $propuesto = new Paralelo([
            'gestion_id' => (int) $data['gestion_id'],
            'turno' => $data['turno'],
            'hora_inicio' => $turno['hora_inicio'],
            'hora_fin' => $turno['hora_fin'],
        ]);

        $paralelosExistentes = Paralelo::where('gestion_id', $data['gestion_id'])
            ->where('activo', true)
            ->when($data['paralelo_id'] ?? null, fn ($query, $paraleloId) => $query->where('id', '!=', $paraleloId))
            ->get();

        $docentesOcupados = $paralelosExistentes
            ->filter(fn (Paralelo $existente) => $propuesto->hayCruceHorario($existente))
            ->pluck('docente_id')
            ->unique();

        $docentes = Docente::where('activo', true)
            ->whereNotIn('id', $docentesOcupados)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get(['id', 'nombres', 'apellidos']);

        return response()->json($docentes);
    }

    public function codigosDisponibles(Request $request)
    {
        $data = $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'gestion_id' => 'required|exists:gestiones,id',
            'turno' => 'required|in:mañana,medio_dia,tarde,noche',
        ]);

        abort_unless(
            Materia::where('id', $data['materia_id'])
                ->where('activa', true)
                ->whereHas('carrera', fn ($query) => $query->where('activa', true))
                ->exists(),
            422,
            'La materia o su carrera está inactiva.'
        );

        $turno = config('academico.turnos')[$data['turno']];
        $propuesto = new Paralelo([
            'gestion_id' => (int) $data['gestion_id'],
            'turno' => $data['turno'],
            'hora_inicio' => $turno['hora_inicio'],
            'hora_fin' => $turno['hora_fin'],
        ]);

        $paralelosExistentes = Paralelo::where('gestion_id', $data['gestion_id'])
            ->where('activo', true)
            ->get();

        $codigosOcupadosEnHorario = $paralelosExistentes
            ->filter(fn (Paralelo $existente) => $propuesto->hayCruceHorario($existente))
            ->pluck('codigo')
            ->all();

        $codigosYaCreadosParaMateria = Paralelo::where('materia_id', $data['materia_id'])
            ->where('gestion_id', $data['gestion_id'])
            ->pluck('codigo')
            ->all();

        $codigosNoDisponibles = array_unique(array_merge($codigosOcupadosEnHorario, $codigosYaCreadosParaMateria));

        return response()->json(array_values(array_diff(config('academico.paralelos'), $codigosNoDisponibles)));
    }

    public function destroy(Paralelo $paralelo)
    {
        if ($paralelo->inscripciones()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar el paralelo porque ya tiene inscripciones registradas.']);
        }
        if ($paralelo->imagen) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($paralelo->imagen);
        }
        $paralelo->delete();
        return redirect()->route('paralelos.index')->with('success', 'Eliminado');
    }

    private function docenteTieneCruce(Paralelo $propuesto, int $docenteId, ?int $paraleloId = null): bool
    {
        return Paralelo::where('docente_id', $docenteId)
            ->where('gestion_id', $propuesto->gestion_id)
            ->where('activo', true)
            ->when($paraleloId, fn ($query) => $query->where('id', '!=', $paraleloId))
            ->get()
            ->contains(fn (Paralelo $existente) => $propuesto->hayCruceHorario($existente));
    }
}
