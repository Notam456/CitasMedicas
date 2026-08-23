<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Eficiencia de Atención</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .fecha { text-align: center; font-size: 10px; margin-bottom: 10px; }
        h1 { color: #20356B; text-align: center; font-size: 18px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 5px 3px; text-align: center; vertical-align: middle; word-wrap: break-word; font-size: 9px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr { page-break-inside: avoid; }
        .total-row { background-color: #c3e6cb; font-weight: bold; }
    </style>
</head>
<body>
    @if(isset($membrete) && $membrete)
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>Eficiencia de Atención</h1>
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
                <th>Mes</th>
                <th>Total</th>
                <th>Atendidas</th>
                <th>Tasa Atención</th>
                <th>Canceladas</th>
                <th>Tasa Cancel.</th>
                <th>1ª Vez</th>
                <th>% 1ª Vez</th>
                <th>Control</th>
                <th>% Control</th>
                <th>Hist. Traída</th>
                <th>% Hist.</th>
                <th>Días Prom.</th>
            </tr>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['mes'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td>{{ $row['atendidas'] }}</td>
                <td>{{ $row['tasa_atencion'] }}%</td>
                <td>{{ $row['canceladas'] }}</td>
                <td>{{ $row['tasa_cancelacion'] }}%</td>
                <td>{{ $row['primera_vez'] }}</td>
                <td>{{ $row['pct_primera_vez'] }}%</td>
                <td>{{ $row['control'] }}</td>
                <td>{{ $row['pct_control'] }}%</td>
                <td>{{ $row['historia_traida'] }}</td>
                <td>{{ $row['pct_historia_traida'] }}%</td>
                <td>{{ $row['promedio_dias_espera'] }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL</strong></td>
                <td><strong>{{ $totales['total'] }}</strong></td>
                <td><strong>{{ $totales['atendidas'] }}</strong></td>
                <td><strong>{{ $totales['tasa_atencion'] }}%</strong></td>
                <td><strong>{{ $totales['canceladas'] }}</strong></td>
                <td><strong>{{ $totales['tasa_cancelacion'] }}%</strong></td>
                <td><strong>{{ $totales['primera_vez'] }}</strong></td>
                <td><strong>{{ $totales['pct_primera_vez'] }}%</strong></td>
                <td><strong>{{ $totales['control'] }}</strong></td>
                <td><strong>{{ $totales['pct_control'] }}%</strong></td>
                <td><strong>{{ $totales['historia_traida'] }}</strong></td>
                <td><strong>{{ $totales['pct_historia_traida'] }}%</strong></td>
                <td><strong>{{ $totales['promedio_dias_espera'] }}</strong></td>
            </tr>
        </table>
    @else
        <p>No hay datos para el período seleccionado.</p>
    @endif
</body>
</html>
