@extends('layouts.app')
@section('title', 'Materias')
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Catálogo académico</p>
            <h1 class="page-title">Materias</h1>
            <p class="page-subtitle">Listado de materias por carrera, semestre y prerrequisitos.</p>
        </div>
        @if(auth()->check() && auth()->user()->role === 'admin')
            <a class="button button-primary" href="{{ route('materias.create') }}">+ Nueva materia</a>
        @endif
    </header>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Listado general</h2>
                <p class="panel-subtitle">Materias registradas en el sistema</p>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Carrera</th>
                        <th>Semestre</th>
                        <th>Estado</th>
                        <th class="align-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materias as $materia)
                        <tr>
                            <td class="mono">{{ $materia->codigo }}</td>
                            <td class="strong">{{ $materia->nombre }}</td>
                            <td>{{ $materia->carrera->nombre ?? '-' }}</td>
                            <td>{{ $materia->semestre }}</td>
                            <td>
                                @if($materia->activa)
                                    <span class="badge badge-success">Activa</span>
                                @else
                                    <span class="badge">Inactiva</span>
                                @endif
                            </td>
                            <td class="align-right">
                                <div class="table-actions">
                                    <a class="button button-ghost button-small" href="{{ route('materias.show', $materia) }}">Ver</a>
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                        <a class="button button-ghost button-small" href="{{ route('materias.edit', $materia) }}">Editar</a>
                                        <form action="{{ route('materias.destroy', $materia) }}" method="POST" onsubmit="return confirm('¿Eliminar esta materia?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button button-ghost button-small button-danger">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No hay materias registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection