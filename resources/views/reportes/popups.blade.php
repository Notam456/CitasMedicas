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
            <label for="mes_morbilidad" class="form-label">Seleccione el Mes</label>
            <input type="month" name="mes" id="mes_morbilidad" class="form-control" required>
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
        <label class="form-label">Edad</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_paciente" id="mov_tipo_adulto" value="adulto" checked>
                <label class="form-check-label" for="mov_tipo_adulto">Mayores de 12 años</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_paciente" id="mov_tipo_pediatria" value="pediatria">
                <label class="form-check-label" for="mov_tipo_pediatria">Pediatría (12 años o menos)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_paciente" id="mov_tipo_todas" value="todas">
                <label class="form-check-label" for="mov_tipo_todas">Todas las Edades</label>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Especialidad</label>
        <x-searchable-select name="especialidad_id" id="mov_especialidad_id"
            :options="$especialidades->pluck('nombre', 'id')"
            placeholder="Seleccione Especialidad (Todas)" icon="fas fa-stethoscope" />
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

    {{--6 Movimiento Consulta Aro --}}
    @component('reportes.modal')
        @slot('modal_id', 'modalMovimientoConsultaAro')
        @slot('modal_title', 'Rango de Fecha Consultas Aro')
        @slot('form_action', route('reportes.movimiento_consulta_aro_pdf'))
        @slot('excel_action', route('reportes.movimiento_consulta_aro_excel'))

        <div class="mb-3">
            <label class="form-label">Tipo de rango</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="aro_tipo_mes" value="mes" checked>
                    <label class="form-check-label" for="aro_tipo_mes">Mes específico</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="aro_tipo_rango" value="rango">
                    <label class="form-check-label" for="aro_tipo_rango">Rango de fechas</label>
                </div>
            </div>
        </div>

        <div class="mb-3" id="aro_div_mes">
            <label class="form-label">Seleccione el Mes</label>
            <div class="row">
                <div class="col-md-6">
                    <select id="aro_mes" class="form-select" required>
                        <option value="">Mes</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6">
                    <select id="aro_anio" class="form-select" required>
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
            <input type="hidden" name="mes" id="aro_mes_hidden">
        </div>

        <div class="mb-3 d-none" id="aro_div_rango">
            <div class="row">
                <div class="col-md-6">
                    <label for="aro_fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" id="aro_fecha_desde" class="form-control" value="{{ \Carbon\Carbon::now()->subMonths(3)->format('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label for="aro_fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" id="aro_fecha_hasta" class="form-control" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                initRangoFechas({
                    tipoMesId: 'aro_tipo_mes',
                    tipoRangoId: 'aro_tipo_rango',
                    divMesId: 'aro_div_mes',
                    divRangoId: 'aro_div_rango',
                    mesSelectId: 'aro_mes',
                    anioSelectId: 'aro_anio',
                    mesHiddenId: 'aro_mes_hidden',
                    fechaDesdeId: 'aro_fecha_desde',
                    fechaHastaId: 'aro_fecha_hasta'
                });
            });
        </script>
        @endpush
    @endcomponent

    {{--7 Inasistencias en Citas --}}
    @component('reportes.modal')
        @slot('modal_id', 'modalInasistenciasCitas')
        @slot('modal_title', 'Reporte Inasistencias en Citas')
        @slot('form_action', route('reportes.inasistencias_pdf'))
        @slot('excel_action', route('reportes.inasistencias_excel'))

        <div class="mb-3">
            <label class="form-label">Tipo de rango</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="inas_tipo_mes" value="mes" checked>
                    <label class="form-check-label" for="inas_tipo_mes">Mes específico</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="inas_tipo_rango" value="rango">
                    <label class="form-check-label" for="inas_tipo_rango">Rango de fechas</label>
                </div>
            </div>
        </div>

        <div class="mb-3" id="inas_div_mes">
            <label class="form-label">Seleccione el Mes</label>
            <div class="row">
                <div class="col-md-6">
                    <select id="inas_mes" class="form-select" required>
                        <option value="">Mes</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6">
                    <select id="inas_anio" class="form-select" required>
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
            <input type="hidden" name="mes" id="inas_mes_hidden">
        </div>

        <div class="mb-3 d-none" id="inas_div_rango">
            <div class="row">
                <div class="col-md-6">
                    <label for="inas_fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" id="inas_fecha_desde" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="inas_fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" id="inas_fecha_hasta" class="form-control">
                </div>
            </div>
        </div>
    @endcomponent

    {{--8 Productividad por Médico --}}
    @component('reportes.modal')
        @slot('modal_id', 'modalProductividadMedico')
        @slot('modal_title', 'Productividad por Médico')
        @slot('form_action', route('reportes.productividad_medico_pdf'))
        @slot('excel_action', route('reportes.productividad_medico_excel'))

        <div class="mb-3">
            <label class="form-label">Especialidad</label>
            <x-searchable-select name="especialidad_id" id="prod_med_especialidad_id"
                :options="$especialidades->pluck('nombre', 'id')"
                placeholder="Seleccione Especialidad (Todas)" icon="fas fa-stethoscope" />
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de rango</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="prod_med_tipo_mes" value="mes" checked>
                    <label class="form-check-label" for="prod_med_tipo_mes">Mes específico</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="prod_med_tipo_rango" value="rango">
                    <label class="form-check-label" for="prod_med_tipo_rango">Rango de fechas</label>
                </div>
            </div>
        </div>

        <div class="mb-3" id="prod_med_div_mes">
            <label class="form-label">Seleccione el Mes</label>
            <div class="row">
                <div class="col-md-6">
                    <select id="prod_med_mes" class="form-select" required>
                        <option value="">Mes</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6">
                    <select id="prod_med_anio" class="form-select" required>
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
            <input type="hidden" name="mes" id="prod_med_mes_hidden">
        </div>

        <div class="mb-3 d-none" id="prod_med_div_rango">
            <div class="row">
                <div class="col-md-6">
                    <label for="prod_med_fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" id="prod_med_fecha_desde" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="prod_med_fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" id="prod_med_fecha_hasta" class="form-control">
                </div>
            </div>
        </div>
    @endcomponent

    {{--9 Citas sin Diagnóstico --}}
    @component('reportes.modal')
        @slot('modal_id', 'modalCitasSinDiagnostico')
        @slot('modal_title', 'Citas sin Diagnóstico')
        @slot('form_action', route('reportes.citas_sin_diagnostico_pdf'))
        @slot('excel_action', route('reportes.citas_sin_diagnostico_excel'))

        <div class="mb-3">
            <label class="form-label">Especialidad</label>
            <x-searchable-select name="especialidad_id" id="sin_diag_especialidad_id"
                :options="$especialidades->pluck('nombre', 'id')"
                placeholder="Seleccione Especialidad (Todas)" icon="fas fa-stethoscope" />
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de rango</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="sin_diag_tipo_mes" value="mes" checked>
                    <label class="form-check-label" for="sin_diag_tipo_mes">Mes específico</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="sin_diag_tipo_rango" value="rango">
                    <label class="form-check-label" for="sin_diag_tipo_rango">Rango de fechas</label>
                </div>
            </div>
        </div>

        <div class="mb-3" id="sin_diag_div_mes">
            <label class="form-label">Seleccione el Mes</label>
            <div class="row">
                <div class="col-md-6">
                    <select id="sin_diag_mes" class="form-select" required>
                        <option value="">Mes</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6">
                    <select id="sin_diag_anio" class="form-select" required>
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
            <input type="hidden" name="mes" id="sin_diag_mes_hidden">
        </div>

        <div class="mb-3 d-none" id="sin_diag_div_rango">
            <div class="row">
                <div class="col-md-6">
                    <label for="sin_diag_fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" id="sin_diag_fecha_desde" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="sin_diag_fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" id="sin_diag_fecha_hasta" class="form-control">
                </div>
            </div>
        </div>
    @endcomponent

    {{--10 Eficiencia de Atención --}}
    @component('reportes.modal')
        @slot('modal_id', 'modalEficienciaAtencion')
        @slot('modal_title', 'Eficiencia de Atención')
        @slot('form_action', route('reportes.eficiencia_atencion_pdf'))
        @slot('excel_action', route('reportes.eficiencia_atencion_excel'))

        <div class="mb-3">
            <label class="form-label">Tipo de rango</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="efic_tipo_mes" value="mes" checked>
                    <label class="form-check-label" for="efic_tipo_mes">Mes específico</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="efic_tipo_rango" value="rango">
                    <label class="form-check-label" for="efic_tipo_rango">Rango de fechas</label>
                </div>
            </div>
        </div>

        <div class="mb-3" id="efic_div_mes">
            <label class="form-label">Seleccione el Mes</label>
            <div class="row">
                <div class="col-md-6">
                    <select id="efic_mes" class="form-select" required>
                        <option value="">Mes</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6">
                    <select id="efic_anio" class="form-select" required>
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
            <input type="hidden" name="mes" id="efic_mes_hidden">
        </div>

        <div class="mb-3 d-none" id="efic_div_rango">
            <div class="row">
                <div class="col-md-6">
                    <label for="efic_fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" id="efic_fecha_desde" class="form-control" value="{{ \Carbon\Carbon::now()->subMonths(3)->format('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label for="efic_fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" id="efic_fecha_hasta" class="form-control" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                </div>
            </div>
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

        // Inasistencias en Citas
        if (document.getElementById('inas_tipo_mes')) {
            initRangoFechas({
                tipoMesId: 'inas_tipo_mes',
                tipoRangoId: 'inas_tipo_rango',
                divMesId: 'inas_div_mes',
                divRangoId: 'inas_div_rango',
                mesSelectId: 'inas_mes',
                anioSelectId: 'inas_anio',
                mesHiddenId: 'inas_mes_hidden',
                fechaDesdeId: 'inas_fecha_desde',
                fechaHastaId: 'inas_fecha_hasta'
            });
        }

        // Productividad por Médico
        if (document.getElementById('prod_med_tipo_mes')) {
            initRangoFechas({
                tipoMesId: 'prod_med_tipo_mes',
                tipoRangoId: 'prod_med_tipo_rango',
                divMesId: 'prod_med_div_mes',
                divRangoId: 'prod_med_div_rango',
                mesSelectId: 'prod_med_mes',
                anioSelectId: 'prod_med_anio',
                mesHiddenId: 'prod_med_mes_hidden',
                fechaDesdeId: 'prod_med_fecha_desde',
                fechaHastaId: 'prod_med_fecha_hasta'
            });
        }

        // Citas sin Diagnóstico
        if (document.getElementById('sin_diag_tipo_mes')) {
            initRangoFechas({
                tipoMesId: 'sin_diag_tipo_mes',
                tipoRangoId: 'sin_diag_tipo_rango',
                divMesId: 'sin_diag_div_mes',
                divRangoId: 'sin_diag_div_rango',
                mesSelectId: 'sin_diag_mes',
                anioSelectId: 'sin_diag_anio',
                mesHiddenId: 'sin_diag_mes_hidden',
                fechaDesdeId: 'sin_diag_fecha_desde',
                fechaHastaId: 'sin_diag_fecha_hasta'
            });
        }

        // Eficiencia de Atención
        if (document.getElementById('efic_tipo_mes')) {
            initRangoFechas({
                tipoMesId: 'efic_tipo_mes',
                tipoRangoId: 'efic_tipo_rango',
                divMesId: 'efic_div_mes',
                divRangoId: 'efic_div_rango',
                mesSelectId: 'efic_mes',
                anioSelectId: 'efic_anio',
                mesHiddenId: 'efic_mes_hidden',
                fechaDesdeId: 'efic_fecha_desde',
                fechaHastaId: 'efic_fecha_hasta'
            });
        }
    });
</script>