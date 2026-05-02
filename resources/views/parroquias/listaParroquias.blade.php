@extends('layouts.template')
@section('title', 'Lista de Parroquias | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Parroquias</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalParroquia">
                <i class="bi bi-plus-circle me-1"></i> Registrar Parroquia
            </button>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Municipio</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($parroquias as $parroquia)
                    <tr>
                        <td>{{ $parroquia->nombre }}</td>
                        <td>{{ $parroquia->municipio->nombre ?? 'N/A' }}</td>
                        <td>{{ $parroquia->municipio->estado->nombre ?? 'N/A' }}</td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" data-id="{{ $parroquia->id }}"
                                    class=" btn-show btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" data-id="{{ $parroquia->id }}"
                                    class=" btn-edit btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('parroquias.destroy', $parroquia->id) }}"
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

    <!-- Modal Registrar Parroquia -->
    <div class="modal fade" id="modalParroquia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Parroquia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('parroquias.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="nombre" placeholder="Nombre"
                                value="{{ old('nombre') }}" required>
                            <label>Nombre de la Parroquia</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="estado_id_registro">
                                <option value="">Seleccione Estado (obligatorio)</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                            <label>Estado</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" name="municipio_id" id="municipio_id_registro" required disabled>
                                <option value="">Primero seleccione un estado</option>
                            </select>
                            <label>Municipio</label>
                            @error('municipio_id')
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

    <!-- Modal Editar Parroquia -->
    <div class="modal fade" id="modalEditarParroquia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Parroquia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ isset($parroquiaToEdit) ? route('parroquias.update', $parroquiaToEdit->id) : '#' }}"
                    method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        <div class="form-floating mb-3">
                            <input type="text" id="editarNombreParroquia" class="form-control" name="nombre" required>
                            <label>Nombre de la Parroquia</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="estado_id_editar">
                                <option value="">Seleccione Estado (obligatorio)</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}">
                                        {{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                            <label>Estado</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" name="municipio_id" id="municipio_id_editar" required>
                                <option value="">-- Seleccione --</option>
                            </select>
                            <label>Municipio</label>
                            @error('municipio_id')
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

    <!-- Modal Mostrar Parroquia -->
    <div class="modal fade" id="modalShowParroquia" tabindex="-1" aria-labelledby="modalShowParroquiaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Datos de la Parroquia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <p class="form-control-plaintext" id="mostrarNombreParroquia"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Municipio</label>
                        <p class="form-control-plaintext" id="mostrarMunicipioParroquia"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <p class="form-control-plaintext" id="mostrarEstadoParroquia"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // AJAX: cargar municipios según estado
        async function cargarMunicipios(estadoId, selectElement, valorSeleccionado = null) {
            if (!estadoId) {
                selectElement.innerHTML = '<option value="">-- Primero seleccione un estado --</option>';
                selectElement.disabled = true;
                return;
            }
            try {
                const response = await fetch(`/municipios-por-estado/${estadoId}`);
                const municipios = await response.json();
                selectElement.innerHTML = '<option value="">-- Seleccione --</option>';
                municipios.forEach(m => {
                    const option = document.createElement('option');
                    option.value = m.id;
                    option.textContent = m.nombre;
                    if (valorSeleccionado && m.id == valorSeleccionado) option.selected = true;
                    selectElement.appendChild(option);
                });
                selectElement.disabled = (municipios.length === 0);
                if (municipios.length === 0) {
                    selectElement.innerHTML = '<option value="">-- No hay municipios --</option>';
                }
            } catch (error) {
                console.error('Error cargando municipios:', error);
                selectElement.innerHTML = '<option value="">-- Error al cargar --</option>';
            }
        }

        // Registrar
        const estadoRegistro = document.getElementById('estado_id_registro');
        const municipioRegistro = document.getElementById('municipio_id_registro');
        estadoRegistro.addEventListener('change', function() {
            cargarMunicipios(this.value, municipioRegistro);
        });
        cargarMunicipios(estadoRegistro.value, municipioRegistro);

        // Editar
        const estadoEditar = document.getElementById('estado_id_editar');
        const municipioEditar = document.getElementById('municipio_id_editar');
        if (estadoEditar) {
            const municipioActualId = {{ isset($parroquiaToEdit) ? $parroquiaToEdit->municipio_id : 'null' }};
            async function actualizarMunicipiosEditar() {
                await cargarMunicipios(estadoEditar.value, municipioEditar, municipioActualId);
            }
            estadoEditar.addEventListener('change', actualizarMunicipiosEditar);
            actualizarMunicipiosEditar();
        }
    </script>

    <script>
        document.addEventListener('click', async function(event) {
            const btn = event.target.closest('.btn-edit');
            const btnShow = event.target.closest('.btn-show');

            if (btn) {
                const parroquiaId = btn.getAttribute('data-id');
                var inputNombre = document.getElementById('editarNombreParroquia');
                var inputMunicipio = document.getElementById('municipio_id_editar');
                var inputEstado = document.getElementById('estado_id_editar');

                try {
                    const modalElement = document.getElementById('modalEditarParroquia');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }
                    inputNombre.disabled = true;
                    inputNombre.value = "Cargando...";
                    inputMunicipio.disabled = true;
                    inputEstado.disabled = true;

                    modalInstance.show();
                    const response = await fetch(`/parroquias/${parroquiaId}/edit`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Error al obtener datos');

                    const data = await response.json();


                    document.getElementById('id').value = data.id;
                    inputNombre.value = data.nombre;
                    inputNombre.disabled = false;
                    inputEstado.value = data.municipio.estado_id;
                    inputEstado.disabled = false;
                    cargarMunicipios(data.municipio.estado_id, inputMunicipio, data.municipio)


                    const form = document.querySelector('#modalEditarParroquia form');
                    form.action = `/municipios/${data.id}`;


                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del municipio', 'error');
                }
            }

            if (btnShow) {
                const parroquiaId = btnShow.getAttribute('data-id');
                var inputNombre = document.getElementById('mostrarNombreParroquia');
                var inputMunicipio = document.getElementById('mostrarMunicipioParroquia');
                var inputEstado = document.getElementById('mostrarEstadoParroquia');


                try {
                    const modalElement = document.getElementById('modalShowParroquia');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }

                    inputNombre.innerHTML = "Cargando...";
                    inputMunicipio.innerHTML = "Cargando...";
                    inputEstado.innerHTML = "Cargando...";

                    modalInstance.show();
                    const response = await fetch(`/parroquias/${parroquiaId}/show`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Error al obtener datos');

                    const data = await response.json();



                    inputNombre.innerHTML = data.nombre;
                    inputMunicipio.innerHTML = data.municipio.nombre;
                    inputEstado.innerHTML = data.municipio.estado.nombre;

                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del estado', 'error');
                }
            }


        });
    </script>

    @if ($errors->any() && !isset($parroquiaToEdit) && !isset($parroquiaToShow))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalEl = document.getElementById('modalParroquia');
                if (modalEl) new bootstrap.Modal(modalEl).show();
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '• {{ $error }}\n';
                @endforeach
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessages,
                    confirmButtonColor: '#3085d6'
                });
            });
        </script>
    @endif

    @include('layouts.footer')
@endsection
