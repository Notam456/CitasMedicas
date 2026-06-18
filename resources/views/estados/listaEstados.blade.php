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

        <table class="table table-hover" id="tablaEstados">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal Registrar Estado (sin cambios) -->
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
                            <input type="text" class="form-control" id="nombreEstado" name="nombre"
                                placeholder="Nombre del estado" value="{{ old('nombre') }}" required
                                pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
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

    <!-- Modal Editar Estado (sin cambios) -->
    <div class="modal fade" id="modalEditarEstado" tabindex="-1" aria-labelledby="modalEditarEstadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarEstadoLabel">Editar Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarNombreEstado" name="nombre"
                                value="" placeholder="Nombre del estado" required
                                pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
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

    <!-- Modal Mostrar Estado (sin cambios) -->
    <div class="modal fade" id="modalShowEstado" tabindex="-1" aria-labelledby="modalShowEstadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowEstadoLabel">Datos del Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <p class="form-control-plaintext" id="mostrarEstadoNombre"></p>
                    </div>
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
$(document).ready(function() {
    $('#tablaEstados').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("estados.index") }}',
        columns: [
            { data: 0, name: 'nombre' },
            { data: 1, name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[0, 'asc']]
    });
});

// Eventos para editar/mostrar (sin cambios, solo usando delegación)
document.addEventListener('click', async function(event) {
    const btn = event.target.closest('.btn-edit');
    const btnShow = event.target.closest('.btn-show');

    if (btn) {
        const estadoId = btn.getAttribute('data-id');
        var inputNombre = document.getElementById('editarNombreEstado');

        try {
            const modalElement = document.getElementById('modalEditarEstado');
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }
            inputNombre.disabled = true;
            inputNombre.value = "Cargando...";
            modalInstance.show();
            const response = await fetch(`/estados/${estadoId}/edit`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al obtener datos');
            const data = await response.json();
            document.getElementById('id').value = data.id;
            inputNombre.value = data.nombre;
            inputNombre.disabled = false;
            const form = document.querySelector('#modalEditarEstado form');
            form.action = `/estados/${data.id}`;
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos del estado', 'error');
        }
    }

    if (btnShow) {
        const estadoId = btnShow.getAttribute('data-id');
        var inputNombre = document.getElementById('mostrarEstadoNombre');

        try {
            const modalElement = document.getElementById('modalShowEstado');
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }
            inputNombre.innerHTML = "Cargando...";
            modalInstance.show();
            const response = await fetch(`/estados/${estadoId}/show`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al obtener datos');
            const data = await response.json();
            inputNombre.innerHTML = data.nombre;
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos del estado', 'error');
        }
    }
});

@if ($errors->any())
const errorMessages = @json(implode("\n", $errors->all()));

    Swal.fire({ 
        icon: 'error', 
        title: 'Error', 
        text: errorMessages 
    });
@endif
</script>
@endpush
