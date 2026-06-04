@extends('layouts.template')
@section('title', 'Configurar Disponibilidad | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold mb-0">Configurar Disponibilidad</h2>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCalendario">
                <i class="fas fa-calendar-alt me-2"></i>Configuración por día
            </button>
        </div>


        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('calendario.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tipo_configuracion" value="masivo">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold small">Especialidad *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-stethoscope"></i></span>
                                <select id="select-especialidad-masivo" class="form-select border-secondary-subtle"
                                    required>
                                    <option value="">Seleccione Especialidad</option>
                                    @foreach ($especialidades as $e)
                                        <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold small">Médico *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user-md"></i></span>
                                <select id="select-medico-masivo" name="medico_id"
                                    class="form-select border-secondary-subtle" required>
                                    <option value="">Seleccione Médico</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold small">Fecha Inicio *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar-day"></i></span>
                                <input type="date" name="fecha_inicio" class="form-control border-secondary-subtle"
                                    value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold small">Duración de la Disponibilidad *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-history"></i></span>
                                <select name="duracion_rango" class="form-select border-secondary-subtle" required>
                                    <option value="1_week" {{ old('duracion_rango') == '1_week' ? 'selected' : '' }}>Una
                                        Semana (7 días)</option>
                                    <option value="1_month"
                                        {{ old('duracion_rango', '1_month') == '1_month' ? 'selected' : '' }}>Un Mes (30
                                        días)</option>
                                    <option value="3_months" {{ old('duracion_rango') == '3_months' ? 'selected' : '' }}>
                                        Trimestral (3 meses)</option>
                                    <option value="6_months" {{ old('duracion_rango') == '6_months' ? 'selected' : '' }}>
                                        Semestral (6 meses)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted fw-bold small d-block">Días de la semana *</label>
                            <div class="d-flex flex-wrap gap-3">
                                @php
                                    $dias = [
                                        1 => 'Lunes',
                                        2 => 'Martes',
                                        3 => 'Miércoles',
                                        4 => 'Jueves',
                                        5 => 'Viernes',
                                        6 => 'Sábado',
                                        7 => 'Domingo',
                                    ];
                                @endphp
                                @foreach ($dias as $value => $label)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="dias_semana[]"
                                            value="{{ $value }}" id="dia_{{ $value }}"
                                            {{ is_array(old('dias_semana')) && in_array($value, old('dias_semana')) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="dia_{{ $value }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted fw-bold small">Hora Inicio *</label>
                            <input type="time" name="hora_inicio" class="form-control"
                                value="{{ old('hora_inicio', '08:00') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-bold small">Hora Fin *</label>
                            <input type="time" name="hora_fin" class="form-control"
                                value="{{ old('hora_fin', '12:00') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-bold small">Cupos 1ra Vez *</label>
                            <input type="number" name="cupos_primera_vez" class="form-control" min="0"
                                value="{{ old('cupos_primera_vez', '10') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-bold small">Cupos Sucesivos *</label>
                            <input type="number" name="cupos_sucesivos" class="form-control" min="0"
                                value="{{ old('cupos_sucesivos', '10') }}" required>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <a href="{{ route('calendario.index') }}" class="btn btn-secondary px-4 me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i>Guardar Configuración Masiva
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCalendario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Configuración por Día Específico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="row g-3 mb-4 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-bold small">Especialidad *</label>
                            <select id="select-especialidad" class="form-select">
                                <option value="">Seleccione Especialidad</option>
                                @foreach ($especialidades as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-bold small">Médico *</label>
                            <select id="select-medico" class="form-select">
                                <option value="">Seleccione Médico</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-outline-primary" onclick="cambiarMes(-1)"><i
                                        class="fas fa-chevron-left"></i></button>
                                <button class="btn btn-white fw-bold text-capitalize" id="mes-actual"
                                    style="min-width: 140px;" disabled></button>
                                <button class="btn btn-outline-primary" onclick="cambiarMes(1)"><i
                                        class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive rounded shadow-sm border">
                        <div
                            class="row g-0 bg-white border-bottom text-center fw-bold py-2 text-muted small text-uppercase">
                            <div class="col" style="width: 14.28%;">Dom</div>
                            <div class="col" style="width: 14.28%;">Lun</div>
                            <div class="col" style="width: 14.28%;">Mar</div>
                            <div class="col" style="width: 14.28%;">Mie</div>
                            <div class="col" style="width: 14.28%;">Jue</div>
                            <div class="col" style="width: 14.28%;">Vie</div>
                            <div class="col" style="width: 14.28%;">Sab</div>
                        </div>
                        <div id="calendario-grid" class="row g-0 bg-white" style="min-height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfigurar" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="form-guardar-cupo">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fas fa-clock me-2"></i>Configurar Jornada</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <h6 class="text-muted small fw-bold text-uppercase border-bottom pb-2">Información del Día</h6>
                            <p class="mb-0 fw-bold fs-5 text-dark" id="display-fecha"></p>
                            <p class="text-primary small mb-0" id="display-medico"></p>
                        </div>
                        <div class="row g-3">
                            <input type="hidden" name="medico_id" id="input-medico-id">
                            <input type="hidden" name="fecha" id="input-fecha">
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold small">Hora Inicio *</label>
                                <input type="time" name="hora_inicio" id="input-inicio" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold small">Hora Fin *</label>
                                <input type="time" name="hora_fin" id="input-fin" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted fw-bold small">Cupos primera vez *</label>
                                <input type="number" name="cupos_primera_vez" id="input-cupos-p" class="form-control"
                                    required min="0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted fw-bold small">Cupos sucesivos *</label>
                                <input type="number" name="cupos_sucesivos" id="input-cupos-s" class="form-control"
                                    required min="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let fechaNavegacion = new Date();
        let modalBS = null;

        document.addEventListener('DOMContentLoaded', function() {
            modalBS = new bootstrap.Modal(document.getElementById('modalConfigurar'));
            cargarCalendario();

            const espMasivo = document.getElementById('select-especialidad-masivo');
            const espManual = document.getElementById('select-especialidad');

            espMasivo.addEventListener('change', function() {
                espManual.value = this.value;
                actualizarMedicos(this.value, 'select-medico-masivo');
                actualizarMedicos(this.value, 'select-medico');
            });

            espManual.addEventListener('change', function() {
                espMasivo.value = this.value;
                actualizarMedicos(this.value, 'select-medico-masivo');
                actualizarMedicos(this.value, 'select-medico');
            });
        });

        function actualizarMedicos(espId, targetId) {
            if (!espId) {
                document.getElementById(targetId).innerHTML = '<option value="">Seleccione Médico</option>';
                return;
            }
            fetch(`/calendario/medicos/${espId}`)
                .then(res => res.json())
                .then(data => {
                    const selectMed = document.getElementById(targetId);
                    selectMed.innerHTML = '<option value="">Seleccione Médico</option>';
                    data.forEach(m => {
                        selectMed.innerHTML += `<option value="${m.id}">${m.nombre} ${m.apellido}</option>`;
                    });
                    if (targetId === 'select-medico') cargarCalendario();
                });
        }

        document.getElementById('select-medico').addEventListener('change', cargarCalendario);
        document.getElementById('select-medico-masivo').addEventListener('change', function() {
            document.getElementById('select-medico').value = this.value;
            cargarCalendario();
        });

        function cambiarMes(offset) {
            fechaNavegacion.setMonth(fechaNavegacion.getMonth() + offset);
            cargarCalendario();
        }

        function cargarCalendario() {
            const mes = fechaNavegacion.getMonth() + 1;
            const anio = fechaNavegacion.getFullYear();
            const medId = document.getElementById('select-medico').value;

            document.getElementById('mes-actual').innerText = fechaNavegacion.toLocaleDateString('es-ES', {
                month: 'long',
                year: 'numeric'
            });

            if (!medId) {
                renderizarGrid([]);
                return;
            }

            fetch(`/calendario/eventos?mes=${mes}&anio=${anio}&medico_id=${medId}`)
                .then(res => res.json())
                .then(eventos => renderizarGrid(eventos));
        }

        function renderizarGrid(eventos) {
            const grid = document.getElementById('calendario-grid');
            grid.innerHTML = '';

            const primerDia = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth(), 1).getDay();
            const ultimoDia = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth() + 1, 0).getDate();

            for (let i = 0; i < primerDia; i++) {
                grid.innerHTML +=
                    `<div class="col p-2 border-end border-bottom bg-light" style="flex: 0 0 14.28%; height: 110px;"></div>`;
            }

            for (let dia = 1; dia <= ultimoDia; dia++) {
                const fechaStr =
                    `${fechaNavegacion.getFullYear()}-${String(fechaNavegacion.getMonth() + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
                const ev = eventos.find(e => e.fecha === fechaStr);

                const div = document.createElement('div');
                div.className = 'col p-2 border-end border-bottom calendar-day bg-white position-relative';
                div.style.cssText = 'flex: 0 0 14.28%; height: 110px; cursor: pointer; transition: 0.2s;';
                div.innerHTML = `<span class="fw-bold text-dark">${dia}</span>`;

                if (ev) {
                    div.innerHTML += `
                        <div class="mt-2 text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-block mb-1">
                                ${ev.hora_inicio.substring(0,5)} - ${ev.hora_fin.substring(0,5)}
                            </span>
                            <span class="small fw-bold text-muted">${ev.cupos_primera_vez + ev.cupos_sucesivos} Cupos</span>
                        </div>`;
                } else {
                    div.innerHTML +=
                        `<div class="mt-3 text-center text-light"><i class="fas fa-plus-circle fa-2x"></i></div>`;
                }

                div.onclick = () => abrirConfigurador(fechaStr, ev);
                grid.appendChild(div);
            }
        }

        function abrirConfigurador(fecha, evento) {
            const medId = document.getElementById('select-medico').value;
            const medNombre = document.getElementById('select-medico').options[document.getElementById('select-medico')
                .selectedIndex].text;

            if (!medId) {
                Swal.fire({
                    title: '¡Atención!',
                    text: 'Para configurar un día, primero debe seleccionar un médico de la lista.',
                    icon: 'warning',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            document.getElementById('display-fecha').innerText = new Date(fecha + "T00:00:00").toLocaleDateString('es-ES', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });
            document.getElementById('display-medico').innerText = "Médico: " + medNombre;
            document.getElementById('input-medico-id').value = medId;
            document.getElementById('input-fecha').value = fecha;

            document.getElementById('input-inicio').value = evento ? evento.hora_inicio.substring(0, 5) : "08:00";
            document.getElementById('input-fin').value = evento ? evento.hora_fin.substring(0, 5) : "12:00";
            document.getElementById('input-cupos-p').value = evento ? evento.cupos_primera_vez : "10";
            document.getElementById('input-cupos-s').value = evento ? evento.cupos_sucesivos : "10";

            modalBS.show();
        }

        document.getElementById('form-guardar-cupo').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("{{ route('calendario.store') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw data;
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        modalBS.hide();
                        cargarCalendario();
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(err => {
                    let errorMsg = 'Ocurrió un inconveniente procesando los datos.';
                    if (err.errors) {
                        errorMsg = Object.values(err.errors).map(msg => `• ${msg}`).join('\n');
                    } else if (err.message) {
                        errorMsg = err.message;
                    }

                    Swal.fire({
                        title: 'Error de Validación',
                        text: errorMsg,
                        icon: 'error',
                        confirmButtonColor: '#0d6efd'
                    });
                });
        };
    </script>
    @push('scripts')

        @if ($errors->any())
            <script>
                const errorMessages = @json(implode("\n", $errors->all()));

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessages
                });
            </script>
        @endif
    @endpush
    <style>
        .calendar-day:hover {
            background: #f0f7ff !important;
            box-shadow: inset 0 0 0 2px #0d6efd;
        }

        .modal-xl {
            max-width: 90%;
        }
    </style>

    @include('layouts.footer')
@endsection
