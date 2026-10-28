<?php
namespace App\Http\Controllers;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
class CarreraController extends Controller
{
    public function index(Request $request)
    {
        $query = Carrera::withCount('materias');
        if ($request->filled('q')) {
            $query->where(fn ($filter) => $filter
                ->where('nombre', 'like', '%'.$request->q.'%')
                ->orWhere('codigo', 'like', '%'.$request->q.'%'));
        }
        if ($request->filled('activa')) {
            $query->where('activa', $request->activa == '1');
        }
        $carreras = $query->orderBy('nombre')->paginate(10)->withQueryString();
        if ($request->wantsJson()) return $carreras;
        return view('carreras.index', compact('carreras'));
    }
    public function create() { return view('carreras.create'); }
    public function store(Request $request)
    {
        $request->validate(['codigo'=>'required|unique:carreras','nombre'=>'required','imagen'=>'nullable|image|max:2048']);
        $data = $request->only('codigo','nombre','duracion_anios','descripcion');
        $data['activa'] = true;
        if ($request->hasFile('imagen')) $data['imagen'] = $request->file('imagen')->store('carreras','public');
        $c = Carrera::create($data);
        if ($request->wantsJson()) return $c;
        return redirect()->route('carreras.index')->with('success','Carrera creada');
    }
    public function show(Carrera $carrera, Request $request)
    {
        $carrera->load(['materias' => fn ($query) => $query->orderBy('nombre')]);
        if ($request->wantsJson()) return $carrera;
        return view('carreras.show', compact('carrera'));
    }
    public function edit(Carrera $carrera) { return view('carreras.edit', compact('carrera')); }
    public function update(Request $request, Carrera $carrera)
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('carreras', 'codigo')->ignore($carrera->id)],
            'nombre' => ['required', 'string', 'max:120', Rule::unique('carreras', 'nombre')->ignore($carrera->id)],
            'duracion_anios' => 'required|integer|min:1|max:20',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data['activa'] = true;
        if ($request->hasFile('imagen')) {
            if ($carrera->imagen) Storage::disk('public')->delete($carrera->imagen);
            $data['imagen'] = $request->file('imagen')->store('carreras','public');
        }
        $carrera->update($data);
        if ($request->wantsJson()) return $carrera;
        return redirect()->route('carreras.index')->with('success','Actualizado');
    }
    public function destroy(Carrera $carrera, Request $request)
    {
        if ($carrera->materias()->exists()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'No se puede eliminar la carrera porque tiene materias registradas.'], 422);
            }
            return back()->withErrors(['error' => 'No se puede eliminar la carrera porque tiene materias registradas.']);
        }
        if ($carrera->imagen) {
            Storage::disk('public')->delete($carrera->imagen);
        }
        $carrera->delete();
        if ($request->wantsJson()) {
            return response()->noContent();
        }
        return redirect()->route('carreras.index')->with('success', 'Eliminado');
    }
}
