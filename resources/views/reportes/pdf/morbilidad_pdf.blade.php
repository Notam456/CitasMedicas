<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Morbilidad</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { color: #20356B; text-align: center; }
        .membrete { width: 100%; margin-bottom: 20px; }
    </style>
</head>
<body>
    @if(file_exists($membrete))
        <img src="{{ $membrete }}" class="membrete">
    @endif

    <h1>Reporte de Morbilidad</h1>
    <p><strong>Fecha del reporte:</strong> {{ now()->format('d/m/Y H:i') }}</p>

    @if($morbilidades->count())
    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Cédula</th>
                <th>Fecha Cita</th>
                <th>Especialidad</th>
                <th>Médico</th>
                <th>Diagnóstico</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($morbilidades as $m)
            <tr>
                <td>{{ $m->paciente_nombre }} {{ $m->paciente_apellido }}</td>
                <td>{{ $m->paciente_cedula }}</td>
                <td>{{ \Carbon\Carbon::parse($m->fecha_cita)->format('d/m/Y') }}</td>
                <td>{{ $m->especialidad_nombre }}</td>
                <td>Dr. {{ $m->medico_nombre }} {{ $m->medico_apellido }}</td>
                <td>{{ $m->diagnostico }}</td>
                <td>{{ $m->morbilidad_observaciones }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No hay registros de morbilidad.</p>
    @endif
</body>
</html>