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
        th, td { border: 1px solid #ddd; padding: 5px 3px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 8px; }
        th:first-child, td:first-child { width: 4%; }
        th:nth-child(2), td:nth-child(2) { width: 18%; text-align: left; }
        tr { page-break-inside: avoid; }
        .total-row { background-color: #c3e6cb; font-weight: bold; }
        .nota { font-size: 8px; color: #666666; margin-top: 12px; }
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
                <th>#</th>
                <th>Especialidad</th>
                <th>Aus. Pac.<br>Cant.</th>
                <th>Aus. Pac.<br>%</th>
                <th>Aus. Méd.<br>Cant.</th>
                <th>Aus. Méd.<br>%</th>
                <th>Canceladas<br>sin Motivo</th>
                <th>Total<br>Inasist.</th>
                <th>Citas<br>Cerradas</th>
                <th>Pendientes</th>
                <th>Tasa<br>Inasist.</th>
            </tr>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['especialidad'] }}</td>
                <td>{{ $row['ausencia_paciente'] }}</td>
                <td>{{ $row['ausencia_paciente_pct'] }}%</td>
                <td>{{ $row['ausencia_medico'] }}</td>
                <td>{{ $row['ausencia_medico_pct'] }}%</td>
                <td>{{ $row['sin_motivo'] }}</td>
                <td>{{ $row['total_inasistencias'] }}</td>
                <td>{{ $row['total_citas'] }}</td>
                <td>{{ $row['pendientes'] }}</td>
                <td>{{ $row['tasa_inasistencia'] }}%</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL GENERAL</strong></td>
                <td><strong>{{ $totales['ausencia_paciente'] }}</strong></td>
                <td><strong>{{ $totales['ausencia_paciente_pct'] }}%</strong></td>
                <td><strong>{{ $totales['ausencia_medico'] }}</strong></td>
                <td><strong>{{ $totales['ausencia_medico_pct'] }}%</strong></td>
                <td><strong>{{ $totales['sin_motivo'] }}</strong></td>
                <td><strong>{{ $totales['total_inasistencias'] }}</strong></td>
                <td><strong>{{ $totales['total_citas'] }}</strong></td>
                <td><strong>{{ $totales['pendientes'] }}</strong></td>
                <td><strong>{{ $totales['tasa_inasistencia'] }}%</strong></td>
            </tr>
        </table>
        <p class="nota">* La tasa se calcula sobre citas cerradas (atendidas + canceladas); las pendientes se informan aparte. "Ausencia Médico" incluye las citas canceladas automáticamente por suspensión del médico sin suplente.</p>
    @else
        <p>No hay datos para el período seleccionado.</p>
    @endif
</body>
</html>
