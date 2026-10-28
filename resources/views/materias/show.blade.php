@extends('layouts.app')
@section('title', $materia->nombre)
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Materias</p>
            <h1 class="page-title">{{ $materia->nombre }}</h1>
            <p class="page-subtitle">Código {{ $materia->codigo }} · Carrera: {{ $materia->carrera->nombre ?? '-' }}</p>
        </div>
        <div class="page-actions">
            @if(auth()->user()->is_admin)
                <a class="button button-primary" href="{{ route('materias.edit', $materia) }}">Editar materia</a>
            @endif
            <a class="button button-secondary" href="{{ route('materias.index') }}">Volver al listado</a>
        </div>
    </header>

    <div class="split-layout">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Información general</h2>
                    <p class="panel-subtitle">Datos académicos de la materia</p>
                </div>
                @if($materia->activa)
                    <span class="badge badge-success">Activa</span>
                @else
                    <span class="badge">Inactiva</span>
                @endif
            </div>
            <div class="detail-grid">
                <article class="detail-item">
                    <p class="detail-label">Semestre</p>
                    <p class="detail-value">{{ $materia->semestre }}</p>
                </article>
                <article class="detail-item span-2">
                    <p class="detail-label">Descripción</p>
                    <p class="detail-value">{{ $materia->descripcion ?: 'Sin descripción registrada.' }}</p>
                </article>
            </div>
            @if($materia->imagen)
                <div class="panel-section">
                    <h3 class="panel-subtitle">Imagen</h3>
                    <img src="{{ $materia->imagen_url }}" alt="Imagen de {{ $materia->nombre }}" style="max-width: 320px; border-radius: 12px; border: 1px solid var(--line);">
                </div>
            @endif
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Prerrequisitos</h2>
                    <p class="panel-subtitle">Materias que deben aprobarse antes</p>
                </div>
            </div>
            <ul class="list">
                @forelse($materia->prerrequisitos as $prerrequisito)
                    <li class="list-item">
                        <div>
                            <p class="list-title">{{ $prerrequisito->nombre }}</p>
                            <p class="list-subtitle">Código {{ $prerrequisito->codigo }} · Semestre {{ $prerrequisito->semestre }}</p>
                        </div>
                        <a class="button button-ghost button-small" href="{{ route('materias.show', $prerrequisito) }}">Ver</a>
                    </li>
                @empty
                    <li class="list-item empty">No tiene prerrequisitos.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Paralelos asociados</h2>
                <p class="panel-subtitle">Grupos habilitados para esta materia</p>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Gestión</th>
                        <th>Docente</th>
                        <th>Horario</th>
                        <th>Cupo</th>
                        <th>Estado</th>
                        <th class="align-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materia->paralelos as $paralelo)
                        <tr>
                            <td class="mono">{{ $paralelo->codigo }}</td>
                            <td>{{ $paralelo->gestion->codigo ?? '-' }}</td>
                            <td>{{ $paralelo->docente ? $paralelo->docente->nombres.' '.$paralelo->docente->apellidos : '-' }}</td>
                            <td>{{ $paralelo->horario }}</td>
                            <td>{{ $paralelo->cupo }}</td>
                            <td>
                                @if($paralelo->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge">Inactivo</span>
                                @endif
                            </td>
                            <td class="align-right">
                                <a class="button button-ghost button-small" href="{{ route('paralelos.show', $paralelo) }}">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">No hay paralelos registrados para esta materia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
