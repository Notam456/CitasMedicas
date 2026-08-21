@extends('layouts.template')
@section('title', 'Auditoría de movimientos | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Auditoría de movimientos del sistema</h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form id="formFiltros" class="row g-3">
            <div class="col-md-3">
                <label for="filterUser" class="form-label">Usuario</label>
                <select id="filterUser" class="form-select">
                    <option value="">Todos los usuarios</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterEvent" class="form-label">Evento</label>
                <select id="filterEvent" class="form-select">
                    <option value="">Todos los eventos</option>
                    @foreach ($events as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterFechaDesde" class="form-label">Fecha Desde</label>
                <input type="date" id="filterFechaDesde" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="filterFechaHasta" class="form-label">Fecha Hasta</label>
                <input type="date" id="filterFechaHasta" class="form-control">
            </div>
            <div class="col-12 text-end">
                <button type="button" id="btnLimpiarFiltros" class="btn btn-secondary btn-sm me-2">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar Filtros
                </button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1"></i> Aplicar Filtros
                </button>
            </div>
        </form>



        <div class="table-responsive rounded shadow-sm border">
            <table class="table table-bordered table-striped mb-0" id="tablaAuditorias" style="width:100%">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Evento</th>
                        <th>Módulo</th>
                        <th>Fecha / Hora</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>



        <div class="modal fade" id="modalDetalleAuditoria" tabindex="-1" aria-labelledby="modalDetalleAuditoriaLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetalleAuditoriaLabel">
                            Detalle de la Auditoría
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-4 border-bottom pb-3">
                            <div class="col-md-6">
                                <strong>Usuario:</strong> <span id="auditUsuario" class="text-secondary"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha / Hora:</strong> <span id="auditFecha" class="text-secondary"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Evento:</strong> <span id="auditEvento"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Módulo:</strong> <span id="auditModelo" class="text-secondary"></span>
                            </div>
                        </div>

                        <h6 class="mb-3 text-primary"><i class="bi bi-code-square me-1"></i>Valores Modificados</h6>
                        <div id="contenedorValores" class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Campo</th>
                                        <th>Valor Anterior</th>
                                        <th>Valor Nuevo</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyValores"></tbody>
                            </table>
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
                var table = $('#tablaAuditorias').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('auditoria.index') }}',
                        data: function(d) {
                            d.user_id = $('#filterUser').val();
                            d.event = $('#filterEvent').val();
                            d.fecha_desde = $('#filterFechaDesde').val();
                            d.fecha_hasta = $('#filterFechaHasta').val();
                        }
                    },
                    columns: [{
                            data: 0,
                            name: 'user_id'
                        },
                        {
                            data: 1,
                            name: 'event'
                        },
                        {
                            data: 2,
                            name: 'auditable_type'
                        },
                        {
                            data: 3,
                            name: 'created_at'
                        },
                        {
                            data: 4,
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                    language: {
                        url: "{{ asset('vendor/datatables/es-ES.json') }}"
                    },
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    order: [
                        [3, 'desc']
                    ]
                });

                $('#formFiltros').on('submit', function(e) {
                    e.preventDefault();
                    table.draw();
                });

                $('#btnLimpiarFiltros').on('click', function() {
                    $('#formFiltros')[0].reset();
                    table.draw();
                });

                $(document).on('click', '.btn-show', async function() {
                    var auditId = $(this).data('id') || $(this).data('group-ids');
                    try {
                        var response = await fetch(`/auditoria/${auditId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!response.ok) throw new Error('Error al obtener datos');
                        var data = await response.json();

                        $('#auditUsuario').text(data.usuario);
                        $('#auditFecha').text(data.fecha);
                        $('#auditEvento').text(data.evento);
                        var modelText = data.modelo_id === 'Lote completo' ? data.modelo :
                            `${data.modelo} (ID: ${data.modelo_id})`;
                        $('#auditModelo').text(modelText);


                        var tbody = $('#tbodyValores');
                        tbody.empty();

                        var oldList = data.old_values || [];
                        var newList = data.new_values || [];

                        // Map by field key
                        var itemsMap = {};

                        oldList.forEach(function(item) {
                            if (['password', 'remember_token'].includes(item.field_key)) return;
                            itemsMap[item.field_key] = {
                                label: item.label,
                                oldVal: item.value,
                                newVal: null
                            };
                        });

                        newList.forEach(function(item) {
                            if (['password', 'remember_token'].includes(item.field_key)) return;
                            if (!itemsMap[item.field_key]) {
                                itemsMap[item.field_key] = {
                                    label: item.label,
                                    oldVal: null,
                                    newVal: item.value
                                };
                            } else {
                                itemsMap[item.field_key].newVal = item.value;
                            }
                        });

                        function escapeHtml(str) {
                            return String(str)
                                .replace(/&/g, "&amp;")
                                .replace(/</g, "&lt;")
                                .replace(/>/g, "&gt;")
                                .replace(/"/g, "&quot;")
                                .replace(/'/g, "&#039;");
                        }

                        function formatVal(v) {
                            if (v === null || v === undefined) {
                                return '<span class="text-muted">N/A</span>';
                            }
                            if (typeof v === 'object') {
                                return escapeHtml(JSON.stringify(v));
                            }
                            return escapeHtml(String(v));
                        }

                        var keys = Object.keys(itemsMap);

                        if (keys.length === 0) {
                            tbody.append(
                                '<tr><td colspan="3" class="text-center text-muted">No hay campos para mostrar.</td></tr>'
                            );
                        } else {
                            keys.forEach(function(key) {
                                var item = itemsMap[key];
                                var oldValText = formatVal(item.oldVal);
                                var newValText = formatVal(item.newVal);

                                tbody.append(`
                                <tr>
                                    <td><strong>${escapeHtml(item.label)}</strong></td>
                                    <td class="text-danger">${oldValText}</td>
                                    <td class="text-success">${newValText}</td>
                                </tr>
                            `);
                            });
                        }

                        var modal = new bootstrap.Modal(document.getElementById('modalDetalleAuditoria'));
                        modal.show();
                    } catch (error) {
                        console.error(error);
                        Swal.fire('Error', 'No se pudo cargar la información de la auditoría.', 'error');
                    }
                });
            });
        </script>
    @endpush
