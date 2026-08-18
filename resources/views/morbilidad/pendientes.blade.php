@extends('layouts.template')
@section('title', 'Atender Citas (' . now()->format('d/m/Y') . ') | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="container-fluid py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Atender Citas ({{ now()->format('d/m/Y') }})</h3>
                <div>
                    @can('Reporte Cita')
                        <a href="{{ route('morbilidad.index') }}" class="btn btn-primary">
                            <i class="bi bi-printer me-1"></i> Reporte de Citas
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                        <x-searchable-select name="especialidad_filtro" id="especialidad_filtro"
                            :options="$especialidades->pluck('nombre', 'id')->prepend('Todas', '')"
                            placeholder="Todas" icon="fas fa-stethoscope" />
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="btnFiltrar" class="btn btn-primary w-100 shadow-sm">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="btnLimpiar" class="btn btn-secondary w-100 shadow-sm">
                            <i class="fas fa-undo me-1"></i> Limpiar
                        </button>
                    </div>
                </div>

                <div class="table-responsive rounded shadow-sm border">
                    <table id="tablaPendientes" class="table table-bordered table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Paciente</th>
                                <th>Cédula</th>
                                <th>N° Historia</th>
                                <th>Fecha Cita</th>
                                <th>Especialidad</th>
                                <th>Médico</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Atender Cita -->
    <div class="modal fade" id="modalAtender" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title">Atender Cita (Registrar Diagnóstico)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formDiagnostico" method="POST">
                    @csrf
                    <input type="hidden" name="cita_id" id="cita_id">
                    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
                        <!-- Información de la cita -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-primary">Información de la Cita</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Paciente:</strong> <span id="info_paciente"></span></p>
                                        <p class="mb-1"><strong>Cédula:</strong> <span id="info_cedula"></span></p>
                                        <p class="mb-0"><strong>Fecha de cita:</strong> <span id="info_fecha"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Médico:</strong> <span id="info_medico"></span></p>
                                        <p class="mb-1"><strong>Número de historia:</strong> <span id="nro_historia"></span></p>
                                        <p class="mb-0"><strong>Especialidad:</strong> <span id="info_especialidad"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnóstico libre -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Diagnóstico libre (impresión diagnóstica)</label>
                            <textarea name="diagnostico_libre" id="diagnostico_libre" class="form-control" rows="2"
                                placeholder="Escriba aquí el diagnóstico general..."></textarea>
                        </div>

                        <!-- Patologías múltiples -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Patologías diagnosticadas (Opcional)</label>
                            <div id="patologias-container">
                                <div class="input-group mb-2 patologia-item">
                                    <select name="patologias[]" class="form-select select-patologia">
                                        <option value="">Seleccione una patología</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-danger btn-remove-patologia"><i
                                            class="bi bi-trash"></i></button>
                                </div>
                            </div>
                            <button type="button" id="add-patologia" class="btn btn-sm btn-secondary mt-1"><i
                                    class="bi bi-plus-circle"></i> Agregar otra patología</button>
                        </div>

                        <!-- Sección Aro (Embarazadas) -->
                        <div id="aroSection" class="mb-4 d-none">
                            <hr>
                            <h6 class="text-primary fw-bold">Datos de Embarazo (Aro)</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="semanas_gestacion" class="form-label">Semanas de Gestación</label>
                                    <input type="number" name="semanas_gestacion" id="semanas_gestacion"
                                           class="form-control" min="0" max="42" step="1"
                                           placeholder="Ej: 24 semanas">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Diagnóstico</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.footer')
@endsection

@push('scripts')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
    <script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#tablaPendientes').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('morbilidad.pendientes') }}",
                    type: 'GET',
                    data: function(d) {
                        d.especialidad_id = $('#especialidad_filtro').val();
                    }
                },
                columns: [{
                        data: 0,
                        name: 'paciente'
                    },
                    {
                        data: 1,
                        name: 'cedula'
                    },
                    {
                        data: 2,
                        name: 'numero_expediente',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 3,
                        name: 'fecha_cita'
                    },
                    {
                        data: 4,
                        name: 'especialidad'
                    },
                    {
                        data: 5,
                        name: 'medico'
                    },
                    {
                        data: 6,
                        name: 'accion',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                language: {
                    url: "{{ asset('vendor/datatables/es-ES.json') }}"
                },
                pageLength: 10,
                order: [
                    [3, 'asc']
                ]
            });

            $('#btnFiltrar').on('click', function() {
                table.ajax.reload();
            });
            $('#btnLimpiar').on('click', function() {
                $('#especialidad_filtro').val('');
                $('#especialidad_filtro_search').val('');
                table.ajax.reload();
            });

            // === Funciones para patologías ===
            function populatePatologiaSelects() {
                $('.select-patologia').each(function() {
                    let $select = $(this);
                    let currentVal = $select.val();
                    $select.empty().append('<option value="">Seleccione una patología</option>');
                    if (window.patologiasList) {
                        $.each(window.patologiasList, function(i, pat) {
                            $select.append('<option value="' + pat.id + '">' + pat.nombre +
                                '</option>');
                        });
                    }
                    if (currentVal) $select.val(currentVal);
                });
            }

            // Agregar nueva patología
            function addPatologiaRow() {
                const original = $('.patologia-item:first');
                const newRow = original.clone();
                newRow.find('select').val('');
                newRow.find('.btn-remove-patologia').show();
                $('#patologias-container').append(newRow);
                populatePatologiaSelects();
                $('.patologia-item .btn-remove-patologia').show();
            }

            // === Agregar elementos dinámicamente ===
            $('#add-patologia').on('click', function() {
                addPatologiaRow();
            });

            // Eliminar elementos (si solo queda una fila, limpia la selección)
            $(document).on('click', '.btn-remove-patologia', function() {
                if ($('.patologia-item').length > 1) {
                    $(this).closest('.patologia-item').remove();
                } else {
                    $(this).closest('.patologia-item').find('select').val('');
                }
            });

            // === Cargar datos de la cita al abrir modal ===
            $('#tablaPendientes').on('click', '.btn-atender', function() {
                var citaId = $(this).data('id');
                $('#cita_id').val(citaId);
                
                // Mantiene la URL original que apunta a store()
                $('#formDiagnostico').attr('action', '/citas/' + citaId + '/diagnostico');

                $('#patologias-container').empty();

                // Fila base
                $('#patologias-container').append(`
                    <div class="input-group mb-2 patologia-item">
                        <select name="patologias[]" class="form-select select-patologia">
                            <option value="">Seleccione una patología</option>
                        </select>
                        <button type="button" class="btn btn-outline-danger btn-remove-patologia"><i class="bi bi-trash"></i></button>
                    </div>
                `);

                $.ajax({
                    url: '/diagnosticos/' + citaId + '/edit',
                    method: 'GET',
                    success: function(data) {
                        window.patologiasList = data.patologias_disponibles || [];

                        populatePatologiaSelects();

                        if (data.cita.patologias && data.cita.patologias.length) {
                            $('#patologias-container').empty();
                            $.each(data.cita.patologias, function(i, pat) {
                                $('#patologias-container').append(`
                                    <div class="input-group mb-2 patologia-item">
                                        <select name="patologias[]" class="form-select select-patologia">
                                            <option value="">Seleccione una patología</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-danger btn-remove-patologia"><i class="bi bi-trash"></i></button>
                                    </div>
                                `);
                            });
                            populatePatologiaSelects();
                            $('.patologia-item').each(function(idx) {
                                let $select = $(this).find('select');
                                if (data.cita.patologias[idx]) {
                                    $select.val(data.cita.patologias[idx].id);
                                }
                            });
                        }

                        let numero_historia = (data.cita.paciente && data.cita.paciente.expediente && data.cita.paciente.expediente.numero_expediente)
                            ? data.cita.paciente.expediente.numero_expediente
                            : "Sin asignar";

                        if (data.cita) {
                            $('#info_paciente').text(data.cita.paciente.nombre + ' ' + data.cita.paciente.apellido);
                            $('#info_cedula').text(data.cita.paciente.cedula);
                            $('#info_fecha').text(new Date(data.cita.fecha_cita).toLocaleDateString());
                            $('#info_medico').text('Dr. ' + data.cita.medico.nombre + ' ' + data.cita.medico.apellido);
                            $('#nro_historia').text(numero_historia);
                            $('#info_especialidad').text(data.cita.medico.especialidad.nombre);
                            $('#diagnostico_libre').val(data.cita.diagnostico_libre || '');

                            if (data.es_aro) {
                                $('#aroSection').removeClass('d-none');
                                $('#semanas_gestacion').prop('required', true);
                                if (data.cita.aro_dato && data.cita.aro_dato.semanas_gestacion) {
                                    $('#semanas_gestacion').val(data.cita.aro_dato.semanas_gestacion);
                                } else {
                                    $('#semanas_gestacion').val('');
                                }
                            } else {
                                $('#aroSection').addClass('d-none');
                                $('#semanas_gestacion').prop('required', false).val('');
                            }
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo cargar la información de la cita', 'error');
                    }
                });
                $('#modalAtender').modal('show');
            });

            // Resetear formulario al cerrar modal
            $('#modalAtender').on('hidden.bs.modal', function() {
                $('#formDiagnostico')[0].reset();
                $('#patologias-container').empty();
                $('#semanas_gestacion').prop('required', false);
            });

            // Cancelar cita (ausencia del paciente o del médico)
            $(document).on('click', '.btn-cancelar-cita', function() {
                var citaId = $(this).data('id');
                var nombre = $(this).closest('tr').find('td:eq(0)').text().trim();

                Swal.fire({
                    title: '¿Cancelar cita?',
                    html:
                        '<p class="mb-3">Paciente: <strong>' + nombre + '</strong><br><small class="text-muted">La cita pasará al estado "Cancelada".</small></p>' +
                        '<div class="text-start">' +
                        '<label class="form-label fw-bold small">Motivo de cancelación</label>' +
                        '<select id="motivo_cancelacion" class="form-select">' +
                        '<option value="ausencia_paciente">Ausencia del paciente</option>' +
                        '<option value="ausencia_medico">Ausencia del médico</option>' +
                        '</select>' +
                        '<label class="form-label fw-bold small mt-3">Observación (opcional)</label>' +
                        '<textarea id="observacion_cancelacion" class="form-control" rows="2" maxlength="500" placeholder="Detalle del motivo..."></textarea>' +
                        '</div>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cancelar cita',
                    cancelButtonText: 'No, mantener',
                    confirmButtonColor: '#dc3545',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: function() {
                        $('#motivo_cancelacion').on('change', function() {
                            $('#observacion_cancelacion').attr('placeholder',
                                $(this).val() === 'ausencia_paciente'
                                    ? 'Ej: paciente no se presentó a la consulta...'
                                    : 'Ej: médico no asistió al turno...'
                            );
                        });
                    }
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    var url = "{{ route('citas.cancelar', ['cita' => '__CITA__']) }}"
                        .replace('__CITA__', citaId);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            motivo: $('#motivo_cancelacion').val(),
                            observacion: $('#observacion_cancelacion').val()
                        })
                    }).then(function(res) {
                        if (!res.ok) {
                            return res.json().then(function(data) {
                                throw new Error(data.message || 'No se pudo cancelar la cita.');
                            });
                        }
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Cita cancelada correctamente.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        table.ajax.reload();
                    }).catch(function(err) {
                        Swal.fire('Error', err.message, 'error');
                    });
                });
            });
        });
    </script>
@endpush