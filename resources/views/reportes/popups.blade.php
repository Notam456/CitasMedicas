    {{--1 Médicos y filtro de Especialidad--}}
    @component('reportes.modal')
    @slot('modal_id', 'modalMedicosEspecialidad')
    @slot('modal_title', 'Filtro por Especialidad')
    @slot('form_action', route('reportes.medicos_especialidad'))
    @slot('excel_action', route('reportes.medicos_especialidad_excel'))
    <div class="mb-3">
        <label for="especialidad_id" class="form-label">Especialidad</label>
        <select name="especialidad_id" id="especialidad_id" class="form-select">
            <option value="">Todos</option>
            @foreach($especialidades as $e)
                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
            @endforeach
        </select>
    </div>
    @endcomponent

    {{--2 Morbilidad (pendiente) --}}
    @component('reportes.modal')
        @slot('modal_id', 'modalMorbilidad')
        @slot('modal_title', 'Reporte de Morbilidad Mensual')
        @slot('form_action', '#')
        <div class="mb-3">
            <label for="mes" class="form-label">Seleccione el Mes</label>
            <input type="month" name="mes" id="mes" class="form-control" required>
        </div>
    @endcomponent

    {{--3 Procedencia de Pacientes --}}
    @component('reportes.modal')
    @slot('modal_id', 'modalProcedenciaPacientes')
    @slot('modal_title', 'Reporte de Procedencia de Pacientes')
    @slot('form_action', route('reportes.procedencia_pacientes_pdf'))
    @slot('excel_action', route('reportes.procedencia_pacientes_excel'))

    <div class="mb-3">
        <label class="form-label">Tipo de rango</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_rango" id="proc_tipo_mes" value="mes" checked>
                <label class="form-check-label" for="proc_tipo_mes">Mes específico</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_rango" id="proc_tipo_rango" value="rango">
                <label class="form-check-label" for="proc_tipo_rango">Rango de fechas</label>
            </div>
        </div>
    </div>

    <div class="mb-3" id="proc_div_mes">
        <label class="form-label">Seleccione el Mes</label>
        <div class="row">
            <div class="col-md-6">
                <select id="proc_mes" class="form-select" required>
                    <option value="">Mes</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6">
                <select id="proc_anio" class="form-select" required>
                    <option value="">Año</option>
                    @php
                        $anioActual = date('Y');
                        $anioInicio = $anioActual - 5;
                    @endphp
                    @for($i = $anioInicio; $i <= $anioActual + 5; $i++)
                        <option value="{{ $i }}" {{ $i == $anioActual ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <input type="hidden" name="mes" id="proc_mes_hidden">
    </div>

    <div class="mb-3 d-none" id="proc_div_rango">
        <div class="row">
            <div class="col-md-6">
                <label for="proc_fecha_desde" class="form-label">Fecha desde</label>
                <input type="date" name="fecha_desde" id="proc_fecha_desde" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="proc_fecha_hasta" class="form-label">Fecha hasta</label>
                <input type="date" name="fecha_hasta" id="proc_fecha_hasta" class="form-control">
            </div>
        </div>
    </div>
    @endcomponent

    {{--4 25 Causas Principales --}}
    @component('reportes.modal')
    @slot('modal_id', 'modal25CausasPrincipales')
    @slot('modal_title', '25 Causas Principales de Consulta Externa')
    @slot('form_action', route('reportes.causas_principales_pdf'))
    @slot('excel_action', route('reportes.causas_principales_excel'))

    <div class="mb-3">
        <label class="form-label">Tipo de rango</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_rango" id="causas_tipo_mes" value="mes" checked>
                <label class="form-check-label" for="causas_tipo_mes">Mes específico</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_rango" id="causas_tipo_rango" value="rango">
                <label class="form-check-label" for="causas_tipo_rango">Rango de fechas</label>
            </div>
        </div>
    </div>

    <div class="mb-3" id="causas_div_mes">
        <label class="form-label">Seleccione el Mes</label>
        <div class="row">
            <div class="col-md-6">
                <select id="causas_mes" class="form-select" required>
                    <option value="">Mes</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6">
                <select id="causas_anio" class="form-select" required>
                    <option value="">Año</option>
                    @php
                        $anioActual = date('Y');
                        $anioInicio = $anioActual - 5;
                    @endphp
                    @for($i = $anioInicio; $i <= $anioActual + 5; $i++)
                        <option value="{{ $i }}" {{ $i == $anioActual ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <input type="hidden" name="mes" id="causas_mes_hidden">
    </div>

    <div class="mb-3 d-none" id="causas_div_rango">
        <div class="row">
            <div class="col-md-6">
                <label for="causas_fecha_desde" class="form-label">Fecha desde</label>
                <input type="date" name="fecha_desde" id="causas_fecha_desde" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="causas_fecha_hasta" class="form-label">Fecha hasta</label>
                <input type="date" name="fecha_hasta" id="causas_fecha_hasta" class="form-control">
            </div>
        </div>
    </div>
    @endcomponent

    {{--5 Movimiento de Consultas --}}
    @component('reportes.modal')
    @slot('modal_id', 'modalMovimientoConsultas')
    @slot('modal_title', 'Movimiento de Consulta Externa por Mes')
    @slot('form_action', route('reportes.movimiento_consultas_pdf'))
    @slot('excel_action', route('reportes.movimiento_consultas_excel'))

    <div class="mb-3">
        <label class="form-label">Tipo de paciente</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_paciente" id="mov_tipo_adulto" value="adulto" checked>
                <label class="form-check-label" for="mov_tipo_adulto">Mayores de 12 años</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_paciente" id="mov_tipo_pediatria" value="pediatria">
                <label class="form-check-label" for="mov_tipo_pediatria">Pediatría (12 años o menos)</label>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Tipo de rango</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_rango" id="mov_tipo_mes" value="mes" checked>
                <label class="form-check-label" for="mov_tipo_mes">Mes específico</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_rango" id="mov_tipo_rango" value="rango">
                <label class="form-check-label" for="mov_tipo_rango">Rango de fechas</label>
            </div>
        </div>
    </div>

    <div class="mb-3" id="mov_div_mes">
        <label class="form-label">Seleccione el Mes</label>
        <div class="row">
            <div class="col-md-6">
                <select id="mov_mes" class="form-select" required>
                    <option value="">Mes</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6">
                <select id="mov_anio" class="form-select" required>
                    <option value="">Año</option>
                    @php
                        $anioActual = date('Y');
                        $anioInicio = $anioActual - 5;
                    @endphp
                    @for($i = $anioInicio; $i <= $anioActual + 5; $i++)
                        <option value="{{ $i }}" {{ $i == $anioActual ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <input type="hidden" name="mes" id="mov_mes_hidden">
    </div>

    <div class="mb-3 d-none" id="mov_div_rango">
        <div class="row">
            <div class="col-md-6">
                <label for="mov_fecha_desde" class="form-label">Fecha desde</label>
                <input type="date" name="fecha_desde" id="mov_fecha_desde" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="mov_fecha_hasta" class="form-label">Fecha hasta</label>
                <input type="date" name="fecha_hasta" id="mov_fecha_hasta" class="form-control">
            </div>
        </div>
    </div>
    @endcomponent

    {{--6 Movimiento Consulta Aro (pendiente) --}}
    @component('reportes.modal')
        @slot('modal_id', 'modalMovimientoConsultaAro')
        @slot('modal_title', 'Rango de Fecha Consultas Aro')
        @slot('form_action', '#')
        <div class="mb-3">
            <label for="mes" class="form-label">Seleccione el Mes</label>
            <input type="month" name="mes" id="mes" class="form-control" required>
        </div>
    @endcomponent

    {{--7 Inasistencia Pacientes (pendiente) --}}
    @component('reportes.modal')
        @slot('modal_id', 'modalPacienteInasistencia')
        @slot('modal_title', 'Mes')
        @slot('form_action', '#')
        <div class="mb-3">
            <label class="form-label">En proceso</label>
        </div>
    @endcomponent

    {{--8 Inasistencia Médicos (pendiente) --}}
    @component('reportes.modal')
        @slot('modal_id', 'modalMedicoInasistencia')
        @slot('modal_title', 'Mes')
        @slot('form_action', '#')
        <div class="mb-3">
            <label for="mes" class="form-label">Seleccione el Mes</label>
            <input type="month" name="mes" id="mes" class="form-control" required>
        </div>
    @endcomponent

<script>
    function initRangoFechas(config) {
        const tipoMes = document.getElementById(config.tipoMesId);
        const tipoRango = document.getElementById(config.tipoRangoId);
        const divMes = document.getElementById(config.divMesId);
        const divRango = document.getElementById(config.divRangoId);
        const mesSelect = document.getElementById(config.mesSelectId);
        const anioSelect = document.getElementById(config.anioSelectId);
        const mesHidden = document.getElementById(config.mesHiddenId);
        const fechaDesde = document.getElementById(config.fechaDesdeId);
        const fechaHasta = document.getElementById(config.fechaHastaId);

        function actualizarHidden() {
            if (mesSelect && anioSelect && mesSelect.value && anioSelect.value) {
                mesHidden.value = anioSelect.value + '-' + String(mesSelect.value).padStart(2, '0');
            } else if (mesHidden) {
                mesHidden.value = '';
            }
        }

        if (mesSelect && anioSelect) {
            mesSelect.addEventListener('change', actualizarHidden);
            anioSelect.addEventListener('change', actualizarHidden);
        }

        function actualizarRequired() {
            if (tipoMes.checked) {
                divMes.classList.remove('d-none');
                divRango.classList.add('d-none');
                if (mesSelect) mesSelect.setAttribute('required', 'required');
                if (anioSelect) anioSelect.setAttribute('required', 'required');
                if (fechaDesde) fechaDesde.removeAttribute('required');
                if (fechaHasta) fechaHasta.removeAttribute('required');
                actualizarHidden();
            } else {
                divMes.classList.add('d-none');
                divRango.classList.remove('d-none');
                if (mesSelect) mesSelect.removeAttribute('required');
                if (anioSelect) anioSelect.removeAttribute('required');
                if (fechaDesde) fechaDesde.setAttribute('required', 'required');
                if (fechaHasta) fechaHasta.setAttribute('required', 'required');
            }
        }

        tipoMes.addEventListener('change', actualizarRequired);
        tipoRango.addEventListener('change', actualizarRequired);

        actualizarRequired();
        actualizarHidden();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Procedencia de Pacientes
        if (document.getElementById('proc_tipo_mes')) {
            initRangoFechas({
                tipoMesId: 'proc_tipo_mes',
                tipoRangoId: 'proc_tipo_rango',
                divMesId: 'proc_div_mes',
                divRangoId: 'proc_div_rango',
                mesSelectId: 'proc_mes',
                anioSelectId: 'proc_anio',
                mesHiddenId: 'proc_mes_hidden',
                fechaDesdeId: 'proc_fecha_desde',
                fechaHastaId: 'proc_fecha_hasta'
            });
        }

        // 25 Causas Principales
        if (document.getElementById('causas_tipo_mes')) {
            initRangoFechas({
                tipoMesId: 'causas_tipo_mes',
                tipoRangoId: 'causas_tipo_rango',
                divMesId: 'causas_div_mes',
                divRangoId: 'causas_div_rango',
                mesSelectId: 'causas_mes',
                anioSelectId: 'causas_anio',
                mesHiddenId: 'causas_mes_hidden',
                fechaDesdeId: 'causas_fecha_desde',
                fechaHastaId: 'causas_fecha_hasta'
            });
        }

        // Movimiento de Consultas
        if (document.getElementById('mov_tipo_mes')) {
            initRangoFechas({
                tipoMesId: 'mov_tipo_mes',
                tipoRangoId: 'mov_tipo_rango',
                divMesId: 'mov_div_mes',
                divRangoId: 'mov_div_rango',
                mesSelectId: 'mov_mes',
                anioSelectId: 'mov_anio',
                mesHiddenId: 'mov_mes_hidden',
                fechaDesdeId: 'mov_fecha_desde',
                fechaHastaId: 'mov_fecha_hasta'
            });
        }
    });
</script>