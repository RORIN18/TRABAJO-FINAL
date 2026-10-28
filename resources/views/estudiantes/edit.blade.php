@extends('layouts.app')
@section('title', 'Editar estudiante')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Comunidad universitaria</p><h1 class="page-title">Editar estudiante</h1><p class="page-subtitle">Actualiza la ficha de {{ $estudiante->nombres }} {{ $estudiante->apellidos }}.</p></div><a href="/estudiantes/{{ $estudiante->id }}" class="button button-secondary">Ver ficha</a></div>
<section class="panel"><form method="POST" action="/estudiantes/{{ $estudiante->id }}" enctype="multipart/form-data">@csrf @method('PUT')
    <div class="form-grid">
        <div class="field span-2"><label for="carrera_id">Carrera</label><select id="carrera_id" name="carrera_id" class="control" required><option value="">Selecciona una carrera</option>@foreach($carreras as $carrera)<option value="{{ $carrera->id }}" @selected(old('carrera_id', $estudiante->carrera_id) == $carrera->id)>{{ $carrera->nombre }}</option>@endforeach</select>@error('carrera_id')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="codigo">Código</label><input id="codigo" name="codigo" value="{{ old('codigo', $estudiante->codigo) }}" class="control"></div>
        <div class="field"><label for="ci">Documento de identidad</label><input id="ci" name="ci" value="{{ old('ci', $estudiante->ci) }}" class="control"></div>
        <div class="field"><label for="nombres">Nombres</label><input id="nombres" name="nombres" value="{{ old('nombres', $estudiante->nombres) }}" class="control" required>@error('nombres')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="apellidos">Apellidos</label><input id="apellidos" name="apellidos" value="{{ old('apellidos', $estudiante->apellidos) }}" class="control" required>@error('apellidos')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field span-2"><label for="correo">Correo electrónico</label><input id="correo" name="correo" type="email" value="{{ old('correo', $estudiante->correo) }}" class="control" required>@error('correo')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field span-2"><label for="imagen">Fotografía</label><img class="image-preview" src="{{ $estudiante->imagen_url }}" alt="Fotografía actual"><input id="imagen" name="imagen" type="file" accept="image/*" class="control"><span class="field-hint">Deja el campo vacío para conservar la fotografía actual.</span></div>
    </div>
    <div class="actions"><button class="button button-primary" type="submit">Guardar cambios</button><a href="/estudiantes/{{ $estudiante->id }}" class="button button-secondary">Cancelar</a></div>
</form></section>
<section class="panel section-spacer"><div class="panel-header"><div><h2 class="panel-title">Eliminar estudiante</h2><p class="panel-note">Esta acción quitará el registro del sistema.</p></div></div><form method="POST" action="/estudiantes/{{ $estudiante->id }}" onsubmit="return confirm('¿Seguro que deseas eliminar este estudiante?')">@csrf @method('DELETE')<button class="button button-danger" type="submit">Eliminar estudiante</button></form></section>
@endsection
