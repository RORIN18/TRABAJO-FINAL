@extends('layouts.app')
@section('title', 'Nuevo docente')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Planta docente</p><h1 class="page-title">Nuevo docente</h1><p class="page-subtitle">Registra sus datos para incorporarlo al sistema.</p></div><a href="/docentes" class="button button-secondary">Volver a docentes</a></div>
<section class="panel"><form method="POST" action="/docentes" enctype="multipart/form-data">@csrf
    <div class="form-grid">
        <div class="field"><label for="codigo">Código</label><input id="codigo" name="codigo" value="{{ old('codigo') }}" class="control" placeholder="Código universitario" required>@error('codigo')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="ci">Documento de identidad</label><input id="ci" name="ci" value="{{ old('ci') }}" class="control" placeholder="Número de CI" required>@error('ci')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="nombres">Nombres</label><input id="nombres" name="nombres" value="{{ old('nombres') }}" class="control" placeholder="Nombres" required>@error('nombres')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="apellidos">Apellidos</label><input id="apellidos" name="apellidos" value="{{old('apellidos') }}" class="control" placeholder="Apellidos" required>@error('apellidos')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field span-2"><label for="correo">Correo electrónico</label><input id="correo" name="correo" type="email" value="{{ old('correo') }}" class="control" placeholder="docente@correo.com" required>@error('correo')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field span-2"><label for="especialidad">Especialidad</label><input id="especialidad" name="especialidad" value="{{ old('especialidad') }}" class="control" placeholder="Ej: Sistemas"></div>
        <div class="field span-2"><label for="imagen">Fotografía</label><input id="imagen" name="imagen" type="file" accept="image/*" class="control"></div>
    </div>
    <div class="actions"><button class="button button-primary" type="submit">Guardar docente</button><a href="/docentes" class="button button-secondary">Cancelar</a></div>
</form></section>
@endsection