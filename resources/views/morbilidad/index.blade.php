@extends('layouts.template')
@section('title', 'Reporte de Citas | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <div class="container py-4">
        <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Reporte de Citas</h3>
                    <a href="{{ route('morbilidad.pendientes') }}" class="btn btn-primary">
                        <i class="fas fa-clinic-medical me-1"></i> Citas por Atender
                    </a>
                </div>
             </div>
            </div>
            <div class="card-body">
                <form method="GET" id="filtroForm" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                        <x-searchable-select
                            name="especialidad_id"
                            id="especialidad_id"
                            :options="$especialidades->pluck('nombre', 'id')"
                            :selected="request('especialidad_id')"
                            placeholder="Todas las especialidades" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Tipo de Cita</label>
                        <select name="tipo_paciente" id="tipo_paciente" class="form-select shadow-none">
                            <option value="">Todos</option>
                            <option value="primera_vez" {{ request('tipo_paciente') == 'primera_vez' ? 'selected' : '' }}>Primera Vez</option>
                            <option value="control" {{ request('tipo_paciente') == 'control' ? 'selected' : '' }}>Sucesiva</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Estado de Cita</label>
                        <select name="estado" id="estado" class="form-select shadow-none">
                            <option value="">Todos</option>
                            <option value="Agendada" {{ request('estado') == 'Agendada' ? 'selected' : '' }}>Agendada</option>
                            <option value="Atendida" {{ request('estado') == 'Atendida' ? 'selected' : '' }}>Atendida</option>
                            <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Fecha cita desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Fecha cita hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="btnFiltrar" class="btn btn-primary w-100 shadow-sm">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="btnLimpiar" class="btn btn-secondary w-100 shadow-sm">
                            <i class="fas fa-undo me-1"></i> Limpiar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Fecha registro desde</label>
                        <input type="date" name="fecha_registro_desde" id="fecha_registro_desde" class="form-control" value="{{ request('fecha_registro_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Fecha registro hasta</label>
                        <input type="date" name="fecha_registro_hasta" id="fecha_registro_hasta" class="form-control" value="{{ request('fecha_registro_hasta') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="button" id="btnExcel" class="btn btn-success w-50 shadow-sm">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </button>
                            <button type="button" id="btnPdf" class="btn btn-danger w-50 shadow-sm">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive rounded shadow-sm border">
                    <table id="tablaMorbilidad" class="table table-bordered table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Paciente</th>
                                <th>Cédula</th>
                                <th>Fecha Cita</th>
                                <th>Especialidad</th>
                                <th>Médico</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('partials.modal-mostrar-cita', ['modalId' => 'modalMostrarCita', 'showPdf' => true])

    @include('partials.modal-editar-diagnostico')

    @include('layouts.footer')
@endsection

@push('scripts')
<link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

<script>
$(document).ready(function() {
    var table = $('#tablaMorbilidad').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('morbilidad.index') }}",
            type: 'GET',
            data: function(d) {
                d.especialidad_id = $('#especialidad_id').val();
                d.fecha_desde = $('#fecha_desde').val();
                d.fecha_hasta = $('#fecha_hasta').val();
                d.tipo_paciente = $('#tipo_paciente').val();
                d.estado = $('#estado').val();
                d.fecha_registro_desde = $('#fecha_registro_desde').val();
                d.fecha_registro_hasta = $('#fecha_registro_hasta').val();
            }
        },
        columns: [
            { data: 0, name: 'paciente' },
            { data: 1, name: 'cedula' },
            { data: 2, name: 'fecha_cita' },
            { data: 3, name: 'especialidad' },
            { data: 4, name: 'medico' },
            { data: 5, name: 'tipo' },
            { data: 6, name: 'estado' },
            { data: 7, name: 'fecha_registro' },
            { data: 8, name: 'accion', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            url: "{{ asset('vendor/datatables/es-ES.json') }}"
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[2, 'asc']]
    });

    $('#btnFiltrar').on('click', function() {
        table.ajax.reload();
    });

    $('#btnLimpiar').on('click', function() {
        $('#especialidad_id').val('');
        $('#tipo_paciente').val('');
        $('#estado').val('');
        $('#fecha_desde').val('');
        $('#fecha_hasta').val('');
        $('#fecha_registro_desde').val('');
        $('#fecha_registro_hasta').val('');
        table.ajax.reload();
    });

    $('#btnExcel').on('click', function() {
        var params = $.param({
            especialidad_id: $('#especialidad_id').val(),
            fecha_desde: $('#fecha_desde').val(),
            fecha_hasta: $('#fecha_hasta').val(),
            tipo_paciente: $('#tipo_paciente').val(),
            estado: $('#estado').val(),
            fecha_registro_desde: $('#fecha_registro_desde').val(),
            fecha_registro_hasta: $('#fecha_registro_hasta').val(),
            export_excel: 1
        });
        window.location.href = "{{ route('morbilidad.index') }}?" + params;
    });

    $('#btnPdf').on('click', function() {
        var params = $.param({
            especialidad_id: $('#especialidad_id').val(),
            fecha_desde: $('#fecha_desde').val(),
            fecha_hasta: $('#fecha_hasta').val(),
            tipo_paciente: $('#tipo_paciente').val(),
            estado: $('#estado').val(),
            fecha_registro_desde: $('#fecha_registro_desde').val(),
            fecha_registro_hasta: $('#fecha_registro_hasta').val(),
            export_pdf: 1
        });
        window.open("{{ route('morbilidad.index') }}?" + params, '_blank');
    });

    // Mostrar cita en modal
    $(document).on('click', '.btn-show-cita', async function() {
        const citaId = $(this).data('id');
        const modalElement = document.getElementById('modalMostrarCita');
        let modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalElement);
        }
        modalInstance.show();

        try {
            const response = await fetch(`/morbilidad/${citaId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const cita = await response.json();
            populateShowModal(cita, `/morbilidad/${citaId}/pdf`);
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos de la cita', 'error');
        }
    });

    $(document).on('click', '.btn-edit-cita', async function() {
        const id = $(this).data('id');
        try {
            const res = await fetch(`/diagnosticos/${id}/edit`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            window.patologiasDisponibles = data.patologias_disponibles || [];
            limpiarContenedoresEdicion();

            const cita = data.cita;
            $('#edit_id').val(cita.id);
            $('#edit_diagnostico_libre').val(cita.diagnostico_libre || '');

            $('#edit_info_paciente').text(`${cita.paciente.nombre} ${cita.paciente.apellido}`);
            $('#edit_info_cedula').text(cita.paciente.cedula);
            $('#edit_info_fecha').text(new Date(cita.fecha_cita).toLocaleDateString());
            $('#edit_info_medico').text(`Dr. ${cita.medico.nombre} ${cita.medico.apellido}`);
            $('#edit_info_especialidad').text(cita.medico.especialidad.nombre);

            if (cita.patologias && cita.patologias.length) {
                cita.patologias.forEach(pat => addEditPatologiaRow(pat.id));
            } else {
                addEditPatologiaRow(null);
            }

            document.getElementById('editForm').action = `/diagnosticos/${cita.id}?redirect_to=morbilidad.index`;
            new bootstrap.Modal(document.getElementById('modalEditarDiagnostico')).show();
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'No se pudo cargar la información para editar', 'error');
        }
    });
});
</script>
@endpush
