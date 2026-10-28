@extends('layouts.app')
@section('title', 'Paralelo '.$paralelo->codigo)
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Paralelo</p><h1 class="page-title">{{ $paralelo->codigo }} <span class="mono" style="color:#667085;font-size:18px">#{{ $paralelo->id }}</span></h1><p class="page-subtitle">Detalle completo del paralelo</p></div><div class="actions"><a href="{{ route('paralelos.index') }}" class="button button-secondary">Volver</a><a href="{{ route('paralelos.edit',$paralelo->id) }}" class="button button-primary">Editar</a></div></div>
<section class="panel">
<div class="detail-grid">
<div class="detail-item"><span class="detail-label">ID interno (BD)</span><span class="detail-value">#{{ $paralelo->id }}</span></div>
<div class="detail-item"><span class="detail-label">Código del paralelo</span><span class="detail-value">{{ $paralelo->codigo }}</span></div>
<div class="detail-item"><span class="detail-label">Materia</span><span class="detail-value">{{ $paralelo->materia->nombre ?? $paralelo->materia_id }}</span></div>
<div class="detail-item"><span class="detail-label">Docente</span><span class="detail-value">{{ $paralelo->docente->nombre ?? $paralelo->docente_id }}</span></div>
<div class="detail-item"><span class="detail-label">Cupo</span><span class="detail-value">{{ $paralelo->inscripciones->count() }} / {{ $paralelo->cupo }}</span></div>
<div class="detail-item"><span class="detail-label">Horario</span><span class="detail-value">{{ $paralelo->horario }}</span></div>
</div>
<h3 class="section-spacer">Inscritos</h3>
@if($paralelo->inscripciones->count())
<div class="list-stack">
@foreach($paralelo->inscripciones as $ins)
<div class="list-row"><div><div class="list-main">#{{ $ins->estudiante->id ?? '' }} - {{ $ins->estudiante->nombres ?? $ins->estudiante_id }}</div><div class="list-sub">{{ $ins->estudiante->codigo ?? '' }}</div></div><span class="badge">{{ $ins->estado }}</span></div>
@endforeach
</div>
@else
<div class="empty-state"><p>Sin inscritos</p></div>
@endif
</section>
@endsection
