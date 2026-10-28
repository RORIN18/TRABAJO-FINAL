@extends('layouts.app')
@section('title', 'Nueva inscripción')
@section('content')
<div class="page-header"><div class="page-heading"><p class="eyebrow">Seguimiento académico</p><h1 class="page-title">Nueva inscripción</h1><p class="page-subtitle">Vincula a un estudiante con el paralelo correspondiente.</p></div><a href="/inscripciones" class="button button-secondary">Volver a inscripciones</a></div>
<section class="panel"><form method="POST" action="{{ route('inscripciones.store') }}">@csrf
    <div class="form-grid">
        <div class="field">
            <label for="estudiante_id">Estudiante</label>
            <select id="estudiante_id" name="estudiante_id" class="control" data-selected="{{ old('estudiante_id') }}" required disabled>
                <option value="">Selecciona primero un paralelo</option>
            </select>
            <span id="estudiantes_disponibilidad" class="field-hint">Los estudiantes disponibles aparecerán al seleccionar un paralelo.</span>
            @error('estudiante_id')<span class="error-text">{{ $message }}</span>@enderror
        </div>
        <div class="field">
            <label for="paralelo_id">Paralelo disponible</label>
            <select id="paralelo_id" name="paralelo_id" class="control" required {{ $paralelos->isEmpty() ? 'disabled' : '' }}>
                <option value="">Selecciona un paralelo</option>
                @foreach($paralelos as $paralelo)
                    <option value="{{ $paralelo->id }}" @selected(old('paralelo_id') == $paralelo->id)>
                        {{ $paralelo->materia->nombre }} · Paralelo {{ $paralelo->codigo }} · {{ $paralelo->docente->nombres }} {{ $paralelo->docente->apellidos }} · {{ $paralelo->horario }} · {{ $paralelo->cupo - $paralelo->inscritos_count }} cupos libres
                    </option>
                @endforeach
            </select>
            @if($paralelos->isEmpty())<span class="field-hint">No hay paralelos disponibles en la gestión vigente.</span>@endif
            @error('paralelo_id')<span class="error-text">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="actions"><button id="registrar_inscripcion" class="button button-primary" type="submit" disabled>Registrar inscripción</button><a href="{{ route('inscripciones.index') }}" class="button button-secondary">Cancelar</a></div>
</form></section>
<script>
    const paraleloSelect = document.getElementById('paralelo_id');
    const estudianteSelect = document.getElementById('estudiante_id');
    const disponibilidadEstudiantes = document.getElementById('estudiantes_disponibilidad');
    const registrarInscripcion = document.getElementById('registrar_inscripcion');
    const estudiantesDisponiblesUrl = @json(route('inscripciones.estudiantes-disponibles'));

    async function actualizarEstudiantes(preservarSeleccion = false) {
        const seleccionAnterior = preservarSeleccion ? estudianteSelect.dataset.selected : '';
        estudianteSelect.disabled = true;
        registrarInscripcion.disabled = true;
        disponibilidadEstudiantes.textContent = 'Buscando estudiantes disponibles…';
        estudianteSelect.replaceChildren(new Option('Cargando estudiantes…', ''));

        if (!paraleloSelect.value) {
            estudianteSelect.replaceChildren(new Option('Selecciona primero un paralelo', ''));
            disponibilidadEstudiantes.textContent = 'Los estudiantes disponibles aparecerán al seleccionar un paralelo.';
            return;
        }

        try {
            const parametros = new URLSearchParams({ paralelo_id: paraleloSelect.value });
            const respuesta = await fetch(`${estudiantesDisponiblesUrl}?${parametros}`);
            if (!respuesta.ok) throw new Error('No se pudo consultar la disponibilidad.');

            const estudiantes = await respuesta.json();
            estudianteSelect.replaceChildren(new Option('Selecciona un estudiante', ''));
            estudiantes.forEach((estudiante) => {
                const opcion = new Option(
                    `${estudiante.apellidos}, ${estudiante.nombres} (${estudiante.codigo})`,
                    estudiante.id,
                );
                opcion.selected = String(estudiante.id) === String(seleccionAnterior);
                estudianteSelect.add(opcion);
            });

            estudianteSelect.disabled = estudiantes.length === 0;
            registrarInscripcion.disabled = estudiantes.length === 0;
            disponibilidadEstudiantes.textContent = estudiantes.length
                ? `${estudiantes.length} estudiante(s) disponible(s) para este paralelo.`
                : 'No hay estudiantes disponibles para este paralelo.';
            estudianteSelect.dataset.selected = '';
        } catch (error) {
            estudianteSelect.replaceChildren(new Option('No se pudo cargar la disponibilidad', ''));
            disponibilidadEstudiantes.textContent = 'No se pudo consultar la disponibilidad. Intenta nuevamente.';
        }
    }

    paraleloSelect.addEventListener('change', () => actualizarEstudiantes());
    actualizarEstudiantes(true);
</script>
@endsection
