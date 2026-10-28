@extends('layouts.app')
@section('title', 'Nueva materia')
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Materias</p>
            <h1 class="page-title">Registrar materia</h1>
            <p class="page-subtitle">Completa los datos para dar de alta una nueva materia.</p>
        </div>
        <div class="page-actions">
            <a class="button button-secondary" href="{{ route('materias.index') }}">Volver al listado</a>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('materias.store') }}" enctype="multipart/form-data" class="form-grid">
            @csrf
            <div class="field span-2">
                <label for="nombre">Nombre de la materia</label>
                <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" required>
            </div>
            <div class="field">
                <label for="codigo">Código</label>
                <input id="codigo" name="codigo" type="text" value="{{ old('codigo') }}" required>
            </div>
            <div class="field">
                <label for="carrera_id">Carrera</label>
                <select id="carrera_id" name="carrera_id" required>
                    <option value="" disabled>Selecciona una carrera</option>
                    @foreach($carreras as $carrera)
                        <option value="{{ $carrera->id }}" {{ old('carrera_id', $carreraSeleccionada ?? null) == $carrera->id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="semestre">Semestre</label>
                <input id="semestre" name="semestre" type="number" min="1" max="6" value="{{ old('semestre') }}" required>
            </div>
            <div class="field span-2">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4">{{ old('descripcion') }}</textarea>
            </div>
            <div class="field span-2">
                <label for="prerrequisitos">Prerrequisitos</label>
                <select id="prerrequisitos" name="prerrequisitos[]" multiple>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}" data-carrera-id="{{ $m->carrera_id }}" {{ collect(old('prerrequisitos'))->contains($m->id) ? 'selected' : '' }}>{{ $m->nombre }} ({{ $m->codigo }})</option>
                    @endforeach
                </select>
                <p class="field-hint">Mantén presionado Ctrl (o Cmd) para seleccionar varios.</p>
            </div>
            <div class="field">
                <label for="imagen">Imagen</label>
                <input id="imagen" name="imagen" type="file" accept="image/*">
            </div>
            <div class="form-actions span-2">
                <button class="button button-primary" type="submit">Guardar materia</button>
                <a class="button button-secondary" href="{{ route('materias.index') }}">Cancelar</a>
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
