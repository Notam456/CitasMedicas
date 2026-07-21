@extends('layouts.template')
@section('title', 'Lista de Distritos | SAGECIM')
@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')
    <div class="table-responsive bg-light rounded h-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Lista de Distritos</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDistrito">
                <i class="bi bi-plus-circle me-1"></i> Registrar Distrito
            </button>
        </div>
        <table class="table table-hover" id="distritosTable">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th class="text-center">Municipios</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal Registrar -->
    <div class="modal fade" id="modalDistrito" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Registrar Distrito</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('distritos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="nombre" required placeholder="Nombre"
                                pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                            <label>Nombre del Distrito</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Municipios <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm mb-2" id="buscarMunicipioCreate" placeholder="Buscar municipio...">
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="municipiosCreateList">
                                <span class="text-muted">Cargando municipios...</span>
                            </div>
                            <small class="text-muted">Debe seleccionar al menos 1 municipio.</small>
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

    <!-- Modal Editar -->
    <div class="modal fade" id="modalEditarDistrito" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Editar Distrito</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST" id="editForm">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required
                                pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                            <label>Nombre del Distrito</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Municipios <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm mb-2" id="buscarMunicipioEdit" placeholder="Buscar municipio...">
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="municipiosEditList">
                                <span class="text-muted">Cargando municipios...</span>
                            </div>
                            <small class="text-muted">Debe seleccionar al menos 1 municipio.</small>
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

    <!-- Modal Mostrar -->
    <div class="modal fade" id="modalShowDistrito" tabindex="-1" aria-labelledby="modalShowDistritoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalShowDistritoLabel">Datos del Distrito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nombre</label>
                            <p class="form-control" id="show_nombre"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Municipios</label>
                            <p class="form-control" id="show_municipios"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
        <script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

        <script>
            $(document).ready(function() {
                $('#distritosTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('api.distritos') }}',
                    columns: [{
                            data: 'nombre',
                            name: 'nombre'
                        },
                        {
                            data: 'municipios_count',
                            name: 'municipios_count',
                            className: 'text-center',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return '<span class="badge bg-info">' + data + '</span>';
                            }
                        },
                        {
                            data: 'action',
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

            function renderMunicipiosCheckboxes(containerId, municipios, checkedIds = []) {
                const container = document.getElementById(containerId);
                if (municipios.length === 0) {
                    container.innerHTML = '<span class="text-muted">No hay municipios disponibles.</span>';
                    return;
                }
                container.innerHTML = municipios.map(m => `
                    <div class="form-check municipio-item">
                        <input class="form-check-input" type="checkbox" name="municipios[]" value="${m.id}" id="${containerId}_${m.id}" ${checkedIds.includes(m.id) ? 'checked' : ''}>
                        <label class="form-check-label" for="${containerId}_${m.id}">${m.nombre}</label>
                    </div>
                `).join('');
            }

            function setupSearch(inputId, containerId) {
                document.getElementById(inputId).addEventListener('input', function() {
                    const filter = this.value.toLowerCase();
                    document.querySelectorAll(`#${containerId} .municipio-item`).forEach(item => {
                        const label = item.querySelector('label').textContent.toLowerCase();
                        item.style.display = label.includes(filter) ? '' : 'none';
                    });
                });
            }

            setupSearch('buscarMunicipioCreate', 'municipiosCreateList');
            setupSearch('buscarMunicipioEdit', 'municipiosEditList');

            async function cargarMunicipiosDisponibles(distritoId = null) {
                const url = distritoId
                    ? `/api/municipios-disponibles/${distritoId}`
                    : '/api/municipios-disponibles';
                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    return await res.json();
                } catch { return []; }
            }

            document.getElementById('modalDistrito').addEventListener('show.bs.modal', async function() {
                const municipios = await cargarMunicipiosDisponibles();
                renderMunicipiosCheckboxes('municipiosCreateList', municipios);
                document.getElementById('buscarMunicipioCreate').value = '';
            });

            document.addEventListener('click', async (e) => {
                const btnEdit = e.target.closest('.btn-edit');
                const btnShow = e.target.closest('.btn-show');
                if (btnEdit) {
                    const id = btnEdit.dataset.id;
                    try {
                        const res = await fetch(`/distritos/${id}/edit`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        document.getElementById('edit_id').value = data.id;
                        document.getElementById('edit_nombre').value = data.nombre;
                        document.getElementById('editForm').action = `/distritos/${data.id}`;

                        const todosMunicipios = await cargarMunicipiosDisponibles(data.id);
                        renderMunicipiosCheckboxes('municipiosEditList', todosMunicipios, data.municipios_actuales);
                        document.getElementById('buscarMunicipioEdit').value = '';

                        new bootstrap.Modal(document.getElementById('modalEditarDistrito')).show();
                    } catch {
                        Swal.fire('Error', 'No se pudo cargar', 'error');
                    }
                }
                if (btnShow) {
                    const id = btnShow.dataset.id;
                    try {
                        const res = await fetch(`/distritos/${id}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        document.getElementById('show_nombre').innerText = data.nombre;
                        document.getElementById('show_municipios').innerText = (data.municipios || []).join(', ') || 'Ninguno';
                        new bootstrap.Modal(document.getElementById('modalShowDistrito')).show();
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



    @include('layouts.footer')
@endsection
