@extends('layouts.template')
@section('title', 'Agendar Cita')

@include('layouts.sidebar')

@section('content')
    @include('layouts.navbar')

    <div class="container-fluid px-4 py-4">

        <style>
            .calendar-day-available:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                z-index: 1;
                position: relative;
            }
            .calendar-day-available .calendar-slot {
                transition: background-color 0.2s;
            }
            .calendar-day-available:hover .calendar-slot {
                background-color: #d1e7dd;
            }
            #barra_busqueda_paciente .form-control,
            #barra_busqueda_paciente .form-control:hover,
            #barra_busqueda_paciente .form-control:focus,
            #barra_busqueda_paciente .form-control:active {
                border-color: #ced4da;
                box-shadow: none !important;
                outline: none;
                transform: none !important;
                transition: none;
            }
            #barra_busqueda_paciente .btn,
            #barra_busqueda_paciente .btn:hover,
            #barra_busqueda_paciente .btn:focus,
            #barra_busqueda_paciente .btn:active {
                box-shadow: none !important;
                transform: none !important;
                transition: none;
            }
        </style>

        <div class="mb-4">
            <h2 class="fw-bold text-primary border-bottom pb-2">Agendar Cita</h2>
            <p class="text-muted small mb-0 mt-2">
                Los campos marcados con <span class="text-danger fw-bold">*</span> son obligatorios.
            </p>
        </div>

        @php
            $old_estado = old('estado_id');
            $old_municipio = old('municipio_id');
        @endphp

        <form action="{{ route('Citas.store') }}" method="POST" class="card shadow-sm border-0" id="form-cita">
            @csrf
            <input type="hidden" name="especialidad_id" id="input_especialidad_id" value="{{ old('especialidad_id', $id ?? '') }}">

            <div class="card-body p-4">

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                    <h4 class="text-secondary mb-0"><i class="fas fa-user-injured me-2"></i>Datos del Paciente</h4>
                    <div class="position-relative" id="barra_busqueda_paciente" style="max-width: 380px; flex: 1 1 320px;">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="buscar_paciente" class="form-control"
                                placeholder="Cédula o N° de historia…" autocomplete="off">
                            <button type="button" class="btn btn-primary text-nowrap" id="btn_buscar_paciente">Buscar</button>
                            <button type="button" class="btn btn-outline-secondary" id="btn_limpiar_busqueda" title="Limpiar búsqueda">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                        <small id="mensaje_busqueda" class="position-absolute bottom-100 start-0 w-100 text-center small d-none mb-1" aria-live="polite"></small>
                    </div>
                </div>
                <div class="row g-3 bg-light p-3 rounded mb-4 border">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">N° Historia</label>
                        <input type="text" name="numero_expediente" id="input_expediente"
                            class="form-control @error('numero_expediente') is-invalid @enderror" placeholder="00-00-00"
                            value="{{ old('numero_expediente') }}" maxlength="8" autocomplete="off">
                        @error('numero_expediente')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="input_nombre"
                            class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required
                            pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+" maxlength="255"
                            oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g,'')">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input type="text" name="apellido" id="input_apellido"
                            class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido') }}"
                            required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+" maxlength="255"
                            oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g,'')">
                        @error('apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Cédula <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="cedula_tipo" id="input_cedula_tipo" class="form-select @error('cedula') is-invalid @enderror" style="max-width: 60px;" required>
                                <option value="V" {{ old('cedula_tipo') == 'E' ? '' : 'selected' }}>V</option>
                                <option value="E" {{ old('cedula_tipo') == 'E' ? 'selected' : '' }}>E</option>
                            </select>
                            <input type="tel" name="cedula" id="input_cedula" class="form-control @error('cedula') is-invalid @enderror" placeholder="12345678"
                                value="{{ old('cedula') }}" required inputmode="numeric" pattern="[0-9]*" minlength="7" maxlength="20"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        </div>
                        @error('cedula')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" name="telefono" id="input_telefono"
                            class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}"
                            required pattern="[\d\-\(\)\s\+]+" maxlength="15">
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">F. Nacimiento <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_nacimiento" id="input_fecha"
                            class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                            value="{{ old('fecha_nacimiento') }}" required min="1900-01-01" max="{{ date('Y-m-d') }}">
                        @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Rif</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">J</span>
                            <input type="text" name="rif" id="input_rif"
                                class="form-control @error('rif') is-invalid @enderror" value="{{ old('rif') }}"
                                placeholder="123456789" pattern="[0-9]*" maxlength="20" title="Solo números">
                        </div>
                        @error('rif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Sexo <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 pt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sexo" id="input_sexo_m"
                                    value="Masculino" {{ old('sexo') == 'Masculino' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="input_sexo_m">Masculino</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sexo" id="input_sexo_f" value="Femenino"
                                    {{ old('sexo') == 'Femenino' ? 'checked' : '' }}>
                                <label class="form-check-label" for="input_sexo_f">Femenino</label>
                            </div>
                        </div>
                        @error('sexo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <h6 class="text-secondary border-bottom pb-2">Ubicación del Paciente</h6>

                    <!-- Estado -->
                    <div class="col-md-4">
                        <label class="form-label" for="select-estado">Estado <span class="text-danger">*</span></label>
                        <select name="estado_id" id="select-estado" class="form-select" required>
                            <option value="">Seleccione Estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id }}"
                                    {{ $old_estado == $estado->id ? 'selected' : (!$old_estado && isset($defaultEstadoId) && $estado->id == $defaultEstadoId ? 'selected' : '') }}>
                                    {{ $estado->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Municipio -->
                    <div class="col-md-4">
                        <label class="form-label" for="select-municipio">Municipio <span class="text-danger">*</span></label>
                        <select name="municipio_id" id="select-municipio" class="form-select" required>
                            <option value="">Seleccione Municipio</option>
                        </select>
                    </div>

                    <!-- Parroquia -->
                    <div class="col-md-4">
                        <label class="form-label" for="select-parroquia">Parroquia <span class="text-danger">*</span></label>
                        <select name="parroquia_id" id="select-parroquia"
                            class="form-select @error('parroquia_id') is-invalid @enderror" required>
                            <option value="">Seleccione Parroquia</option>
                        </select>
                        @error('parroquia_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div class="col-md-12">
                        <label class="form-label">Dirección exacta</label>
                        <input type="text" name="direccion" id="input_direccion" value="{{ old('direccion') }}"
                            class="form-control @error('direccion') is-invalid @enderror" maxlength="255">
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h4 class="text-secondary mb-3"><i class="fas fa-calendar-check me-2"></i>Datos de la Cita</h4>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Especialidad <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-stethoscope"></i></span>
                            <select name="especialidad_id" id="select-especialidad" class="form-select shadow-none @error('especialidad_id') is-invalid @enderror" required>
                                <option value="">Seleccione Especialidad</option>
                                @foreach ($especialidades as $e)
                                    <option value="{{ $e->id }}" {{ old('especialidad_id') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                            @error('especialidad_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="aviso_suspension" class="mb-2 text-danger small fw-bold animate__animated animate__fadeIn" style="display: none; background-color: #f8d7da; border: 1px solid #f5c2c7; padding: 6px 12px; border-radius: .25rem;"></div>
                        <label class="form-label fw-bold small text-uppercase text-muted">Médico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user-md"></i></span>
                            <select name="medico_id" id="select-medico" class="form-select shadow-none">
                                <option value="">Seleccione Médico</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 fw-bold small text-uppercase text-muted">
                        <label class="form-label">Tipo de atención <span class="text-danger">*</span></label>
                        <select name="tipo_paciente" id="tipo_paciente" class="form-select @error('tipo_paciente') is-invalid @enderror" required>
                            <option value="">Seleccione una opción</option>
                            <option value="primera_vez" {{ old('tipo_paciente') == 'primera_vez' ? 'selected' : '' }}>Primera vez</option>
                            <option value="control" {{ old('tipo_paciente') == 'control' ? 'selected' : '' }}>Control / Sucesivo</option>
                            <option value="orden_medica" {{ old('tipo_paciente') == 'orden_medica' ? 'selected' : '' }}>Orden Médica</option>
                        </select>
                        @error('tipo_paciente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <br>

                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <div class="btn-group shadow-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary px-3" id="btn-mes-anterior">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-light fw-bold text-capitalize" style="min-width: 150px;" id="mes-actual"
                                disabled>
                            </button>
                            <button type="button" class="btn btn-outline-secondary px-3" id="btn-mes-siguiente">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive rounded shadow-sm border">
                        <div
                            class="row g-0 bg-light border-bottom text-center fw-bold py-2 text-muted small text-uppercase">
                            <div class="col" style="width: 14.28%;">Dom</div>
                            <div class="col" style="width: 14.28%;">Lun</div>
                            <div class="col" style="width: 14.28%;">Mar</div>
                            <div class="col" style="width: 14.28%;">Mie</div>
                            <div class="col" style="width: 14.28%;">Jue</div>
                            <div class="col" style="width: 14.28%;">Vie</div>
                            <div class="col" style="width: 14.28%;">Sab</div>
                        </div>


                        <div id="calendario-grid" class="row g-0 bg-white" style="min-height: 400px;">
                            <!-- lo llena el JavaScript -->
                        </div>
                    </div>
                </div>
                <br>

                <div class="row g-3">
                    <div class="col-md-4 fw-bold small text-uppercase text-muted">
                        <label class="form-label">Fecha de Cita <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_cita" id="input_fecha_cita"
                            class="form-control @error('fecha_cita') is-invalid @enderror" required readonly
                            value="{{ old('fecha_cita') }}" min="{{ date('Y-m-d') }}">
                        @error('fecha_cita')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <input type="hidden" name="calendario_id" id="input_calendario_id"
                            value="{{ old('calendario_id') }}">
                        <span id="old-data"
                            data-especialidad="{{ old('especialidad_id') }}"
                            data-estado="{{ old('estado_id') }}"
                            data-municipio="{{ old('municipio_id') }}"
                            data-parroquia="{{ old('parroquia_id') }}"
                            data-medico="{{ old('medico_id') }}"
                            data-tipo="{{ old('tipo_paciente') }}"
                            data-fecha="{{ old('fecha_cita') }}"
                            data-calendario="{{ old('calendario_id') }}"
                            data-paciente-id="{{ session('paciente_id') }}"></span>
                        @isset($defaultEstadoId)
                            <span id="default-location"
                                data-default-estado="{{ $defaultEstadoId }}"
                                data-default-municipio="{{ $defaultMunicipioId }}"></span>
                        @endisset
                    </div>
                    <div class="col-md-8 fw-bold small text-uppercase text-muted">
                        <label class="form-label">Observación</label>
                        <textarea name="observacion" class="form-control @error('observacion') is-invalid @enderror" rows="1"
                            placeholder="Síntomas o nota..." maxlength="5000">{{ old('observacion') }}</textarea>
                        @error('observacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-end py-3">
                <a href="{{ route('dashboard') }}" id="btn-cancelar-cita" class="btn btn-outline-danger me-2">Cancelar</a>
                <button type="submit" class="btn btn-success px-5">Confirmar y Agendar Cita</button>
            </div>
        </form>
    </div>

    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script>
        const especialidadesSoloFemenino = @json(config('citas.especialidades_solo_femenino'));
        const HAY_ERRORES_SERVIDOR = {{ $errors->any() ? 'true' : 'false' }};
        const CITA_REGISTRADA = {{ session('cita_registrada') ? 'true' : 'false' }};
        const DRAFT_KEY = 'sagecim_cita_draft_{{ (int) auth()->id() }}';

        function aplicarRestriccionSexo() {
            const sexoMasculino = document.getElementById('input_sexo_m').checked;
            const selectEsp = document.getElementById('select-especialidad');

            if (!selectEsp) return;

            let seleccionBloqueada = false;
            Array.from(selectEsp.options).forEach(opt => {
                const esBloqueada = especialidadesSoloFemenino.includes(opt.textContent.trim());
                opt.disabled = sexoMasculino && esBloqueada;
                if (esBloqueada && opt.selected) seleccionBloqueada = true;
            });

            if (seleccionBloqueada) {
                selectEsp.value = '';
                selectEsp.dispatchEvent(new Event('change'));
            }
        }
        function llenarFormularioPaciente(datos) {
            const cedulaParts = datos.cedula ? datos.cedula.split('-') : ['V', ''];
            document.getElementById('input_cedula_tipo').value = cedulaParts[0] || 'V';
            document.getElementById('input_cedula').value = cedulaParts.slice(1).join('-') || '';
            document.getElementById('input_cedula_tipo').disabled = true;
            document.getElementById('input_cedula').readOnly = true;

            const rifParts = datos.rif ? datos.rif.split('-') : [];
            document.getElementById('input_rif').value = rifParts.length > 1 ? rifParts.slice(1).join('-') : '';
            const inputExpediente = document.getElementById('input_expediente');
            inputExpediente.value = datos?.expediente?.numero_expediente || '';
            inputExpediente.readOnly = true;
            inputExpediente.classList.add('bg-light');
            document.getElementById('input_nombre').value = datos.nombre;
            document.getElementById('input_apellido').value = datos.apellido;
            document.getElementById('input_fecha').value = datos.fecha_nacimiento;
            document.getElementById('input_telefono').value = datos.telefono;
            document.getElementById('input_direccion').value = datos.direccion;

            if (datos.sexo === 'Masculino') {
                document.getElementById('input_sexo_m').checked = true;
            } else if (datos.sexo === 'Femenino') {
                document.getElementById('input_sexo_f').checked = true;
            }

            document.getElementById('input_sexo_m').disabled = true;
            document.getElementById('input_sexo_f').disabled = true;

            const p = datos?.parroquia;
            const m = p?.municipio;
            const e = m?.estado;

            if (e?.id && m?.id && p?.id) {
                document.getElementById('select-estado').value = e.id;
                let selectM = document.getElementById('select-municipio');
                selectM.innerHTML = `<option value="${m.id}" selected>${m.nombre}</option>`;

                let selectP = document.getElementById('select-parroquia');
                selectP.innerHTML = `<option value="${p.id}" selected>${p.nombre}</option>`;

                document.getElementById('select-estado').disabled = true;
                document.getElementById('select-municipio').disabled = true;
                document.getElementById('select-parroquia').disabled = true;
            } else {
                document.getElementById('select-estado').disabled = false;
                document.getElementById('select-municipio').disabled = false;
                document.getElementById('select-parroquia').disabled = false;
            }

            document.querySelectorAll('#input_rif, #input_nombre, #input_apellido, #input_fecha, #input_telefono, #input_direccion, #input_cedula').forEach(el => el.readOnly = true);

            window.pacienteId = datos.id;
            actualizarTipoPaciente();
            aplicarRestriccionSexo();
            guardarDraft();
        }

        function limpiarFormularioPaciente() {
            document.getElementById('input_cedula_tipo').disabled = false;
            document.getElementById('input_cedula').readOnly = false;
            document.getElementById('input_rif').value = '';
            const inputExpediente = document.getElementById('input_expediente');
            inputExpediente.value = '';
            inputExpediente.readOnly = false;
            inputExpediente.classList.remove('bg-light');
            document.getElementById('input_nombre').value = '';
            document.getElementById('input_apellido').value = '';
            document.getElementById('input_fecha').value = '';
            document.getElementById('input_telefono').value = '';
            document.getElementById('input_direccion').value = '';
            document.getElementById('input_sexo_m').checked = false;
            document.getElementById('input_sexo_f').checked = false;
            document.getElementById('input_sexo_m').disabled = false;
            document.getElementById('input_sexo_f').disabled = false;
            aplicarRestriccionSexo();

            document.getElementById('select-estado').value = '';
            document.getElementById('select-estado').disabled = false;
            document.getElementById('select-municipio').innerHTML = '<option value="">Seleccione Municipio</option>';
            document.getElementById('select-municipio').disabled = false;
            document.getElementById('select-parroquia').innerHTML = '<option value="">Seleccione Parroquia</option>';
            document.getElementById('select-parroquia').disabled = false;

            document.querySelectorAll('#input_rif, #input_nombre, #input_apellido, #input_fecha, #input_telefono, #input_direccion, #input_cedula').forEach(el => el.readOnly = false);

            window.pacienteId = null;
            setDefaultLocation();
            guardarDraft();
        }

        let temporizadorMensaje = null;

        function mostrarMensaje(texto, tipo) {
            const mensaje = document.getElementById('mensaje_busqueda');
            const colores = {
                success: 'text-success',
                danger: 'text-danger',
                warning: 'text-warning',
                primary: 'text-primary',
                muted: 'text-muted'
            };

            if (temporizadorMensaje) clearTimeout(temporizadorMensaje);

            mensaje.style.transition = 'none';
            mensaje.classList.remove('d-none', 'opacity-0', 'text-success', 'text-danger', 'text-warning', 'text-primary', 'text-muted');
            mensaje.textContent = texto;
            mensaje.classList.add(colores[tipo] || 'text-muted');

            temporizadorMensaje = setTimeout(() => {
                mensaje.style.transition = 'opacity 0.5s ease';
                mensaje.classList.add('opacity-0');
                setTimeout(() => {
                    mensaje.classList.add('d-none');
                    mensaje.classList.remove('opacity-0');
                }, 500);
            }, 3500);
        }

        async function buscarPaciente() {
            const q = document.getElementById('buscar_paciente').value.trim();

            if (!q) {
                mostrarMensaje('Ingrese una cédula o N° de historia para buscar.', 'danger');
                return;
            }

            mostrarMensaje('Buscando...', 'warning');

            try {
                const res = await fetch("{{ route('paciente.buscar') }}?q=" + encodeURIComponent(q));
                const data = await res.json();

                if (data.encontrado) {
                    llenarFormularioPaciente(data.datos);
                    mostrarMensaje('Paciente encontrado. Datos autocompletados.', 'success');
                } else if (data.liberado) {
                    limpiarFormularioPaciente();
                    const motivoTxt = { fallecido: 'Paciente fallecido', sin_retorno: 'Sin retorno (muchos años sin asistir)', trasladado: 'Trasladado a otro centro' }[data.motivo] || data.motivo;
                    mostrarMensaje('Este N° de historia perteneció a ' + data.paciente + ' (' + motivoTxt + ', liberado el ' + data.fecha_liberacion + ') y está libre para asignar. No se puede vincular a un paciente ya registrado.', 'warning');
                } else {
                    limpiarFormularioPaciente();

                    const valor = data.valor || '';
                    if (data.tipo === 'cedula' && valor) {
                        const partes = valor.split('-');
                        if (partes.length > 1) {
                            document.getElementById('input_cedula_tipo').value = partes[0];
                            document.getElementById('input_cedula').value = partes.slice(1).join('-');
                        } else {
                            document.getElementById('input_cedula').value = valor;
                        }
                        mostrarMensaje('Paciente no registrado. Complete los datos restantes.', 'primary');
                    } else if (data.tipo === 'historia' && valor) {
                        document.getElementById('input_expediente').value = valor;
                        mostrarMensaje('Paciente no registrado. Complete los datos restantes.', 'primary');
                    } else if (data.tipo === 'incompleto') {
                        mostrarMensaje('Número incompleto: cédula (7+ dígitos) o N° de historia (6 dígitos).', 'warning');
                    } else {
                        mostrarMensaje('Paciente no registrado. Llene los campos.', 'primary');
                    }

                    guardarDraft();
                }
            } catch (error) {
                console.error(error);
                mostrarMensaje('Error al procesar la solicitud.', 'danger');
            }
        }

        document.getElementById('btn_buscar_paciente').addEventListener('click', buscarPaciente);

        document.getElementById('buscar_paciente').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarPaciente();
            }
        });

        document.getElementById('btn_limpiar_busqueda').addEventListener('click', function () {
            document.getElementById('buscar_paciente').value = '';
            limpiarFormularioPaciente();
            if (temporizadorMensaje) clearTimeout(temporizadorMensaje);
            const mensaje = document.getElementById('mensaje_busqueda');
            mensaje.style.transition = 'none';
            mensaje.classList.add('d-none');
            document.getElementById('buscar_paciente').focus();
        });

        async function cargarMunicipios(estadoId, selectedId = '') {
            const selectMunicipio = document.getElementById('select-municipio');
            const selectParroquia = document.getElementById('select-parroquia');

            selectMunicipio.innerHTML = '<option value="">Seleccione Municipio</option>';
            selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';

            if (!estadoId) return;

            try {
                const res = await fetch(`/api/municipios/${estadoId}`);
                const data = await res.json();
                const fragment = document.createDocumentFragment();
                data.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nombre;
                    fragment.appendChild(opt);
                });
                selectMunicipio.appendChild(fragment);
                if (selectedId) selectMunicipio.value = selectedId;
            } catch (err) {
                console.error(err);
            }
        }

        async function cargarParroquias(municipioId, selectedId = '') {
            const selectParroquia = document.getElementById('select-parroquia');
            selectParroquia.innerHTML = '<option value="">Seleccione Parroquia</option>';

            if (!municipioId) return;

            try {
                const res = await fetch(`/api/parroquias/${municipioId}`);
                const data = await res.json();
                const fragment = document.createDocumentFragment();
                data.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nombre;
                    fragment.appendChild(opt);
                });
                selectParroquia.appendChild(fragment);
                if (selectedId) selectParroquia.value = selectedId;
            } catch (err) {
                console.error(err);
            }
        }

        async function cargarMedicos(especialidadId, selectedId = '') {
            const selectMedico = document.getElementById('select-medico');
            selectMedico.innerHTML = '<option value="">Seleccione Médico</option>';

            if (!especialidadId) return;

            try {
                const res = await fetch(`/api/especialidades/${especialidadId}/medicos`);
                const medicos = await res.json();
                medicos.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = `${m.nombre} ${m.apellido}`;
                    selectMedico.appendChild(opt);
                });
                if (selectedId) selectMedico.value = selectedId;
            } catch (error) {
                console.error("Error cargando médicos:", error);
            }
        }

        document.getElementById('select-estado').addEventListener('change', function () {
            cargarMunicipios(this.value);
        });

        document.getElementById('select-municipio').addEventListener('change', function () {
            cargarParroquias(this.value);
        });

        let fechaNavegacion = new Date();

        document.addEventListener('DOMContentLoaded', async function () {
            if (CITA_REGISTRADA) {
                limpiarDraft();
            }

            actualizarTextoMes();
            renderizarGrid();

            const inputExpediente = document.getElementById('input_expediente');
            inputExpediente.addEventListener('input', function () {
                const digitos = this.value.replace(/\D/g, '').slice(0, 6);
                const partes = digitos.match(/.{1,2}/g) || [];
                const formateado = partes.join('-');
                if (this.value !== formateado) {
                    this.value = formateado;
                }
            });

            const selectEspecialidad = document.getElementById('select-especialidad');
            const selectMedico = document.getElementById('select-medico');
            const selectTipoPaciente = document.getElementById('tipo_paciente');

            // 1. Cargar médicos al cambiar especialidad
            selectEspecialidad.addEventListener('change', async function () {
                const espId = this.value;
                document.getElementById('input_especialidad_id').value = espId;
                limpiarCalendario();
                await cargarMedicos(espId);
                actualizarTipoPaciente();
            });

            // 2. Escuchar cambios para renderizar calendario
            selectMedico.addEventListener('change', cargarCalendario);
            selectTipoPaciente.addEventListener('change', cargarCalendario);

            // Escuchar cambios de médico para mostrar avisos de suspensión
            selectMedico.addEventListener('change', async function() {
                const medicoId = this.value;
                const aviso = document.getElementById('aviso_suspension');
                if (!aviso) return;

                aviso.style.display = 'none';
                aviso.innerHTML = '';

                if (!medicoId || medicoId === 'any') return;

                try {
                    const res = await fetch(`/api/medicos/${medicoId}/suspensiones-activas`);
                    if (res.ok) {
                        const suspensions = await res.json();
                        if (suspensions.length > 0) {
                            let text = 'Inactivo por suspensión: ';
                            suspensions.forEach((s, idx) => {
                                const partsStart = s.fecha_inicio.split('-');
                                const partsEnd = s.fecha_fin.split('-');
                                const fInicio = `${partsStart[2]}/${partsStart[1]}/${partsStart[0]}`;
                                const fFin = `${partsEnd[2]}/${partsEnd[1]}/${partsEnd[0]}`;
                                
                                if (idx > 0) text += ', ';
                                text += `desde el ${fInicio} hasta el ${fFin}`;
                            });
                            aviso.innerHTML = text;
                            aviso.style.display = 'block';
                        }
                    }
                } catch (error) {
                    console.error('Error fetching suspensions:', error);
                }
            });

            // 3. Navegación de meses
            document.getElementById('btn-mes-anterior').addEventListener('click', () => cambiarMes(-1));
            document.getElementById('btn-mes-siguiente').addEventListener('click', () => cambiarMes(1));

            // 4. Preseleccionar ubicación por defecto (Yaracuy, San Felipe)
            await setDefaultLocation();

            // 5. Restaurar datos de validación previa (old input) — sobrescribe defaults si existe
            await restoreForm();

            // 6. Restricción de especialidades por sexo
            document.getElementById('input_sexo_m').addEventListener('change', aplicarRestriccionSexo);
            document.getElementById('input_sexo_f').addEventListener('change', aplicarRestriccionSexo);
            aplicarRestriccionSexo();

            // 7. Validar día seleccionado y habilitar selects deshabilitados al enviar
            const formCita = document.getElementById('form-cita');
            formCita.addEventListener('submit', function (e) {
                const fecha = document.getElementById('input_fecha_cita').value;
                const calendario = document.getElementById('input_calendario_id').value;

                if (!fecha || !calendario) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Seleccione un día',
                        text: 'Debe elegir una fecha disponible en el calendario para agendar la cita.'
                    });
                    return;
                }

                this.querySelectorAll('[disabled]').forEach(el => el.disabled = false);
            });

            // 8. Autoguardado y restauración del borrador ante recarga accidental
            formCita.addEventListener('input', guardarDraft);
            formCita.addEventListener('change', guardarDraft);
            await restaurarDraft();

            const btnCancelar = document.getElementById('btn-cancelar-cita');
            if (btnCancelar) {
                btnCancelar.addEventListener('click', limpiarDraft);
            }
        });

        // ===== Borrador en sessionStorage (recuperación ante recarga accidental) =====
        let restaurandoDraft = false;

        function camposFormulario() {
            return document.querySelectorAll('#form-cita input[name], #form-cita select[name], #form-cita textarea[name]');
        }

        function guardarDraft() {
            if (restaurandoDraft) return;

            const datos = {};
            camposFormulario().forEach(el => {
                if (el.name === '_token') return;

                if (el.type === 'radio') {
                    if (el.checked) datos[el.name] = el.value;
                } else if (el.type === 'checkbox') {
                    datos[el.name] = el.checked;
                } else {
                    datos[el.name] = el.value;
                }
            });

            datos.__pacienteId = window.pacienteId || null;

            try {
                sessionStorage.setItem(DRAFT_KEY, JSON.stringify(datos));
            } catch (e) {
                console.warn('No se pudo guardar el borrador de la cita.', e);
            }
        }

        function limpiarDraft() {
            try {
                sessionStorage.removeItem(DRAFT_KEY);
            } catch (e) {
                /* noop */
            }
        }

        function leerDraft() {
            try {
                return JSON.parse(sessionStorage.getItem(DRAFT_KEY) || 'null');
            } catch (e) {
                return null;
            }
        }

        async function restaurarDraft() {
            // Si el servidor devolvió errores/old input, esa fuente tiene prioridad.
            if (HAY_ERRORES_SERVIDOR) {
                limpiarDraft();
                return;
            }

            const datos = leerDraft();
            if (!datos) return;

            const claves = Object.keys(datos).filter(k => k !== '__pacienteId');
            if (claves.length === 0) return;

            restaurandoDraft = true;

            try {
                // 1. Campos simples (textos, sexo, estado, especialidad, etc.)
                camposFormulario().forEach(el => {
                    if (!el.name || !(el.name in datos)) return;

                    if (el.type === 'radio') {
                        el.checked = (datos[el.name] === el.value);
                    } else if (el.type === 'checkbox') {
                        el.checked = !!datos[el.name];
                    } else {
                        el.value = datos[el.name];
                    }
                });

                window.pacienteId = datos.__pacienteId || null;

                // 2. Cascada Estado → Municipio → Parroquia
                if (datos.estado_id) {
                    document.getElementById('select-estado').value = datos.estado_id;
                    await cargarMunicipios(datos.estado_id, datos.municipio_id || '');
                    if (datos.municipio_id) {
                        await cargarParroquias(datos.municipio_id, datos.parroquia_id || '');
                    }
                }

                // 3. Cascada Especialidad → Médico
                if (datos.especialidad_id) {
                    document.getElementById('input_especialidad_id').value = datos.especialidad_id;
                    await cargarMedicos(datos.especialidad_id, datos.medico_id || '');
                }

                // 4. Tipo de paciente y re-aplicar el valor guardado
                if (window.pacienteId) {
                    await actualizarTipoPaciente();
                }
                if (datos.tipo_paciente) {
                    document.getElementById('tipo_paciente').value = datos.tipo_paciente;
                }

                // 5. Re-render del calendario y día seleccionado
                if (datos.especialidad_id && datos.medico_id && datos.tipo_paciente) {
                    await cargarCalendario();
                }
                if (datos.fecha_cita && datos.calendario_id) {
                    seleccionarDia(datos.fecha_cita, datos.calendario_id);
                }

                mostrarMensaje('Se restauraron los datos que había escrito.', 'primary');
            } catch (e) {
                console.error('Error restaurando el borrador de la cita:', e);
            } finally {
                restaurandoDraft = false;
            }
        }

        function actualizarTextoMes() {
            const opciones = { month: 'long', year: 'numeric' };
            document.getElementById('mes-actual').innerText = fechaNavegacion.toLocaleDateString('es-ES', opciones);
        }

        function cambiarMes(offset) {
            fechaNavegacion.setMonth(fechaNavegacion.getMonth() + offset);
            actualizarTextoMes();
            cargarCalendario();
        }

        function limpiarCalendario() {
            renderizarGrid();
            document.getElementById('input_fecha_cita').value = '';
            document.getElementById('input_calendario_id').value = '';
        }

        // 3. Cargar la disponibilidad desde el backend
        async function cargarCalendario() {
            const medicoId = document.getElementById('select-medico').value;
            const especialidadId = document.getElementById('select-especialidad').value;
            const tipoPaciente = document.getElementById('tipo_paciente').value;
            const grid = document.getElementById('calendario-grid');

            if (!medicoId || !tipoPaciente) {
                renderizarGrid();
                document.getElementById('input_fecha_cita').value = '';
                document.getElementById('input_calendario_id').value = '';
                return;
            }

            const mes = fechaNavegacion.getMonth() + 1;
            const anio = fechaNavegacion.getFullYear();

            grid.innerHTML = '<div class="col-12 py-5 text-center text-primary"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';

            try {
                const res = await fetch(`/api/medicos/${medicoId}/disponibilidad?mes=${mes}&anio=${anio}&tipo_paciente=${tipoPaciente}&especialidad_id=${especialidadId}`);
                const eventos = await res.json();
                renderizarGrid(eventos);
            } catch (error) {
                console.error("Error cargando disponibilidad:", error);
                grid.innerHTML = '<div class="col-12 py-5 text-center text-danger">Error al cargar el calendario.</div>';
            }
        }

        // 4. Actualizar tipo de paciente según citas previas en la especialidad
        async function actualizarTipoPaciente() {
            const selectTipo = document.getElementById('tipo_paciente');
            const selectEsp = document.getElementById('select-especialidad');
            const espId = selectEsp?.value;
            const id = window.pacienteId;

            if (!id || !espId) {
                selectTipo.innerHTML = `
                    <option value="">Seleccione Tipo</option>
                    <option value="primera_vez">Primera vez</option>
                    <option value="control">Control / Sucesivo</option>
                    <option value="orden_medica">Orden Médica</option>
                `;
                return;
            }

            try {
                const res = await fetch(`/api/citas/paciente/${id}/especialidad/${espId}/tiene-citas`);
                const data = await res.json();

                if (data.tieneCitas) {
                    selectTipo.innerHTML = `
                        <option value="">Seleccione Tipo</option>
                        <option value="control">Control / Sucesivo</option>
                        <option value="orden_medica">Orden Médica</option>
                    `;
                } else {
                    selectTipo.innerHTML = `
                        <option value="">Seleccione Tipo</option>
                        <option value="primera_vez">Primera vez</option>
                        <option value="orden_medica">Orden Médica</option>
                    `;
                }
            } catch (err) {
                console.error('Error verificando citas:', err);
            }
        }

        // 5. Dibujar el calendario interactivo
        function renderizarGrid(eventos = null) {
            const grid = document.getElementById('calendario-grid');
            grid.innerHTML = '';

            const primerDia = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth(), 1).getDay();
            const ultimoDia = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth() + 1, 0).getDate();

            const fragment = document.createDocumentFragment();

            // Rellenar cuadros vacíos antes del día 1
            for (let i = 0; i < primerDia; i++) {
                const empty = document.createElement('div');
                empty.className = 'col border-end border-bottom bg-light';
                empty.style.cssText = 'flex: 0 0 14.28%; height: 90px;';
                fragment.appendChild(empty);
            }

            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            // Renderizar los días
            for (let dia = 1; dia <= ultimoDia; dia++) {
                const mesStr = String(fechaNavegacion.getMonth() + 1).padStart(2, '0');
                const diaStr = String(dia).padStart(2, '0');
                const fechaStr = `${fechaNavegacion.getFullYear()}-${mesStr}-${diaStr}`;
                const fechaCelda = new Date(fechaNavegacion.getFullYear(), fechaNavegacion.getMonth(), dia);

                const ev = eventos?.find(e => e.fecha === fechaStr);

                const div = document.createElement('div');
                div.className = 'col p-2 border-end border-bottom position-relative calendar-day';
                div.style.cssText = 'flex: 0 0 14.28%; height: 90px; transition: background-color 0.2s, box-shadow 0.2s;';
                div.id = `celda-${fechaStr}`;

                div.innerHTML = `<span class="fw-bold d-block text-start">${dia}</span>`;

                if (ev && fechaCelda >= hoy) {
                    if (ev.disponibles > 0 || ev.tipo === 'orden_medica') {
                        div.style.cursor = 'pointer';
                        div.classList.add('bg-white', 'calendar-day-available');

                        if (ev.tipo === 'orden_medica') {
                            div.innerHTML += `
                                <div class="text-center mt-1 px-1 py-1 rounded border border-purple border-opacity-25 calendar-slot" style="border-color: #6f42c1 !important;">
                                    <div class="fw-bold text-purple" style="font-size:0.75rem; line-height:1.2; color: #6f42c1;">Disponible (O.M.)</div>
                                    <div class="text-muted" style="font-size:0.6rem; line-height:1.1;">${ev.hora_inicio.substring(0, 5)} - ${ev.hora_fin.substring(0, 5)}</div>
                                </div>`;
                        } else {
                            div.innerHTML += `
                                <div class="text-center mt-1 px-1 py-1 rounded border border-success border-opacity-25 calendar-slot">
                                    <div class="fw-bold text-success" style="font-size:0.75rem; line-height:1.2;">${ev.disponibles} Cupo${ev.disponibles !== 1 ? 's' : ''}</div>
                                    <div class="text-muted" style="font-size:0.6rem; line-height:1.1;">${ev.hora_inicio.substring(0, 5)} - ${ev.hora_fin.substring(0, 5)}</div>
                                </div>`;
                        }

                        div.onclick = () => seleccionarDia(fechaStr, ev.id);
                    } else {
                        div.classList.add('bg-light');
                        div.style.cursor = 'not-allowed';
                        div.innerHTML += `
                                <div class="text-center mt-2 opacity-75">
                                    <span class="badge bg-danger">Agotado</span>
                                </div>`;
                    }
                } else {
                    div.classList.add('bg-light');
                }

                fragment.appendChild(div);
            }

            grid.appendChild(fragment);
        }

        // 6. Preseleccionar ubicación por defecto (Yaracuy, San Felipe)
        async function setDefaultLocation() {
            const el = document.getElementById('default-location');
            if (!el) return;

            const estadoId = el.dataset.defaultEstado;
            const municipioId = el.dataset.defaultMunicipio;
            if (!estadoId) return;

            const selectEst = document.getElementById('select-estado');
            selectEst.value = estadoId;

            try {
                const res = await fetch(`/api/municipios/${estadoId}`);
                const municipios = await res.json();
                const selectMun = document.getElementById('select-municipio');
                selectMun.innerHTML = '<option value="">Seleccione Municipio</option>';
                municipios.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nombre;
                    selectMun.appendChild(opt);
                });
                if (municipioId) {
                    selectMun.value = municipioId;
                    selectMun.dispatchEvent(new Event('change'));
                }
            } catch (err) {
                console.error('Error cargando municipios por defecto:', err);
            }
        }

        // 7. Restaurar formulario tras error de validación
        async function restoreForm() {
            const oldData = document.getElementById('old-data');
            if (!oldData) return;

            const espId = oldData.dataset.especialidad;
            const estId = oldData.dataset.estado;
            const munId = oldData.dataset.municipio;
            const parId = oldData.dataset.parroquia;
            const medId = oldData.dataset.medico;
            const tipo = oldData.dataset.tipo;

            // Restaurar solo si hay algún dato guardado
            if (!espId && !estId && !medId && !tipo) return;

            // --- Restaurar cascada Estado → Municipio → Parroquia ---
            if (estId) {
                const selectEst = document.getElementById('select-estado');
                selectEst.value = estId;

                if (munId) {
                    const res1 = await fetch(`/api/municipios/${estId}`);
                    const municipios = await res1.json();
                    const selectMun = document.getElementById('select-municipio');
                    selectMun.innerHTML = '<option value="">Seleccione Municipio</option>';
                    municipios.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        opt.textContent = m.nombre;
                        selectMun.appendChild(opt);
                    });
                    selectMun.value = munId;

                    if (parId) {
                        const res2 = await fetch(`/api/parroquias/${munId}`);
                        const parroquias = await res2.json();
                        const selectPar = document.getElementById('select-parroquia');
                        selectPar.innerHTML = '<option value="">Seleccione Parroquia</option>';
                        parroquias.forEach(p => {
                            const opt = document.createElement('option');
                            opt.value = p.id;
                            opt.textContent = p.nombre;
                            selectPar.appendChild(opt);
                        });
                        selectPar.value = parId;
                    } else {
                        selectMun.dispatchEvent(new Event('change'));
                    }
                }
            }

            // --- Restaurar cascada Especialidad → Médico → Calendario ---
            if (espId) {
                const selectEsp = document.getElementById('select-especialidad');
                selectEsp.value = espId;
                document.getElementById('input_especialidad_id').value = espId;

                if (medId) {
                    const res = await fetch(`/api/especialidades/${espId}/medicos`);
                    const medicos = await res.json();
                    const selectMed = document.getElementById('select-medico');
                    selectMed.innerHTML = '<option value="">Seleccione Médico</option>';
                    medicos.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        opt.textContent = m.nombre + ' ' + m.apellido;
                        selectMed.appendChild(opt);
                    });
                    selectMed.value = medId;

                    await cargarCalendario();
                }
            }

            // --- Restaurar pacienteId y actualizar tipo_paciente según citas ---
            const pacienteId = oldData.dataset.pacienteId;
            if (pacienteId) {
                window.pacienteId = pacienteId;
                await actualizarTipoPaciente();
            }

            // --- Restaurar tipo_paciente ---
            if (tipo) {
                const selectTipo = document.getElementById('tipo_paciente');
                selectTipo.value = tipo;
                if (!espId || !medId) {
                    await cargarCalendario();
                }
            }

            // --- Restaurar fecha / calendario (seleccionar el día) ---
            const fecha = oldData.dataset.fecha;
            const calendario = oldData.dataset.calendario;
            if (fecha && calendario) {
                seleccionarDia(fecha, calendario);
            }
        }

        // 8. Rellenar inputs al hacer clic en un cupo disponible
        let fechaSeleccionadaAnterior = null;

        function seleccionarDia(fecha, calendario_id) {
            // Remover color de selección anterior si existe
            if (fechaSeleccionadaAnterior) {
                const celdaAnterior = document.getElementById(`celda-${fechaSeleccionadaAnterior}`);
                if (celdaAnterior) {
                    celdaAnterior.classList.remove('bg-primary-subtle', 'border-primary');
                }
            }

            // Pintar celda actual de azul
            const celdaActual = document.getElementById(`celda-${fecha}`);
            if (celdaActual) {
                celdaActual.classList.add('bg-primary-subtle', 'border-primary');
            }
            fechaSeleccionadaAnterior = fecha;

            // Autocompletar inputs
            document.getElementById('input_fecha_cita').value = fecha;
            document.getElementById('input_calendario_id').value = calendario_id;
            guardarDraft();
        }
    </script>

    @include('layouts.footer')
@endsection