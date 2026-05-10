@extends('layouts.template')
@section('title', 'Reporte de Morbilidad | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h3 class="mb-0">Reporte de Morbilidad</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('morbilidad.index') }}" id="filtroForm" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-stethoscope"></i></span>
                            <select name="especialidad_id" class="form-select shadow-none">
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
                        <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-uppercase text-muted">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" name="export_excel" value="1" class="btn btn-success w-50 shadow-sm">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </button>
                            <button type="submit" name="export_pdf" value="1" class="btn btn-danger w-50 shadow-sm">
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
                            @foreach ($morbilidades as $m)
                            <tr>
                                <td>{{ $m->paciente_nombre }} {{ $m->paciente_apellido }}</td>
                                <td>{{ $m->paciente_cedula }}</td>
                                <td>{{ \Carbon\Carbon::parse($m->fecha_cita)->format('d/m/Y') }}</td>
                                <td>{{ $m->especialidad_nombre }}</td>
                                <td>Dr. {{ $m->medico_nombre }} {{ $m->medico_apellido }}</td>
                                <td>{{ $m->diagnostico }}</td>
                                <td>{{ $m->morbilidad_observaciones }}</td>
                            </tr>
                            @endforeach
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
        $('#tablaMorbilidad').DataTable({
            language: {
                url: "{{ asset('vendor/datatables/es-ES.json') }}" 
            },
            pageLength: 10,      
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
            order: [[2, 'desc']]
        });
    });
</script>
@endpush