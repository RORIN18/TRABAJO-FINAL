@extends('layouts.app')
@section('title', 'Gestión '.$gestion->codigo)
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Gestiones</p>
            <h1 class="page-title">Gestión {{ $gestion->codigo }}</h1>
            <p class="page-subtitle">{{ $gestion->fecha_inicio->format('d/m/Y') }} - {{ $gestion->fecha_fin->format('d/m/Y') }}</p>
        </div>
        <div class="page-actions">
            @if(auth()->user()->is_admin)
                <a class="button button-primary" href="{{ route('gestiones.edit', $gestion) }}">Editar gestión</a>
            @endif
            <a class="button button-secondary" href="{{ route('gestiones.index') }}">Volver al listado</a>
        </div>
    </header>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Paralelos en esta gestión</h2>
                <p class="panel-subtitle">Listado de grupos asignados</p>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paralelo</th>
                        <th>Materia</th>
                        <th>Docente</th>
                        <th>Turno</th>
                        <th>Horario</th>
                        <th>Cupo</th>
                        <th class="align-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gestion->paralelos as $paralelo)
                        <tr>
                            <td class="mono">{{ $paralelo->codigo }}</td>
                            <td>{{ $paralelo->materia->nombre ?? '-' }}</td>
                            <td>{{ $paralelo->docente ? $paralelo->docente->nombres.' '.$paralelo->docente->apellidos : '-' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $paralelo->turno)) }}</td>
                            <td>{{ $paralelo->horario }}</td>
                            <td>{{ $paralelo->cupo }}</td>
                            <td class="align-right">
                                <a class="button button-ghost button-small" href="{{ route('paralelos.show', $paralelo) }}">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">No hay paralelos registrados en esta gestión.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
