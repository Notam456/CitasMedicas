@extends('layouts.template')
@section('title', 'Agendar Cita')

@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<div class="container-fluid px-4 py-4">
    
    <div class="mb-4">
        <h2 class="fw-bold text-primary border-bottom pb-2">Agendar Cita</h2>
    </div>

    <form action="{{ route('Citas.store') }}" method="POST" class="card shadow-sm border-0">
        @csrf
        <!-- <input type="hidden" name="especialidad_id" value=""> -->
        
        <div class="card-body p-4">
            
            <h4 class="text-secondary mb-3"><i class="fas fa-user-injured me-2"></i>Datos del Paciente</h4>
            
            <div class="row g-3 bg-light p-3 rounded mb-4 border">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Cédula del Paciente *</label>
                    <div class="input-group">
                        <input type="text" name="cedula" id="input_cedula" class="form-control" placeholder="V-12345678" required>
                        <button type="button" class="btn btn-secondary" id="btn_buscar_cedula">Buscar</button>
                    </div>
                    <small id="mensaje_cedula" class="form-text mt-1 text-primary">Ingrese cédula para buscar.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" id="input_nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" id="input_apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido') }}" required>
                    @error('apellido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Rif</label>
                    <input type="text" name="rif" id="input_rif" class="form-control @error('rif') is-invalid @enderror" value="{{ old('rif') }}">
                    @error('rif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">F. Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="input_fecha" class="form-control @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento') }}" required>
                    @error('fecha_nacimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" id="input_telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" required>
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <h6 class="text-secondary border-bottom pb-2">Ubicación del Paciente</h6>
                    
                <!-- Estado -->
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select id="select-estado" class="form-select">
                        <option value="">Seleccione Estado</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                                {{ $estado->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            
                <!-- Municipio -->
                <div class="col-md-4">
                    <label class="form-label">Municipio</label>
                    <select id="select-municipio" class="form-select">
                        <option value="">Seleccione Municipio</option>
                    </select>
                </div>
            
                <!-- Parroquia -->
                <div class="col-md-4">
                    <label class="form-label">Parroquia</label>
                    <select name="parroquia_id" id="select-parroquia" class="form-select @error('parroquia_id') is-invalid @enderror" required>
                        <option value="">Seleccione Parroquia</option>
                    </select>
                    @error('parroquia_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Dirección -->
                <div class="col-md-12">
                    <label class="form-label">Dirección exacta</label>
                    <input type="text" name="direccion" id="input_direccion" value="{{ old('direccion') }}" class="form-control">
                </div>
            </div>

            <h4 class="text-secondary mb-3"><i class="fas fa-calendar-check me-2"></i>Datos de la Cita</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-stethoscope"></i></span>
                        <select id="select-especialidad" class="form-select shadow-none">
                            <option value="">Seleccione Especialidad</option>
                            @foreach ($especialidades as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Médico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-user-md"></i></span>
                        <select id="select-medico" class="form-select shadow-none">
                            <option value="">Seleccione Médico</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 fw-bold small text-uppercase text-muted">
                    <label class="form-label">Tipo de atención</label>
                    <select name="tipo_paciente" id="tipo_paciente" class="form-select" required>
                        <option value="">Seleccione una opción</option>
                        <option value="primera_vez">Primera vez</option>
                        <option value="control">Control / Sucesivo</option>
                    </select>
                </div>
            </div>
            <br>

            <div class="row g-3">
                <div class="col-md-4 text-center">
                    <div class="btn-group shadow-sm" role="group">
                        <button class="btn btn-outline-secondary px-3" onclick="cambiarMes(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-light fw-bold text-capitalize" style="min-width: 150px;" id="mes-actual"
                            disabled>
                        </button>
                        <button class="btn btn-outline-secondary px-3" onclick="cambiarMes(1)">
                            <i class="fas fa-chevron-right"></i>
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
                        <!-- lo llena el JavaScript -->
                    </div>
                </div>
            </div>
            <br>

            <div class="row g-3">
                <div class="col-md-4 fw-bold small text-uppercase text-muted">
                    <label class="form-label">Fecha de Cita</label>
                    <input type="date" name="fecha_cita" id="input_fecha_cita" class="form-control" required readonly>
                    <input type="hidden" name="calendario_id" id="input_calendario_id" required>
                </div>
                <div class="col-md-8 fw-bold small text-uppercase text-muted">
                    <label class="form-label">Observación</label>
                    <textarea name="observacion" class="form-control" rows="1" placeholder="Síntomas o nota..."></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end py-3">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-danger me-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-5">Confirmar y Agendar Cita</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('btn_buscar_cedula').addEventListener('click', function() {
        let cedula = document.getElementById('input_cedula').value;
        let mensaje = document.getElementById('mensaje_cedula');
        
        if(cedula.length < 5) return;

        mensaje.innerHTML = 'Buscando...';
        mensaje.className = 'form-text mt-1 text-warning';

        fetch(`/api/paciente/buscar/${cedula}`)
            .then(response => response.json())
            .then(data => {
                if (data.encontrado) {
                    document.getElementById('input_nombre').value = data.datos.nombre;
                    document.getElementById('input_apellido').value = data.datos.apellido;
                    document.getElementById('input_fecha').value = data.datos.fecha_nacimiento;
                    document.getElementById('input_telefono').value = data.datos.telefono;
                    document.getElementById('input_direccion').value = data.datos.direccion;

                    const p = data.datos.parroquia;
                    const m = p.municipio;
                    const e = m.estado;
                    
                    document.getElementById('select-estado').value = e.id;
                    
                    let selectM = document.getElementById('select-municipio');
                    selectM.innerHTML = `<option value="${m.id}" selected>${m.nombre}</option>`;
                    
                    let selectP = document.getElementById('select-parroquia');
                    selectP.innerHTML = `<option value="${p.id}" selected>${p.nombre}</option>`;

                    document.getElementById('select-estado').disabled = true;
                    document.getElementById('select-municipio').disabled = true;
                    document.getElementById('select-parroquia').disabled = true;
                    if(!document.getElementById('hidden_parroquia')) {
                        let hiddenP = document.createElement('input');
                        hiddenP.type = 'hidden';
                        hiddenP.name = 'parroquia_id';
                        hiddenP.id = 'hidden_parroquia';
                        hiddenP.value = p.id;
                        document.querySelector('form').appendChild(hiddenP);
                    } else {
                        document.getElementById('hidden_parroquia').value = p.id;
                    }
                    
                    document.querySelectorAll('#input_nombre, #input_apellido, #input_fecha, #input_telefono, #input_direccion').forEach(el => el.readOnly = true);
                    
                    mensaje.innerHTML = 'Paciente encontrado. Datos autocompletados.';
                    mensaje.className = 'form-text mt-1 text-success fw-bold';
                } else {
                    document.getElementById('input_nombre').value = '';
                    document.getElementById('input_apellido').value = '';
                    document.getElementById('input_fecha').value = '';
                    document.getElementById('input_telefono').value = '';
                    document.getElementById('input_direccion').value = '';
                    
                    document.getElementById('select-estado').value = '';
                    document.getElementById('select-estado').disabled = false;
                    document.getElementById('select-municipio').innerHTML = '<option value="">Seleccione Municipio</option>';
                    document.getElementById('select-municipio').disabled = false;
                    document.getElementById('select-parroquia').innerHTML = '<option value="">Seleccione Parroquia</option>';
                    document.getElementById('select-parroquia').disabled = false;
                    
                    let hiddenP = document.getElementById('hidden_parroquia');
                    if(hiddenP) hiddenP.remove();
                    
                    document.querySelectorAll('#input_nombre, #input_apellido, #input_fecha, #input_telefono, #input_direccion').forEach(el => el.readOnly = false);
                    
                    mensaje.innerHTML = 'Paciente nuevo. Por favor llene todos los campos.';
                    mensaje.className = 'form-text mt-1 text-primary fw-bold';
                }
            })
            .catch(error => {
                console.error(error);
                mensaje.innerHTML = 'Error al procesar la solicitud.';
                mensaje.className = 'form-text mt-1 text-danger';
            });
    });

    document.getElementById('select-estado').addEventListener('change', function() {
        let estadoId = this.value;
        let selectMunicipio = document.getElementById('select-municipio');
        let selectParroquia = document.getElementById('select-parroquia');
    
        selectMunicipio.innerHTML = '<option value="">Cargando...</option>';
        selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';
    
        if(estadoId) {
            fetch(`/api/municipios/${estadoId}`)
                .then(res => res.json())
                .then(data => {
                    selectMunicipio.innerHTML = '<option value="">Seleccione Municipio</option>';
                    data.forEach(m => {
                        selectMunicipio.innerHTML += `<option value="${m.id}">${m.nombre}</option>`;
                    });
                })
                .catch(err => console.error(err));
        }
    });
    
    document.getElementById('select-municipio').addEventListener('change', function() {
        let municipioId = this.value;
        let selectParroquia = document.getElementById('select-parroquia');
    
        selectParroquia.innerHTML = '<option value="">Cargando...</option>';
    
        if(municipioId) {
            fetch(`/api/parroquias/${municipioId}`)
                .then(res => res.json())
                .then(data => {
                    selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';
                    data.forEach(p => {
                        selectParroquia.innerHTML += `<option value="${p.id}">${p.nombre}</option>`;
                    });
                })
                .catch(err => console.error(err));
        }
    });

    let fechaNavegacion = new Date();

    document.addEventListener('DOMContentLoaded', function() {
        actualizarTextoMes();
        
        const selectEspecialidad = document.getElementById('select-especialidad');
        const selectMedico = document.getElementById('select-medico');
        const selectTipoPaciente = document.getElementById('tipo_paciente');

        // 1. Cargar médicos al cambiar especialidad
        selectEspecialidad.addEventListener('change', async function() {
            const espId = this.value;
            selectMedico.innerHTML = '<option value="">Seleccione Médico</option>';
            limpiarCalendario();

            if (!espId) return;

            try {
                const res = await fetch(`/api/especialidades/${espId}/medicos`);
                const medicos = await res.json();
                
                medicos.forEach(m => {
                    selectMedico.innerHTML += `<option value="${m.id}">${m.nombre} ${m.apellido}</option>`;
                });
            } catch (error) {
                console.error("Error cargando médicos:", error);
            }
        });

        // 2. Escuchar cambios para renderizar calendario
        selectMedico.addEventListener('change', cargarCalendario);
        selectTipoPaciente.addEventListener('change', cargarCalendario);
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
        document.getElementById('calendario-grid').innerHTML = '<div class="col-12 py-5 text-center text-muted">Seleccione un médico y el tipo de atención para ver disponibilidad.</div>';
        document.getElementById('input_fecha_cita').value = '';
        document.getElementById('input_calendario_id').value = '';
    }

    // 3. Cargar la disponibilidad desde el backend
    async function cargarCalendario() {
        const medicoId = document.getElementById('select-medico').value;
        const tipoPaciente = document.getElementById('tipo_paciente').value;
        const grid = document.getElementById('calendario-grid');

        if (!medicoId || !tipoPaciente) {
            limpiarCalendario();
            return;
        }

        const mes = fechaNavegacion.getMonth() + 1;
        const anio = fechaNavegacion.getFullYear();

        grid.innerHTML = '<div class="col-12 py-5 text-center text-primary"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';

        try {
            const res = await fetch(`/api/medicos/${medicoId}/disponibilidad?mes=${mes}&anio=${anio}&tipo_paciente=${tipoPaciente}`);
            const eventos = await res.json();
            renderizarGrid(eventos);
        } catch (error) {
            console.error("Error cargando disponibilidad:", error);
            grid.innerHTML = '<div class="col-12 py-5 text-center text-danger">Error al cargar el calendario.</div>';
        }
    }

    // 4. Dibujar el calendario interactivo
    function renderizarGrid(eventos) {
        const grid = document.getElementById('calendario-grid');
        grid.innerHTML = '';

        const primerDia = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth(), 1).getDay();
        const ultimoDia = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth() + 1, 0).getDate();

        // Rellenar cuadros vacíos antes del día 1
        for (let i = 0; i < primerDia; i++) {
            grid.innerHTML += `<div class="col border-end border-bottom bg-light" style="flex: 0 0 14.28%; height: 90px;"></div>`;
        }

        // Renderizar los días
        for (let dia = 1; dia <= ultimoDia; dia++) {
            const mesStr = String(fechaNavegacion.getMonth() + 1).padStart(2, '0');
            const diaStr = String(dia).padStart(2, '0');
            const fechaStr = `${fechaNavegacion.getFullYear()}-${mesStr}-${diaStr}`;
            
            // Buscar si hay evento planificado para esta fecha
            const ev = eventos.find(e => e.fecha === fechaStr);

            const div = document.createElement('div');
            div.className = 'col p-2 border-end border-bottom position-relative calendar-day';
            div.style.cssText = 'flex: 0 0 14.28%; height: 90px; transition: 0.2s;';
            div.id = `celda-${fechaStr}`;
            
            div.innerHTML = `<span class="fw-bold d-block text-start">${dia}</span>`;

            if (ev) {
                if (ev.disponibles > 0) {
                    div.style.cursor = 'pointer';
                    div.classList.add('bg-white');
                    div.innerHTML += `
                        <div class="text-center mt-1">
                            <span class="badge bg-success-subtle text-success border border-success-subtle d-block mb-1 shadow-sm">
                                ${ev.disponibles} Cupos
                            </span>
                            <small class="text-muted" style="font-size:0.65rem;">${ev.hora_inicio.substring(0,5)}</small>
                        </div>`;
                    
                    // Función al hacer CLIC
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

    // 5. Rellenar inputs al hacer clic en un cupo disponible
    let fechaSeleccionadaAnterior = null;

    function seleccionarDia(fecha, calendario_id) {
        // Remover color de selección anterior si existe
        if (fechaSeleccionadaAnterior) {
            const celdaAnterior = document.getElementById(`celda-${fechaSeleccionadaAnterior}`);
            if(celdaAnterior) {
                celdaAnterior.classList.remove('bg-primary-subtle', 'border-primary');
            }
        }

        // Pintar celda actual de azul
        const celdaActual = document.getElementById(`celda-${fecha}`);
        if(celdaActual) {
            celdaActual.classList.add('bg-primary-subtle', 'border-primary');
        }
        fechaSeleccionadaAnterior = fecha;

        // Autocompletar inputs
        document.getElementById('input_fecha_cita').value = fecha;
        document.getElementById('input_calendario_id').value = calendario_id;
    }
</script>

@include('layouts.footer')
@endsection