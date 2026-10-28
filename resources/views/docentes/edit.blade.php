cat > resources/views/docentes/edit.blade.php <<'BLADE'
@extends('layouts.app')
@section('title', 'Editar docente')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Planta docente</p><h1 class="page-title">Editar docente</h1><p class="page-subtitle">Actualiza la información de {{ $docente->nombres }} {{ $docente->apellidos }}.</p></div><a href="/docentes" class="button button-secondary">Volver a docentes</a></div>
<section class="panel">
    @if($docente->imagen)
        <img src="{{ asset('storage/'.$docente->imagen) }}" class="image-preview" style="width:74px;height:74px;border-radius:18px;object-fit:cover;margin-bottom:12px" alt="">
    @endif
    <form method="POST" action="/docentes/{{ $docente->id }}" enctype="multipart/form-data">@csrf @method('PUT')
        <div class="form-grid">
            <div class="field"><label for="codigo">Código</label><input id="codigo" name="codigo" value="{{ old('codigo',$docente->codigo) }}" class="control" disabled><span class="field-hint">El código no se edita.</span></div>
            <div class="field"><label for="ci">Documento de identidad</label><input id="ci" name="ci" value="{{ old('ci',$docente->ci) }}" class="control" disabled></div>
            <div class="field"><label for="nombres">Nombres</label><input id="nombres" name="nombres" value="{{ old('nombres',$docente->nombres) }}" class="control" required>@error('nombres')<span class="error-text">{{ $message }}</span>@enderror</div>
            <div class="field"><label for="apellidos">Apellidos</label><input id="apellidos" name="apellidos" value="{{ old('apellidos',$docente->apellidos) }}" class="control" required>@error('apellidos')<span class="error-text">{{ $message }}</span>@enderror</div>
            <div class="field span-2"><label for="correo">Correo electrónico</label><input id="correo" name="correo" type="email" value="{{ old('correo',$docente->correo) }}" class="control" required>@error('correo')<span class="error-text">{{ $message }}</span>@enderror</div>
            <div class="field span-2"><label for="especialidad">Especialidad</label><input id="especialidad" name="especialidad" value="{{ old('especialidad',$docente->especialidad) }}" class="control" placeholder="Ej: Sistemas">@error('especialidad')<span class="error-text">{{ $message }}</span>@enderror</div>
            <div class="field span-2"><label for="imagen">Fotografía</label><input id="imagen" name="imagen" type="file" accept="image/*" class="control"><span class="field-hint">Deja vacío para mantener la actual.</span>@error('imagen')<span class="error-text">{{ $message }}</span>@enderror</div>
        </div>
        <div class="actions"><button class="button button-primary" type="submit">Actualizar docente</button><a href="/docentes" class="button button-secondary">Cancelar</a></div>
    </form>
</section>
@endsection
BLADE