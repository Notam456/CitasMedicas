    {{--1--}}
    @component('reportes.modal')
    @slot('modal_id', 'modalMedicosEspecialidad')
    @slot('modal_title', 'Filtro por Especialidad')
    @slot('form_action', route('reportes.medicos_especialidad'))

    <div class="mb-3">
        <label for="especialidad_id" class="form-label">Especialidad</label>
        <select name="especialidad_id" id="especialidad_id" class="form-select">
            <option value="">Todos</option>
            @foreach($especialidades as $e)
                <option value="{{ $e->id_especialidad }}">{{ $e->nombre }}</option>
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
    
    {{-- 3 --}}
        @component('reportes.modal')
        @slot('modal_id', 'modalProcedenciaPacientes')
        @slot('modal_title', 'Reporte de Procedencia de Pacientes')
        @slot('form_action', route('reportes.procedencia_pacientes_pdf'))

        <div class="mb-3">
            <label class="form-label">Tipo de rango</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="tipo_mes" value="mes" checked>
                    <label class="form-check-label" for="tipo_mes">Mes específico</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_rango" id="tipo_rango_fechas" value="rango">
                    <label class="form-check-label" for="tipo_rango_fechas">Rango de fechas</label>
                </div>
            </div>
        </div>

        <div class="mb-3" id="div_mes">
            <label for="mes" class="form-label">Seleccione el Mes</label>
            <input type="month" name="mes" id="mes" class="form-control">
        </div>

        <div class="mb-3 d-none" id="div_rango_fechas">
            <div class="row">
                <div class="col-md-6">
                    <label for="fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control">
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tipoMes = document.getElementById('tipo_mes');
                const tipoRango = document.getElementById('tipo_rango_fechas');
                const divMes = document.getElementById('div_mes');
                const divRango = document.getElementById('div_rango_fechas');

                tipoMes.addEventListener('change', function() {
                    if (this.checked) {
                        divMes.classList.remove('d-none');
                        divRango.classList.add('d-none');
                    }
                });
                tipoRango.addEventListener('change', function() {
                    if (this.checked) {
                        divMes.classList.add('d-none');
                        divRango.classList.remove('d-none');
                    }
                });
            });
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