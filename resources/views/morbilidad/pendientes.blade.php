@extends('layouts.template')
@section('title', 'Citas Pendientes del Día | SAGECIM')

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
                            <i class="bi bi-printer me-1"></i> Reporte de Morbilidad
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-stethoscope"></i></span>
                            <select id="especialidad_filtro" class="form-select shadow-none">
                                <option value="">Todas</option>
                                @foreach ($especialidades as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
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
                                        <p class="mb-0"><strong>Especialidad:</strong> <span
                                                id="info_especialidad"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnóstico libre -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Diagnóstico libre (impresión diagnóstica)</label>
                            <textarea name="diagnostico_libre" id="diagnostico_libre" class="form-control" rows="2"
                                placeholder="Escriba aquí el diagnóstico general..." required></textarea>
                        </div>

                        <!-- Patologías múltiples -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Patologías diagnosticadas</label>
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


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Diagnóstico</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Asignar Número de Historia -->
    <div class="modal fade" id="modalAsignarHistoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-plus me-2"></i>Asignar Número de Historia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAsignarHistoria">
                    @csrf
                    <input type="hidden" name="paciente_id" id="historia_paciente_id">
                    <div class="modal-body">
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-info">Datos del Paciente</h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="mb-1"><strong>Nombre:</strong> <span
                                                id="historia_paciente_nombre"></span></p>
                                        <p class="mb-0"><strong>Cédula:</strong> <span
                                                id="historia_paciente_cedula"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="numero_expediente" class="form-label fw-bold">Número de Historia <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="numero_expediente" id="numero_expediente" class="form-control"
                                placeholder="Ingrese el número de historia" required>
                            <div class="form-text">Este número será asignado al paciente y no podrá ser modificado.</div>
                            <div class="invalid-feedback" id="historia_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info text-white" id="btnGuardarHistoria">
                            <i class="bi bi-check-lg me-1"></i> Asignar Número
                        </button>
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

            // Agregar nueva patología (clona la primera fila y limpia valores)
            function addPatologiaRow() {
                const original = $('.patologia-item:first');
                const newRow = original.clone();
                newRow.find('select').val('');
                newRow.find('.btn-remove-patologia').show();
                $('#patologias-container').append(newRow);
                populatePatologiaSelects(); // Asegurar que el nuevo select tenga las opciones
                // Mostrar el botón de eliminar en la primera fila también (por si estaba oculto)
                $('.patologia-item .btn-remove-patologia').show();
            }

            // === Agregar elementos dinámicamente ===
            $('#add-patologia').on('click', function() {
                addPatologiaRow();
            });

            // Eliminar elementos
            $(document).on('click', '.btn-remove-patologia', function() {
                if ($('.patologia-item').length > 1) {
                    $(this).closest('.patologia-item').remove();
                } else {
                    Swal.fire('Advertencia', 'Debe haber al menos una patología seleccionable', 'warning');
                }
            });

            // === Cargar datos de la cita al abrir modal ===
            $('#tablaPendientes').on('click', '.btn-atender', function() {
                var citaId = $(this).data('id');
                $('#cita_id').val(citaId);
                $('#formDiagnostico').attr('action', '/citas/' + citaId + '/diagnostico');

                $('#patologias-container').empty();

                // Agregar una fila base de patología con botón visible
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

                        // Poblar selects de patologías
                        populatePatologiaSelects();

                        // Cargar patologías existentes (si las hay)
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
                            // Seleccionar los valores correspondientes
                            $('.patologia-item').each(function(idx) {
                                let $select = $(this).find('select');
                                if (data.cita.patologias[idx]) {
                                    $select.val(data.cita.patologias[idx].id);
                                }
                            });
                        }

                        // Información de la cita
                        if (data.cita) {
                            $('#info_paciente').text(data.cita.paciente.nombre + ' ' + data.cita
                                .paciente.apellido);
                            $('#info_cedula').text(data.cita.paciente.cedula);
                            $('#info_fecha').text(new Date(data.cita.fecha_cita)
                                .toLocaleDateString());
                            $('#info_medico').text('Dr. ' + data.cita.medico.nombre + ' ' + data
                                .cita.medico.apellido);
                            $('#info_especialidad').text(data.cita.medico.especialidad.nombre);
                            $('#diagnostico_libre').val(data.cita.diagnostico_libre || '');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo cargar la información de la cita',
                            'error');
                    }
                });
                $('#modalAtender').modal('show');
            });

            // === Asignar Número de Historia ===
            $('#tablaPendientes').on('click', '.btn-asignar-historia', function() {
                var pacienteId = $(this).data('paciente-id');
                var pacienteNombre = $(this).data('paciente-nombre');
                var pacienteCedula = $(this).data('paciente-cedula');

                $('#historia_paciente_id').val(pacienteId);
                $('#historia_paciente_nombre').text(pacienteNombre);
                $('#historia_paciente_cedula').text(pacienteCedula);
                $('#numero_expediente').val('').removeClass('is-invalid');
                $('#historia_error').text('');

                $('#modalAsignarHistoria').modal('show');
            });

            $('#formAsignarHistoria').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitBtn = $('#btnGuardarHistoria');
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...');

                $.ajax({
                    url: "{{ route('expedientes.asignar') }}",
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#tablaPendientes').DataTable().ajax.reload(null, true);
                        $('#modalAsignarHistoria').modal('hide');
                        Swal.fire('Éxito', response.message, 'success');
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="bi bi-check-lg me-1"></i> Asignar Número');
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            if (errors && errors.numero_expediente) {
                                $('#numero_expediente').addClass('is-invalid');
                                $('#historia_error').text(errors.numero_expediente[0]);
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                Swal.fire('Error', xhr.responseJSON.message, 'error');
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            Swal.fire('Error', xhr.responseJSON.message, 'error');
                        } else {
                            Swal.fire('Error',
                                'Ocurrió un error al asignar el número de historia.',
                                'error');
                        }
                    }
                });
            });

            $('#modalAsignarHistoria').on('hidden.bs.modal', function() {
                $('#formAsignarHistoria')[0].reset();
                $('#numero_expediente').removeClass('is-invalid');
                $('#historia_error').text('');
                $('#btnGuardarHistoria').prop('disabled', false).html(
                    '<i class="bi bi-check-lg me-1"></i> Asignar Número');
            });

            // Resetear formulario al cerrar modal
            $('#modalAtender').on('hidden.bs.modal', function() {
                $('#formDiagnostico')[0].reset();
                $('#patologias-container').empty();
            });
        });
    </script>
@endpush
