@extends('layouts.app')
@section('title', 'Nueva inscripción')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Seguimiento académico</p><h1 class="page-title">Nueva inscripción</h1><p class="page-subtitle">Busca por estudiante o por materia y revisa las opciones disponibles.</p></div><a href="/inscripciones" class="button button-secondary">Volver a inscripciones</a></div>
<section class="panel"><form method="POST" action="{{ route('inscripciones.store') }}">@csrf
    <div class="form-grid">
        <div class="field">
            <label for="buscar_estudiante">Buscar estudiante</label>
            <input id="buscar_estudiante" class="control" type="search" placeholder="Nombre, apellido o código" autocomplete="off">
            <label for="estudiante_id" style="margin-top:10px">Estudiante</label>
            <select id="estudiante_id" name="estudiante_id" class="control" required>
                <option value="">Selecciona un estudiante</option>
                @foreach($estudiantes as $estudiante)
                    <option value="{{ $estudiante->id }}" @selected(old('estudiante_id', $estudianteSeleccionado?->id) == $estudiante->id)>{{ $estudiante->apellidos }}, {{ $estudiante->nombres }} ({{ $estudiante->codigo }})</option>
                @endforeach
            </select>
            <span id="estudiantes_disponibilidad" class="field-hint">Busca un estudiante para ver sus materias o elige una materia para ver estudiantes disponibles.</span>
            @error('estudiante_id')<span class="error-text">{{ $message }}</span>@enderror
        </div>
        <div class="field">
            <label for="buscar_materia">Buscar materia o paralelo</label>
            <input id="buscar_materia" class="control" type="search" placeholder="Nombre de materia o código de paralelo" autocomplete="off">
            <label for="paralelo_id" style="margin-top:10px">Materia y paralelo</label>
            <select id="paralelo_id" name="paralelo_id" class="control" required disabled>
                <option value="">Selecciona primero un estudiante</option>
            </select>
            <span id="paralelos_disponibilidad" class="field-hint">Busca una materia para ver sus paralelos y estudiantes disponibles.</span>
            @error('paralelo_id')<span class="error-text">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="actions"><button id="registrar_inscripcion" class="button button-primary" type="submit" disabled>Registrar inscripción</button><a href="{{ route('inscripciones.index') }}" class="button button-secondary">Cancelar</a></div>
