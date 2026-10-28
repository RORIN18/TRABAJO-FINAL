@extends('layouts.app')
@section('title', 'Nueva carrera')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Oferta académica</p><h1 class="page-title">Nueva carrera</h1><p class="page-subtitle">Añade un programa al catálogo académico.</p></div><a href="/carreras" class="button button-secondary">Volver a carreras</a></div>
<section class="panel"><form method="POST" action="/carreras" enctype="multipart/form-data">@csrf
    <div class="form-grid">
        <div class="field"><label for="codigo">Código</label><input id="codigo" name="codigo" value="{{ old('codigo') }}" class="control" placeholder="Ej. INF-01" required>@error('codigo')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="nombre">Nombre de la carrera</label><input id="nombre" name="nombre" value="{{ old('nombre') }}" class="control" placeholder="Nombre del programa" required>@error('nombre')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="duracion_anios">Duración en años</label><input id="duracion_anios" name="duracion_anios" type="number" min="1" value="{{ old('duracion_anios') }}" class="control" placeholder="Ej. 4"></div>
        <div class="field span-2"><label for="descripcion">Descripción</label><textarea id="descripcion" name="descripcion" class="control" placeholder="Información breve del programa">{{ old('descripcion') }}</textarea></div>
        <div class="field span-2"><label for="imagen">Imagen de la carrera</label><input id="imagen" name="imagen" type="file" accept="image/*" class="control"><span class="field-hint">Opcional · imagen de hasta 2 MB.</span></div>
    </div>
    <div class="actions"><button class="button button-primary" type="submit">Guardar carrera</button><a href="/carreras" class="button button-secondary">Cancelar</a></div>
</form></section>
@endsection
