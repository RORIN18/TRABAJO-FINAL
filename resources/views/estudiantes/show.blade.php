@extends('layouts.app')
@section('title', $estudiante->nombres . ' ' . $estudiante->apellidos)
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Ficha del estudiante</p><h1 class="page-title">Detalle completo</h1><p class="page-subtitle">Personal + Paralelos + Cursos</p></div><div class="actions"><a href="/estudiantes" class="button button-secondary">Volver</a><a href="/estudiantes/{{ $estudiante->id }}/edit" class="button button-primary">Editar ficha</a></div></div>

<section class="panel">
<div class="profile-head"><img class="avatar-large" src="{{ $estudiante->imagen_url }}" alt=""><div><h2 class="profile-name">{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</h2><p class="profile-meta">ID #{{ $estudiante->id }} · {{ $estudiante->codigo }} · CI {{ $estudiante->ci }}</p><span class="badge badge-success">{{ ucfirst($estudiante->estado) }}</span></div></div>
<div class="detail-grid">
<div class="detail-item"><span class="detail-label">ID interno (BD)</span><span class="detail-value">#{{ $estudiante->id }}</span></div>
<div class="detail-item"><span class="detail-label">Código universitario</span><span class="detail-value">{{ $estudiante->codigo }}</span></div>
<div class="detail-item"><span class="detail-label">Documento</span><span class="detail-value">{{ $estudiante->ci }}</span></div>
<div class="detail-item"><span class="detail-label">Correo</span><span class="detail-value">{{ $estudiante->correo }}</span></div>
<div class="detail-item"><span class="detail-label">Carrera</span><span class="detail-value">{{ $estudiante->carrera->nombre ?? 'Sin carrera asignada' }}</span></div>
</div>
</section>

<section class="panel" style="margin-top:20px">
<h2 style="margin:0 0 4px 0">Información académica</h2>
<p class="page-subtitle" style="margin-bottom:16px">Carrera asignada, paralelos, materias, notas y asistencias.</p>

@forelse($estudiante->inscripciones as $ins)
<div style="border:1px solid #EAECF0;border-radius:12px;padding:16px;margin-bottom:12px">
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
<div>
<h3 style="margin:0;font-size:16px">{{ $ins->paralelo->materia->nombre ?? 'Materia #'.$ins->paralelo->materia_id }} <span style="font-weight:400;color:#667085">— {{ $ins->paralelo->codigo }}</span></h3>
<p style="margin:4px 0 0 0;font-size:13px;color:#667085">
Paralelo ID #{{ $ins->paralelo->id }} · Docente: {{ $ins->paralelo->docente->nombre ?? $ins->paralelo->docente_id }} · Gestión: {{ $ins->paralelo->gestion->nombre ?? $ins->paralelo->gestion_id }} · Horario: {{ $ins->paralelo->horario }}
</p>
</div>
<span class="badge">{{ $ins->estado }}</span>
</div>

@if($ins->notas->count())
<div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
@foreach($ins->notas as $nota)
<span style="background:#F2F4F7;padding:6px 10px;border-radius:20px;font-size:12px"><b>{{ $nota->tipo }}:</b> {{ $nota->calificacion }}</span>
@endforeach
<span style="background:#101828;color:white;padding:6px 10px;border-radius:20px;font-size:12px">Final: {{ $ins->calcularNotaFinal() ?? '—' }}</span>
</div>
@endif

<div style="margin-top:10px;font-size:12px;color:#667085">Inscrito el {{ $ins->fecha_inscripcion?->format('d/m/Y') }} · Asistencias: {{ $ins->asistencias->where('presente',1)->count() }}/{{ $ins->asistencias->count() }}</div>
</div>
@empty
<div class="empty-state">
<span class="empty-mark">!</span>
<h3>Sin paralelos ni cursos</h3>
<p>Este estudiante no tiene inscripciones.</p>
<a href="{{ route('inscripciones.create') }}?estudiante_id={{ $estudiante->id }}" class="button button-primary" style="margin-top:10px">Inscribir en paralelo</a>
</div>
@endforelse

@if($estudiante->inscripciones->count())
<div style="margin-top:16px;padding:12px;background:#F9FAFB;border-radius:10px;font-size:13px">
<b>Resumen:</b> {{ $estudiante->inscripciones->count() }} paralelos / cursos activos.
Carrera/Materias: {{ $estudiante->inscripciones->pluck('paralelo.materia.nombre')->unique()->join(', ') }}
</div>
@endif
</section>

@endsection
