<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateEstudianteRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('estudiante')?->id ?? $this->estudiante?->id;
        return [
            'codigo' => 'required|unique:estudiantes,codigo,'.$id,
            'ci' => 'required|unique:estudiantes,ci,'.$id,
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'correo' => 'required|email|unique:estudiantes,correo,'.$id,
            'telefono' => 'nullable|string|max:30',
            'fecha_nacimiento' => 'nullable|date',
            'estado' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
