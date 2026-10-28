@extends('layouts.app')
@section('title', 'Editar gestión')
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Gestiones</p>
            <h1 class="page-title">Editar gestión {{ $gestion->codigo }}</h1>
            <p class="page-subtitle">Actualiza fechas o estado.</p>
        </div>
        <div class="page-actions">
            <a class="button button-secondary" href="{{ route('gestiones.index') }}">Volver al listado</a>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('gestiones.update', $gestion) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field">
                <label for="codigo">Código</label>
                <input id="codigo" name="codigo" type="text" value="{{ old('codigo', $gestion->codigo) }}" required>
            </div>
            <div class="field">
                <label for="fecha_inicio">Fecha de inicio</label>
                <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio', $gestion->fecha_inicio->format('Y-m-d')) }}" required>
            </div>
            <div class="field">
                <label for="fecha_fin">Fecha de fin</label>
                <input id="fecha_fin" name="fecha_fin" type="date" value="{{ old('fecha_fin', $gestion->fecha_fin->format('Y-m-d')) }}" required>
            </div>
            <div class="field checkbox-field">
                <label>
                    <input type="checkbox" name="vigente" value="1" {{ old('vigente', $gestion->vigente) ? 'checked' : '' }}>
                    Gestión vigente
                </label>
            </div>
            <div class="form-actions span-2">
                <button class="button button-primary" type="submit">Guardar cambios</button>
                <a class="button button-secondary" href="{{ route('gestiones.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection
