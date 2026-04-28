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
                @foreach($medicos as $medico)
                <tr>
                    <td>{{ $medico->id_medico }}</td>
                    <td>{{ $medico->nombres }}</td>
                    <td>{{ $medico->apellidos }}</td>
                    <td>{{ $medico->cedula }}</td>
                    <td>{{ $medico->telefono }}</td>
                    <td>{{ $medico->especialidad->nombre ?? 'N/A' }}</td>
                    <td>
                        @if($medico->estado)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="hstack gap-2 justify-content-end">
                            <a href="{{ route('medicos.edit', $medico->id_medico) }}" class="btn btn-xs btn-square btn-neutral">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('medicos.destroy', $medico->id_medico) }}" method="POST" class="d-inline form-eliminar">
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
                    <div class="form-floating mb-3">
                        <input type="text" value="{{ old('nombres') }}" class="form-control" name="nombres" placeholder="Nombres" required>
                        <label>Nombres</label>
                        @error('nombres') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" value="{{ old('apellidos') }}" class="form-control" name="apellidos" placeholder="Apellidos" required>
                        <label>Apellidos</label>
                        @error('apellidos') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" value="{{ old('cedula') }}" class="form-control" name="cedula" placeholder="Cédula" required>
                                <label>Cédula</label>
                                @error('cedula') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" value="{{ old('telefono') }}" class="form-control" name="telefono" placeholder="Teléfono" required>
                                <label>Teléfono</label>
                                @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-control" name="id_especialidad" required>
                            <option value="">Seleccione una especialidad</option>
                            @foreach($especialidades as $e)
                                <option value="{{ $e->id_especialidad }}" {{ old('id_especialidad') == $e->id_especialidad ? 'selected' : '' }}>{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                        <label>Especialidad</label>
                        @error('id_especialidad') <small class="text-danger">{{ $message }}</small> @enderror
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

<!-- Modal Editar Médico -->
<div class="modal fade" id="modalEditarMedico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Médico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ isset($medicoToEdit) ? route('medicos.update', $medicoToEdit->id_medico) : '#' }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" value="{{ old('nombres', $medicoToEdit->nombres ?? '') }}" class="form-control" name="nombres" placeholder="Nombres" required>
                        <label>Nombres</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" value="{{ old('apellidos', $medicoToEdit->apellidos ?? '') }}" class="form-control" name="apellidos" placeholder="Apellidos" required>
                        <label>Apellidos</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" value="{{ old('cedula', $medicoToEdit->cedula ?? '') }}" class="form-control" name="cedula" placeholder="Cédula" required>
                                <label>Cédula</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" value="{{ old('telefono', $medicoToEdit->telefono ?? '') }}" class="form-control" name="telefono" placeholder="Teléfono" required>
                                <label>Teléfono</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-control" name="id_especialidad" required>
                            @foreach($especialidades as $e)
                                <option value="{{ $e->id_especialidad }}" {{ old('id_especialidad', $medicoToEdit->id_especialidad ?? '') == $e->id_especialidad ? 'selected' : '' }}>{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                        <label>Especialidad</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="hidden" name="estado" value="0">
                        <input type="checkbox" name="estado" value="1" class="form-check-input" {{ old('estado', $medicoToEdit->estado ?? false) ? 'checked' : '' }}>
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
        // Editar si hay medicoToEdit
        @if(isset($medicoToEdit))
            var modalEl = document.getElementById('modalEditarMedico');
            if (modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        @endif

        // SweetAlert
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