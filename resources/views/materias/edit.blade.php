@extends('layouts.app')
@section('title', 'Editar materia')
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Materias</p>
            <h1 class="page-title">Editar materia</h1>
            <p class="page-subtitle">Actualiza la información de {{ $materia->nombre }}.</p>
        </div>
        <div class="page-actions">
            <a class="button button-secondary" href="{{ route('materias.show', $materia) }}">Ver detalle</a>
            <a class="button button-secondary" href="{{ route('materias.index') }}">Volver al listado</a>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('materias.update', $materia) }}" enctype="multipart/form-data" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field span-2">
                <label for="nombre">Nombre de la materia</label>
                <input id="nombre" name="nombre" type="text" value="{{ old('nombre', $materia->nombre) }}" required>
            </div>
            <div class="field">
                <label for="codigo">Código</label>
                <input id="codigo" name="codigo" type="text" value="{{ old('codigo', $materia->codigo) }}" required>
            </div>
            <div class="field">
                <label for="carrera_id">Carrera</label>
                <select id="carrera_id" name="carrera_id" required>
                    @foreach($carreras as $carrera)
                        <option value="{{ $carrera->id }}" {{ old('carrera_id', $materia->carrera_id) == $carrera->id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="semestre">Semestre</label>
                <input id="semestre" name="semestre" type="number" min="1" max="6" value="{{ old('semestre', $materia->semestre) }}" required>
            </div>
            <div class="field span-2">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4">{{ old('descripcion', $materia->descripcion) }}</textarea>
            </div>
            <div class="field span-2">
                <label for="prerrequisitos">Prerrequisitos</label>
                <select id="prerrequisitos" name="prerrequisitos[]" multiple>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}" data-carrera-id="{{ $m->carrera_id }}" {{ collect(old('prerrequisitos', $materia->prerrequisitos->pluck('id')))->contains($m->id) ? 'selected' : '' }}>{{ $m->nombre }} ({{ $m->codigo }})</option>
                    @endforeach
                </select>
                <p class="field-hint">Mantén presionado Ctrl (o Cmd) para seleccionar varios.</p>
            </div>
            <div class="field">
                <label for="imagen">Imagen</label>
                <input id="imagen" name="imagen" type="file" accept="image/*">
                @if($materia->imagen)
                    <p class="field-hint">Imagen actual: <a href="{{ $materia->imagen_url }}" target="_blank">Ver</a></p>
                @endif
            </div>
            <div class="form-actions span-2">
                <button class="button button-primary" type="submit">Guardar cambios</button>
                <a class="button button-secondary" href="{{ route('materias.show', $materia) }}">Cancelar</a>
            </div>
        </form>
    </section>
    <script>
        const carreraSelect = document.getElementById('carrera_id');
        const prerrequisitosSelect = document.getElementById('prerrequisitos');
        const filtrarPrerrequisitos = () => {
            Array.from(prerrequisitosSelect.options).forEach((option) => {
                const esMismaCarrera = option.dataset.carreraId === carreraSelect.value;
                option.hidden = !esMismaCarrera;
                option.disabled = !esMismaCarrera;
                if (!esMismaCarrera) option.selected = false;
            });
        };
        carreraSelect.addEventListener('change', filtrarPrerrequisitos);
        filtrarPrerrequisitos();
    </script>
@endsection
