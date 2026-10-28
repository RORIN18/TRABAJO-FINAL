<?php

namespace App\Http\Controllers;

use App\Models\Gestion;
use Illuminate\Http\Request;

class GestionController extends Controller
{
    public function index()
    {
        $gestiones = Gestion::withCount('paralelos')->orderByDesc('fecha_inicio')->get();

        return view('gestiones.index', compact('gestiones'));
    }

    public function create()
    {
        return view('gestiones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:20|unique:gestiones,codigo',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'vigente' => 'nullable|boolean',
        ]);

        if ($request->boolean('vigente')) {
            Gestion::where('vigente', true)->update(['vigente' => false]);
        }

        Gestion::create([
            'codigo' => $request->codigo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'vigente' => $request->boolean('vigente'),
        ]);

        return redirect()->route('gestiones.index')->with('success', 'Gestión creada correctamente');
    }

    public function show(Gestion $gestion)
    {
        $gestion->load(['paralelos' => fn ($query) => $query
            ->with(['materia', 'docente'])
            ->orderBy('materia_id')
            ->orderBy('codigo')]);

        return view('gestiones.show', compact('gestion'));
    }

    public function edit(Gestion $gestion)
    {
        return view('gestiones.edit', compact('gestion'));
    }

    public function update(Request $request, Gestion $gestion)
    {
        $request->validate([
            'codigo' => 'required|string|max:20|unique:gestiones,codigo,' . $gestion->id,
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'vigente' => 'nullable|boolean',
        ]);

        if ($request->boolean('vigente') && ! $gestion->vigente) {
            Gestion::where('vigente', true)->update(['vigente' => false]);
        }

        $gestion->update([
            'codigo' => $request->codigo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'vigente' => $request->boolean('vigente'),
        ]);

        return redirect()->route('gestiones.index')->with('success', 'Gestión actualizada correctamente');
    }

    public function destroy(Gestion $gestion)
    {
        if ($gestion->vigente) {
            return back()->withErrors(['error' => 'No se puede eliminar una gestión vigente. Ciérrala primero.']);
        }

        $gestion->delete();

        return redirect()->route('gestiones.index')->with('success', 'Gestión eliminada correctamente');
    }

    public function activar(Gestion $gestion)
    {
        Gestion::where('vigente', true)->update(['vigente' => false]);
        $gestion->update(['vigente' => true]);

        return redirect()->route('gestiones.index')->with('success', 'Gestión ' . $gestion->codigo . ' activada como vigente');
    }
}
