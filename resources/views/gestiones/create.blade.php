@extends('layouts.app')
@section('title', 'Nueva gestión')
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Gestiones</p>
            <h1 class="page-title">Crear gestión académica</h1>
            <p class="page-subtitle">Define el periodo lectivo.</p>
        </div>
        <div class="page-actions">
            <a class="button button-secondary" href="{{ route('gestiones.index') }}">Volver al listado</a>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('gestiones.store') }}" class="form-grid">
            @csrf
            <div class="field">
                <label for="codigo">Código</label>
                <input id="codigo" name="codigo" type="text" value="{{ old('codigo') }}" placeholder="Ej: 2026-2" required>
            </div>
            <div class="field">
                <label for="fecha_inicio">Fecha de inicio</label>
                <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio') }}" required>
            </div>
            <div class="field">
                <label for="fecha_fin">Fecha de fin</label>
                <input id="fecha_fin" name="fecha_fin" type="date" value="{{ old('fecha_fin') }}" required>
            </div>
            <div class="field checkbox-field">
                <label>
                    <input type="checkbox" name="vigente" value="1" {{ old('vigente') ? 'checked' : '' }}>
                    Marcar como gestión vigente
                </label>
            </div>
            <div class="form-actions span-2">
                <button class="button button-primary" type="submit">Guardar gestión</button>
                <a class="button button-secondary" href="{{ route('gestiones.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection
