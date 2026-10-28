@extends('layouts.app')
@section('title', 'Crear Paralelo')
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Paralelos</p>
            <h1 class="page-title">Crear paralelo</h1>
            <p class="page-subtitle">Asigna docente, paralelo y turno, con validación de disponibilidad.</p>
        </div>
        <div class="page-actions">
            <a class="button button-secondary" href="{{ route('paralelos.index') }}">Volver al listado</a>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('paralelos.store') }}" class="form-grid">
            @csrf
            <div class="field">
                <label for="materia_id">Materia</label>
                <select id="materia_id" name="materia_id" required>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}" {{ old('materia_id') == $m->id ? 'selected' : '' }}>{{ $m->nombre }} ({{ $m->codigo }})</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="gestion_id">Gestión (vigente)</label>
                <select id="gestion_id" name="gestion_id" required>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id }}" {{ old('gestion_id') == $g->id ? 'selected' : '' }}>{{ $g->codigo ?? $g->id }} {{ $g->vigente ? '(vigente)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="docente_id">Docente</label>
                <select id="docente_id" name="docente_id" data-selected="{{ old('docente_id') }}" required disabled>
                    <option value="">Selecciona primero una gestión y un turno</option>
                </select>
                <p id="docente_disponibilidad" class="field-hint">Buscando docentes disponibles…</p>
            </div>
            <div class="field">
                <label for="codigo">Paralelo / Código</label>
                <select id="codigo" name="codigo" data-selected="{{ old('codigo') }}" required disabled>
                    <option value="">Selecciona materia, gestión y turno</option>
                </select>
                <p id="paralelo_disponibilidad" class="field-hint">Selecciona materia, gestión y turno para ver los paralelos libres.</p>
            </div>
            <div class="field">
                <label for="turno">Turno</label>
                <select id="turno" name="turno" required>
                    @foreach($turnos as $key => $t)
                        <option value="{{ $key }}" {{ old('turno') == $key ? 'selected' : '' }}>{{ $t['label'] }} ({{ $t['hora_inicio'] }} - {{ $t['hora_fin'] }})</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="cupo">Cupo</label>
                <input id="cupo" name="cupo" type="number" min="1" max="100" value="{{ old('cupo', 30) }}" required>
            </div>
            <div class="form-actions span-2">
                <button id="guardar_paralelo" class="button button-primary" type="submit" disabled>Guardar paralelo</button>
                <a class="button button-secondary" href="{{ route('paralelos.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
    <script>
        const gestionSelect = document.getElementById('gestion_id');
        const turnoSelect = document.getElementById('turno');
        const docenteSelect = document.getElementById('docente_id');
        const disponibilidadHint = document.getElementById('docente_disponibilidad');
        const docentesDisponiblesUrl = @json(route('paralelos.docentes-disponibles'));
        const materiaSelect = document.getElementById('materia_id');
        const codigoSelect = document.getElementById('codigo');
        const paraleloHint = document.getElementById('paralelo_disponibilidad');
        const guardarParalelo = document.getElementById('guardar_paralelo');
        const codigosDisponiblesUrl = @json(route('paralelos.codigos-disponibles'));

        async function actualizarParalelos(preservarSeleccion = false) {
            const seleccionAnterior = preservarSeleccion ? codigoSelect.dataset.selected : '';
            codigoSelect.disabled = true;
            guardarParalelo.disabled = true;
            paraleloHint.textContent = 'Buscando paralelos disponibles…';
            codigoSelect.replaceChildren(new Option('Cargando paralelos…', ''));

            if (!materiaSelect.value || !gestionSelect.value || !turnoSelect.value) {
                paraleloHint.textContent = 'Selecciona materia, gestión y turno.';
                codigoSelect.replaceChildren(new Option('Completa los datos del horario', ''));
                return;
            }

            const parametros = new URLSearchParams({
                materia_id: materiaSelect.value,
                gestion_id: gestionSelect.value,
                turno: turnoSelect.value,
            });

            try {
                const respuesta = await fetch(`${codigosDisponiblesUrl}?${parametros}`);
                if (!respuesta.ok) throw new Error('No se pudo consultar la disponibilidad.');
                const paralelosLibres = await respuesta.json();
                codigoSelect.replaceChildren(new Option('Selecciona un paralelo', ''));
                paralelosLibres.forEach((paralelo) => {
                    const opcion = new Option(`Paralelo ${paralelo}`, paralelo);
                    opcion.selected = paralelo === seleccionAnterior;
                    codigoSelect.add(opcion);
                });
                codigoSelect.disabled = paralelosLibres.length === 0;
                guardarParalelo.disabled = paralelosLibres.length === 0;
                paraleloHint.textContent = paralelosLibres.length
                    ? `${paralelosLibres.length} paralelo(s) disponible(s) para esta materia y horario.`
                    : 'No quedan paralelos disponibles para esta materia en esta gestión.';
                codigoSelect.dataset.selected = '';
            } catch (error) {
                codigoSelect.replaceChildren(new Option('No se pudo cargar la disponibilidad', ''));
                paraleloHint.textContent = 'No se pudo consultar la disponibilidad. Intenta nuevamente.';
            }
        }

        async function actualizarDocentes(preservarSeleccion = false) {
            const seleccionAnterior = preservarSeleccion ? docenteSelect.dataset.selected : '';
            docenteSelect.disabled = true;
            disponibilidadHint.textContent = 'Buscando docentes disponibles…';
            docenteSelect.replaceChildren(new Option('Cargando docentes…', ''));

            if (!gestionSelect.value || !turnoSelect.value) {
                disponibilidadHint.textContent = 'Selecciona una gestión y un turno.';
                docenteSelect.replaceChildren(new Option('Selecciona una gestión y un turno', ''));
                return;
            }

            const parametros = new URLSearchParams({
                gestion_id: gestionSelect.value,
                turno: turnoSelect.value,
            });

            try {
                const respuesta = await fetch(`${docentesDisponiblesUrl}?${parametros}`);
                if (!respuesta.ok) throw new Error('No se pudo consultar la disponibilidad.');

                const docentes = await respuesta.json();
                docenteSelect.replaceChildren(new Option('Selecciona un docente', ''));

                docentes.forEach((docente) => {
                    const opcion = new Option(`${docente.apellidos}, ${docente.nombres}`, docente.id);
                    opcion.selected = String(docente.id) === String(seleccionAnterior);
                    docenteSelect.add(opcion);
                });

                docenteSelect.disabled = docentes.length === 0;
                disponibilidadHint.textContent = docentes.length
                    ? `${docentes.length} docente(s) disponible(s) para este horario.`
                    : 'No hay docentes disponibles para este horario.';
                docenteSelect.dataset.selected = '';
            } catch (error) {
                docenteSelect.replaceChildren(new Option('No se pudo cargar la disponibilidad', ''));
                disponibilidadHint.textContent = 'No se pudo consultar la disponibilidad. Intenta nuevamente.';
            }
        }

        gestionSelect.addEventListener('change', () => actualizarDocentes());
        turnoSelect.addEventListener('change', () => actualizarDocentes());
        materiaSelect.addEventListener('change', () => actualizarParalelos());
        gestionSelect.addEventListener('change', () => actualizarParalelos());
        turnoSelect.addEventListener('change', () => actualizarParalelos());
        actualizarDocentes(true);
        actualizarParalelos(true);
    </script>
@endsection
