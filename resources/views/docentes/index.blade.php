@extends('layouts.app')
@section('title','Docentes')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Planta docente</p><h1 class="page-title">Docentes</h1></div><a href="{{ route('docentes.create') }}" class="button button-primary">+ Nuevo docente</a></div>
<section class="panel">
<div class="table-wrap"><table class="data-table"><thead><tr><th>ID</th><th>Docente</th><th>Código</th><th>Especialidad</th><th>Paralelos</th><th></th></tr></thead><tbody>
@foreach($docentes as $d)
<tr><td class="mono">#{{ $d->id }}</td><td>{{ $d->nombres }} {{ $d->apellidos }}</td><td class="mono">{{ $d->codigo }}</td><td>{{ $d->especialidad ?? '-' }}</td><td><span class="badge">{{ $d->paralelos_count }} paralelos</span></td><td class="align-right"><div class="table-actions"><a href="{{ route('docentes.show',$d->id) }}" class="button button-ghost button-small">Ver</a>@if(auth()->user()->is_admin)<a href="{{ route('docentes.edit',$d->id) }}" class="button button-ghost button-small">Editar</a><form action="{{ route('docentes.destroy',$d->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este docente?');">@csrf @method('DELETE')<button class="button button-ghost button-small button-danger">Eliminar</button></form>@endif</div></td></tr>
@endforeach
</tbody></table></div>
<div class="pagination-wrap">{{ $docentes->links() }}</div>
</section>
@endsection
