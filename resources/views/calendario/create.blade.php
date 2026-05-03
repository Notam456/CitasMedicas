@extends('layouts.template')
@section('title', 'Planificación Trimestral | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="container py-4">

        <h2 class="text-primary fw-bold mb-4">Configurar Disponibilidad</h2>

        <div class="card shadow-sm border-0 bg-light p-3">
            <div class="card-body bg-white rounded shadow-sm">

                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold small">Especialidad *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-stethoscope"></i></span>
                            <select id="select-especialidad" class="form-select border-secondary-subtle shadow-none">
                                <option value="">Seleccione Especialidad</option>
                                @foreach ($especialidades as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold small">Médico *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user-md"></i></span>
                            <select id="select-medico" class="form-select border-secondary-subtle shadow-none">
                                <option value="">Seleccione Médico</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group shadow-sm">
                            <button class="btn btn-outline-primary" onclick="cambiarMes(-1)"><i
                                    class="fas fa-chevron-left"></i></button>
                            <button class="btn btn-white fw-bold text-capitalize" id="mes-actual" style="min-width: 140px;"
                                disabled></button>
                            <button class="btn btn-outline-primary" onclick="cambiarMes(1)"><i
                                    class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Calendario -->
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
                        <!-- lo llena el javascript -->
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('calendario.index') }}" class="btn btn-secondary px-4">Volver al Visor</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfigurar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="form-guardar-cupo">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fas fa-clock me-2"></i>Configurar Jornada</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                                <input type="time" name="hora_inicio" id="input-inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold small">Hora Fin *</label>
                                <input type="time" name="hora_fin" id="input-fin" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted fw-bold small">Cupos Disponibles *</label>
                                <input type="number" name="cupos_disponibles" id="input-cupos" class="form-control"
                                    required min="1">
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
        });

        document.getElementById('select-especialidad').addEventListener('change', function() {

            fetch(`/calendario/medicos/${this.value}`)
                .then(res => res.json())
                .then(data => {
                    const selectMed = document.getElementById('select-medico');
                    selectMed.innerHTML = '<option value="">Seleccione Médico</option>';
                    data.forEach(m => {
                        selectMed.innerHTML +=
                            `<option value="${m.id}">${m.nombre} ${m.apellido}</option>`;
                    });
                    cargarCalendario();
                });
        });

        document.getElementById('select-medico').addEventListener('change', cargarCalendario);

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
                div.innerHTML = `<span class="fw-bold">${dia}</span>`;

                if (ev) {
                    div.innerHTML += `
                        <div class="mt-2 text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-block mb-1">
                                ${ev.hora_inicio.substring(0,5)} - ${ev.hora_fin.substring(0,5)}
                            </span>
                            <span class="small fw-bold text-muted">${ev.cupos_disponibles} Cupos</span>
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


            document.getElementById('input-inicio').value = evento ? evento.hora_inicio : "08:00";
            document.getElementById('input-fin').value = evento ? evento.hora_fin : "12:00";
            document.getElementById('input-cupos').value = evento ? evento.cupos_disponibles : "10";

            modalBS.show();
        }


        document.getElementById('form-guardar-cupo').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("{{ route('calendario.store') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {

                });
            modalBS.hide();
            cargarCalendario();
        };
    </script>

    <style>
        .calendar-day:hover {
            background: #f0f7ff !important;
            box-shadow: inset 0 0 0 2px #0d6efd;
        }
    </style>

    @include('layouts.footer')
@endsection