</form></section>
<script>
    const buscarEstudiante = document.getElementById('buscar_estudiante');
    const buscarMateria = document.getElementById('buscar_materia');
    const estudianteSelect = document.getElementById('estudiante_id');
    const paraleloSelect = document.getElementById('paralelo_id');
    const disponibilidadEstudiantes = document.getElementById('estudiantes_disponibilidad');
    const disponibilidadParalelos = document.getElementById('paralelos_disponibilidad');
    const registrarInscripcion = document.getElementById('registrar_inscripcion');
    const paralelosDisponiblesUrl = @json(route('inscripciones.paralelos-disponibles'));
    const estudiantesDisponiblesUrl = @json(route('inscripciones.estudiantes-disponibles'));
    let paraleloAnterior = @json(old('paralelo_id'));
    let todosLosEstudiantes = Array.from(estudianteSelect.options).slice(1).map((opcion) => ({ id: opcion.value, texto: opcion.text }));
    let estudiantesActuales = todosLosEstudiantes;
    let paralelosActuales = [];

    function normalizar(texto) {
        return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    }

    function mostrarEstudiantes(seleccion = estudianteSelect.value) {
        const termino = normalizar(buscarEstudiante.value);
        const opciones = estudiantesActuales.filter((estudiante) => normalizar(estudiante.texto).includes(termino));
        estudianteSelect.replaceChildren(new Option('Selecciona un estudiante', ''));
        opciones.forEach((estudiante) => estudianteSelect.add(new Option(estudiante.texto, estudiante.id)));
        if (opciones.some((estudiante) => String(estudiante.id) === String(seleccion))) estudianteSelect.value = seleccion;
        if (!opciones.length) estudianteSelect.add(new Option('No se encontraron estudiantes disponibles', ''));
    }

    function mostrarParalelos(seleccion = paraleloSelect.value) {
        const termino = normalizar(buscarMateria.value);
        const opciones = paralelosActuales.filter((paralelo) => normalizar(paralelo.texto).includes(termino));
        paraleloSelect.replaceChildren(new Option('Selecciona una materia y paralelo', ''));
        opciones.forEach((paralelo) => paraleloSelect.add(new Option(paralelo.texto, paralelo.id)));
        if (opciones.some((paralelo) => String(paralelo.id) === String(seleccion))) paraleloSelect.value = seleccion;
        if (!opciones.length) paraleloSelect.add(new Option('No se encontraron materias disponibles', ''));
        paraleloSelect.disabled = opciones.length === 0;
        registrarInscripcion.disabled = !paraleloSelect.value || !estudianteSelect.value;
    }

    async function actualizarParalelos(seleccion = estudianteSelect.value) {
        const paraleloSeleccionado = paraleloSelect.value;
        paraleloSelect.disabled = true;
        registrarInscripcion.disabled = true;
        disponibilidadParalelos.textContent = seleccion ? 'Buscando materias disponibles para el estudiante…' : 'Buscando materias y paralelos disponibles…';
        paraleloSelect.replaceChildren(new Option('Cargando opciones…', ''));

        try {
            const parametros = new URLSearchParams();
            if (seleccion) parametros.set('estudiante_id', seleccion);
            const respuesta = await fetch(`${paralelosDisponiblesUrl}?${parametros}`, { headers: { Accept: 'application/json' } });
            if (!respuesta.ok) throw new Error('No se pudo consultar la disponibilidad.');

            const paralelos = await respuesta.json();
            paralelosActuales = paralelos.map((paralelo) => ({
                id: paralelo.id,
                texto: `${paralelo.materia} · Paralelo ${paralelo.codigo}${paralelo.docente ? ` · Docente: ${paralelo.docente}` : ''}${paralelo.horario ? ` · ${paralelo.horario}` : ''} · ${paralelo.cupos_libres} cupos libres`,
            }));
            const seleccionParalelo = paralelosActuales.some((paralelo) => String(paralelo.id) === String(paraleloAnterior))
                ? paraleloAnterior
                : paraleloSeleccionado;
            mostrarParalelos(seleccionParalelo);
            disponibilidadParalelos.textContent = paralelos.length
                ? (seleccion ? `${paralelos.length} paralelo(s) disponibles para este estudiante.` : `${paralelos.length} paralelo(s) disponibles. Busca una materia o selecciona un paralelo.`)
                : 'No hay materias disponibles con los cupos, prerrequisitos y horarios actuales.';
            paraleloAnterior = null;

            if (paraleloSelect.value) {
                await actualizarEstudiantesPorParalelo(false);
            } else if (!seleccion) {
                estudiantesActuales = todosLosEstudiantes;
                mostrarEstudiantes();
                disponibilidadEstudiantes.textContent = 'Estudiantes activos con carrera asignada.';
            } else {
                disponibilidadEstudiantes.textContent = 'Elige una materia para ver quiénes pueden inscribirse en ese paralelo.';
            }
        } catch (error) {
            paraleloSelect.replaceChildren(new Option('No se pudo cargar la disponibilidad', ''));
            disponibilidadParalelos.textContent = 'No se pudo consultar la disponibilidad. Intenta nuevamente.';
        }
    }

    async function actualizarEstudiantesPorParalelo(limpiarSiNoDisponible = true) {
        if (!paraleloSelect.value) {
            if (!estudianteSelect.value) {
                estudiantesActuales = todosLosEstudiantes;
                mostrarEstudiantes();
                disponibilidadEstudiantes.textContent = 'Estudiantes activos con carrera asignada.';
            }
            registrarInscripcion.disabled = true;
            return;
        }

        disponibilidadEstudiantes.textContent = 'Buscando estudiantes que pueden inscribirse…';
        try {
            const parametros = new URLSearchParams({ paralelo_id: paraleloSelect.value });
            const respuesta = await fetch(`${estudiantesDisponiblesUrl}?${parametros}`, { headers: { Accept: 'application/json' } });
            if (!respuesta.ok) throw new Error('No se pudo consultar la disponibilidad.');
            const disponibles = await respuesta.json();
            estudiantesActuales = disponibles.map((estudiante) => ({
                id: estudiante.id,
                texto: `${estudiante.apellidos}, ${estudiante.nombres} (${estudiante.codigo})`,
            }));
            if (limpiarSiNoDisponible && estudianteSelect.value
                && !disponibles.some((estudiante) => String(estudiante.id) === String(estudianteSelect.value))) {
                estudianteSelect.value = '';
            }
            mostrarEstudiantes();
            disponibilidadEstudiantes.textContent = disponibles.length
                ? `${disponibles.length} estudiante(s) pueden inscribirse en este paralelo.`
                : 'No hay estudiantes disponibles para este paralelo.';
            registrarInscripcion.disabled = !estudianteSelect.value || !paraleloSelect.value;
        } catch (error) {
            disponibilidadEstudiantes.textContent = 'No se pudo consultar la disponibilidad de estudiantes.';
            registrarInscripcion.disabled = true;
        }
    }

    buscarEstudiante.addEventListener('input', () => {
        const seleccionado = estudianteSelect.value;
        mostrarEstudiantes(seleccionado);
        if (seleccionado && !Array.from(estudianteSelect.options).some((opcion) => opcion.value === seleccionado)) {
            actualizarParalelos('');
        }
    });
    buscarMateria.addEventListener('input', () => {
        const seleccionado = paraleloSelect.value;
        mostrarParalelos(seleccionado);
        if (seleccionado && !Array.from(paraleloSelect.options).some((opcion) => opcion.value === seleccionado)) {
            actualizarEstudiantesPorParalelo();
        }
    });
    estudianteSelect.addEventListener('change', () => actualizarParalelos());
    paraleloSelect.addEventListener('change', () => actualizarEstudiantesPorParalelo());
    const estudianteInicial = estudianteSelect.value;
    mostrarEstudiantes(estudianteInicial);
    actualizarParalelos(estudianteInicial);
</script>
@endsection
