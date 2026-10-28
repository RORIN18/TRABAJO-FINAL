@extends('layouts.app')
@section('title', 'Inicio')
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Sistema Universitario</p>
            <h1 class="page-title">Panel de control</h1>
            <p class="page-subtitle">Bienvenido, {{ auth()->user()->name }}. Resumen general de la gestión académica.</p>
        </div>
        @if(isset($gestionVigente))
            <div class="page-actions">
                <span class="badge badge-success">Gestión vigente: {{ $gestionVigente->codigo }}</span>
            </div>
        @else
            <div class="page-actions">
                <span class="badge">Sin gestión vigente</span>
            </div>
        @endif
    </header>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Módulos principales</h2>
                <p class="panel-subtitle">Acceso rápido a cada módulo</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;">
            <a class="panel" href="{{ route('estudiantes.index') }}" style="margin:0;text-decoration:none;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p class="eyebrow">Estudiantes</p>
                        <p style="font-size:32px;font-weight:750;letter-spacing:-.04em;margin:6px 0;">{{ $totalEstudiantes ?? 0 }}</p>
                        <p class="panel-note">Registrados</p>
                    </div>
                    <div class="empty-mark">E</div>
                </div>
            </a>
            <a class="panel" href="{{ route('docentes.index') }}" style="margin:0;text-decoration:none;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p class="eyebrow">Docentes</p>
                        <p style="font-size:32px;font-weight:750;letter-spacing:-.04em;margin:6px 0;">{{ $totalDocentes ?? 0 }}</p>
                        <p class="panel-note">Registrados</p>
                    </div>
                    <div class="empty-mark">D</div>
                </div>
            </a>
            <a class="panel" href="{{ route('carreras.index') }}" style="margin:0;text-decoration:none;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p class="eyebrow">Carreras</p>
                        <p style="font-size:32px;font-weight:750;letter-spacing:-.04em;margin:6px 0;">{{ $totalCarreras ?? 0 }}</p>
                        <p class="panel-note">Habilitadas</p>
                    </div>
                    <div class="empty-mark">C</div>
                </div>
            </a>
            <a class="panel" href="{{ route('materias.index') }}" style="margin:0;text-decoration:none;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p class="eyebrow">Materias</p>
                        <p style="font-size:32px;font-weight:750;letter-spacing:-.04em;margin:6px 0;">{{ $totalMaterias ?? 0 }}</p>
                        <p class="panel-note">Del catálogo</p>
                    </div>
                    <div class="empty-mark">M</div>
                </div>
            </a>
            <a class="panel" href="{{ route('paralelos.index') }}" style="margin:0;text-decoration:none;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p class="eyebrow">Paralelos</p>
                        <p style="font-size:32px;font-weight:750;letter-spacing:-.04em;margin:6px 0;">{{ $totalParalelos ?? 0 }}</p>
                        <p class="panel-note">Grupos</p>
                    </div>
                    <div class="empty-mark">P</div>
                </div>
            </a>
            <a class="panel" href="{{ route('inscripciones.index') }}" style="margin:0;text-decoration:none;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p class="eyebrow">Inscripciones</p>
                        <p style="font-size:32px;font-weight:750;letter-spacing:-.04em;margin:6px 0;">{{ $totalInscripciones ?? 0 }}</p>
                        <p class="panel-note">Registradas</p>
                    </div>
                    <div class="empty-mark">I</div>
                </div>
            </a>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Acceso rápido</h2>
                <p class="panel-subtitle">Tareas frecuentes</p>
            </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:10px;">
            @if(auth()->user()->is_admin)
                <a class="button button-primary" href="{{ route('estudiantes.create') }}">Nuevo estudiante</a>
                <a class="button button-primary" href="{{ route('inscripciones.create') }}">Nueva inscripción</a>
                <a class="button button-primary" href="{{ route('paralelos.create') }}">Crear paralelo</a>
                <a class="button button-primary" href="{{ route('materias.create') }}">Nueva materia</a>
                <a class="button button-primary" href="{{ route('gestiones.create') }}">Nueva gestión</a>
                <a class="button button-secondary" href="{{ route('gestiones.index') }}">Administrar gestiones</a>
            @endif
            <a class="button button-secondary" href="{{ route('paralelos.index') }}">Ver horarios / Paralelos</a>
            <a class="button button-secondary" href="{{ route('materias.index') }}">Catálogo de materias</a>
        </div>
    </section>
@endsection
