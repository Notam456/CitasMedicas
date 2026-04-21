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
                    <th>Acciones</th>
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
                                <a href="#" class="btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover">
                                    <i class="bi bi-trash"></i>
                                </button>
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

@include('layouts.footer')

@endsection