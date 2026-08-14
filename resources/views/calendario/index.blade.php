@extends('layouts.template')
@section('title', 'Calendario de Disponibilidad | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="container py-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fs-4 text-success"></i>
                    <div>
                        {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
                        <div id="aviso_suspension_cal" class="mb-2 text-danger small fw-bold animate__animated animate__fadeIn" style="display: none; background-color: #f8d7da; border: 1px solid #f5c2c7; padding: 6px 12px; border-radius: .25rem;"></div>
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
                    <div id="lista-medicos" class="mt-4"></div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
                    <!--<button id="btn-agendar-global" class="btn btn-success px-4 shadow-sm">
                            <i class="fas fa-calendar-check me-1"></i> Agendar Cita
                        </button>-->
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
                .then(data => {
                    selectMed.innerHTML = '<option value="">Todos los médicos</option>';
                    data.forEach(m => {
                        selectMed.innerHTML +=
                            `<option value="${m.id}">${m.nombre} ${m.apellido}</option>`;
                    });

                    if (data.length === 1) {
                        selectMed.value = data[0].id;
                    }
                    cargarCalendario();
                });
        });

        document.getElementById('select-medico').addEventListener('change', cargarCalendario);

        let suspensionesMedico = [];

        function cambiarMes(offset) {
            fechaActual.setMonth(fechaActual.getMonth() + offset);
            cargarCalendario();
        }

        async function cargarCalendario() {
            const mes = fechaActual.getMonth() + 1;
            const anio = fechaActual.getFullYear();
            const espId = document.getElementById('select-especialidad').value;
            const medId = document.getElementById('select-medico').value;
            
            const aviso = document.getElementById('aviso_suspension_cal');
            if (aviso) {
                aviso.style.display = 'none';
                aviso.innerHTML = '';
            }

            if (!espId) {
                renderizarGrid([]);
                return;
            }

            const opciones = {
                month: 'long',
                year: 'numeric'
            };
            document.getElementById('mes-actual').innerText = fechaActual.toLocaleDateString('es-ES', opciones);

            // Fetch active/future suspensions of selected doctor
            suspensionesMedico = [];
            if (medId && medId !== 'any') {
                try {
                    const resSusp = await fetch(`/api/medicos/${medId}/suspensiones-activas`);
                    if (resSusp.ok) {
                        suspensionesMedico = await resSusp.json();
                        if (suspensionesMedico.length > 0 && aviso) {
                            let text = 'Inactivo por suspensión: ';
                            suspensionesMedico.forEach((s, idx) => {
                                const partsStart = s.fecha_inicio.split('-');
                                const partsEnd = s.fecha_fin.split('-');
                                const fInicio = `${partsStart[2]}/${partsStart[1]}/${partsStart[0]}`;
                                const fFin = `${partsEnd[2]}/${partsEnd[1]}/${partsEnd[0]}`;
                                if (idx > 0) text += ', ';
                                text += `desde el ${fInicio} hasta el ${fFin}`;
                            });
                            aviso.innerHTML = text;
                            aviso.style.display = 'block';
                        }
                    }
                } catch (e) {
                    console.error(e);
                }
            }

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

                const totalCuposConfigurados = eventosDia.reduce((sum, e) => sum + e.cupos_primera_vez + e.cupos_sucesivos,
                    0);
                const totalCuposAsignados = eventosDia.reduce((sum, e) => sum + e.citas_primera_vez_count + e
                    .citas_sucesivas_count, 0);
                const cuposDisponibles = totalCuposConfigurados - totalCuposAsignados;

                let porcentajeOcupacion = 0;
                if (totalCuposConfigurados > 0) {
                    porcentajeOcupacion = (cuposDisponibles / totalCuposConfigurados) * 100;
                }

                let colorClase = 'text-danger';
                let bgBarra = 'bg-danger';
                if (totalCuposConfigurados > 0) {
                    colorClase = cuposDisponibles > 0 ? 'text-success' : 'text-warning';
                    bgBarra = cuposDisponibles > 0 ? 'bg-success' : 'bg-warning';
                }

                // Check if suspended
                let isSuspended = false;
                if (suspensionesMedico.length > 0) {
                    isSuspended = suspensionesMedico.some(s => {
                        return fechaStr >= s.fecha_inicio && fechaStr <= s.fecha_fin;
                    });
                }

                const divDia = document.createElement('div');
                divDia.className = 'col p-2 border-end border-bottom calendar-day bg-white text-dark';
                divDia.style.cssText = 'flex: 0 0 14.28%; height: 110px; cursor: pointer; transition: background 0.2s;';
                
                if (isSuspended) {
                    divDia.style.backgroundColor = '#f8d7da';
                    divDia.onmouseover = function() {
                        this.style.background = '#f5c2c7';
                    };
                    divDia.onmouseout = function() {
                        this.style.background = '#f8d7da';
                    };
                } else {
                    divDia.onmouseover = function() {
                        this.style.background = '#f8f9fa';
                    };
                    divDia.onmouseout = function() {
                        this.style.background = 'white';
                    };
                }

                divDia.onclick = () => abrirResumen(fechaStr, eventosDia);
                grid.appendChild(divDia);

                let innerHtml = `
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="fw-bold">${dia}</span>
                        ${totalCuposConfigurados > 0 && !isSuspended ? `<span class="badge rounded-pill ${cuposDisponibles > 0 ? 'bg-success' : 'bg-warning'} p-1"><i class="fas ${cuposDisponibles > 0 ? 'fa-check' : 'fa-exclamation'}"></i></span>` : ''}
                        ${isSuspended ? `<span class="badge rounded-pill bg-danger p-1 text-white" title="Médico Suspendido"><i class="fas fa-ban"></i></span>` : ''}
                    </div>`;

                if (isSuspended) {
                    innerHtml += `
                        <div class="text-center mt-2">
                            <div class="small fw-bold text-danger" style="font-size: 0.72rem;">Inactivo (Suspendido)</div>
                            ${totalCuposConfigurados > 0 ? `<small class="text-muted text-decoration-line-through" style="font-size: 0.65rem;">${totalCuposConfigurados} cupos inactivos</small>` : '<small class="text-muted" style="font-size: 0.65rem;">Sin planificar</small>'}
                        </div>`;
                } else {
                    innerHtml += `
                        <div class="text-center mt-2">
                            <div class="small fw-bold ${colorClase}">
                                ${totalCuposConfigurados > 0 ? `${cuposDisponibles} disp. de ${totalCuposConfigurados}` : '0 Cupos'}
                            </div>
                            <div class="progress mt-1" style="height: 6px; background-color: #e9ecef;">
                                <div class="progress-bar ${bgBarra}" style="width: ${totalCuposConfigurados > 0 ? porcentajeOcupacion : 0}%"></div>
                            </div>
                        </div>`;
                }

                divDia.innerHTML = innerHtml;
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
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No hay médicos programados para este día.
                    </div>`;
            } else {
                eventos.forEach(ev => {
                    const totalMed = ev.cupos_primera_vez + ev.cupos_sucesivos;
                    const asignadosMed = ev.citas_primera_vez_count + ev.citas_sucesivas_count;
                    const dispMed = ev.suspended ? 0 : (totalMed - asignadosMed);

                    let borderClass = 'border-primary';
                    if (ev.suspended) {
                        borderClass = 'border-danger';
                    } else if (dispMed <= 0) {
                        borderClass = 'border-warning';
                    }

                    let badgeHtml = '';
                    if (ev.suspended) {
                        badgeHtml = `<span class="badge bg-danger text-white fw-bold">Inactivo (Médico Suspendido)</span>`;
                    } else {
                        badgeHtml = `<span class="badge ${dispMed > 0 ? 'bg-info' : 'bg-warning'} text-dark fw-bold">${dispMed} / ${totalMed} Disponibles</span>`;
                    }

                    let bodyHtml = '';
                    if (ev.suspended) {
                        bodyHtml = `
                            <div class="alert alert-danger mb-2 py-2 small border-0 d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Los cupos programados para este día están inactivos por suspensión del médico.
                            </div>`;
                    } else {
                        bodyHtml = `
                                <div class="row g-2 text-center my-2">
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded border">
                                            <small class="text-muted d-block">Primera Vez</small>
                                            <span class="fw-bold text-dark">${ev.citas_primera_vez_count}</span> 
                                            <span class="text-muted">/ ${ev.cupos_primera_vez}</span>
                                            <small class="text-muted d-block">(${ev.cupos_primera_vez - ev.citas_primera_vez_count} cupos disponibles)</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 bg-light rounded border">
                                            <small class="text-muted d-block">Control (Sucesivos)</small>
                                            <span class="fw-bold text-dark">${ev.citas_sucesivas_count}</span> 
                                            <span class="text-muted">/ ${ev.cupos_sucesivos}</span>
                                             <small class="text-muted d-block">(${ev.cupos_sucesivos - ev.citas_sucesivas_count} cupos disponibles)</small>
                                        </div>
                                    </div>
                                </div>`;
                    }

                    lista.innerHTML += `
                        <div class="card mb-3 border-start border-4 ${borderClass} shadow-sm">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-primary">${ev.medico ? `Dr. ${ev.medico.nombre} ${ev.medico.apellido}`  : 'Cualquier médico'}</h6>
                                    ${badgeHtml}
                                </div>
                                
                                ${bodyHtml}

                                <div class="small text-muted mt-2 pt-2 border-top">
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
