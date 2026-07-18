@extends('layouts.template')
@section('title', 'Reagendar Cita')

@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<div class="container-fluid px-4 py-4">
    
    <div class="mb-4">
        <h2 class="fw-bold text-primary border-bottom pb-2">Reagendar Cita</h2>
    </div>

    <form action="{{ route('Citas.update', $cita->id) }}" method="POST" class="card shadow-sm border-0">
        @csrf
        @method('PUT')
        <input type="hidden" name="especialidad_id" id="input_especialidad_id" value="{{ $cita->calendario->medico->especialidad_id }}">
        
        <div class="card-body p-4">
            
            <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <span>Esta cita se ha reagendado <strong>{{ $cita->reagendada_contador }}</strong> de <strong>2</strong> veces permitidas.</span>
            </div>

            <h4 class="text-secondary mb-3"><i class="bi bi-person-fill me-2"></i>Datos del Paciente</h4>
            
            <div class="row g-3 bg-light p-3 rounded mb-4 border">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Cédula</label>
                    <p class="form-control-plaintext fw-semibold">{{ $cita->paciente->cedula }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Nombre</label>
                    <p class="form-control-plaintext fw-semibold">{{ $cita->paciente->nombre }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Apellido</label>
                    <p class="form-control-plaintext fw-semibold">{{ $cita->paciente->apellido }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Teléfono</label>
                    <p class="form-control-plaintext fw-semibold">{{ $cita->paciente->telefono }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Fecha actual de la cita</label>
                    <p class="form-control-plaintext fw-semibold">{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Estado actual</label>
                    <p class="form-control-plaintext fw-semibold">
                        @if($cita->estado == 'Agendada')
                            <span class="badge bg-success">Agendada</span>
                        @else
                            <span class="badge bg-secondary">{{ $cita->estado }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <h4 class="text-secondary mb-3"><i class="bi bi-calendar-check me-2"></i>Nueva fecha para la cita</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-bookmark-star"></i></span>
                        <select id="select-especialidad" class="form-select shadow-none" disabled>
                            <option value="">Seleccione Especialidad</option>
                            @foreach ($especialidades as $e)
                                <option value="{{ $e->id }}" {{ $e->id == $cita->calendario->medico->especialidad_id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Médico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                        <select id="select-medico" class="form-select shadow-none" disabled>
                            @if($cita->calendario->medico_id)
                                <option value="{{ $cita->calendario->medico_id }}" selected>Dr. {{ $cita->calendario->medico->nombre }} {{ $cita->calendario->medico->apellido }}</option>
                            @else
                                <option value="any" selected>Cualquier Médico</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-4 fw-bold small text-uppercase text-muted">
                    <label class="form-label">Tipo de atención</label>
                        <select name="tipo_paciente" id="tipo_paciente" class="form-select" disabled>
                            <option value="primera_vez" {{ $cita->tipo_paciente == 'primera_vez' ? 'selected' : '' }}>Primera vez</option>
                            <option value="control" {{ $cita->tipo_paciente == 'control' ? 'selected' : '' }}>Control / Sucesivo</option>
                            <option value="orden_medica" {{ $cita->tipo_paciente == 'orden_medica' ? 'selected' : '' }}>Orden Médica</option>
                        </select>
                </div>
            </div>
            <br>

            <div class="row g-3">
                <div class="col-md-4 text-center">
                    <div class="btn-group shadow-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary px-3" onclick="cambiarMes(-1)">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="btn btn-light fw-bold text-capitalize" style="min-width: 150px;" id="mes-actual" disabled>
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-3" onclick="cambiarMes(1)">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="table-responsive rounded shadow-sm border">
                    <div class="row g-0 bg-light border-bottom text-center fw-bold py-2 text-muted small text-uppercase">
                        <div class="col" style="width: 14.28%;">Dom</div>
                        <div class="col" style="width: 14.28%;">Lun</div>
                        <div class="col" style="width: 14.28%;">Mar</div>
                        <div class="col" style="width: 14.28%;">Mie</div>
                        <div class="col" style="width: 14.28%;">Jue</div>
                        <div class="col" style="width: 14.28%;">Vie</div>
                        <div class="col" style="width: 14.28%;">Sab</div>
                    </div>

                    <div id="calendario-grid" class="row g-0 bg-white" style="min-height: 400px;">
                    </div>
                </div>
            </div>
            <br>

            <div class="row g-3">
                <div class="col-md-4 fw-bold small text-uppercase text-muted">
                    <label class="form-label">Nueva Fecha de Cita</label>
                    <input type="date" name="fecha_cita" id="input_fecha_cita" class="form-control @error('fecha_cita') is-invalid @enderror" required readonly>
                    @error('fecha_cita')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <input type="hidden" name="calendario_id" id="input_calendario_id" required>
                </div>
                <div class="col-md-8 fw-bold small text-uppercase text-muted">
                    <label class="form-label">Observación</label>
                    <textarea name="observacion" class="form-control" rows="1" placeholder="Motivo del reagendamiento..." maxlength="5000">{{ old('observacion', $cita->observacion) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end py-3">
            <a href="{{ route('Citas.index') }}" class="btn btn-outline-danger me-2">Cancelar</a>
            <button type="submit" class="btn btn-warning px-5"><i class="bi bi-calendar2-week me-2"></i>Reagendar Cita</button>
        </div>
    </form>
</div>

<script>
    let fechaNavegacion = new Date();
    const medicoId = '{{ $cita->calendario->medico_id ?? "any" }}';
    const especialidadId = '{{ $cita->calendario->especialidad_id }}';
    const tipoPaciente = '{{ $cita->tipo_paciente }}';

    document.addEventListener('DOMContentLoaded', function() {
        actualizarTextoMes();
        cargarCalendario();
    });

    function actualizarTextoMes() {
        const opciones = { month: 'long', year: 'numeric' };
        document.getElementById('mes-actual').innerText = fechaNavegacion.toLocaleDateString('es-ES', opciones);
    }

    function cambiarMes(offset) {
        fechaNavegacion.setMonth(fechaNavegacion.getMonth() + offset);
        actualizarTextoMes();
        cargarCalendario();
    }

    function limpiarCalendario() {
        renderizarGrid();
        document.getElementById('input_fecha_cita').value = '';
        document.getElementById('input_calendario_id').value = '';
    }

    async function cargarCalendario() {
        const grid = document.getElementById('calendario-grid');

        const mes = fechaNavegacion.getMonth() + 1;
        const anio = fechaNavegacion.getFullYear();

        grid.innerHTML = '<div class="col-12 py-5 text-center text-primary"><i class="bi bi-arrow-repeat bi-spin fs-1"></i></div>';

        try {
            const res = await fetch(`/api/medicos/${medicoId}/disponibilidad?mes=${mes}&anio=${anio}&tipo_paciente=${tipoPaciente}&especialidad_id=${especialidadId}`);
            const eventos = await res.json();
            renderizarGrid(eventos);
        } catch (error) {
            console.error("Error cargando disponibilidad:", error);
            grid.innerHTML = '<div class="col-12 py-5 text-center text-danger">Error al cargar el calendario.</div>';
        }
    }

    function renderizarGrid(eventos = null) {
        const grid = document.getElementById('calendario-grid');
        grid.innerHTML = '';

        const primerDia = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth(), 1).getDay();
        const ultimoDia = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth() + 1, 0).getDate();

        for (let i = 0; i < primerDia; i++) {
            grid.innerHTML += `<div class="col border-end border-bottom bg-light" style="flex: 0 0 14.28%; height: 90px;"></div>`;
        }

        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        for (let dia = 1; dia <= ultimoDia; dia++) {
            const mesStr = String(fechaNavegacion.getMonth() + 1).padStart(2, '0');
            const diaStr = String(dia).padStart(2, '0');
            const fechaStr = `${fechaNavegacion.getFullYear()}-${mesStr}-${diaStr}`;
            const fechaCelda = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth(), dia);
            
            const ev = eventos?.find(e => e.fecha === fechaStr);

            const div = document.createElement('div');
            div.className = 'col p-2 border-end border-bottom position-relative calendar-day';
            div.style.cssText = 'flex: 0 0 14.28%; height: 90px; transition: 0.2s;';
            div.id = `celda-${fechaStr}`;

            div.innerHTML = `<span class="fw-bold d-block text-start">${dia}</span>`;

            if (ev && fechaCelda >= hoy) {
                if (ev.disponibles > 0) {
                    div.style.cursor = 'pointer';
                    div.classList.add('bg-white');
                    div.innerHTML += `
                        <div class="text-center mt-1 px-1 py-1 rounded border border-success border-opacity-25">
                            <div class="fw-bold text-success" style="font-size:0.75rem; line-height:1.2;">${ev.disponibles} Cupo${ev.disponibles !== 1 ? 's' : ''}</div>
                            <div class="text-muted" style="font-size:0.6rem; line-height:1.1;">${ev.hora_inicio.substring(0,5)} - ${ev.hora_fin.substring(0,5)}</div>
                        </div>`;
                    
                    div.onclick = () => seleccionarDia(fechaStr, ev.id);
                } else {
                    div.classList.add('bg-light');
                    div.style.cursor = 'not-allowed';
                    div.innerHTML += `
                        <div class="text-center mt-2 opacity-75">
                            <span class="badge bg-danger">Agotado</span>
                        </div>`;
                }
            } else {
                div.classList.add('bg-light');
            }

            grid.appendChild(div);
        }
    }

    let fechaSeleccionadaAnterior = null;

    function seleccionarDia(fecha, calendario_id) {
        if (fechaSeleccionadaAnterior) {
            const celdaAnterior = document.getElementById(`celda-${fechaSeleccionadaAnterior}`);
            if(celdaAnterior) {
                celdaAnterior.classList.remove('bg-primary-subtle', 'border-primary');
            }
        }

        const celdaActual = document.getElementById(`celda-${fecha}`);
        if(celdaActual) {
            celdaActual.classList.add('bg-primary-subtle', 'border-primary');
        }
        fechaSeleccionadaAnterior = fecha;

        document.getElementById('input_fecha_cita').value = fecha;
        document.getElementById('input_calendario_id').value = calendario_id;
    }
</script>

@include('layouts.footer')
@endsection
