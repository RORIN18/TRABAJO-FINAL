@extends('layouts.app')
@section('title', 'Gestiones')
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Administración</p>
            <h1 class="page-title">Gestiones académicas</h1>
            <p class="page-subtitle">Controla las gestiones y define cuál está vigente para inscripciones y paralelos.</p>
        </div>
        @if(auth()->user()->is_admin)
            <div class="page-actions">
                <a class="button button-primary" href="{{ route('gestiones.create') }}">Nueva gestión</a>
            </div>
        @endif
    </header>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Listado de gestiones</h2>
                <p class="panel-subtitle">Solo puede haber una gestión vigente a la vez</p>
            </div>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Estado</th>
                        <th>Paralelos</th>
                        <th class="align-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gestiones as $gestion)
                        <tr>
                            <td class="mono">{{ $gestion->codigo }}</td>
                            <td>{{ $gestion->fecha_inicio->format('d/m/Y') }}</td>
                            <td>{{ $gestion->fecha_fin->format('d/m/Y') }}</td>
                            <td>
                                @if($gestion->vigente)
                                    <span class="badge badge-success">Vigente</span>
                                @else
                                    <span class="badge">Cerrada</span>
                                @endif
                            </td>
                            <td>{{ $gestion->paralelos_count }}</td>
                            <td class="align-right">
                                <div class="table-actions">
                                    <a class="button button-ghost button-small" href="{{ route('gestiones.show', $gestion) }}">Ver</a>
                                    @if(auth()->user()->is_admin)
                                        @if(!$gestion->vigente)
                                            <form action="{{ route('gestiones.activar', $gestion) }}" method="POST">
                                                @csrf
                                                <button class="button button-ghost button-small">Activar</button>
                                            </form>
                                        @endif
                                        <a class="button button-ghost button-small" href="{{ route('gestiones.edit', $gestion) }}">Editar</a>
                                        @if(!$gestion->vigente)
                                            <form action="{{ route('gestiones.destroy', $gestion) }}" method="POST" onsubmit="return confirm('¿Eliminar esta gestión?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="button button-ghost button-small button-danger">Eliminar</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No hay gestiones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
