@extends('layouts.template')
@section('title', 'Lista de Especialidades | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Especialidades</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEspecialidad">
                <i class="bi bi-person-plus me-1"></i> Registrar Especialidad
            </button>
        </div>

        <table class="table table-hover" id="tablaEspecialidades">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal Registrar Especialidad -->
    <div class="modal fade" id="modalEspecialidad" tabindex="-1" aria-labelledby="modalEspecialidadLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEspecialidadLabel">Registrar Especialidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('especialidades.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" value="{{ old('nombre') }}" class="form-control" id="nombreEspecialidad"
                                name="nombre" placeholder="Nombre de la especialidad" required
                                pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                            <label for="nombreEspecialidad">Nombre</label>
                            @error('nombre')
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

    <!-- Modal Editar Especialidad -->
    <div class="modal fade" id="modalEditarEspecialidad" tabindex="-1" aria-labelledby="modalEditarEspecialidadLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarEspecialidadLabel">Editar Especialidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarNombreEspecialidad" name="nombre"
                                placeholder="Nombre de la especialidad" required
                                pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                            <label for="editarNombreEspecialidad">Nombre</label>
                            @error('nombre')
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

    <!-- Modal Mostrar Datos -->
    <div class="modal fade" id="modalShowEspecialidad" tabindex="-1" aria-labelledby="modalShowEspecialidadLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowEspecialidadLabel">Datos de la Especialidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nombre</label>
                            <p class="form-control" id="mostrarEspecialidadNombre"></p>
                        </div>
                    </div>
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
            $('#tablaEspecialidades').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('especialidades.index') }}',
                columns: [{
                        data: 0,
                        name: 'nombre'
                    },
                    {
                        data: 1,
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

        document.addEventListener('click', async (e) => {
            const btnEdit = e.target.closest('.btn-edit');
            const btnShow = e.target.closest('.btn-show');
            if (btnEdit) {
                const id = btnEdit.dataset.id;
                try {
                    const res = await fetch(`/especialidades/${id}/edit`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    document.getElementById('id').value = data.id;
                    document.getElementById('editarNombreEspecialidad').value = data.nombre;
                    document.getElementById('modalEditarEspecialidad').querySelector('form').action =
                        `/especialidades/${data.id}`;
                    new bootstrap.Modal(document.getElementById('modalEditarEspecialidad')).show();
                } catch {
                    Swal.fire('Error', 'No se pudo cargar', 'error');
                }
            }
            if (btnShow) {
                const id = btnShow.dataset.id;
                try {
                    const res = await fetch(`/especialidades/${id}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    document.getElementById('mostrarEspecialidadNombre').innerText = data.nombre;
                    new bootstrap.Modal(document.getElementById('modalShowEspecialidad')).show();
                } catch {
                    Swal.fire('Error', 'No se pudo cargar', 'error');
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
