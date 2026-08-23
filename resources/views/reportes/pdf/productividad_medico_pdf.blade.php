<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Productividad por Médico</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .fecha { text-align: center; font-size: 10px; margin-bottom: 10px; }
        h1 { color: #20356B; text-align: center; font-size: 18px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 9px; }
        th:nth-child(2), td:nth-child(2) { text-align: left; }
        tr { page-break-inside: avoid; }
        .total-row { background-color: #c3e6cb; font-weight: bold; }
    </style>
</head>
<body>
    @if(isset($membrete) && $membrete)
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>Productividad por Médico</h1>
    </div>

    <div class="fecha">
        <p>
            <strong>Período:</strong> {{ $fechaTexto }}
            &nbsp;|&nbsp; <strong>Especialidad:</strong> {{ $especialidadNombre }}
            &nbsp;|&nbsp; Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>

    @if(count($data) > 0)
        <table>
            <tr>
                <th>#</th>
                <th>Médico</th>
                <th>Especialidad</th>
                <th>Total Citas</th>
                <th>Atendidas</th>
                <th>Agendadas</th>
                <th>Canceladas</th>
                <th>Tasa Atención</th>
            </tr>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['medico'] }}</td>
                <td>{{ $row['especialidad'] }}</td>
                <td>{{ $row['total_citas'] }}</td>
                <td>{{ $row['atendidas'] }}</td>
                <td>{{ $row['agendadas'] }}</td>
                <td>{{ $row['canceladas'] }}</td>
                <td>{{ $row['tasa_atencion'] }}%</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3"><strong>TOTAL GENERAL</strong></td>
                <td><strong>{{ $totales['total_citas'] }}</strong></td>
                <td><strong>{{ $totales['atendidas'] }}</strong></td>
                <td><strong>{{ $totales['agendadas'] }}</strong></td>
                <td><strong>{{ $totales['canceladas'] }}</strong></td>
                <td><strong>{{ $totales['tasa_atencion'] }}%</strong></td>
            </tr>
        </table>
    @else
        <p>No hay datos para el período seleccionado.</p>
    @endif
</body>
</html>
