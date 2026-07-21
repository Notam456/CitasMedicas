@extends('layouts.template')
@section('title', 'Lista de Usuarios | SAGECIM')

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

        <table class="table table-hover" id="tablaUsuarios">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal Registrar Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
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
                                name="name" placeholder="Nombre de usuario" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+" maxlength="255" title="Solo letras">
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
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="rolUsuario" class="form-label mb-0">Rol del Usuario</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnNuevoRol">
                                    <i class="bi bi-plus-circle me-1"></i> Nuevo Rol
                                </button>
                            </div>
                            <select class="form-select" id="rolUsuario" name="role" required>
                                <option value="">Seleccione un rol</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Sección para ver/crear roles y permisos -->
                        <div id="seccionPermisos" class="mt-4 p-3 border rounded bg-white" style="display: none;">
                            <h6 class="mb-3 border-bottom pb-2" id="tituloPermisos">Permisos del Rol</h6>

                            <div id="crearRolCampos" style="display: none;">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="nombreNuevoRol"
                                        placeholder="Nombre del nuevo rol">
                                    <label for="nombreNuevoRol">Nombre del Nuevo Rol</label>
                                </div>
                            </div>

                            <div class="row g-3">
                                @foreach ($permisos as $permiso)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input check-permiso" type="checkbox"
                                                value="{{ $permiso->name }}" id="permiso_{{ $permiso->id }}">
                                            <label class="form-check-label" for="permiso_{{ $permiso->id }}">
                                                {{ $permiso->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="guardarRolAccion" class="mt-3 text-end" style="display: none;">
                                <button type="button" class="btn btn-success btn-sm" id="btnGuardarRol">Guardar
                                    Rol</button>
                            </div>
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

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
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
                                placeholder="Nombre de usuario" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+" maxlength="255" title="Solo letras">
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
                                placeholder="Contraseña">
                            <label for="editarPasswordUsuario">Contraseña (dejar en blanco para no cambiar)</label>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="editarPasswordUsuario_confirmation" name="password_confirmation"
                                placeholder="Contraseña">
                            <label for="editarPasswordUsuario">Confirmar Contraseña</label>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="editarRolUsuario" class="form-label mb-0">Rol del Usuario</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnNuevoRolEditar">
                                    <i class="bi bi-plus-circle me-1"></i> Nuevo Rol
                                </button>
                            </div>
                            <select class="form-select" id="editarRolUsuario" name="role" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div id="seccionPermisosEditar" class="mt-4 p-3 border rounded bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <h6 class="mb-0" id="tituloPermisosEditar">Permisos del Rol Seleccionado</h6>
                                <button type="button" class="btn btn-xs btn-outline-warning" id="btnEditarPermisosRol">
                                    <i class="bi bi-pencil me-1"></i> Editar Permisos del Rol
                                </button>
                            </div>

                            <div id="crearRolCamposEditar" style="display: none;">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="nombreNuevoRolEditar"
                                        placeholder="Nombre del nuevo rol">
                                    <label for="nombreNuevoRolEditar">Nombre del Nuevo Rol</label>
                                </div>
                            </div>

                            <div class="row g-3">
                                @foreach ($permisos as $permiso)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input check-permiso-editar" type="checkbox"
                                                value="{{ $permiso->name }}" id="permiso_edit_{{ $permiso->id }}"
                                                disabled>
                                            <label class="form-check-label" for="permiso_edit_{{ $permiso->id }}">
                                                {{ $permiso->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="guardarPermisosRolAccion" class="mt-3 text-end" style="display: none;">
                                <button type="button" class="btn btn-success btn-sm" id="btnGuardarPermisosRol">Guardar
                                    Cambios en el Rol</button>
                            </div>
                            <small class="text-muted mt-2 d-block">Nota: Al editar los permisos del rol, se verán afectados
                                todos los usuarios con este rol.</small>
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

    @include('layouts.footer')
@endsection

@push('scripts')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
    <script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#tablaUsuarios').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('users.index') }}',
                columns: [{
                        data: 0,
                        name: 'name'
                    },
                    {
                        data: 1,
                        name: 'email'
                    },
                    {
                        data: 2,
                        name: 'role',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 3,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    }
                ],
                language: {
                    url: "{{ asset('vendor/datatables/es-ES.json') }}"
                },
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Todas"]
                ],
                order: [
                    [0, 'asc']
                ]
            });
        });
    </script>

    <!-- Copia exacta del JavaScript original para roles y permisos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            async function guardarNuevoRol(nameInput, checks, selectToUpdate, otherSelect, camposContainer,
                accionContainer, tituloElement) {
                const name = nameInput.value;
                if (!name) {
                    Swal.fire('Error', 'Por favor ingrese un nombre para el rol', 'error');
                    return;
                }

                const selectedPermissions = Array.from(checks)
                    .filter(c => c.checked)
                    .map(c => c.value);

                try {
                    const response = await fetch('{{ route('roles.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name,
                            permissions: selectedPermissions
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.errors && data.errors.name) {
                            throw new Error(data.errors.name[0]);
                        }
                        throw new Error(data.message || 'Error al crear el rol');
                    }

                    const optionText = data.role.name.charAt(0).toUpperCase() + data.role.name.slice(1);
                    const optionValue = data.role.name;

                    selectToUpdate.add(new Option(optionText, optionValue));
                    otherSelect.add(new Option(optionText, optionValue));

                    selectToUpdate.value = optionValue;

                    camposContainer.style.display = 'none';
                    accionContainer.style.display = 'none';
                    tituloElement.textContent = 'Permisos del Rol: ' + data.role.name;
                    nameInput.value = '';

                    checks.forEach(c => c.disabled = true);

                    Swal.fire('Éxito', 'Rol creado correctamente', 'success');

                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }

            const btnNuevoRol = document.getElementById('btnNuevoRol');
            const seccionPermisos = document.getElementById('seccionPermisos');
            const crearRolCampos = document.getElementById('crearRolCampos');
            const guardarRolAccion = document.getElementById('guardarRolAccion');
            const selectRol = document.getElementById('rolUsuario');
            const otherSelectEditar = document.getElementById('editarRolUsuario');
            const tituloPermisos = document.getElementById('tituloPermisos');
            const checksPermisos = document.querySelectorAll('.check-permiso');
            const btnGuardarRol = document.getElementById('btnGuardarRol');
            const nombreNuevoRol = document.getElementById('nombreNuevoRol');

            btnNuevoRol.addEventListener('click', function() {
                seccionPermisos.style.display = 'block';
                crearRolCampos.style.display = 'block';
                guardarRolAccion.style.display = 'block';
                tituloPermisos.textContent = 'Crear Nuevo Rol y Asignar Permisos';
                selectRol.value = '';
                checksPermisos.forEach(check => {
                    check.checked = false;
                    check.disabled = false;
                });
            });

            selectRol.addEventListener('change', async function() {
                const roleName = this.value;
                if (!roleName) {
                    seccionPermisos.style.display = 'none';
                    return;
                }
                seccionPermisos.style.display = 'block';
                crearRolCampos.style.display = 'none';
                guardarRolAccion.style.display = 'none';
                tituloPermisos.textContent = 'Permisos del Rol: ' + roleName;
                await cargarPermisosDeRol(roleName, checksPermisos);
            });

            btnGuardarRol.addEventListener('click', function() {
                guardarNuevoRol(nombreNuevoRol, checksPermisos, selectRol, otherSelectEditar,
                    crearRolCampos, guardarRolAccion, tituloPermisos);
            });

            const selectRolEditar = document.getElementById('editarRolUsuario');
            const checksPermisosEditar = document.querySelectorAll('.check-permiso-editar');
            const btnEditarPermisosRol = document.getElementById('btnEditarPermisosRol');
            const btnGuardarPermisosRol = document.getElementById('btnGuardarPermisosRol');
            const guardarPermisosRolAccion = document.getElementById('guardarPermisosRolAccion');

            const btnNuevoRolEditar = document.getElementById('btnNuevoRolEditar');
            const crearRolCamposEditar = document.getElementById('crearRolCamposEditar');
            const nombreNuevoRolEditar = document.getElementById('nombreNuevoRolEditar');
            const tituloPermisosEditar = document.getElementById('tituloPermisosEditar');

            btnNuevoRolEditar.addEventListener('click', function() {
                crearRolCamposEditar.style.display = 'block';
                guardarPermisosRolAccion.style.display = 'block';
                btnEditarPermisosRol.style.display = 'none';
                tituloPermisosEditar.textContent = 'Crear Nuevo Rol y Asignar Permisos';
                selectRolEditar.value = '';
                checksPermisosEditar.forEach(check => {
                    check.checked = false;
                    check.disabled = false;
                });

                const originalHandler = btnGuardarPermisosRol.onclick;
                btnGuardarPermisosRol.onclick = function() {
                    guardarNuevoRol(nombreNuevoRolEditar, checksPermisosEditar, selectRolEditar,
                        selectRol, crearRolCamposEditar, guardarPermisosRolAccion,
                        tituloPermisosEditar);
                    btnGuardarPermisosRol.onclick = originalHandler;
                    btnEditarPermisosRol.style.display = 'block';
                };
            });

            selectRolEditar.addEventListener('change', async function() {
                crearRolCamposEditar.style.display = 'none';
                guardarPermisosRolAccion.style.display = 'none';
                btnEditarPermisosRol.style.display = this.value === 'administrador' ? 'none' : 'block';
                tituloPermisosEditar.textContent = 'Permisos del Rol Seleccionado';
                await cargarPermisosDeRol(this.value, checksPermisosEditar);
            });

            btnEditarPermisosRol.addEventListener('click', function() {
                if (selectRolEditar.value === 'administrador') return;

                checksPermisosEditar.forEach(check => check.disabled = false);
                guardarPermisosRolAccion.style.display = 'block';
            });

            btnGuardarPermisosRol.addEventListener('click', async function() {
                const roleName = selectRolEditar.value;
                const selectedPermissions = Array.from(checksPermisosEditar)
                    .filter(c => c.checked)
                    .map(c => c.value);

                try {
                    const response = await fetch(`/roles/${roleName}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            permissions: selectedPermissions
                        })
                    });

                    if (!response.ok) throw new Error('Error al actualizar permisos del rol');

                    guardarPermisosRolAccion.style.display = 'none';
                    checksPermisosEditar.forEach(check => check.disabled = true);

                    Swal.fire('Éxito', 'Permisos del rol actualizados correctamente', 'success');
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            });

            async function cargarPermisosDeRol(roleName, containerChecks) {
                if (roleName === 'administrador') {
                    containerChecks.forEach(check => {
                        check.checked = true;
                        check.disabled = true;
                    });
                    return;
                }

                try {
                    const response = await fetch(`/roles/${roleName}/permissions`);
                    const data = await response.json();

                    containerChecks.forEach(check => {
                        check.checked = data.permissions.includes(check.value);
                        check.disabled = true;
                    });
                } catch (error) {
                    console.error('Error al cargar permisos:', error);
                }
            }

            document.addEventListener('click', async function(event) {
                const btn = event.target.closest('.btn-edit');

                if (btn) {
                    const userId = btn.getAttribute('data-id');
                    const inputNombre = document.getElementById('editarNombreUsuario');
                    const inputEmail = document.getElementById('editarEmailUsuario');
                    const selectRolE = document.getElementById('editarRolUsuario');

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
                        selectRolE.disabled = true;
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
                        selectRolE.value = data.role || '';
                        selectRolE.disabled = false;
                        document.getElementById('editarPasswordUsuario').value = "";

                        const form = document.querySelector('#modalEditarUsuario form');
                        form.action = `/users/${data.id}`;

                        btnEditarPermisosRol.style.display = data.role === 'administrador' ? 'none' :
                            'block';
                        guardarPermisosRolAccion.style.display = 'none';

                        if (data.role) {
                            await cargarPermisosDeRol(data.role, checksPermisosEditar);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire('Error', 'No se pudieron cargar los datos del usuario', 'error');
                    }
                }
            });
        });
    </script>

    @if ($errors->any())
        <script>
            const errorMessages = @json(implode("\n", $errors->all()));

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessages
            });
        </script>
    @endif
@endpush
