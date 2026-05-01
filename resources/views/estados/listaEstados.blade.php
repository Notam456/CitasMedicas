@extends('layouts.template')
@section('title', 'Lista de Estados | SAGECIM')

@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<div class="table-responsive bg-light rounded h-100 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Lista de Estados</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEstado">
            <i class="bi bi-plus-circle me-1"></i> Registrar Estado
        </button>
    </div>

    <table class="table table-hover" id="estadosTable">
        <thead>
            <tr>
                <th>Nombre</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Registrar Estado -->
<div class="modal fade" id="modalEstado" tabindex="-1" aria-labelledby="modalEstadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEstadoLabel">Registrar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('estados.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nombreEstado" name="nombre" placeholder="Nombre del estado" value="{{ old('nombre') }}" required>
                        <label for="nombreEstado">Nombre del Estado</label>
                        @error('nombre')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Estado -->
<div class="modal fade" id="modalEditarEstado" tabindex="-1" aria-labelledby="modalEditarEstadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarEstadoLabel">Editar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ isset($estadoToEdit) ? route('estados.update', $estadoToEdit->id) : '#' }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="editarNombreEstado" name="nombre" value="{{ old('nombre', $estadoToEdit->nombre ?? '') }}" placeholder="Nombre del estado" required>
                        <label for="editarNombreEstado">Nombre del Estado</label>
                        @error('nombre')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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

<!-- Modal Mostrar Estado -->
<div class="modal fade" id="modalShowEstado" tabindex="-1" aria-labelledby="modalShowEstadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalShowEstadoLabel">Datos del Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ID</label>
                    <p class="form-control">{{ $estadoToShow->id ?? '' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <p class="form-control">{{ $estadoToShow->nombre ?? '' }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@if(isset($estadoToEdit))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalEditarEstado');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    });
</script>
@endif

@if(isset($estadoToShow))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalShowEstado');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    });
</script>
@endif

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let errorMessages = '';
        @foreach ($errors->all() as $error) errorMessages += '• {{ $error }}\n'; @endforeach
        Swal.fire({ icon: 'error', title: '¡Ups! Algo salió mal', text: errorMessages, confirmButtonColor: '#3085d6', confirmButtonText: 'Entendido' });
    });
</script>
@endif

@push('scripts')
<script>
    $(document).ready(function() {
        $('#estadosTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("api.estados") }}',
            columns: [
                { data: 'nombre', name: 'nombre' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            responsive: true,
            order: [[0, 'asc']]
        });
    });
</script>
@endpush

@include('layouts.footer')
@endsection