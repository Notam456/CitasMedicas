@extends('layouts.template')

@section('title', 'Gestión de Suspensiones (Médicos Inactivos) | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Gestión de Suspensiones (Médicos Inactivos)</h3>
            <div class="hstack gap-2">
                <a href="{{ route('medicos.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver a Médicos
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalSuspender">
                    <i class="bi bi-person-x-fill me-1"></i> Suspender Médico
                </button>
            </div>
        </div>

        <table class="table table-hover" id="tablaSuspensiones">
            <thead>
                <tr>
                    <th>Médico</th>
                    <th>Especialidad</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Médico Suplente</th>
                    <th>Motivo</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal Suspender Médico -->
    <div class="modal fade" id="modalSuspender" tabindex="-1" aria-labelledby="modalSuspenderLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSuspenderLabel"><i class="bi bi-person-x-fill me-2 text-danger"></i>Registrar Suspensión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="formSuspender" action="{{ route('suspensiones.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Especialidad -->
                            <div class="col-md-12 mb-3">
                                <label for="especialidad_filtro_id" class="form-label fw-bold">Especialidad</label>
                                <select id="especialidad_filtro_id" class="form-select" required>
                                    <option value="">Seleccione una especialidad</option>
                                    @foreach ($especialidades as $esp)
                                        <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Médico a suspender -->
                            <div class="col-md-12 mb-3">
                                <label for="medico_id" class="form-label fw-bold">Médico a suspender</label>
                                <select name="medico_id" id="medico_id" class="form-select" required disabled>
                                    <option value="">Seleccione primero una especialidad</option>
                                </select>
                            </div>

                            <!-- Fechas -->
                            <div class="col-md-6 mb-3">
                                <label for="fecha_inicio" class="form-label fw-bold">Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_fin" class="form-label fw-bold">Fecha de Fin</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required min="{{ date('Y-m-d') }}">
                            </div>

                            <!-- Médico Suplente -->
                            <div class="col-md-12 mb-3">
                                <label for="suplente_id" class="form-label fw-bold">Médico Suplente (Opcional)</label>
                                <select name="suplente_id" id="suplente_id" class="form-select" disabled>
                                    <option value="">Seleccione primero el médico y el rango de fechas</option>
                                </select>
                                <small class="text-muted d-block mt-1">Si no selecciona ningún suplente, las citas agendadas en este periodo de tiempo se cancelarán automáticamente.</small>
                            </div>

                            <!-- Motivo -->
                            <div class="col-md-12 mb-3">
                                <label for="motivo" class="form-label fw-bold">Motivo (Opcional)</label>
                                <textarea name="motivo" id="motivo" rows="3" class="form-control" placeholder="Escriba el motivo de la suspensión..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-person-dash-fill me-1"></i> Suspender Médico</button>
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
    // Inicializar DataTable
    const table = $('#tablaSuspensiones').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("suspensiones.index") }}',
        columns: [
            { data: 0, name: 'medico' },
            { data: 1, name: 'especialidad' },
            { data: 2, name: 'fecha_inicio' },
            { data: 3, name: 'fecha_fin' },
            { data: 4, name: 'suplente' },
            { data: 5, name: 'motivo' },
            { data: 6, name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[2, 'desc']]
    });

    // Inputs
    const selectEspecialidad = $('#especialidad_filtro_id');
    const selectMedico = $('#medico_id');
    const inputInicio = $('#fecha_inicio');
    const inputFin = $('#fecha_fin');
    const selectSuplente = $('#suplente_id');

    // Escuchar cambio en Especialidad para cargar médicos
    selectEspecialidad.on('change', async function() {
        const espId = $(this).val();
        selectMedico.html('<option value="">Seleccione primero una especialidad</option>').prop('disabled', true);
        selectSuplente.html('<option value="">Seleccione primero el médico y el rango de fechas</option>').prop('disabled', true);

        if (!espId) return;

        try {
            selectMedico.html('<option value="">Cargando médicos...</option>');
            const response = await fetch(`/api/especialidades/${espId}/medicos`);
            if (!response.ok) throw new Error('Error al obtener médicos');

            const medicos = await response.json();
            let options = '<option value="">Seleccione un médico</option>';
            medicos.forEach(m => {
                // El endpoint /api/especialidades/{id}/medicos puede devolver la opción "Cualquier médico" con id 'any'.
                // Para suspensión, solo permitimos suspender médicos reales (numéricos).
                if (m.id !== 'any') {
                    options += `<option value="${m.id}">${m.nombre} ${m.apellido}</option>`;
                }
            });

            selectMedico.html(options).prop('disabled', false);
        } catch (error) {
            console.error('Error:', error);
            selectMedico.html('<option value="">Error al cargar médicos</option>');
        }
    });

    // Función para actualizar médicos suplentes disponibles
    async function actualizarSuplentes() {
        const medicoId = selectMedico.val();
        const start = inputInicio.val();
        const end = inputFin.val();

        if (!medicoId || !start || !end) {
            selectSuplente.html('<option value="">Seleccione primero el médico y el rango de fechas</option>');
            selectSuplente.prop('disabled', true);
            return;
        }

        try {
            selectSuplente.prop('disabled', true);
            selectSuplente.html('<option value="">Cargando suplentes disponibles...</option>');

            const response = await fetch(`/api/medicos/${medicoId}/suplentes-disponibles?fecha_inicio=${start}&fecha_fin=${end}`);
            if (!response.ok) throw new Error('Error al obtener suplentes');

            const suplentes = await response.json();

            let options = '<option value="">Ninguno (Se cancelarán las citas agendadas)</option>';
            suplentes.forEach(s => {
                options += `<option value="${s.id}">Dr. ${s.nombre} ${s.apellido}</option>`;
            });

            selectSuplente.html(options);
            selectSuplente.prop('disabled', false);
        } catch (error) {
            console.error('Error:', error);
            selectSuplente.html('<option value="">Error al cargar suplentes</option>');
        }
    }

    // Escuchar cambios
    selectMedico.on('change', actualizarSuplentes);
    inputInicio.on('change', function() {
        // Ajustar el mínimo del fin
        inputFin.attr('min', inputInicio.val());
        actualizarSuplentes();
    });
    inputFin.on('change', actualizarSuplentes);

    // Enviar formulario con validaciones y confirmación SweetAlert
    $('#formSuspender').on('submit', async function(e) {
        e.preventDefault();

        const medicoId = selectMedico.val();
        const start = inputInicio.val();
        const end = inputFin.val();
        const suplenteId = selectSuplente.val();

        if (!medicoId || !start || !end) {
            Swal.fire('Atención', 'Por favor complete todos los campos obligatorios.', 'warning');
            return;
        }

        // Si no se selecciona suplente, verificar si hay citas para mostrar confirmación SweetAlert
        if (!suplenteId) {
            try {
                // Consultar número de citas activas
                const response = await fetch(`/api/medicos/${medicoId}/citas-activas-count?fecha_inicio=${start}&fecha_fin=${end}`);
                if (!response.ok) throw new Error('Error al consultar citas');

                const data = await response.json();

                if (data.count > 0) {
                    const confirmRes = await Swal.fire({
                        title: '¿Confirmar cancelación de citas?',
                        text: `El médico tiene ${data.count} cita(s) agendada(s) para este rango de fechas. Si no selecciona un suplente, se cancelarán automáticamente y se enviará una notificación a los administradores.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, cancelar y suspender',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirmRes.isConfirmed) {
                        return;
                    }
                }
            } catch (error) {
                console.error(error);
            }
        }

        // Enviar la suspensión vía AJAX
        try {
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espere mientras registramos la suspensión.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData(this);
            const storeRes = await fetch('{{ route("suspensiones.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const storeData = await storeRes.json();

            if (storeData.error) {
                Swal.fire('Error', storeData.message, 'error');
            } else {
                Swal.fire('¡Éxito!', storeData.message, 'success').then(() => {
                    // Cerrar modal y reiniciar formulario
                    $('#modalSuspender').modal('hide');
                    $('#formSuspender')[0].reset();
                    selectEspecialidad.val('').trigger('change');
                    table.ajax.reload();
                });
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'No se pudo completar el registro de la suspensión.', 'error');
        }
    });

    // Acción de reactivar médico
    $(document).on('click', '.btn-reactivar', async function() {
        const id = $(this).data('id');

        const confirmRes = await Swal.fire({
            title: '¿Reactivar médico?',
            text: 'Esta acción levantará la suspensión del médico y restaurará la disponibilidad de sus cupos para las fechas previamente establecidas.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, reactivar',
            cancelButtonText: 'Cancelar'
        });

        if (confirmRes.isConfirmed) {
            try {
                Swal.fire({
                    title: 'Procesando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch(`/suspensiones/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.error) {
                    Swal.fire('Error', data.message, 'error');
                } else {
                    Swal.fire('¡Reactivado!', data.message, 'success').then(() => {
                        table.ajax.reload();
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudo completar la reactivación del médico.', 'error');
            }
        }
    });
});
</script>
@endpush
