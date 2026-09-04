@extends('layouts.template')
@section('title', 'Gestión de Citas | SAGECIM')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <style>
        .historia-inline {
            min-width: 130px;
        }
        .historia-inline .input-historia {
            font-family: monospace;
            letter-spacing: 0.5px;
        }
        .th-fh-switch {
            display: inline-flex;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            overflow: hidden;
            cursor: not-allowed;
            opacity: 0.6;
            margin-top: 0.35rem;
            user-select: none;
        }
        .th-fh-switch .seg {
            padding: 0.1rem 0.55rem;
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.4;
        }
        .th-fh-switch.editable {
            cursor: pointer;
            opacity: 1;
        }
        .th-fh-switch.editable:hover {
            border-color: #86b7fe;
        }
        .th-fh-switch .seg.th {
            background: #fff;
            color: #6c757d;
        }
        .th-fh-switch .seg.fh {
            background: #fff;
            color: #6c757d;
        }
        .th-fh-switch.th .seg.th {
            background: #198754;
            color: #fff;
        }
        .th-fh-switch.fh .seg.fh {
            background: #dc3545;
            color: #fff;
        }
        html.dark .th-fh-switch {
            border-color: #2d3037;
        }
        html.dark .th-fh-switch .seg.th,
        html.dark .th-fh-switch .seg.fh {
            background: #252830;
            color: #b0b0b0;
        }
        html.dark .th-fh-switch.th .seg.th {
            background: #1a6b3a;
            color: #fff;
        }
        html.dark .th-fh-switch.fh .seg.fh {
            background: #7a1b1b;
            color: #f08080;
        }
    </style>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Gestión de Citas</h3>
                <div class="d-flex gap-2">
                    @can('Cita')
                        <a href="{{ route('Citas.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> Agendar Cita
                        </a>
                    @endcan
                    @can('Atender Cita')
                        <a href="{{ route('morbilidad.pendientes') }}" class="btn btn-primary">
                            <i class="fas fa-clinic-medical me-1"></i> Citas por Atender
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" id="filtroForm">
            <div class="row g-3 mb-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Especialidad</label>
                    <x-searchable-select
                        name="especialidad_id"
                        id="especialidad_id"
                        placeholder="Todas las especialidades"
                        :options="$especialidades->pluck('nombre', 'id')->toArray()"
                        :selected="request('especialidad_id')"
                    />
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tipo de Cita</label>
                    <select name="tipo_paciente" id="tipo_paciente" class="form-select shadow-none">
                        <option value="">Todos</option>
                        <option value="primera_vez" {{ request('tipo_paciente') == 'primera_vez' ? 'selected' : '' }}>Primera Vez</option>
                        <option value="control" {{ request('tipo_paciente') == 'control' ? 'selected' : '' }}>Sucesiva</option>
                        <option value="orden_medica" {{ request('tipo_paciente') == 'orden_medica' ? 'selected' : '' }}>Orden Médica</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Estado de Cita</label>
                    <select name="estado" id="estado" class="form-select shadow-none">
                        <option value="">Todos</option>
                        <option value="Agendada" {{ request('estado') == 'Agendada' ? 'selected' : '' }}>Agendada</option>
                        <option value="Atendida" {{ request('estado') == 'Atendida' ? 'selected' : '' }}>Atendida</option>
                        <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Médico</label>
                    <x-searchable-select
                        name="medico_id"
                        id="medico_id"
                        placeholder="Todos los médicos"
                        :options="$medicos->mapWithKeys(function($m) {
                            return [$m->id => 'Dr. ' . $m->nombre . ' ' . $m->apellido];
                        })->toArray()"
                        :selected="request('medico_id')"
                    />
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Fecha cita desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                        value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Fecha cita hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                        value="{{ request('fecha_hasta') }}" min="{{ request('fecha_desde') }}">
                </div>
            </div>
            <div class="row g-3 mb-4 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Fecha registro desde</label>
                    <input type="date" name="fecha_registro_desde" id="fecha_registro_desde" class="form-control"
                        value="{{ request('fecha_registro_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-uppercase text-muted">Fecha registro hasta</label>
                    <input type="date" name="fecha_registro_hasta" id="fecha_registro_hasta" class="form-control"
                        value="{{ request('fecha_registro_hasta') }}" min="{{ request('fecha_registro_desde') }}">
                </div>
                <div class="col-md-1">
                    <button type="button" id="btnFiltrar" class="btn btn-primary w-100 shadow-sm">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                </div>
                <div class="col-md-1">
                    <button type="button" id="btnLimpiar" class="btn btn-secondary w-100 shadow-sm">
                        <i class="fas fa-undo me-1"></i> Limpiar
                    </button>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExcel" class="btn btn-success w-50 shadow-sm">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </button>
                        <button type="button" id="btnPdf" class="btn btn-danger w-50 shadow-sm">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive rounded shadow-sm border">
            <table id="tablaMorbilidad" class="table table-bordered table-striped mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>N° Historia</th>
                        <th>Paciente</th>
                        <th>Cédula</th>
                        <th>Fecha Cita</th>
                        <th>Especialidad</th>
                        <th>Médico</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    </div>
    </div>

    @include('partials.modal-mostrar-cita', ['modalId' => 'modalMostrarCita', 'showPdf' => true])

    @include('partials.modal-editar-diagnostico')

    @include('layouts.footer')
