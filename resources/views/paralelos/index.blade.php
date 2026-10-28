@extends('layouts.app')
@section('title', 'Paralelos')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Gestión académica</p><h1 class="page-title">Paralelos</h1><p class="page-subtitle">ID interno y código del paralelo.</p></div><a href="{{ route('paralelos.create') }}" class="button button-primary">+ Nuevo Paralelo</a></div>
<section class="panel">
@if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
<div class="table-wrap"><table class="data-table"><thead><tr><th>ID</th><th>Código</th><th>Materia</th><th>Gestión</th><th>Docente</th><th>Cupo</th><th></th></tr></thead><tbody>
@foreach($paralelos as $p)
<tr>
<td class="mono">#{{ $p->id }}</td>
<td class="mono">{{ $p->codigo }}</td>
<td>{{ $p->materia->nombre ?? $p->materia_id }}</td>
<td>{{ $p->gestion->nombre ?? $p->gestion_id }}</td>
<td>{{ $p->docente->nombre ?? $p->docente_id }}</td>
<td>{{ $p->inscripciones_count ?? $p->inscripciones->count() }} / {{ $p->cupo }}</td>
<td><div class="actions"><a href="{{ route('paralelos.show',$p->id) }}" class="button button-secondary button-small">Ver</a><a href="{{ route('paralelos.edit',$p->id) }}" class="button button-quiet button-small">Editar</a></div></td>
</tr>
@endforeach
</tbody></table></div>
</section>
@endsection