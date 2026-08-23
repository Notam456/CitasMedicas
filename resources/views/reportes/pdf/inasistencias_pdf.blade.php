<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inasistencias en Citas</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .fecha { text-align: center; font-size: 10px; margin-bottom: 10px; }
        h1 { color: #20356B; text-align: center; font-size: 18px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 9px; }
        th:first-child, td:first-child { width: 4%; }
        th:nth-child(2), td:nth-child(2) { width: 22%; text-align: left; }
        tr { page-break-inside: avoid; }
        .total-row { background-color: #c3e6cb; font-weight: bold; }
        .section-header { background-color: #e8e8e8; font-weight: bold; }
    </style>
</head>
<body>
    @if(isset($membrete) && $membrete)
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>Inasistencias en Citas</h1>
    </div>

    <div class="fecha">
        <p>
            <strong>Período:</strong> {{ $fechaTexto }}
            &nbsp;|&nbsp; Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>

    @if(count($data) > 0)
        <table>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Especialidad</th>
                <th colspan="2">Ausencia Paciente</th>
                <th colspan="2">Ausencia Médico</th>
                <th rowspan="2">Total Inasistencias</th>
                <th rowspan="2">Total Citas</th>
                <th rowspan="2">Tasa Inasistencia</th>
            </tr>
            <tr>
                <th>Cantidad</th>
                <th>%</th>
                <th>Cantidad</th>
                <th>%</th>
            </tr>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['especialidad'] }}</td>
                <td>{{ $row['ausencia_paciente'] }}</td>
                <td>{{ $row['ausencia_paciente_pct'] }}%</td>
                <td>{{ $row['ausencia_medico'] }}</td>
                <td>{{ $row['ausencia_medico_pct'] }}%</td>
                <td>{{ $row['total_inasistencias'] }}</td>
                <td>{{ $row['total_citas'] }}</td>
                <td>{{ $row['tasa_inasistencia'] }}%</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL GENERAL</strong></td>
                <td><strong>{{ $totales['ausencia_paciente'] }}</strong></td>
                <td><strong>{{ $totales['ausencia_paciente_pct'] }}%</strong></td>
                <td><strong>{{ $totales['ausencia_medico'] }}</strong></td>
                <td><strong>{{ $totales['ausencia_medico_pct'] }}%</strong></td>
                <td><strong>{{ $totales['total_inasistencias'] }}</strong></td>
                <td><strong>{{ $totales['total_citas'] }}</strong></td>
                <td><strong>{{ $totales['tasa_inasistencia'] }}%</strong></td>
            </tr>
        </table>
    @else
        <p>No hay datos para el período seleccionado.</p>
    @endif
</body>
</html>
