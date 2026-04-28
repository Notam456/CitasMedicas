@extends('layouts.template')
@section('title', 'Reportes | SAGECIM')
@include('layouts.sidebar')
@section('content')
@include('layouts.navbar')

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        
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

        @component('reportes.modal')
        @slot('modal_id', 'modalMorbilidad')
        @slot('modal_title', 'Reporte de Morbilidad Mensual')
        @slot('form_action', '#' /*route('reportes.morbilidad')*/)

        <div class="mb-3">
            <label for="mes" class="form-label">Seleccione el Mes</label>
            <input type="month" name="mes" id="mes" class="form-control" required>
        </div>
    @endcomponent

    </div>
</div>

@include('layouts.footer')
@endsection