<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Morbilidad</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Hospital Central "Dr. Plácido D. Rodriguez Rivero"</h2>
    <h3>Reporte de Morbilidad</h3>
    <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>Paciente</th><th>Cédula</th><th>Fecha Cita</th><th>Especialidad</th><th>Médico</th><th>Diagnóstico</th><th>Observaciones</th></tr>
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
</body>
</html>