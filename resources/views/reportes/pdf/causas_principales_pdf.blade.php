<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>25 Causas Principales</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 9px; }
        .header { text-align: center; margin-bottom: 20px; }
        .fecha { text-align: center; font-size: 10px; margin-bottom: 10px; }
        h1 { color: #20356B; text-align: center; font-size: 16px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 15px; }
        thead { display: table-row-group; }
        th, td { border: 1px solid #ddd; padding: 3px 2px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 8px; }
        th:first-child, td:first-child { width: 4%; }
        th:nth-child(2), td:nth-child(2) { width: 28%; }
        th:nth-child(3), td:nth-child(3) { width: 13%; }
        th:nth-child(4), td:nth-child(4) { width: 13%; }
        th:nth-child(5), td:nth-child(5) { width: 13%; }
        th:nth-child(6), td:nth-child(6) { width: 13%; }
        th:nth-child(7), td:nth-child(7) { width: 10%; }
        tr { page-break-inside: avoid; }
        .total-row { background-color: #c3e6cb; font-weight: bold; }
        .text-left { text-align: left; }
        .sexo-header { background-color: #e8e8e8; }
    </style>
</head>
<body>
    @if(isset($membrete) && $membrete)
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>25 Principales Causas de Consulta Externa</h1>
    </div>

    <div class="fecha">
        <p><strong>Período:</strong> {{ $fechaTexto }}</p>
        <p>Reporte generado: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if(count($data) > 0)
        <table>
            <thead>
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Patología (Diagnóstico)</th>
                    <th colspan="2">Masculino</th>
                    <th colspan="2">Femenino</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <th>1ra Vez</th>
                    <th>Sucesivas</th>
                    <th>1ra Vez</th>
                    <th>Sucesivas</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totales = ['m1' => 0, 'ms' => 0, 'f1' => 0, 'fs' => 0, 'total' => 0];
                @endphp
                @foreach($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $row['patologia'] }}</td>
                    <td>{{ $row['masculino_primera'] }}</td>
                    <td>{{ $row['masculino_sucesivas'] }}</td>
                    <td>{{ $row['femenino_primera'] }}</td>
                    <td>{{ $row['femenino_sucesivas'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
                @php
                    $totales['m1'] += $row['masculino_primera'];
                    $totales['ms'] += $row['masculino_sucesivas'];
                    $totales['f1'] += $row['femenino_primera'];
                    $totales['fs'] += $row['femenino_sucesivas'];
                    $totales['total'] += $row['total'];
                @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="2"><strong>TOTAL GENERAL</strong></td>
                    <td><strong>{{ $totales['m1'] }}</strong></td>
                    <td><strong>{{ $totales['ms'] }}</strong></td>
                    <td><strong>{{ $totales['f1'] }}</strong></td>
                    <td><strong>{{ $totales['fs'] }}</strong></td>
                    <td><strong>{{ $totales['total'] }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <p>No hay datos para el período seleccionado.</p>
    @endif
</body>
</html>