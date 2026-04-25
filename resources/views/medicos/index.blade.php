@extends('layouts.template')
@section('title', 'Médicos | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Médicos</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearMedico">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Médico
            </button>
        </div>
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Especialidad</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicos as $m)
                <tr>
                    <td>{{ $m->id_medico }}</td>
                    <td>{{ $m->nombres }}</td>
                    <td>{{ $m->apellidos }}</td>
                    <td>{{ $m->cedula }}</td>
                    <td>{{ $m->telefono }}</td>
                    <td>{{ $m->especialidad->nombre ?? 'N/A' }}</td>
                    <td>
                        @if($m->estado)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-xs btn-square btn-neutral" data-bs-toggle="modal" data-bs-target="#modalEditarMedico{{ $m->id_medico }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="#!" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-delete-url="{{ route('medicos.destroy', $m->id_medico) }}" data-confirm-delete="true">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                    </td>

                    <!-- Modal Editar Médico -->
                    <div class="modal fade" id="modalEditarMedico{{ $m->id_medico }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Médico</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <form action="{{ route('medicos.update', $m->id_medico) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nombres</label>
                                                <input type="text" name="nombres" class="form-control" value="{{ $m->nombres }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Apellidos</label>
                                                <input type="text" name="apellidos" class="form-control" value="{{ $m->apellidos }}" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Cédula</label>
                                                <input type="text" name="cedula" class="form-control" value="{{ $m->cedula }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Teléfono</label>
                                                <input type="text" name="telefono" class="form-control" value="{{ $m->telefono }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Especialidad</label>
                                            <select name="id_especialidad" class="form-control" required>
                                                @foreach($especialidades as $e)
                                                    <option value="{{ $e->id_especialidad }}" {{ $m->id_especialidad == $e->id_especialidad ? 'selected' : '' }}>
                                                        {{ $e->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="hidden" name="estado" value="0">
                                            <input type="checkbox" name="estado" value="1" class="form-check-input" {{ $m->estado ? 'checked' : '' }}>
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

<!-- Modal Crear Médico -->
<div class="modal fade" id="modalCrearMedico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Médico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('medicos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombres</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cédula</label>
                            <input type="text" name="cedula" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Especialidad</label>
                        <select name="id_especialidad" class="form-control" required>
                            <option value="">Seleccione una especialidad</option>
                            @foreach($especialidades as $e)
                                <option value="{{ $e->id_especialidad }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
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
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;
                        form.style.display = 'none';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
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