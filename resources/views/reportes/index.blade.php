@extends('layouts.template')
@section('title', 'Reportes | SAGECIM')
@include('layouts.sidebar')
@section('content')
@include('layouts.navbar')

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <div class="bg-light rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0">Médicos por Especialidad</h5>
                    <i class="bi bi-printer fa-2x text-primary"></i>
                </div>
                <p class="mb-3">Listado de médicos con opcion de filtro por especialidad.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMedicosEspecialidad">
                        Generar Reporte
                    </button>
                    
                    <a href="{{ route('reportes.medicos_excel') }}" class="btn btn-success">
                        Exportar a Excel
                    </a>
                </div>
            </div>
        </div>
        <!--  -->
    </div>
</div>

<!-- pop up modal -->
<div class="modal fade" id="modalMedicosEspecialidad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtro por Especialidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reportes.medicos_especialidad') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="especialidad_id" class="form-label">Especialidad</label>
                        <select name="especialidad_id" id="especialidad_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($especialidades as $e)
                                <option value="{{ $e->id_especialidad }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" formaction="{{ route('reportes.medicos_especialidad') }}" class="btn btn-primary">PDF</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.footer')
@endsection