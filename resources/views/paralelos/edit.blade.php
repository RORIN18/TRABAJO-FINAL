@extends('layouts.app')
@section('title', 'Editar Paralelo')
@section('content')
    <header class="page-header">
        <div class="page-heading">
            <p class="eyebrow">Paralelos</p>
            <h1 class="page-title">Editar paralelo {{ $paralelo->codigo }}</h1>
            <p class="page-subtitle">Actualiza la asignación, con validación de disponibilidad.</p>
        </div>
        <div class="page-actions">
            <a class="button button-secondary" href="{{ route('paralelos.show', $paralelo) }}">Ver detalle</a>
            <a class="button button-secondary" href="{{ route('paralelos.index') }}">Volver al listado</a>
        </div>
    </header>

    <section class="panel">
        <form method="POST" action="{{ route('paralelos.update', $paralelo) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field">
                <label for="materia_id">Materia</label>
                <select id="materia_id" name="materia_id" required>
                    @foreach($materias as $m)
                        <option value="{{ $m->id }}" @selected($m->id == $paralelo->materia_id)>{{ $m->nombre }} ({{ $m->codigo }})</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="gestion_id">Gestión</label>
                <select id="gestion_id" name="gestion_id" required>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id }}" @selected($g->id == $paralelo->gestion_id)>{{ $g->codigo ?? $g->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="docente_id">Docente</label>
                <select id="docente_id" name="docente_id" data-selected="{{ old('docente_id', $paralelo->docente_id) }}" required disabled>
                    <option value="">Selecciona primero una gestión y un turno</option>
                </select>
                <p id="docente_disponibilidad" class="field-hint">Buscando docentes disponibles…</p>
            </div>
            <div class="field">
                <label for="codigo">Paralelo / Código</label>
                <select id="codigo" name="codigo" required>
                    @foreach($paralelos as $p)
                        <option value="{{ $p }}" @selected(old('codigo', $paralelo->codigo) == $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="turno">Turno</label>
                <select id="turno" name="turno" required>
                    @foreach($turnos as $key => $t)
                        <option value="{{ $key }}" @selected(old('turno', $paralelo->turno) == $key)>{{ $t['label'] }} ({{ $t['hora_inicio'] }} - {{ $t['hora_fin'] }})</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="cupo">Cupo</label>
                <input id="cupo" name="cupo" type="number" min="1" value="{{ old('cupo', $paralelo->cupo) }}" required>
            </div>
            <div class="field">
                <label for="activo">Estado</label>
                <select id="activo" name="activo">
                    <option value="1" @selected($paralelo->activo)>Activo</option>
                    <option value="0" @selected(!$paralelo->activo)>Inactivo</option>
                </select>
            </div>
            <div class="form-actions span-2">
                <button class="button button-primary" type="submit">Guardar cambios</button>
                <a class="button button-secondary" href="{{ route('paralelos.show', $paralelo) }}">Cancelar</a>
            </div>
        </form>
    </section>
    <script>
        const gestionSelect = document.getElementById('gestion_id');
        const turnoSelect = document.getElementById('turno');
        const docenteSelect = document.getElementById('docente_id');
        const disponibilidadHint = document.getElementById('docente_disponibilidad');
        const docentesDisponiblesUrl = @json(route('paralelos.docentes-disponibles'));
        const paraleloId = @json($paralelo->id);

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
                paralelo_id: paraleloId,
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
        actualizarDocentes(true);
    </script>
@endsection
