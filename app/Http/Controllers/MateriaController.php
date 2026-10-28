<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::with('carrera', 'prerrequisitos')->orderBy('nombre')->get();
        return view('materias.index', compact('materias'));
    }

    public function create(Request $request)
    {
        $carreras = Carrera::where('activa', true)->orderBy('nombre')->get();
        $materias = Materia::where('activa', true)->orderBy('nombre')->get();
        $carreraSeleccionada = $request->input('carrera_id');

        return view('materias.create', compact('carreras', 'materias', 'carreraSeleccionada'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'carrera_id' => ['required', Rule::exists('carreras', 'id')->where('activa', true)],
            'codigo' => 'required|unique:materias,codigo',
            'nombre' => 'required|string|max:120',
            'semestre' => 'required|integer|between:1,6',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'prerrequisitos' => 'nullable|array',
            'prerrequisitos.*' => [
                'integer',
                Rule::exists('materias', 'id')->where('carrera_id', $r->input('carrera_id')),
            ],
        ]);

        $data = $r->only('carrera_id', 'codigo', 'nombre', 'semestre', 'descripcion');
        $data['activa'] = true;

        if ($r->hasFile('imagen')) {
            $data['imagen'] = $r->file('imagen')->store('materias', 'public');
        }

        $materia = Materia::create($data);

        if ($r->filled('prerrequisitos')) {
            $materia->prerrequisitos()->sync($r->prerrequisitos);
        }

        return redirect()->route('materias.index')->with('success', 'Materia creada correctamente');
    }

    public function show(Materia $materia)
    {
        $materia->load([
            'carrera',
            'prerrequisitos' => fn ($query) => $query->orderBy('nombre'),
            'paralelos' => fn ($query) => $query->with(['docente', 'gestion'])->orderBy('codigo'),
        ]);
        return view('materias.show', compact('materia'));
    }

    public function edit(Materia $materia)
    {
        $carreras = Carrera::where('activa', true)->orderBy('nombre')->get();
        $materias = Materia::where('activa', true)->where('id', '!=', $materia->id)->orderBy('nombre')->get();
        return view('materias.edit', compact('materia', 'carreras', 'materias'));
    }

    public function update(Request $r, Materia $materia)
    {
        $r->validate([
            'carrera_id' => ['required', Rule::exists('carreras', 'id')->where('activa', true)],
            'codigo' => 'required|unique:materias,codigo,' . $materia->id,
            'nombre' => 'required|string|max:120',
            'semestre' => 'required|integer|between:1,6',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'prerrequisitos' => 'nullable|array',
            'prerrequisitos.*' => [
                'integer',
                Rule::exists('materias', 'id')->where('carrera_id', $r->input('carrera_id')),
                Rule::notIn([$materia->id]),
            ],
        ]);

        if ((int) $r->input('carrera_id') !== (int) $materia->carrera_id && $materia->paralelos()->exists()) {
            return back()->withErrors(['carrera_id' => 'No se puede cambiar la carrera de una materia que ya tiene paralelos.'])->withInput();
        }

        $data = $r->only('carrera_id', 'codigo', 'nombre', 'semestre', 'descripcion');
        $data['activa'] = true;

        if ($r->hasFile('imagen')) {
            if ($materia->imagen) {
                Storage::disk('public')->delete($materia->imagen);
            }
            $data['imagen'] = $r->file('imagen')->store('materias', 'public');
        }

        $materia->update($data);

        if ($r->has('prerrequisitos')) {
            $materia->prerrequisitos()->sync($r->prerrequisitos ?? []);
        }

        return redirect()->route('materias.show', $materia)->with('success', 'Materia actualizada correctamente');
    }

    public function destroy(Materia $materia)
    {
        if ($materia->paralelos()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar la materia porque tiene paralelos asignados.']);
        }
        if ($materia->prerrequisitos()->exists() || $materia->materiasDependientes()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar la materia porque es prerrequisito o tiene materias dependientes.']);
        }
        if ($materia->imagen) {
            Storage::disk('public')->delete($materia->imagen);
        }
        $materia->delete();
        return redirect()->route('materias.index')->with('success', 'Materia eliminada correctamente');
    }
}
