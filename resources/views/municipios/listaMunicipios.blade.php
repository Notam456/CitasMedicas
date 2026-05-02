@extends('layouts.template')
@section('title', 'Lista de Municipios | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Municipios</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMunicipio">
                <i class="bi bi-plus-circle me-1"></i> Registrar Municipio
            </button>
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($municipios as $municipio)
                    <tr>
                        <td>{{ $municipio->nombre }}</td>
                        <td>{{ $municipio->estado->nombre ?? 'N/A' }}</td>
                        <td class="text-end">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" data-id="{{ $municipio->id }}"
                                    class=" btn-show btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" data-id="{{ $municipio->id }}"
                                    class=" btn-edit btn btn-xs btn-square btn-neutral">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('municipios.destroy', $municipio->id) }}"
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

    <!-- Modal Registrar Municipio -->
    <div class="modal fade" id="modalMunicipio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Municipio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('municipios.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="nombre" placeholder="Nombre"
                                value="{{ old('nombre') }}" required>
                            <label>Nombre del Municipio</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" name="estado_id" required>
                                <option value="">Seleccione</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}"
                                        {{ old('estado_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <label>Estado</label>
                            @error('estado_id')
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

    <!-- Modal Editar Municipio -->
    <div class="modal fade" id="modalEditarMunicipio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Municipio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarNombreMunicipio" name="nombre" required>
                            <label>Nombre del Municipio</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" name="estado_id" id="editarEstadoMunicipio" required>
                                <option value="">Seleccione</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}">
                                        {{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                            <label>Estado</label>
                            @error('estado_id')
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

    <!-- Modal Mostrar Municipio -->
    <div class="modal fade" id="modalShowMunicipio" tabindex="-1" aria-labelledby="modalShowMunicipioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Datos del Municipio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <p class="form-control-plaintext" id="mostrarMunicipioNombre"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <p class="form-control-plaintext" id="mostrarMunicipioEstado"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('click', async function(event) {
            const btn = event.target.closest('.btn-edit');
            const btnShow = event.target.closest('.btn-show');

            if (btn) {
                const municipioId = btn.getAttribute('data-id');
                var inputNombre = document.getElementById('editarNombreMunicipio');
                var inputEstado = document.getElementById('editarEstadoMunicipio');

                try {
                    const modalElement = document.getElementById('modalEditarMunicipio');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }
                    inputNombre.disabled = true;
                    inputNombre.value = "Cargando...";
                    inputEstado.disabled = true;

                    modalInstance.show();
                    const response = await fetch(`/municipios/${municipioId}/edit`, {
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
                    inputEstado.value = data.estado_id;
                    inputEstado.disabled = false;


                    const form = document.querySelector('#modalEditarMunicipio form');
                    form.action = `/municipios/${data.id}`;


                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del municipio', 'error');
                }
            }

            if (btnShow) {
                const municipioId = btnShow.getAttribute('data-id');
                var inputNombre = document.getElementById('mostrarMunicipioNombre');
                var inputEstado = document.getElementById('mostrarMunicipioEstado');


                try {
                    const modalElement = document.getElementById('modalShowMunicipio');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }

                    inputNombre.innerHTML = "Cargando...";
                    inputEstado.innerHTML = "Cargando..."

                    modalInstance.show();
                    const response = await fetch(`/municipios/${municipioId}/show`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Error al obtener datos');

                    const data = await response.json();

                    console.log(data);

                    inputNombre.innerHTML = data.nombre;
                    inputEstado.innerHTML = data.estado.nombre;

                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del estado', 'error');
                }
            }


        });
    </script>

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
