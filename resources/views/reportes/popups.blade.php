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
    
    {{--3--}}
    @component('reportes.modal')
            @slot('modal_id', 'modalProcedenciaPacientes')
            @slot('modal_title', 'Reporte de Procedencia en Pacientes')
            @slot('form_action', '#' /*route('reportes.morbilidad')*/)

                <div class="mb-3">
                    <label for="mes" class="form-label">Seleccione el Mes</label>
                    <input type="month" name="mes" id="mes" class="form-control" required>
                </div>
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