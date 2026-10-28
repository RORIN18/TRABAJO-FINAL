@extends('layouts.app')
@section('title', 'Estudiantes')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Comunidad universitaria</p><h1 class="page-title">Estudiantes</h1><p class="page-subtitle">Busca y consulta la información de los estudiantes registrados.</p></div>@if(auth()->user()->role === 'admin')<a href="/estudiantes/create" class="button button-primary">＋ Nuevo estudiante</a>@endif</div>
<section class="panel">
    <div class="toolbar"><form method="GET" action="/estudiantes"><input name="q" value="{{ request('q') }}" class="control" placeholder="Buscar por nombre, apellido o código"><button class="button button-secondary" type="submit">Buscar</button></form></div>
    @if($estudiantes->count())<div class="table-wrap"><table class="data-table"><thead><tr><th>ID</th><th>Estudiante</th><th>Código</th><th>Carrera</th><th>Correo</th><th></th></tr></thead><tbody>@foreach($estudiantes as $e)<tr><td class="mono">#{{ $e->id }}</td><td><div class="table-person"><img class="avatar" src="{{ $e->imagen_url }}" alt=""><span>{{ $e->nombres }} {{ $e->apellidos }}</span></div></td><td class="mono">{{ $e->codigo }}</td><td>{{ $e->carrera->nombre ?? 'Sin carrera asignada' }}</td><td>{{ $e->correo }}</td><td><div class="actions"><a href="/estudiantes/{{ $e->id }}" class="button button-secondary button-small">Ver ficha</a>@if(auth()->user()->role === 'admin')<a href="/estudiantes/{{ $e->id }}/edit" class="button button-quiet button-small">Editar</a>@endif</div></td></tr>@endforeach</tbody></table></div>@else<div class="empty-state"><span class="empty-mark">E</span><h3>No se encontraron estudiantes</h3><p>Prueba con otro nombre o código.</p></div>@endif
    <div class="pagination-wrap">{{ $estudiantes->links() }}</div>
</section>
@endsection
