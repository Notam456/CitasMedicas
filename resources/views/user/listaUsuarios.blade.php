@extends('layouts.template')
@section('title', 'Dashboard | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Usuarios</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="# ">
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
                @foreach ($usuarios as $usuario)
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
                                <button type="button" data-id="{{ $usuario->id }}"
                                    class=" btn-edit btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('users.destroy', $usuario->id) }}"
                                    class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover"
                                    data-confirm-delete="true">
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
                            <input type="text" value="{{ old('name') }}" class="form-control" id="nombreUsuario"
                                name="name" placeholder="Nombre de usuario" required>
                            <label for="nombreUsuario">Nombre</label>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" value="{{ old('email') }}" class="form-control" id="emailUsuario"
                                name="email" placeholder="Correo electrónico" required>
                            <label for="emailUsuario">Email</label>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="passwordUsuario" name="password"
                                placeholder="Contraseña" required>
                            <label for="passwordUsuario">Contraseña</label>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="passwordConfirmUsuario"
                                name="password_confirmation" placeholder="Confirmar contraseña" required>
                            <label for="passwordConfirmUsuario">Confirmar Contraseña</label>
                            @error('password_confirmation')
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

    <!-- Modal Editar Usuario (similar al de registrar, pero con campos prellenados) -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarNombreUsuario" name="name"
                                placeholder="Nombre de usuario" required>
                            <label for="editarNombreUsuario">Nombre</label>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="editarEmailUsuario" name="email"
                                placeholder="Correo electrónico" required>
                            <label for="editarEmailUsuario">Email</label>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="editarPasswordUsuario" name="password"
                                placeholder="Contraseña"">
                            <label for="editarPasswordUsuario">Contraseña (dejar en blanco para no cambiar)</label>
                            @error('password')
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

    <script>
        document.addEventListener('click', async function(event) {
            const btn = event.target.closest('.btn-edit');

            if (btn) {
                const userId = btn.getAttribute('data-id');
                var inputNombre = document.getElementById('editarNombreUsuario');
                var inputEmail = document.getElementById('editarEmailUsuario');

                try {
                    const modalElement = document.getElementById('modalEditarUsuario');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }

                    inputNombre.value = "Cargando...";
                    inputNombre.disabled = true;
                    inputEmail.value = "Cargando...";
                    inputEmail.disabled = true;
                    modalInstance.show();
                    const response = await fetch(`/users/${userId}/edit`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Error al obtener datos');

                    const data = await response.json();


                    document.getElementById('id').value = data.id;
                    inputNombre.value = data.name;
                    inputNombre.disabled = false;
                    inputEmail.value = data.email;
                    inputEmail.disabled = false;
                    document.getElementById('editarPasswordUsuario').value = "";

                    const form = document.querySelector('#modalEditarUsuario form');
                    form.action = `/users/${data.id}`;


                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del usuario', 'error');
                }
            }
        });
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let errorMessages = '';

                // Recorremos todos los errores de Laravel y los acumulamos
                @foreach ($errors->all() as $error)
                    errorMessages += '• {{ $error }}\n';
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: '¡Ups! Algo salió mal en tu accion intentalo de nuevo',
                    text: errorMessages,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Entendido'
                });
            });
        </script>
    @endif

    @include('layouts.footer')

@endsection
