<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EstudianteController extends Controller
{
    public function index(Request $request){
        $estudiantes = Estudiante::with('carrera')->filtro($request->all())->orderBy('apellidos')->orderBy('nombres')->paginate(10)->withQueryString();
        return view('estudiantes.index', compact('estudiantes'));
    }

    public function create(){
        $carreras = Carrera::where('activa', true)->orderBy('nombre')->get();
        return view('estudiantes.create', compact('carreras'));
    }

    public function store(Request $request){
        $data = $request->validate([
            'carrera_id' => ['required', Rule::exists('carreras', 'id')->where('activa', true)],
            'codigo'=>'required|unique:estudiantes,codigo',
            'ci'=>'required|unique:estudiantes,ci',
            'nombres'=>'required',
            'apellidos'=>'required',
            'correo'=>'required|email|unique:estudiantes,correo',
            'imagen'=>'nullable|image|max:2048'
        ]);
        if($request->hasFile('imagen')){
            $data['imagen']=$request->file('imagen')->store('estudiantes','public');
        }
        $data['estado']='activo';
        Estudiante::create($data);
        return redirect()->route('estudiantes.index')->with('success','Creado');
    }

    public function show(Estudiante $estudiante){
        $estudiante->load('carrera');
        $estudiante->load([
            'inscripciones.paralelo.materia',
            'inscripciones.paralelo.docente',
            'inscripciones.paralelo.gestion',
            'inscripciones.notas',
            'inscripciones.asistencias'
        ]);
        return view('estudiantes.show', compact('estudiante')); 
    }

    public function edit(Estudiante $estudiante){
        $carreras = Carrera::where('activa', true)->orderBy('nombre')->get();
        return view('estudiantes.edit', compact('estudiante', 'carreras'));
    }

    public function update(Request $request, Estudiante $estudiante){
        $data=$request->validate([
            'carrera_id' => ['required', Rule::exists('carreras', 'id')->where('activa', true)],
            'codigo' => ['required', 'string', 'max:20', Rule::unique('estudiantes', 'codigo')->ignore($estudiante->id)],
            'ci' => ['required', 'string', 'max:20', Rule::unique('estudiantes', 'ci')->ignore($estudiante->id)],
            'nombres'=>'required|string|max:100',
            'apellidos'=>'required|string|max:100',
            'correo' => ['required', 'email', Rule::unique('estudiantes', 'correo')->ignore($estudiante->id)],
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if ((int) $data['carrera_id'] !== (int) $estudiante->carrera_id
            && $estudiante->inscripciones()->where('estado', '!=', 'no_inscrito')->exists()) {
            return back()->withErrors(['carrera_id' => 'No se puede cambiar la carrera mientras el estudiante tenga inscripciones activas.'])->withInput();
        }
        if($request->hasFile('imagen')){
            if($estudiante->imagen) Storage::disk('public')->delete($estudiante->imagen);
            $data['imagen']=$request->file('imagen')->store('estudiantes','public');
        }
        $estudiante->update($data);
        return redirect()->route('estudiantes.index')->with('success','Actualizado');
    }

    public function destroy(Estudiante $estudiante){
        if($estudiante->imagen) Storage::disk('public')->delete($estudiante->imagen);
        $estudiante->delete();
        return redirect()->route('estudiantes.index')->with('success','Eliminado');
    }
}
