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

        <table class="table table-hover" id="tablaMunicipios">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Distrito</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
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
                        <div class="form-floating mb-3">
                            <select class="form-select" name="distrito_id">
                                <option value="">Sin distrito asignado</option>
                                @foreach ($distritos as $distrito)
                                    <option value="{{ $distrito->id }}"
                                        {{ old('distrito_id') == $distrito->id ? 'selected' : '' }}>
                                        {{ $distrito->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <label>Distrito (opcional)</label>
                            @error('distrito_id')
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
                        <div class="form-floating mb-3">
                            <select class="form-select" name="distrito_id" id="editarDistritoMunicipio">
                                <option value="">Sin distrito asignado</option>
                                @foreach ($distritos as $distrito)
                                    <option value="{{ $distrito->id }}">{{ $distrito->nombre }}</option>
                                @endforeach
                            </select>
                            <label>Distrito (opcional)</label>
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
                    <div class="mb-3">
                        <label class="form-label">Distrito</label>
                        <p class="form-control-plaintext" id="mostrarMunicipioDistrito"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
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
            $('#tablaMunicipios').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('municipios.index') }}',
                columns: [{
                        data: 0,
                        name: 'nombre'
                    },
                    {
                        data: 1,
                        name: 'estado'
                    },
                    {
                        data: 2,
                        name: 'distrito'
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

        document.addEventListener('click', async function(event) {
            const btn = event.target.closest('.btn-edit');
            const btnShow = event.target.closest('.btn-show');

            if (btn) {
                const municipioId = btn.getAttribute('data-id');
                const inputNombre = document.getElementById('editarNombreMunicipio');
                const inputEstado = document.getElementById('editarEstadoMunicipio');
                const selectDistrito = document.getElementById('editarDistritoMunicipio');

                try {
                    const modalElement = document.getElementById('modalEditarMunicipio');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }
                    inputNombre.disabled = true;
                    inputNombre.value = "Cargando...";
                    inputEstado.disabled = true;
                    if (selectDistrito) selectDistrito.disabled = true;

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
                    if (selectDistrito) {
                        selectDistrito.value = data.distrito_id || '';
                        selectDistrito.disabled = false;
                    }

                    const form = document.querySelector('#modalEditarMunicipio form');
                    form.action = `/municipios/${data.id}`;

                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del municipio', 'error');
                }
            }

            if (btnShow) {
                const municipioId = btnShow.getAttribute('data-id');
                const spanNombre = document.getElementById('mostrarMunicipioNombre');
                const spanEstado = document.getElementById('mostrarMunicipioEstado');
                const spanDistrito = document.getElementById('mostrarMunicipioDistrito');

                try {
                    const modalElement = document.getElementById('modalShowMunicipio');
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalElement);
                    }

                    spanNombre.innerHTML = "Cargando...";
                    spanEstado.innerHTML = "Cargando...";
                    spanDistrito.innerHTML = "Cargando...";

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

                    spanNombre.innerHTML = data.nombre;
                    spanEstado.innerHTML = data.estado;
                    spanDistrito.innerHTML = data.distrito || 'No asignado';

                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos del municipio', 'error');
                }
            }
        });

        @if ($errors->any())

            const errorMessages = @json(implode("\n", $errors->all()));

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessages
            });
        @endif
    </script>
@endpush
