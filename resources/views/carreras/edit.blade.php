@extends('layouts.app')
@section('title', 'Editar carrera')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Oferta académica</p><h1 class="page-title">Editar carrera</h1><p class="page-subtitle">Actualiza la información de {{ $carrera->nombre }}.</p></div><a href="/carreras/{{ $carrera->id }}" class="button button-secondary">Ver carrera</a></div>
<section class="panel"><form method="POST" action="/carreras/{{ $carrera->id }}" enctype="multipart/form-data">@csrf @method('PUT')
    <div class="form-grid">
        <div class="field"><label for="codigo">Código</label><input id="codigo" name="codigo" value="{{ old('codigo', $carrera->codigo) }}" class="control"></div>
        <div class="field"><label for="nombre">Nombre de la carrera</label><input id="nombre" name="nombre" value="{{ old('nombre', $carrera->nombre) }}" class="control"></div>
        <div class="field"><label for="duracion_anios">Duración en años</label><input id="duracion_anios" name="duracion_anios" type="number" min="1" value="{{ old('duracion_anios', $carrera->duracion_anios) }}" class="control"></div>
        <div class="field span-2"><label for="descripcion">Descripción</label><textarea id="descripcion" name="descripcion" class="control">{{ old('descripcion', $carrera->descripcion) }}</textarea></div>
        <div class="field span-2"><label for="imagen">Imagen de la carrera</label><img class="image-preview" src="{{ $carrera->imagen_url }}" alt="Imagen actual"><input id="imagen" name="imagen" type="file" accept="image/*" class="control"><span class="field-hint">Deja el campo vacío para conservar la imagen actual.</span></div>
    </div>
    <div class="actions"><button class="button button-primary" type="submit">Guardar cambios</button><a href="/carreras/{{ $carrera->id }}" class="button button-secondary">Cancelar</a></div>
</form></section>
@endsection
