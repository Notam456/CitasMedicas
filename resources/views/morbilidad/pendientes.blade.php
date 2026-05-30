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
                <a href="{{ route('diagnosticos.index') }}" target="_blank" class="btn btn-secondary me-2">
                    <i class="bi bi-list-ul me-1"></i> Gestionar Citas Atendidas
                </a>
                <a href="{{ route('morbilidad.index') }}" class="btn btn-primary">
                    <i class="bi bi-printer me-1"></i> Reporte de Morbilidad
                </a>
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
                            @foreach($especialidades as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Fecha desde</label>
                    <input type="date" id="fecha_desde" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Fecha hasta</label>
                    <input type="date" id="fecha_hasta" class="form-control">
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
                <h5 class="modal-title">Atender Cita (Registrar Diagnóstico, Tratamiento y Referencia Médica)</h5>
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
                                    <p class="mb-0"><strong>Especialidad:</strong> <span id="info_especialidad"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnóstico libre -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Diagnóstico libre (impresión diagnóstica)</label>
                        <textarea name="diagnostico_libre" id="diagnostico_libre" class="form-control" rows="2" placeholder="Escriba aquí el diagnóstico general..."></textarea>
                    </div>

                    <!-- Patologías múltiples -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Patologías diagnosticadas</label>
                        <div id="patologias-container">
                            <div class="input-group mb-2 patologia-item">
                                <select name="patologias[]" class="form-select select-patologia">
                                    <option value="">Seleccione una patología</option>
                                </select>
                                <button type="button" class="btn btn-outline-danger btn-remove-patologia" style="display: none;"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        <button type="button" id="add-patologia" class="btn btn-sm btn-secondary mt-1"><i class="bi bi-plus-circle"></i> Agregar otra patología</button>
                    </div>

                    <!-- Medicamentos recetados múltiples -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Medicamentos recetados</label>
                        <div id="medicamentos-container">
                            <!-- La primera fila se generará dinámicamente al cargar el modal -->
                        </div>
                        <button type="button" id="add-medicamento" class="btn btn-sm btn-secondary mt-1"><i class="bi bi-plus-circle"></i> Agregar otro medicamento</button>
                    </div>

                    <!-- Referencias médicas múltiples -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Referencias a especialidades</label>
                        <div id="referencias-container">
                            <!-- La primera fila se generará dinámicamente al cargar el modal -->
                        </div>
                        <button type="button" id="add-referencia" class="btn btn-sm btn-secondary mt-1"><i class="bi bi-plus-circle"></i> Agregar otra referencia</button>
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
                d.fecha_desde = $('#fecha_desde').val();
                d.fecha_hasta = $('#fecha_hasta').val();
            }
        },
        columns: [
            { data: 0, name: 'paciente' },
            { data: 1, name: 'cedula' },
            { data: 2, name: 'fecha_cita' },
            { data: 3, name: 'especialidad' },
            { data: 4, name: 'medico' },
            { data: 5, name: 'accion', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
        pageLength: 10,
        order: [[2, 'asc']]
    });

    $('#btnFiltrar').on('click', function() { table.ajax.reload(); });
    $('#btnLimpiar').on('click', function() {
        $('#especialidad_filtro').val('');
        $('#fecha_desde').val('');
        $('#fecha_hasta').val('');
        table.ajax.reload();
    });

    // Contadores para índices únicos
    let medicamentoCounter = 0;
    let referenciaCounter = 0;

    // === Funciones para medicamentos ===
    function updateMedicamentoRequired(container) {
        const selectMed = container.find('.select-medicamento');
        const dosisInput = container.find('.dosis-input');
        const duracionInput = container.find('.duracion-input');
        const indicacionesInput = container.find('.indicaciones-input');
        const hasValue = selectMed.val() !== '';
        dosisInput.prop('required', hasValue);
        duracionInput.prop('required', hasValue);
        indicacionesInput.prop('required', hasValue);
    }

    function bindMedicamentoEvents(container) {
        container.find('.select-medicamento').off('change').on('change', function() {
            updateMedicamentoRequired(container);
        });
    }

    function createMedicamentoRow(med = null, idx = null) {
        const index = (idx !== null) ? idx : medicamentoCounter++;
        const row = $(`
            <div class="card mb-2 medicamento-item" data-idx="${index}">
                <div class="card-body py-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small">Medicamento</label>
                            <select name="medicamentos[${index}][id]" class="form-select select-medicamento">
                                <option value="">Seleccione un medicamento</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Dosis (mg)</label>
                            <input type="number" step="any" inputmode="numeric" name="medicamentos[${index}][dosis]" class="form-control dosis-input" placeholder="Ej: 500" value="${med ? (med.dosis || '') : ''}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Duración (días)</label>
                            <input type="number" step="1" inputmode="numeric" name="medicamentos[${index}][duracion]" class="form-control duracion-input" placeholder="Ej: 7" value="${med ? (med.duracion || '') : ''}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Indicaciones</label>
                            <input type="text" name="medicamentos[${index}][indicaciones]" class="form-control indicaciones-input" placeholder="Tomar después de comida" value="${med ? (med.indicaciones || '') : ''}">
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-outline-danger btn-remove-medicamento"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        // Llenar el select de medicamentos si existen opciones
        if (window.medicamentosList) {
            const select = row.find('.select-medicamento');
            select.empty().append('<option value="">Seleccione un medicamento</option>');
            $.each(window.medicamentosList, function(i, medItem) {
                select.append(`<option value="${medItem.id}" ${(med && med.medicamento_id == medItem.id) ? 'selected' : ''}>${medItem.nombre}</option>`);
            });
        }
        bindMedicamentoEvents(row);
        updateMedicamentoRequired(row);
        return row;
    }

    // === Funciones para referencias ===
    function updateReferenciaRequired(container) {
        const selectEsp = container.find('.select-especialidad');
        const observacionesInput = container.find('.observaciones-input');
        const hasValue = selectEsp.val() !== '';
        observacionesInput.prop('required', hasValue);
    }

    function bindReferenciaEvents(container) {
        container.find('.select-especialidad').off('change').on('change', function() {
            updateReferenciaRequired(container);
        });
    }

    function createReferenciaRow(ref = null, idx = null) {
        const index = (idx !== null) ? idx : referenciaCounter++;
        const row = $(`
            <div class="card mb-2 referencia-item" data-idx="${index}">
                <div class="card-body py-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small">Especialidad</label>
                            <select name="referencias[${index}][especialidad_id]" class="form-select select-especialidad">
                                <option value="">Seleccione una especialidad</option>
                                @foreach($especialidades as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Observaciones</label>
                            <input type="text" name="referencias[${index}][observaciones]" class="form-control observaciones-input" placeholder="Motivo de referencia" value="${ref ? (ref.observaciones || '') : ''}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Fecha referencia</label>
                            <input type="date" name="referencias[${index}][fecha_referencia]" class="form-control" value="${ref ? (ref.fecha_referencia ? ref.fecha_referencia.split('T')[0] : '') : ''}">
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-outline-danger btn-remove-referencia"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        // Si hay especialidades predefinidas (en el DOM ya están), se conservan.
        bindReferenciaEvents(row);
        updateReferenciaRequired(row);
        return row;
    }

    // === Agregar elementos dinámicamente ===
    $('#add-medicamento').on('click', function() {
        const newRow = createMedicamentoRow();
        $('#medicamentos-container').append(newRow);
    });

    $('#add-referencia').on('click', function() {
        const newRow = createReferenciaRow();
        $('#referencias-container').append(newRow);
    });

    // Eliminar elementos
    $(document).on('click', '.btn-remove-medicamento', function() {
        if ($('.medicamento-item').length > 1) {
            $(this).closest('.medicamento-item').remove();
        } else {
            Swal.fire('Advertencia', 'Debe haber al menos un medicamento (puede dejarlo vacío)', 'warning');
        }
    });

    $(document).on('click', '.btn-remove-referencia', function() {
        if ($('.referencia-item').length > 1) {
            $(this).closest('.referencia-item').remove();
        } else {
            Swal.fire('Advertencia', 'Debe haber al menos una referencia (puede dejarla vacía)', 'warning');
        }
    });

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

        // Reiniciar contadores y contenedores
        medicamentoCounter = 0;
        referenciaCounter = 0;
        $('#medicamentos-container').empty();
        $('#referencias-container').empty();
        $('#patologias-container').empty();

        // Agregar una fila base de patología
        $('#patologias-container').append(`
            <div class="input-group mb-2 patologia-item">
                <select name="patologias[]" class="form-select select-patologia">
                    <option value="">Seleccione una patología</option>
                </select>
                <button type="button" class="btn btn-outline-danger btn-remove-patologia" style="display: none;"><i class="bi bi-trash"></i></button>
            </div>
        `);

        $.ajax({
            url: '/diagnosticos/' + citaId + '/edit',
            method: 'GET',
            success: function(data) {
                // Guardar listas globales para usar en createMedicamentoRow
                window.medicamentosList = data.medicamentos || [];
                window.patologiasList = data.patologias_disponibles || [];

                // Llenar selects de patologías
                $('.select-patologia').each(function() {
                    let $select = $(this);
                    $select.empty().append('<option value="">Seleccione una patología</option>');
                    $.each(window.patologiasList, function(i, pat) {
                        $select.append('<option value="'+pat.id+'">'+pat.nombre+'</option>');
                    });
                });

                // Cargar patologías existentes
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
                        let $select = $('#patologias-container .patologia-item:last select');
                        $select.empty().append('<option value="">Seleccione una patología</option>');
                        $.each(window.patologiasList, function(i, patItem) {
                            $select.append(`<option value="${patItem.id}" ${patItem.id == pat.id ? 'selected' : ''}>${patItem.nombre}</option>`);
                        });
                    });
                }

                // Medicamentos existentes
                if (data.cita.tratamientos && data.cita.tratamientos.length) {
                    $.each(data.cita.tratamientos, function(i, tr) {
                        const row = createMedicamentoRow({
                            medicamento_id: tr.medicamento_id,
                            dosis: tr.dosis,
                            duracion: tr.duracion,
                            indicaciones: tr.indicaciones
                        }, i);
                        $('#medicamentos-container').append(row);
                    });
                    medicamentoCounter = data.cita.tratamientos.length;
                } else {
                    $('#medicamentos-container').append(createMedicamentoRow());
                }

                // Referencias existentes
                if (data.cita.referencias && data.cita.referencias.length) {
                    $.each(data.cita.referencias, function(i, ref) {
                        const row = createReferenciaRow(ref, i);
                        $('#referencias-container').append(row);
                    });
                    referenciaCounter = data.cita.referencias.length;
                } else {
                    $('#referencias-container').append(createReferenciaRow());
                }

                // Información de la cita
                if (data.cita) {
                    $('#info_paciente').text(data.cita.paciente.nombre + ' ' + data.cita.paciente.apellido);
                    $('#info_cedula').text(data.cita.paciente.cedula);
                    $('#info_fecha').text(new Date(data.cita.fecha_cita).toLocaleDateString());
                    $('#info_medico').text('Dr. ' + data.cita.medico.nombre + ' ' + data.cita.medico.apellido);
                    $('#info_especialidad').text(data.cita.medico.especialidad.nombre);
                    $('#diagnostico_libre').val(data.cita.diagnostico_libre || '');
                }
            },
            error: function() { Swal.fire('Error', 'No se pudo cargar la información de la cita', 'error'); }
        });
        $('#modalAtender').modal('show');
    });

    // Resetear formulario al cerrar modal
    $('#modalAtender').on('hidden.bs.modal', function() {
        $('#formDiagnostico')[0].reset();
        medicamentoCounter = 0;
        referenciaCounter = 0;
    });
});
</script>
@endpush