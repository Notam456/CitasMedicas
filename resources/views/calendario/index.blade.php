@extends('layouts.template')
@section('title', 'Planificación Trimestral | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h3 class="mb-0"> Calendario de Disponibilidad</h3>
                <a href="{{ route('calendario.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Configurar Cupos
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4 align-items-end">
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
                        <!-- lo llena el JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="bsModalResumen" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"><i class="fas fa-info-circle me-2"></i>Resumen de Disponibilidad
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-3 rounded-circle me-3 text-primary">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Fecha seleccionada</h6>
                            <h5 id="fecha-seleccionada" class="fw-bold mb-0 text-dark"></h5>
                        </div>
                    </div>
                    <div id="lista-medicos" class="mt-4">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
                    <button id="btn-agendar-global" class="btn btn-success px-4 shadow-sm">
                        <i class="fas fa-calendar-check me-1"></i> Agendar Cita
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let fechaActual = new Date();
        let bsModal = null;

        document.addEventListener('DOMContentLoaded', function() {
            bsModal = new bootstrap.Modal(document.getElementById('bsModalResumen'));
            cargarCalendario();
        });


        document.getElementById('select-especialidad').addEventListener('change', function() {
            const espId = this.value;
            const selectMed = document.getElementById('select-medico');

            if (!espId) {
                selectMed.innerHTML = '<option value="">Seleccione Médico</option>';
                cargarCalendario();
                return;
            }

            fetch(`/calendario/medicos/${espId}`)
                .then(res => res.json())
                .then(data = > {
                    selectMed.innerHTML = '<option value="">Todos los médicos</option>';
                    data.forEach(m => {
                        selectMed.innerHTML += `<option value="${m.id}">${m.nombre} ${m.apellido}</option>`;
                    });

                    if (data.length === 1) {
                        selectMed.value = data[0].id;
                    }
                    cargarCalendario();
                });
        });

        document.getElementById('select-medico').addEventListener('change', cargarCalendario);

        function cambiarMes(offset) {
            fechaActual.setMonth(fechaActual.getMonth() + offset);
            cargarCalendario();
        }

        function cargarCalendario() {
            const mes = fechaActual.getMonth() + 1;
            const anio = fechaActual.getFullYear();
            const espId = document.getElementById('select-especialidad').value;
            const medId = document.getElementById('select-medico').value;
            if (!espId) {
                renderizarGrid([]);
                return;
            }

            const opciones = {
                month: 'long',
                year: 'numeric'
            };
            document.getElementById('mes-actual').innerText = fechaActual.toLocaleDateString('es-ES', opciones);

            fetch(`/calendario/eventos?mes=${mes}&anio=${anio}&especialidad_id=${espId}&medico_id=${medId}`)
                .then(res => res.json())
                .then(eventos => {
                    renderizarGrid(eventos);
                });
        }

        function renderizarGrid(eventos) {
            const grid = document.getElementById('calendario-grid');
            grid.innerHTML = '';

            const primerDiaSemana = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), 1).getDay();
            const ultimoDiaMes = new Date(fechaActual.getFullYear(), fechaActual.getMonth() + 1, 0).getDate();

            for (let i = 0; i < primerDiaSemana; i++) {
                grid.innerHTML +=
                    `<div class="col p-2 border-end border-bottom bg-light" style="flex: 0 0 14.28%; height: 100px;"></div>`;
            }

            for (let dia = 1; dia <= ultimoDiaMes; dia++) {
                const fechaStr =
                    `${fechaActual.getFullYear()}-${String(fechaActual.getMonth() + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
                const eventosDia = eventos.filter(e => e.fecha === fechaStr);
                const totalCupos = eventosDia.reduce((sum, e) => sum + e.cupos_disponibles, 0);

                const divDia = document.createElement('div');
                divDia.className = 'col p-2 border-end border-bottom calendar-day bg-white text-dark';
                divDia.style.cssText = 'flex: 0 0 14.28%; height: 110px; cursor: pointer; transition: background 0.2s;';
                divDia.onmouseover = function() {
                    this.style.background = '#f8f9fa';
                };
                divDia.onmouseout = function() {
                    this.style.background = 'white';
                };

                divDia.onclick = () => abrirResumen(fechaStr, eventosDia);

                divDia.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="fw-bold">${dia}</span>
                        ${totalCupos > 0 ? '<span class="badge rounded-pill bg-success p-1"><i class="fas fa-check"></i></span>' : ''}
                    </div>
                    <div class="text-center mt-3">
                        <div class="small fw-bold ${totalCupos > 0 ? 'text-success' : 'text-danger'}">
                            ${totalCupos} Cupos
                        </div>
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar ${totalCupos > 0 ? 'bg-success' : 'bg-danger'}" style="width: ${totalCupos > 0 ? '100%' : '0%'}"></div>
                        </div>
                    </div>
                `;
                grid.appendChild(divDia);
            }
        }

        function abrirResumen(fecha, eventos) {
            const fechaObjeto = new Date(fecha + "T00:00:00");
            document.getElementById('fecha-seleccionada').innerText = fechaObjeto.toLocaleDateString('es-ES', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });

            const lista = document.getElementById('lista-medicos');
            lista.innerHTML = '';

            if (eventos.length === 0) {
                lista.innerHTML = `
                    <div class="     alert-warning border-0 shadow-sm d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No hay médicos programados para este día.
                    </div>`;
            } else {
                eventos.forEach(ev => {
                    lista.innerHTML += `
                        <div class="card mb-3 border-start border-4 border-primary shadow-sm">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-primary">Dr. ${ev.medico.nombre} ${ev.medico.apellido}</h6>
                                    <span class="badge bg-primary rounded-pill">${ev.cupos_disponibles} cupos</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="far fa-clock me-1 text-secondary"></i> Jornada: ${ev.hora_inicio.substring(0,5)} - ${ev.hora_fin.substring(0,5)}
                                </div>
                            </div>
                        </div>`;
                });
            }

            bsModal.show();
        }
    </script>

    <style>
        .calendar-day:hover {
            box-shadow: inset 0 0 0 2px #0d6efd !important;
            z-index: 10;
        }
    </style>

    @include('layouts.footer')
@endsection