@endsection

@push('scripts')
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
    <script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            var table = $('#tablaMorbilidad').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('morbilidad.index') }}",
                    type: 'GET',
                    data: function(d) {
                        d.especialidad_id = $('#especialidad_id').val();
                        d.medico_id = $('#medico_id').val();
                        d.fecha_desde = $('#fecha_desde').val();
                        d.fecha_hasta = $('#fecha_hasta').val();
                        d.tipo_paciente = $('#tipo_paciente').val();
                        d.estado = $('#estado').val();
                        d.fecha_registro_desde = $('#fecha_registro_desde').val();
                        d.fecha_registro_hasta = $('#fecha_registro_hasta').val();
                    }
                },
                columns: [{
                        data: 0,
                        name: 'numero_expediente'
                    },
                    {
                        data: 1,
                        name: 'paciente'
                    },
                    {
                        data: 2,
                        name: 'cedula'
                    },
                    {
                        data: 3,
                        name: 'fecha_cita'
                    },
                    {
                        data: 4,
                        name: 'especialidad'
                    },
                    {
                        data: 5,
                        name: 'medico'
                    },
                    {
                        data: 6,
                        name: 'tipo'
                    },
                    {
                        data: 7,
                        name: 'estado'
                    },
                    {
                        data: 8,
                        name: 'fecha_registro'
                    },
                    {
                        data: 9,
                        name: 'accion',
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
                    [10, 25, 50, -1],
                    [10, 25, 50, "Todas"]
                ],
                order: [
                    [3, 'asc']
                ]
            });

            // Auto-fill fechas con min dinámico
            $('#fecha_desde').on('change', function() {
                var val = $(this).val();
                $('#fecha_hasta').attr('min', val);
                if (val) {
                    $('#fecha_hasta').val(val);
                }
            });
            $('#fecha_hasta').on('change', function() {
                if ($(this).val() && !$('#fecha_desde').val()) {
                    $('#fecha_desde').val($(this).val());
                }
            });
            $('#fecha_registro_desde').on('change', function() {
                var val = $(this).val();
                $('#fecha_registro_hasta').attr('min', val);
                if (val) {
                    $('#fecha_registro_hasta').val(val);
                }
            });
            $('#fecha_registro_hasta').on('change', function() {
                if ($(this).val() && !$('#fecha_registro_desde').val()) {
                    $('#fecha_registro_desde').val($(this).val());
                }
            });

            $('#btnFiltrar').on('click', function() {
                table.ajax.reload();
            });

            $('#btnLimpiar').on('click', function() {
                // Reset searchable selects
                $('.searchable-select-wrapper').each(function() {
                    const wrapper = $(this);
                    const input = wrapper.find('.searchable-select-input');
                    const hidden = wrapper.find('input[type="hidden"]');
                    const dropdown = wrapper.find('.searchable-select-dropdown');
                    hidden.val('');
                    input.val('');
                    dropdown.find('.searchable-select-option').removeClass('active');
                    dropdown.find('li').show();
                });
                $('#tipo_paciente').val('');
                $('#estado').val('');
                $('#fecha_desde').val('');
                $('#fecha_hasta').val('');
                $('#fecha_registro_desde').val('');
                $('#fecha_registro_hasta').val('');
                table.ajax.reload();
            });

            function confirmarExportacion(formato) {
                var params = $.param({
                    especialidad_id: $('#especialidad_id').val(),
                    medico_id: $('#medico_id').val(),
                    fecha_desde: $('#fecha_desde').val(),
                    fecha_hasta: $('#fecha_hasta').val(),
                    tipo_paciente: $('#tipo_paciente').val(),
                    estado: $('#estado').val(),
                    fecha_registro_desde: $('#fecha_registro_desde').val(),
                    fecha_registro_hasta: $('#fecha_registro_hasta').val()
                });
                var extra = formato === 'pdf' ? '&export_pdf=1' : '&export_excel=1';
                var url = "{{ route('morbilidad.index') }}?" + params + extra;

                Swal.fire({
                    title: 'Generar reporte ' + formato.toUpperCase(),
                    text: 'Se generará un reporte en ' + formato.toUpperCase() + ' con los filtros actuales. ¿Desea continuar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, generar',
                    cancelButtonText: 'Cancelar',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        if (formato === 'pdf') window.open(url, '_blank');
                        else window.location.href = url;
                    }
                });
            }

            $('#btnExcel').on('click', function() {
                confirmarExportacion('excel');
            });
            $('#btnPdf').on('click', function() {
                confirmarExportacion('pdf');
            });

            // N° Historia inline: máscara y guardado
            function aplicarMascaraHistoria(val) {
                var digitos = val.replace(/\D/g, '').slice(0, 6);
                var partes = digitos.match(/.{1,2}/g) || [];
                return partes.join('-');
            }

            $(document).on('input', '.input-historia', function() {
                var formateado = aplicarMascaraHistoria($(this).val());
                if ($(this).val() !== formateado) {
                    $(this).val(formateado);
                }
            });

            $(document).on('change', '.input-historia', function() {
                var $input = $(this);
                var valor = $input.val().trim();
                var anterior = $input.data('anterior') || '';

                if (valor === anterior) return;

                if (valor === '') {
                    $input.val(anterior);
                    return;
                }

                if (!/^\d{2}-\d{2}-\d{2}$/.test(valor)) {
                    Swal.fire('Formato inválido', 'El N° de Historia debe tener el formato 00-00-00.', 'warning');
                    $input.val(anterior);
                    return;
                }

                var url = "{{ route('expedientes.guardar', ['paciente' => '__PACIENTE__']) }}"
                    .replace('__PACIENTE__', $input.data('paciente-id'));

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ numero_expediente: valor })
                }).then(function(res) {
                    if (!res.ok) {
                        return res.json().then(function(data) {
                            throw new Error(data.message || 'No se pudo guardar el N° de Historia.');
                        });
                    }
                    $input.data('anterior', valor);
                    Swal.fire({
                        icon: 'success',
                        title: 'Guardado',
                        text: 'N° de Historia actualizado.',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }).catch(function(err) {
                    $input.val(anterior);
                    Swal.fire('Error', err.message, 'error');
                });
            });

            // Switch TH/FH
            $(document).on('click', '.th-fh-switch.editable', function() {
                var $switch = $(this);
                var url = "{{ route('citas.historia-traida', ['cita' => '__CITA__']) }}"
                    .replace('__CITA__', $switch.data('cita-id'));

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(function(res) {
                    if (!res.ok) {
                        return res.json().then(function(data) {
                            throw new Error(data.message || 'No se pudo actualizar el estado.');
                        });
                    }
                    return res.json();
                }).then(function(data) {
                    var traida = data.historia_traida ? 1 : 0;
                    $switch.data('traida', traida).toggleClass('th', !!data.historia_traida).toggleClass('fh', !data.historia_traida);
                }).catch(function(err) {
                    Swal.fire('Error', err.message, 'error');
                });
            });

            // Mostrar cita en modal
            $(document).on('click', '.btn-show-cita', async function() {
                const citaId = $(this).data('id');
                const modalElement = document.getElementById('modalMostrarCita');
                let modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(modalElement);
                }
                modalInstance.show();

                try {
                    const response = await fetch(`/morbilidad/${citaId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const cita = await response.json();
                    populateShowModal(cita, `/morbilidad/${citaId}/pdf`);
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudieron cargar los datos de la cita', 'error');
                }
            });

            $(document).on('click', '.btn-edit-cita', async function() {
                const id = $(this).data('id');
                try {
                    const res = await fetch(`/diagnosticos/${id}/edit`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    window.patologiasDisponibles = data.patologias_disponibles || [];
                    limpiarContenedoresEdicion();

                    const cita = data.cita;
                    $('#edit_id').val(cita.id);
                    $('#edit_diagnostico_libre').val(cita.diagnostico_libre || '');

                    $('#edit_info_paciente').text(`${cita.paciente.nombre} ${cita.paciente.apellido}`);
                    $('#edit_info_cedula').text(cita.paciente.cedula);
                    $('#edit_info_fecha').text(new Date(cita.fecha_cita).toLocaleDateString());
                    $('#edit_info_medico').text(`Dr. ${cita.medico.nombre} ${cita.medico.apellido}`);
                    $('#edit_info_especialidad').text(cita.medico.especialidad.nombre);

                    if (cita.patologias && cita.patologias.length) {
                        cita.patologias.forEach(pat => addEditPatologiaRow(pat.id));
                    } else {
                        addEditPatologiaRow(null);
                    }

                    document.getElementById('editForm').action =
                        `/diagnosticos/${cita.id}?redirect_to=morbilidad.index`;
                    new bootstrap.Modal(document.getElementById('modalEditarDiagnostico')).show();
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo cargar la información para editar', 'error');
                }
            });

            // Cascada: al cambiar especialidad, filtrar médicos
            const medicoEspMap = @json($medicos->pluck('especialidad_id', 'id'));

            function filtrarMedicos() {
                const espId = $('#especialidad_id').val();
                const wrapper = $('.searchable-select-wrapper[data-target="medico_id"]');
                const input = wrapper.find('.searchable-select-input');
                const hidden = wrapper.find('#medico_id');
                const dropdown = wrapper.find('.searchable-select-dropdown');
                const currentVal = hidden.val();

                dropdown.find('.searchable-select-option').each(function() {
                    const $opt = $(this);
                    const val = $opt.data('value');
                    if (val === '') return;
                    const espDelMedico = medicoEspMap[val];
                    const match = !espId || espDelMedico == espId;
                    $opt.closest('li').toggle(match);
                });

                if (currentVal) {
                    const $current = dropdown.find(`.searchable-select-option[data-value="${currentVal}"]`);
                    if ($current.length === 0 || $current.closest('li').is(':hidden')) {
                        hidden.val('');
                        input.val('');
                        dropdown.find('.searchable-select-option').removeClass('active');
                    }
                }
            }

            $('#especialidad_id').on('change', filtrarMedicos);
            filtrarMedicos();
        });
    </script>
@endpush
