@extends('layouts.template')
@section('title', 'Lista de Citas Atendidas | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Citas Atendidas - Diagnósticos</h3>
        </div>

        <!-- Filtros -->
        <div class="row g-3 mb-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-stethoscope"></i></span>
                    <select id="especialidad_filtro" class="form-select shadow-none">
                        <option value="">Todas</option>
                        @foreach($especialidades ?? [] as $e)
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

        <table class="table table-hover" id="tablaDiagnosticos">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Cédula</th>
                    <th>Fecha Cita</th>
                    <th>Especialidad</th>
                    <th>Médico</th>
                    <th>Diagnóstico</th>
                    <th>Estado</th>
                    <th>Registrado por</th>
                    <th>Fecha Registro</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal Editar Diagnóstico (cita completa) -->
    <div class="modal fade" id="modalEditarDiagnostico" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title">Editar Cita (Diagnóstico, Tratamiento y Referencia Médica)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST" id="editForm">
                    @csrf @method('PUT')
                    <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
                        <input type="hidden" id="edit_id" name="id">

                        <!-- Información de la cita -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-primary">Información de la Cita</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Paciente:</strong> <span id="edit_info_paciente"></span></p>
                                        <p class="mb-1"><strong>Cédula:</strong> <span id="edit_info_cedula"></span></p>
                                        <p class="mb-0"><strong>Fecha de cita:</strong> <span id="edit_info_fecha"></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Médico:</strong> <span id="edit_info_medico"></span></p>
                                        <p class="mb-0"><strong>Especialidad:</strong> <span
                                                id="edit_info_especialidad"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnóstico libre -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Diagnóstico libre (impresión diagnóstica)</label>
                            <textarea name="diagnostico_libre" id="edit_diagnostico_libre" class="form-control"
                                rows="2"></textarea>
                        </div>

                        <!-- Patologías múltiples -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Patologías diagnosticadas</label>
                            <div id="edit_patologias_container"></div>
                            <button type="button" class="btn btn-sm btn-secondary mt-1" id="edit_add_patologia"><i
                                    class="bi bi-plus-circle"></i> Agregar patología</button>
                        </div>

                        <!-- Medicamentos recetados múltiples -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Medicamentos recetados</label>
                            <div id="edit_medicamentos_container"></div>
                            <button type="button" class="btn btn-sm btn-secondary mt-1" id="edit_add_medicamento"><i
                                    class="bi bi-plus-circle"></i> Agregar medicamento</button>
                        </div>

                        <!-- Referencias médicas múltiples -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Referencias a especialidades</label>
                            <div id="edit_referencias_container"></div>
                            <button type="button" class="btn btn-sm btn-secondary mt-1" id="edit_add_referencia"><i
                                    class="bi bi-plus-circle"></i> Agregar referencia</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Mostrar Diagnóstico -->
    <div class="modal fade" id="modalShowDiagnostico" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title">Detalles de la Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title text-primary">Información de la Cita</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Paciente:</strong> <span id="show_paciente"></span></p>
                                    <p><strong>Cédula:</strong> <span id="show_cedula"></span></p>
                                    <p><strong>Fecha Cita:</strong> <span id="show_fecha_cita"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Médico:</strong> <span id="show_medico"></span></p>
                                    <p><strong>Especialidad:</strong> <span id="show_especialidad"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p><strong>Diagnóstico libre:</strong> <span id="show_diagnostico_libre"></span></p>
                    <p><strong>Patologías:</strong> <span id="show_patologias"></span></p>
                    <p><strong>Medicamentos recetados:</strong></p>
                    <div id="show_medicamentos_list"></div>
                    <p><strong>Referencias:</strong></p>
                    <div id="show_referencias_list"></div>
                    <p><strong>Atendido por:</strong> <span id="show_atendido_por"></span></p>
                    <p><strong>Fecha registro:</strong> <span id="show_fecha_registro"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
@endsection

@push('scripts')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
    <script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var table = $('#tablaDiagnosticos').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("diagnosticos.index") }}',
                    data: function (d) {
                        d.especialidad_id = $('#especialidad_filtro').val();
                        d.fecha_desde = $('#fecha_desde').val();
                        d.fecha_hasta = $('#fecha_hasta').val();
                    }
                },
                columnDefs: [
                    {
                        targets: 1,      
                        className: "text-nowrap"
                    }
                ],
                columns: [
                    { data: 0, name: 'paciente' },
                    { data: 1, name: 'cedula' },
                    { data: 2, name: 'fecha_cita' },
                    { data: 3, name: 'especialidad' },
                    { data: 4, name: 'medico' },
                    { data: 5, name: 'diagnostico' },
                    { data: 6, name: 'estado' },
                    { data: 7, name: 'user' },
                    { data: 8, name: 'fecha_registro' },
                    { data: 9, name: 'action', orderable: false, searchable: false, className: 'text-end' }
                ],
                language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
                pageLength: 10,
                order: [[2, 'desc']]

            });

            $('#btnFiltrar').on('click', function () { table.ajax.reload(); });
            $('#btnLimpiar').on('click', function () {
                $('#especialidad_filtro').val('');
                $('#fecha_desde').val('');
                $('#fecha_hasta').val('');
                table.ajax.reload();
            });
        });

        // Variables globales para el modal de edición
        let patologiasDisponibles = [];
        let medicamentosDisponibles = [];
        let especialidadesDisponibles = [];
        let editMedicamentoCounter = 0;
        let editReferenciaCounter = 0;

        // ---------- Patologías ----------
        function addEditPatologiaRow(selectedId = null) {
            const container = $('#edit_patologias_container');
            const row = $(`
            <div class="input-group mb-2 patologia-edit-item">
                <select name="patologias[]" class="form-select">
                    <option value="">Seleccione una patología</option>
                </select>
                <button type="button" class="btn btn-outline-danger btn-remove-edit-patologia"><i class="bi bi-trash"></i></button>
            </div>
        `);
            const select = row.find('select');
            patologiasDisponibles.forEach(pat => {
                select.append(`<option value="${pat.id}" ${pat.id == selectedId ? 'selected' : ''}>${pat.nombre}</option>`);
            });
            container.append(row);
        }

        // ---------- Medicamentos con required condicional e índices explícitos ----------
        function updateEditMedicamentoRequired(container) {
            const selectMed = container.find('.select-medicamento-edit');
            const dosisInput = container.find('.dosis-input');
            const duracionInput = container.find('.duracion-input');
            const indicacionesInput = container.find('.indicaciones-input');
            const hasValue = selectMed.val() !== '';
            dosisInput.prop('required', hasValue);
            duracionInput.prop('required', hasValue);
            indicacionesInput.prop('required', hasValue);
        }

        function bindEditMedicamentoEvents(container) {
            container.find('.select-medicamento-edit').off('change').on('change', function () {
                updateEditMedicamentoRequired(container);
            });
        }

        function createEditMedicamentoRow(med = null, idx = null) {
            const index = (idx !== null) ? idx : editMedicamentoCounter++;
            const row = $(`
            <div class="card mb-2 medicamento-edit-item" data-idx="${index}">
                <div class="card-body py-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small">Medicamento</label>
                            <select name="medicamentos[${index}][id]" class="form-select select-medicamento-edit">
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
                            <input type="text" name="medicamentos[${index}][indicaciones]" class="form-control indicaciones-input" placeholder="Indicaciones" value="${med ? (med.indicaciones || '') : ''}">
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-outline-danger btn-remove-edit-medicamento"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `);
            const select = row.find('.select-medicamento-edit');
            medicamentosDisponibles.forEach(medic => {
                select.append(`<option value="${medic.id}" ${(med && med.medicamento_id == medic.id) ? 'selected' : ''}>${medic.nombre}</option>`);
            });
            bindEditMedicamentoEvents(row);
            updateEditMedicamentoRequired(row);
            return row;
        }

        // ---------- Referencias con required condicional e índices explícitos ----------
        function updateEditReferenciaRequired(container) {
            const selectEsp = container.find('select[name$="[especialidad_id]"]');
            const observacionesInput = container.find('input[name$="[observaciones]"]');
            const hasValue = selectEsp.val() !== '';
            observacionesInput.prop('required', hasValue);
        }

        function bindEditReferenciaEvents(container) {
            container.find('select[name$="[especialidad_id]"]').off('change').on('change', function () {
                updateEditReferenciaRequired(container);
            });
        }

        function createEditReferenciaRow(ref = null, idx = null) {
            const index = (idx !== null) ? idx : editReferenciaCounter++;
            const row = $(`
            <div class="card mb-2 referencia-edit-item" data-idx="${index}">
                <div class="card-body py-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small">Especialidad</label>
                            <select name="referencias[${index}][especialidad_id]" class="form-select">
                                <option value="">Seleccione una especialidad</option>
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
                            <button type="button" class="btn btn-outline-danger btn-remove-edit-referencia"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `);
            const select = row.find('select');
            especialidadesDisponibles.forEach(esp => {
                select.append(`<option value="${esp.id}" ${(ref && ref.especialidad_id == esp.id) ? 'selected' : ''}>${esp.nombre}</option>`);
            });
            bindEditReferenciaEvents(row);
            updateEditReferenciaRequired(row);
            return row;
        }

        // ---------- Limpiar contenedores ----------
        function limpiarContenedoresEdicion() {
            $('#edit_patologias_container').empty();
            $('#edit_medicamentos_container').empty();
            $('#edit_referencias_container').empty();
            editMedicamentoCounter = 0;
            editReferenciaCounter = 0;
        }

        // ---------- Evento Editar ----------
        document.addEventListener('click', async (e) => {
            const btnEdit = e.target.closest('.btn-edit');
            const btnShow = e.target.closest('.btn-show');

            if (btnEdit) {
                const id = btnEdit.dataset.id;
                try {
                    const res = await fetch(`/diagnosticos/${id}/edit`, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    patologiasDisponibles = data.patologias_disponibles || [];
                    medicamentosDisponibles = data.medicamentos || [];
                    especialidadesDisponibles = data.especialidades || [];

                    limpiarContenedoresEdicion();

                    const cita = data.cita;
                    $('#edit_id').val(cita.id);
                    $('#edit_diagnostico_libre').val(cita.diagnostico_libre || '');

                    $('#edit_info_paciente').text(`${cita.paciente.nombre} ${cita.paciente.apellido}`);
                    $('#edit_info_cedula').text(cita.paciente.cedula);
                    $('#edit_info_fecha').text(new Date(cita.fecha_cita).toLocaleDateString());
                    $('#edit_info_medico').text(`Dr. ${cita.medico.nombre} ${cita.medico.apellido}`);
                    $('#edit_info_especialidad').text(cita.medico.especialidad.nombre);

                    // Patologías existentes
                    if (cita.patologias && cita.patologias.length) {
                        cita.patologias.forEach(pat => addEditPatologiaRow(pat.id));
                    } else {
                        addEditPatologiaRow();
                    }

                    // Medicamentos existentes
                    if (cita.tratamientos && cita.tratamientos.length) {
                        cita.tratamientos.forEach((tr, idx) => {
                            const row = createEditMedicamentoRow({
                                medicamento_id: tr.medicamento_id,
                                dosis: tr.dosis,
                                duracion: tr.duracion,
                                indicaciones: tr.indicaciones
                            }, idx);
                            $('#edit_medicamentos_container').append(row);
                        });
                        editMedicamentoCounter = cita.tratamientos.length;
                    } else {
                        $('#edit_medicamentos_container').append(createEditMedicamentoRow());
                    }

                    // Referencias existentes
                    if (cita.referencias && cita.referencias.length) {
                        cita.referencias.forEach((ref, idx) => {
                            const row = createEditReferenciaRow(ref, idx);
                            $('#edit_referencias_container').append(row);
                        });
                        editReferenciaCounter = cita.referencias.length;
                    } else {
                        $('#edit_referencias_container').append(createEditReferenciaRow());
                    }

                    document.getElementById('editForm').action = `/diagnosticos/${cita.id}`;
                    new bootstrap.Modal(document.getElementById('modalEditarDiagnostico')).show();
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo cargar la información para editar', 'error');
                }
            }

            // ---------- Evento Mostrar ----------
            if (btnShow) {
                const id = btnShow.dataset.id;
                try {
                    const res = await fetch(`/diagnosticos/${id}`, { headers: { 'Accept': 'application/json' } });
                    const cita = await res.json();

                    document.getElementById('show_paciente').innerText = `${cita.paciente.nombre} ${cita.paciente.apellido}`;
                    document.getElementById('show_cedula').innerText = cita.paciente.cedula;
                    document.getElementById('show_fecha_cita').innerText = new Date(cita.fecha_cita).toLocaleDateString();
                    document.getElementById('show_medico').innerText = `Dr. ${cita.medico.nombre} ${cita.medico.apellido}`;
                    document.getElementById('show_especialidad').innerText = cita.medico.especialidad.nombre;
                    document.getElementById('show_diagnostico_libre').innerText = cita.diagnostico_libre || 'Ninguno';
                    document.getElementById('show_atendido_por').innerText = cita.atendido_por ? cita.atendido_por.name : 'No especificado';
                    document.getElementById('show_fecha_registro').innerText = new Date(cita.created_at).toLocaleString();

                    const patologiasSpan = document.getElementById('show_patologias');
                    if (cita.patologias && cita.patologias.length) {
                        patologiasSpan.innerText = cita.patologias.map(p => p.nombre).join(', ');
                    } else {
                        patologiasSpan.innerText = 'Ninguna';
                    }

                    const medicamentosDiv = document.getElementById('show_medicamentos_list');
                    medicamentosDiv.innerHTML = '';
                    if (cita.tratamientos && cita.tratamientos.length) {
                        const ul = document.createElement('ul');
                        cita.tratamientos.forEach(tr => {
                            const li = document.createElement('li');
                            li.innerHTML = `<strong>${tr.medicamento.nombre}</strong> - Dosis (mg): ${tr.dosis || 'N/E'} - Duración (Días): ${tr.duracion || 'N/E'} - Indicaciones: ${tr.indicaciones || 'N/E'}`;
                            ul.appendChild(li);
                        });
                        medicamentosDiv.appendChild(ul);
                    } else {
                        medicamentosDiv.innerText = 'Ninguno';
                    }

                    const referenciasDiv = document.getElementById('show_referencias_list');
                    referenciasDiv.innerHTML = '';
                    if (cita.referencias && cita.referencias.length) {
                        const ul = document.createElement('ul');
                        cita.referencias.forEach(ref => {
                            const li = document.createElement('li');
                            li.innerHTML = `<strong>${ref.especialidad.nombre}</strong> - Obs: ${ref.observaciones || 'Ninguna'} - Fecha: ${ref.fecha_referencia ? new Date(ref.fecha_referencia).toLocaleDateString() : 'No definida'}`;
                            ul.appendChild(li);
                        });
                        referenciasDiv.appendChild(ul);
                    } else {
                        referenciasDiv.innerText = 'Ninguna';
                    }

                    new bootstrap.Modal(document.getElementById('modalShowDiagnostico')).show();
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo cargar la información', 'error');
                }
            }
        });

        // Eventos dinámicos para agregar/eliminar en el modal de edición
        $(document).on('click', '#edit_add_patologia', function () { addEditPatologiaRow(); });
        $(document).on('click', '.btn-remove-edit-patologia', function () { $(this).closest('.patologia-edit-item').remove(); });
        $(document).on('click', '#edit_add_medicamento', function () {
            $('#edit_medicamentos_container').append(createEditMedicamentoRow());
        });
        $(document).on('click', '.btn-remove-edit-medicamento', function () { $(this).closest('.medicamento-edit-item').remove(); });
        $(document).on('click', '#edit_add_referencia', function () {
            $('#edit_referencias_container').append(createEditReferenciaRow());
        });
        $(document).on('click', '.btn-remove-edit-referencia', function () { $(this).closest('.referencia-edit-item').remove(); });

        // Limpiar al cerrar modal de edición
        $('#modalEditarDiagnostico').on('hidden.bs.modal', function () {
            limpiarContenedoresEdicion();
        });
    </script>
@endpush