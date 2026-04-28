@extends('layouts.template')
@section('title', 'Especialidades | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Especialidades Médicas</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearEspecialidad">
                <i class="bi bi-plus-circle me-1"></i> Nueva Especialidad
            </button>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($especialidades as $especialidad)
                <tr>
                    <td>{{ $especialidad->id_especialidad }}</td>
                    <td>{{ $especialidad->nombre }}</td>
                    <td>{{ $especialidad->descripcion ?? 'Sin descripción' }}</td>
                    <td>
                        @if($especialidad->estado)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="hstack gap-2 justify-content-end">
                            <a href="{{ route('especialidades.edit', $especialidad->id_especialidad) }}" class="btn btn-xs btn-square btn-neutral">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('especialidades.destroy', $especialidad->id_especialidad) }}" method="POST" class="d-inline form-eliminar">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('layouts.footer')
@endsection

<!-- Modal Crear Especialidad -->
<div class="modal fade" id="modalCrearEspecialidad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Especialidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('especialidades.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" value="{{ old('nombre') }}" class="form-control" name="nombre" placeholder="Nombre" required>
                        <label>Nombre</label>
                        @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" name="descripcion" placeholder="Descripción" style="height: 100px">{{ old('descripcion') }}</textarea>
                        <label>Descripción</label>
                        @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-check mb-3">
                        <input type="hidden" name="estado" value="0">
                        <input type="checkbox" name="estado" value="1" class="form-check-input" checked>
                        <label class="form-check-label">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Especialidad -->
<div class="modal fade" id="modalEditarEspecialidad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Especialidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ isset($especialidadToEdit) ? route('especialidades.update', $especialidadToEdit->id_especialidad) : '#' }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" value="{{ old('nombre', $especialidadToEdit->nombre ?? '') }}" class="form-control" name="nombre" placeholder="Nombre" required>
                        <label>Nombre</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" name="descripcion" placeholder="Descripción" style="height: 100px">{{ old('descripcion', $especialidadToEdit->descripcion ?? '') }}</textarea>
                        <label>Descripción</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="hidden" name="estado" value="0">
                        <input type="checkbox" name="estado" value="1" class="form-check-input" {{ old('estado', $especialidadToEdit->estado ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Activo</label>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Editar si hay especialidadToEdit
        @if(isset($especialidadToEdit))
            var modalEl = document.getElementById('modalEditarEspecialidad');
            if (modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        @endif

        //  SweetAlert
        const forms = document.querySelectorAll('.form-eliminar');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>