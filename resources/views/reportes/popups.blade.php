    {{--1--}}
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

    {{--2--}}
    @component('reportes.modal')
            @slot('modal_id', 'modalMorbilidad')
            @slot('modal_title', 'Reporte de Morbilidad Mensual')
            @slot('form_action', '#' /*route('reportes.morbilidad')*/)

                <div class="mb-3">
                    <label for="mes" class="form-label">Seleccione el Mes</label>
                    <input type="month" name="mes" id="mes" class="form-control" required>
                </div>
    @endcomponent
    
    {{--3--}}
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
                            {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6">
                <select id="proc_anio" class="form-select" required>
                    <option value="">Año</option>
                    @php
                        $anioActual = date('Y');
                        $anioInicio = date('Y') - 5;
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
    
    <script>
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                const tipoMes = document.getElementById('proc_tipo_mes');
                const tipoRango = document.getElementById('proc_tipo_rango');
                const divMes = document.getElementById('proc_div_mes');
                const divRango = document.getElementById('proc_div_rango');
                const mesSelect = document.getElementById('proc_mes');
                const anioSelect = document.getElementById('proc_anio');
                const mesHidden = document.getElementById('proc_mes_hidden');
                const fechaDesde = document.getElementById('proc_fecha_desde');
                const fechaHasta = document.getElementById('proc_fecha_hasta');
    
                function actualizarHidden() {
                    if (mesSelect.value && anioSelect.value) {
                        mesHidden.value = anioSelect.value + '-' + String(mesSelect.value).padStart(2, '0');
                    } else {
                        mesHidden.value = '';
                    }
                }
    
                mesSelect.addEventListener('change', actualizarHidden);
                anioSelect.addEventListener('change', actualizarHidden);
    
                function actualizarRequired() {
                    if (tipoMes.checked) {
                        divMes.classList.remove('d-none');
                        divRango.classList.add('d-none');
                        mesSelect.setAttribute('required', 'required');
                        anioSelect.setAttribute('required', 'required');
                        fechaDesde.removeAttribute('required');
                        fechaHasta.removeAttribute('required');
                        actualizarHidden();
                    } else {
                        divMes.classList.add('d-none');
                        divRango.classList.remove('d-none');
                        mesSelect.removeAttribute('required');
                        anioSelect.removeAttribute('required');
                        fechaDesde.setAttribute('required', 'required');
                        fechaHasta.setAttribute('required', 'required');
                    }
                }
    
                tipoMes.addEventListener('change', actualizarRequired);
                tipoRango.addEventListener('change', actualizarRequired);
    
                actualizarRequired();
                actualizarHidden();
            });
        })();
    </script>
    @endcomponent
    {{--4--}}
    @component('reportes.modal')
            @slot('modal_id', 'modal25CausasPrincipales')
            @slot('modal_title', 'Mes')
            @slot('form_action',/* route('reportes.medicos_especialidad')*/)

            <div class="mb-3">
                <label for="especialidad_id" class="form-label">En proceso</label>
            </div>
    @endcomponent

    {{--5--}}
    @component('reportes.modal')
            @slot('modal_id', 'modalMovimientoConsultas')
            @slot('modal_title', 'Movimiento Mensual de Consultas')
            @slot('form_action', '#' /*route('reportes.morbilidad')*/)

                <div class="mb-3">
                    <label for="mes" class="form-label">Seleccione el Mes</label>
                    <input type="month" name="mes" id="mes" class="form-control" required>
                </div>
    @endcomponent

    {{--6--}}
    @component('reportes.modal')
            @slot('modal_id', 'modalMovimientoConsultaAro')
            @slot('modal_title', 'Rango de Fecha Consultas Aro')
            @slot('form_action', '#' /*route('reportes.morbilidad')*/)

                <div class="mb-3">
                    <label for="mes" class="form-label">Seleccione el Mes</label>
                    <input type="month" name="mes" id="mes" class="form-control" required>
                </div>
    @endcomponent

    {{--7--}}
    @component('reportes.modal')
            @slot('modal_id', 'modalPacienteInasistencia')
            @slot('modal_title', 'Mes')
            @slot('form_action',/* route('reportes.medicos_especialidad')*/)

            <div class="mb-3">
                <label for="especialidad_id" class="form-label">En proceso</label>
            </div>
    @endcomponent

    {{--8--}}
    @component('reportes.modal')
            @slot('modal_id', 'modalMedicoInasistencia')
            @slot('modal_title', 'Mes')
            @slot('form_action', '#' /*route('reportes.morbilidad')*/)

                <div class="mb-3">
                    <label for="mes" class="form-label">Seleccione el Mes</label>
                    <input type="month" name="mes" id="mes" class="form-control" required>
                </div>
    @endcomponent