<?php
namespace App\Http\Controllers;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
class DocenteController extends Controller
{
    public function index(Request $request){
        $q = $request->get('q');
        $docentes = Docente::when($q, fn($query) => $query->where(fn ($filter) => $filter->where('nombres','like',"%$q%")->orWhere('apellidos','like',"%$q%")->orWhere('codigo','like',"%$q%")))->withCount('paralelos')->orderBy('apellidos')->orderBy('nombres')->paginate(10)->withQueryString();
        return view('docentes.index', compact('docentes'));
    }
    public function create(){ return view('docentes.create'); }
    public function store(Request $request){
        $data = $request->validate(['codigo'=>'required|unique:docentes,codigo','ci'=>'required|unique:docentes,ci','nombres'=>'required','apellidos'=>'required','correo'=>'required|email|unique:docentes,correo','especialidad'=>'nullable','imagen'=>'nullable|image|max:2048']);
        if($request->hasFile('imagen')){ $data['imagen']=$request->file('imagen')->store('docentes','public'); }
        $data['activo']=true;
        Docente::create($data);
        return redirect()->route('docentes.index')->with('success','Docente creado');
    }
    public function show(Docente $docente){ $docente->load(['paralelos' => fn ($query) => $query
        ->with(['materia', 'gestion', 'inscripciones'])
        ->orderBy('materia_id')
        ->orderBy('codigo')]); return view('docentes.show', compact('docente')); }
    public function edit(Docente $docente){ return view('docentes.edit', compact('docente')); }
    public function update(Request $request, Docente $docente){
        $data=$request->validate([
            'nombres'=>'required|string|max:100',
            'apellidos'=>'required|string|max:100',
            'correo' => ['required', 'email', Rule::unique('docentes', 'correo')->ignore($docente->id)],
            'especialidad'=>'nullable|string|max:120',
            'imagen'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if($request->hasFile('imagen')){ if($docente->imagen) Storage::disk('public')->delete($docente->imagen); $data['imagen']=$request->file('imagen')->store('docentes','public'); }
        $docente->update($data);
        return redirect()->route('docentes.index')->with('success','Actualizado');
    }
    public function destroy(Docente $docente){
        if ($docente->paralelos()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar el docente porque tiene paralelos asignados.']);
        }
        if ($docente->imagen) {
            Storage::disk('public')->delete($docente->imagen);
        }
        $docente->delete();
        return redirect()->route('docentes.index')->with('success', 'Eliminado correctamente');
    }
}
