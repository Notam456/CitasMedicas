@extends('layouts.template')
@section('title', 'Notificaciones | SAGECIM')

@include('layouts.sidebar')

@section('content')
@include('layouts.navbar')

<div class="table-responsive bg-light rounded h-100 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Notificaciones</h3>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <button type="button" id="btn-mark-all-read" class="btn btn-primary">
                <i class="bi bi-check-all"></i> Marcar todas como leídas
            </button>
        @endif
    </div>

    <table class="table table-hover" id="tablaNotificaciones">
        <thead>
            <tr>
                <th style="width: 40px;"></th>
                <th>Mensaje</th>
                <th style="width: 180px;">Fecha</th>
                <th style="width: 100px;" class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@include('layouts.footer')
@endsection

@push('scripts')
<link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

<script>
$(document).ready(function() {
    const table = $('#tablaNotificaciones').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("notificaciones.index") }}',
        columns: [
            { data: 0, name: 'estado', orderable: false, searchable: false, className: 'text-center' },
            { data: 1, name: 'data' },
            { data: 2, name: 'created_at' },
            { data: 3, name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: { url: "{{ asset('vendor/datatables/es-ES.json') }}" },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todas"]],
        order: [[2, 'desc']]
    });

    $('#btn-mark-all-read').on('click', function() {
        $.ajax({
            url: '{{ route("notificaciones.markAllAsRead") }}',
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function() {
                table.ajax.reload();
                $('#notif-count').hide();
                cargarNotificaciones();
            }
        });
    });

    $(document).on('click', '.btn-mark-read', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '{{ url("notificaciones") }}/' + id + '/leida',
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function() {
                table.ajax.reload();
                cargarNotificaciones();
            }
        });
    });
});
</script>
@endpush
