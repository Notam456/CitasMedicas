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
                @foreach($especialidades as $e)
                <tr>
                    <td>{{ $e->id_especialidad }}</td>
                    <td>{{ $e->nombre }}</td>
                    <td>{{ $e->descripcion ?? 'Sin descripción' }}</td>
                    <td>
                        @if($e->estado)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-xs btn-square btn-neutral" data-bs-toggle="modal" data-bs-target="#modalEditarEspecialidad{{ $e->id_especialidad }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="#!" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-delete-url="{{ route('especialidades.destroy', $e->id_especialidad) }}" data-confirm-delete="true">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                    </td>

                    <!-- Modal Editar Especialidad -->
                    <div class="modal fade" id="modalEditarEspecialidad{{ $e->id_especialidad }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Especialidad</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <form action="{{ route('especialidades.update', $e->id_especialidad) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="nombre" class="form-control" value="{{ $e->nombre }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="descripcion" class="form-control" rows="3">{{ $e->descripcion }}</textarea>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="hidden" name="estado" value="0">
                                            <input type="checkbox" name="estado" value="1" class="form-check-input" {{ $e->estado ? 'checked' : '' }}>
                                            <label class="form-check-label">Activo</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('[data-confirm-delete="true"]');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const deleteUrl = this.getAttribute('data-delete-url');
                
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
                        // Crear formulario y enviar
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;
                        form.style.display = 'none';
                        
                        // Token CSRF
                        const csrfToken = document.createElement('input');
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        // Method DELETE
                        const methodField = document.createElement('input');
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        form.appendChild(methodField);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
</script>