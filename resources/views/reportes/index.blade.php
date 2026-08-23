@extends('layouts.template')
@section('title', 'Reportes | SAGECIM')
@include('layouts.sidebar')
@section('content')
@include('layouts.navbar')

<div class="container-fluid pt-4 px-4">

    <div class="row g-4">

        {{-- 1 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Médicos')
            @slot('card_desc')
            Listado de médicos con opción de filtro por especialidad.
            @endslot
            @slot('reporte_bs_target','#modalMedicosEspecialidad')
            @slot('reporte_excel', '#')
            @slot('excel_modal', true)
            @endcomponent
        </div>

        {{-- 2 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Procedencia de Pacientes')

            @slot('card_desc')
            Reporte de la Procedencia de los Pacientes atendidos en un rango de fecha establecido.
            @endslot

            @slot('reporte_bs_target','#modalProcedenciaPacientes')
            
            @slot('reporte_excel')
            {{route('reportes.procedencia_pacientes_excel')}}
            @endslot
            @slot('excel_modal', true)
            @endcomponent
        </div>

    {{-- 3 --}}
    <div class="col-md-4">
        @component('reportes.card')
        @slot('card_title','25 Causas Principales')

        @slot('card_desc')
        diagnóstico y sexo de las 25 principales causas de consulta externa.
        @endslot

        @slot('reporte_bs_target','#modal25CausasPrincipales')
        
        @slot('reporte_excel', '#')
        @slot('excel_modal', true)
        
        @endcomponent
    </div>
</div>

{{--SEGUNDA ROW--}}


    <div class="row g-4 mt-4">

        {{-- 1 --}}
        

        {{-- Movimiento de Consultas --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Movimiento de Consultas')
            @slot('card_desc')
            Reporte con Pacientes de Primera Consulta o Consulta Sucesiva por rango de fechas.
            @endslot
            @slot('reporte_bs_target','#modalMovimientoConsultas')
            @slot('reporte_excel', '#')
            @slot('excel_modal', true)
            @endcomponent
        </div>

        {{-- 2 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Movimiento Consulta Aro Mensual')

            @slot('card_desc')
            Pacientes con menos de 13 semanas de gestación de primera, y las adolescentes entre 10-19 de primera.
            @endslot

            @slot('reporte_bs_target','#modalMovimientoConsultaAro')

            @slot('reporte_excel')
            {{route('reportes.movimiento_consulta_aro_excel')}}
            @endslot
            @slot('excel_modal', true)
            
            @endcomponent
        </div>

        {{-- 3 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Inasistencias en Citas')

            @slot('card_desc')
            Reporte de Ausencias por Especialidad con Tasas de Inasistencia de Pacientes y Médicos.
            @endslot

            @slot('reporte_bs_target','#modalInasistenciasCitas')
            
            @slot('reporte_excel', '#')
            @slot('excel_modal', true)
            
            @endcomponent
        </div>
    </div>

    {{--TERCERA ROW--}}
    <div class="row g-4 mt-4">

        {{-- 1 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Productividad por Médico')

            @slot('card_desc')
            Citas totales, atendidas, agendadas, canceladas y tasa de atención por médico.
            @endslot

            @slot('reporte_bs_target','#modalProductividadMedico')

            @slot('reporte_excel', '#')
            @slot('excel_modal', true)

            @endcomponent
        </div>

        {{-- 2 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Citas sin Diagnóstico')

            @slot('card_desc')
            Listado de citas atendidas que no tienen diagnóstico registrado.
            @endslot

            @slot('reporte_bs_target','#modalCitasSinDiagnostico')

            @slot('reporte_excel', '#')
            @slot('excel_modal', true)

            @endcomponent
        </div>

        {{-- 3 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Eficiencia de Atención')

            @slot('card_desc')
            Métricas de gestión: tasas de atención, cancelación, primera vez, control e historial traído.
            @endslot

            @slot('reporte_bs_target','#modalEficienciaAtencion')

            @slot('reporte_excel', '#')
            @slot('excel_modal', true)

            @endcomponent
        </div>
    </div>

    @include('reportes.estadisticas')
</div>

@include('reportes.popups')

<script>
    window.routeEstadisticas = "{{ route('reportes.estadisticas.datos') }}";
</script>
<script src="{{ asset('assets/js/reportes-estadisticas.js') }}"></script>

@include('layouts.footer')
@endsection