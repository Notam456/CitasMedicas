@extends('layouts.template')
@section('title', 'Dashboard | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Usuarios</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                <i class="bi bi-person-plus me-1"></i> Registrar Usuario
            </button>
        </div>
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Role</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td>
                            <div>
                                <a class="d-inline-block text-heading text-primary-hover fw-semibold" href="#">
                                    {{ $usuario->name }}
                                </a>
                                <span class="d-block text-sm">{{ $usuario->email }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">No programado aun</span>
                        </td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <a href="{{ route('users.edit', $usuario->id_user) }}" class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('users.destroy', $usuario->id_user) }}" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach 
            </tbody>
        </table>
    </div>


    <!-- Modal Registrar Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUsuarioLabel">Registrar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="nombreUsuario" name="name" placeholder="Nombre de usuario" required>
                            <label for="nombreUsuario">Nombre</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="emailUsuario" name="email" placeholder="Correo electrónico" required>
                            <label for="emailUsuario">Email</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="passwordUsuario" name="password" placeholder="Contraseña" required>
                            <label for="passwordUsuario">Contraseña</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="passwordConfirmUsuario" name="password_confirmation" placeholder="Confirmar contraseña" required>
                            <label for="passwordConfirmUsuario">Confirmar Contraseña</label>
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

    <!-- Modal Editar Usuario (similar al de registrar, pero con campos prellenados) -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ isset($userToEdit) ? route('users.update', $userToEdit->id_user) : '#' }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarNombreUsuario" name="name" placeholder="Nombre de usuario" required value="{{ old('name', $userToEdit->name ?? '') }}">
                            <label for="editarNombreUsuario">Nombre</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="editarEmailUsuario" name="email" placeholder="Correo electrónico" required value="{{ old('email', $userToEdit->email ?? '') }}">
                            <label for="editarEmailUsuario">Email</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="editarPasswordUsuario" name="password" placeholder="Contraseña"">
                            <label for="editarPasswordUsuario">Contraseña (dejar en blanco para no cambiar)</label>
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

@if(isset($userToEdit))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('modalEditarUsuario');
    if (modalEl) {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
});
</script>
@endif

@include('layouts.footer')

@endsection