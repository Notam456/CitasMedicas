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
                    <th>Municipios</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal Registrar -->
    <div class="modal fade" id="modalDistrito" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Registrar Distrito</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('distritos.store') }}" method="POST" id="createForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="nombre" required placeholder="Nombre"
                                pattern="[A-Za-zÁÉÍÓÚáéíóúñÑüÜ\s]+" title="Solo se permiten letras y espacios">
                            <label>Nombre del Distrito</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Municipios <span class="text-danger">*</span></label>
                            <small class="text-muted d-block mb-2">Seleccione al menos un municipio que pertenezca a este distrito.</small>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchMunicipiosCreate" placeholder="Buscar municipios...">
                            </div>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="municipiosListCreate">
                                <div class="text-center text-muted py-3">Cargando municipios...</div>
                            </div>
                            <div id="errorMunicipiosCreate" class="text-danger small mt-1" style="display:none;">Debe seleccionar al menos un municipio.</div>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
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
                            <small class="text-muted d-block mb-2">Seleccione al menos un municipio que pertenezca a este distrito.</small>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchMunicipiosEdit" placeholder="Buscar municipios...">
                            </div>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="municipiosListEdit">
                                <div class="text-center text-muted py-3">Cargando municipios...</div>
                            </div>
                            <div id="errorMunicipiosEdit" class="text-danger small mt-1" style="display:none;">Debe seleccionar al menos un municipio.</div>
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
    <div class="modal fade" id="modalShowDistrito" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Datos del Distrito</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID:</strong> <span id="show_id"></span></p>
                    <p><strong>Nombre:</strong> <span id="show_nombre"></span></p>
                    <p><strong>Municipios:</strong></p>
                    <ul id="show_municipios"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
        <script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

        <script>
            function renderMunicipiosCheckboxes(containerId, municipios, selectedIds = []) {
                const container = document.getElementById(containerId);
                if (municipios.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted py-3">No hay municipios disponibles</div>';
                    return;
                }
                let html = '';
                municipios.forEach(m => {
                    const checked = selectedIds.includes(m.id) ? 'checked' : '';
                    html += `
                        <div class="form-check municipio-item">
                            <input class="form-check-input" type="checkbox" name="municipios[]" value="${m.id}" id="${containerId}_${m.id}" ${checked}>
                            <label class="form-check-label" for="${containerId}_${m.id}">${m.nombre}</label>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }

            function filterMunicipios(searchId, containerId) {
                const search = document.getElementById(searchId).value.toLowerCase();
                const items = document.getElementById(containerId).querySelectorAll('.municipio-item');
                items.forEach(item => {
                    const label = item.querySelector('label').textContent.toLowerCase();
                    item.style.display = label.includes(search) ? '' : 'none';
                });
            }

            async function loadMunicipiosCreate() {
                try {
                    const res = await fetch('{{ route("api.municipios-disponibles") }}');
                    const data = await res.json();
                    renderMunicipiosCheckboxes('municipiosListCreate', data);
                } catch {
                    document.getElementById('municipiosListCreate').innerHTML = '<div class="text-center text-danger py-3">Error al cargar municipios</div>';
                }
            }

            async function loadMunicipiosEdit(distritoId, selectedIds) {
                try {
                    const res = await fetch('{{ route("api.municipios-disponibles") }}/' + distritoId);
                    const data = await res.json();
                    renderMunicipiosCheckboxes('municipiosListEdit', data, selectedIds);
                } catch {
                    document.getElementById('municipiosListEdit').innerHTML = '<div class="text-center text-danger py-3">Error al cargar municipios</div>';
                }
            }

            $(document).ready(function() {
                const table = $('#distritosTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('api.distritos') }}',
                    columns: [
                        { data: 'nombre', name: 'nombre' },
                        { data: 'municipios_count', name: 'municipios_count', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
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

                $('#searchMunicipiosCreate').on('keyup', function() {
                    filterMunicipios('searchMunicipiosCreate', 'municipiosListCreate');
                });

                $('#searchMunicipiosEdit').on('keyup', function() {
                    filterMunicipios('searchMunicipiosEdit', 'municipiosListEdit');
                });

                $('#modalDistrito').on('show.bs.modal', function() {
                    document.getElementById('searchMunicipiosCreate').value = '';
                    loadMunicipiosCreate();
                });

                $('#createForm').on('submit', function(e) {
                    const checked = document.querySelectorAll('#municipiosListCreate input[name="municipios[]"]:checked');
                    if (checked.length === 0) {
                        e.preventDefault();
                        document.getElementById('errorMunicipiosCreate').style.display = 'block';
                        return false;
                    }
                    document.getElementById('errorMunicipiosCreate').style.display = 'none';
                });

                $('#editForm').on('submit', function(e) {
                    const checked = document.querySelectorAll('#municipiosListEdit input[name="municipios[]"]:checked');
                    if (checked.length === 0) {
                        e.preventDefault();
                        document.getElementById('errorMunicipiosEdit').style.display = 'block';
                        return false;
                    }
                    document.getElementById('errorMunicipiosEdit').style.display = 'none';
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
                            document.getElementById('searchMunicipiosEdit').value = '';
                            await loadMunicipiosEdit(data.id, data.municipios_actuales);
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
                            document.getElementById('show_id').innerText = data.id;
                            document.getElementById('show_nombre').innerText = data.nombre;
                            const ul = document.getElementById('show_municipios');
                            ul.innerHTML = '';
                            (data.municipios || []).forEach(m => {
                                const li = document.createElement('li');
                                li.textContent = m;
                                ul.appendChild(li);
                            });
                            new bootstrap.Modal(document.getElementById('modalShowDistrito')).show();
                        } catch {
                            Swal.fire('Error', 'No se pudo cargar', 'error');
                        }
                    }
                });
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
