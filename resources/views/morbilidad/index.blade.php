@extends('layouts.template')
@section('title', 'Reporte de Morbilidad | SAGECIM')

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
            <div class="card-header bg-white py-3">
                <h3 class="mb-0">Reporte de Morbilidad</h3>
            </div>
            <div class="card-body">
                <form method="GET" id="filtroForm" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-stethoscope"></i></span>
                            <select name="especialidad_id" id="especialidad_id" class="form-select shadow-none">
                                <option value="">Todas</option>
                                @foreach ($especialidades as $e)
                                    <option value="{{ $e->id }}" {{ request('especialidad_id') == $e->id ? 'selected' : '' }}>
                                        {{ $e->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Fecha desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="btnFiltrar" class="btn btn-primary w-100 shadow-sm">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-3">
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
                                <th>Diagnóstico</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables llenará el cuerpo dinámicamente -->
                        </tbody>
                    </table>
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
            }
        },
        columns: [
            { data: 0, name: 'paciente' },
            { data: 1, name: 'cedula' },
            { data: 2, name: 'fecha_cita' },
            { data: 3, name: 'especialidad' },
            { data: 4, name: 'medico' },
            { data: 5, name: 'diagnostico' },
            { data: 6, name: 'observaciones' }
        ],
        language: {
            url: "{{ asset('vendor/datatables/es-ES.json') }}"
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[2, 'desc']]
    });

    // Refrescar tabla al hacer clic en Filtrar
    $('#btnFiltrar').on('click', function() {
        table.ajax.reload();
    });

    // Exportar Excel (recargar página con parámetros de filtro)
    $('#btnExcel').on('click', function() {
        var params = $.param({
            especialidad_id: $('#especialidad_id').val(),
            fecha_desde: $('#fecha_desde').val(),
            fecha_hasta: $('#fecha_hasta').val(),
            export_excel: 1
        });
        window.location.href = "{{ route('morbilidad.index') }}?" + params;
    });

    // Exportar PDF
    $('#btnPdf').on('click', function() {
        var params = $.param({
            especialidad_id: $('#especialidad_id').val(),
            fecha_desde: $('#fecha_desde').val(),
            fecha_hasta: $('#fecha_hasta').val(),
            export_pdf: 1
        });
        window.location.href = "{{ route('morbilidad.index') }}?" + params;
    });
});
</script>
@endpush