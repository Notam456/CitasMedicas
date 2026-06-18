@extends('layouts.template')
@section('title', 'Lista de Medicamentos | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Medicamentos</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMedicamento">
                <i class="bi bi-capsule me-1"></i> Registrar Medicamento
            </button>
        </div>

        <table class="table table-hover" id="tablaMedicamentos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal Registrar Medicamento -->
    <div class="modal fade" id="modalMedicamento" tabindex="-1" aria-labelledby="modalMedicamentoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMedicamentoLabel">Registrar Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('medicamentos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" value="{{ old('nombre') }}" class="form-control" id="nombreMedicamento"
                                name="nombre" placeholder="Nombre del medicamento" required
                                pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                            <label for="nombreMedicamento">Nombre</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="descripcionMedicamento" name="descripcion"
                                placeholder="Descripción del medicamento" style="height: 100px;">{{ old('descripcion') }}</textarea>
                            <label for="descripcionMedicamento">Descripción</label>
                            @error('descripcion')
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

    <!-- Modal Editar Medicamento -->
    <div class="modal fade" id="modalEditarMedicamento" tabindex="-1" aria-labelledby="modalEditarMedicamentoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarMedicamentoLabel">Editar Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="editarNombreMedicamento" name="nombre"
                                placeholder="Nombre del medicamento" required
                                pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                            <label for="editarNombreMedicamento">Nombre</label>
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="editarDescripcionMedicamento" name="descripcion"
                                placeholder="Descripción del medicamento" style="height: 100px;"></textarea>
                            <label for="editarDescripcionMedicamento">Descripción</label>
                            @error('descripcion')
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
    <div class="modal fade" id="modalShowMedicamento" tabindex="-1" aria-labelledby="modalShowMedicamentoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowMedicamentoLabel">Datos del Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <label for="mostrarNombreMedicamento" class="form-label">Nombre</label>
                    <div class="form-floating mb-3">
                        <p class="form-control-plaintext" id="mostrarMedicamentoNombre"></p>
                    </div>
                    <label for="mostrarDescripcionMedicamento" class="form-label">Descripción</label>
                    <div class="form-floating mb-3">
                        <p class="form-control-plaintext" id="mostrarMedicamentoDescripcion"></p>
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
            $('#tablaMedicamentos').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('medicamentos.index') }}',
                columns: [{
                        data: 0,
                        name: 'nombre'
                    },
                    {
                        data: 1,
                        name: 'descripcion'
                    },
                    {
                        data: 2,
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
                    const res = await fetch(`/medicamentos/${id}/edit`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    document.getElementById('id').value = data.id;
                    document.getElementById('editarNombreMedicamento').value = data.nombre;
                    document.getElementById('editarDescripcionMedicamento').value = data.descripcion || '';
                    document.getElementById('modalEditarMedicamento').querySelector('form').action =
                        `/medicamentos/${data.id}`;
                    new bootstrap.Modal(document.getElementById('modalEditarMedicamento')).show();
                } catch {
                    Swal.fire('Error', 'No se pudo cargar', 'error');
                }
            }
            if (btnShow) {
                const id = btnShow.dataset.id;
                try {
                    const res = await fetch(`/medicamentos/${id}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    document.getElementById('mostrarMedicamentoNombre').innerText = data.nombre;
                    document.getElementById('mostrarMedicamentoDescripcion').innerText = data.descripcion || 'Sin descripción';
                    new bootstrap.Modal(document.getElementById('modalShowMedicamento')).show();
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
