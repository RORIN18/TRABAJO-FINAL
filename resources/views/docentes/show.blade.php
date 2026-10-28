@extends('layouts.app')
@section('title', $docente->nombres.' '.$docente->apellidos)
@section('content')
<div class="page-header"><div class="page-heading"><h1 class="page-title">{{ $docente->nombres }} {{ $docente->apellidos }}</h1><p>ID #{{ $docente->id }} · {{ $docente->codigo }}</p></div><div class="actions">@if(auth()->user()->is_admin)<a href="{{ route('docentes.edit', $docente->id) }}" class="button button-primary">Editar</a>@endif<a href="{{ route('docentes.index') }}" class="button button-secondary">Volver</a></div></div>
<section class="panel">
<p>CI: {{ $docente->ci }} · Correo: {{ $docente->correo }} · Especialidad: {{ $docente->especialidad }}</p>
<h2>Paralelos que dicta ({{ $docente->paralelos->count() }})</h2>
@forelse($docente->paralelos as $p)
<div style="border:1px solid #ccc;padding:10px;margin-bottom:8px;border-radius:8px">
{{ $p->codigo }} — {{ $p->materia->nombre ?? $p->materia_id }} | ID #{{ $p->id }} | Cupo {{ $p->inscripciones->count() }}/{{ $p->cupo }}
<a href="{{ route('paralelos.show',$p->id) }}" class="button button-small">Ver</a>
</div>
@empty
<p>Sin paralelos asignados</p>
@endforelse
</section>
@endsection