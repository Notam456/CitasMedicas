<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Médicos en {{ $especialidad->nombre }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { color: #20356B; text-align: center; }
    </style>
</head>

<img src="{{ $membrete }}" style="width: 100%;">

<body>
    <h1>Médicos en {{ $especialidad->nombre }}</h1>
    <p><strong>Fecha del reporte:</strong> {{ now()->format('d/m/Y H:i') }}</p>

    @if($medicos->count())
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicos as $medico)
                <tr>
                    <td>{{ $medico->id_medico }}</td>
                    <td>{{ $medico->nombres }}</td>
                    <td>{{ $medico->apellidos }}</td>
                    <td>{{ $medico->cedula }}</td>
                    <td>{{ $medico->telefono }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay médicos registrados para esta especialidad.</p>
    @endif
</body>
</html>