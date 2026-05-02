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
            Listado de médicos con opcion de filtro por especialidad.
            @endslot

            @slot('reporte_bs_target','#modalMedicosEspecialidad')
            
            @slot('reporte_excel')
            {{route('reportes.medicos_excel')}}
            @endslot
            
            @endcomponent
        </div>    

        {{-- 2 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Morbilidad')

            @slot('card_desc')
            Reporte de Morbilidad, con filtro mensual o exportación en Excel.
            @endslot

            @slot('reporte_bs_target','#modalMorbilidad')
            
            @slot('reporte_excel')
            {{route('reportes.medicos_excel')}}
            @endslot
        
            @endcomponent
        </div>

        {{-- 3 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Procedencia de Pacientes')

            @slot('card_desc')
            Reporte de la Procedencia de los Pacientes atendidos en un rango de fecha establecido.
            @endslot

            @slot('reporte_bs_target','#modalProcedenciaPacientes')
            
            @slot('reporte_excel')
            {{route('reportes.medicos_excel')}}
            @endslot
            @endcomponent
        </div>
    </div>


{{--SEGUNDA ROW--}}


    <div class="row g-4 mt-4">

        {{-- 1 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','25 Causas Principales')

            @slot('card_desc')
            diagnóstico y sexo de las 25 principales causas de consulta externa.
            @endslot

            @slot('reporte_bs_target','#modal25CausasPrincipales')
            
            @slot('reporte_excel')
            {{route('reportes.medicos_excel')}}
            @endslot
            
            @endcomponent
        </div>

        {{-- 2 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Movimiento de Consultas')

            @slot('card_desc')
            Reporte con Pacientes de Primera Consulta o Consulta Sucesiva por Mes.
            @endslot

            @slot('reporte_bs_target','#modalMovimientoConsultas')
            
            @slot('reporte_excel')
            {{route('reportes.medicos_excel')}}
            @endslot
            
            @endcomponent
        </div>

        {{-- 3 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Movimiento Consulta Aro')

            @slot('card_desc')
            Pacientes con menos de 13 semanas de gestación de primera, y las adolescentes entre 10-19 de primera.
            @endslot

            @slot('reporte_bs_target','#modalMovimientoConsultaAro')
            
            @slot('reporte_excel')
            {{route('reportes.medicos_excel')}}
            @endslot
            
            @endcomponent
        </div>
    </div>


{{--TERCERA ROW--}}

    <div class="row g-4 mt-4">

        {{-- 1 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Inasistencia Pacientes')

            @slot('card_desc')
            Reporte Mensual de las Ausencias de Pacientes Ordenadas por Especialidad.
            @endslot

            @slot('reporte_bs_target','#modalPacienteInasistencia')
            
            @slot('reporte_excel')
            {{route('reportes.medicos_excel')}}
            @endslot
            
            @endcomponent
        </div>

        {{-- 2 --}}
        <div class="col-md-4">
            @component('reportes.card')
            @slot('card_title','Inasistencia Médicos')

            @slot('card_desc')
            Reporte Mensual con Información Basica de los Pacientes que Perdieron Citas.
            @endslot

            @slot('reporte_bs_target','#modalMedicoInasistencia')
            
            @slot('reporte_excel')
            {{route('reportes.medicos_excel')}}
            @endslot
            
            @endcomponent
        </div>
    </div>
</div>

@include('reportes.popups')

@include('layouts.footer')
@endsection