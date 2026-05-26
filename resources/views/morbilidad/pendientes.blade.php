@extends('layouts.template')
@section('title', 'Morbilidad - Citas Pendientes del Día | SAGECIM')

@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Citas del Día ({{ now()->format('d/m/Y') }})</h3>
            <div>
                <a href="{{ route('diagnosticos.index') }}" target="_blank" class="btn btn-secondary me-2">
                    <i class="bi bi-list-ul me-1"></i> Gestionar Diagnósticos
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Registrar Diagnóstico</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formDiagnostico" method="POST">
                @csrf
                <input type="hidden" name="cita_id" id="cita_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Patología (opcional)</label>
                        <select name="patologia_id" id="patologia_id" class="form-select">
                            <option value="">Seleccione</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Diagnóstico libre</label>
                        <textarea name="diagnostico_libre" id="diagnostico_libre" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="asistio" value="1" class="form-check-input" id="asistio">
                        <label class="form-check-label" for="asistio">Paciente asistió</label>
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

    $('#btnFiltrar').on('click', function() {
        table.ajax.reload();
    });

    $('#btnLimpiar').on('click', function() {
        $('#especialidad_filtro').val('');
        table.ajax.reload();
    });

    $('#tablaPendientes').on('click', '.btn-atender', function() {
        var citaId = $(this).data('id');
        $('#cita_id').val(citaId);
        
        // Actualizar action del formulario dinámicamente
        $('#formDiagnostico').attr('action', '/citas/' + citaId + '/diagnostico');
        
        // Cargar patologías
        $.ajax({
            url: '/api/patologias/por-cita/' + citaId,
            method: 'GET',
            success: function(data) {
                var select = $('#patologia_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(i, pat) {
                    select.append('<option value="'+pat.id+'">'+pat.nombre+'</option>');
                });
            },
            error: function() {
                Swal.fire('Error', 'No se pudieron cargar las patologías', 'error');
            }
        });
        
        $('#modalAtender').modal('show');
    });
});
</script>
@endpush