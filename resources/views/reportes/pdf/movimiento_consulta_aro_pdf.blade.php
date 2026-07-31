<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Movimiento Consulta Aro Mensual</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1 { color: #20356B; text-align: center; font-size: 16px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: center; vertical-align: middle; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 9px; }
        tr { page-break-inside: avoid; }
        .fecha { text-align: center; font-size: 10px; margin-bottom: 10px; }
        .total-row { background-color: #c3e6cb; font-weight: bold; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    @if(!empty($membrete))
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>{{ $titulo }}</h1>
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
                <th>Mes</th>
                <th>&lt;13 Sem (1ra Vez)</th>
                <th>Adolesc 10-19 (1ra Vez)</th>
                <th>Total Consultas Aro</th>
            </tr>
            @foreach($data as $row)
            <tr>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $row['mes'])->locale('es')->translatedFormat('F Y') }}</td>
                <td>{{ $row['menos_13_sem'] }}</td>
                <td>{{ $row['adolescentes'] }}</td>
                <td>{{ $row['total_consultas'] }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td><strong>TOTAL GENERAL</strong></td>
                <td><strong>{{ $totales['menos_13_sem'] }}</strong></td>
                <td><strong>{{ $totales['adolescentes'] }}</strong></td>
                <td><strong>{{ $totales['total_consultas'] }}</strong></td>
            </tr>
        </table>
    @else
        <p>No hay datos para el período seleccionado.</p>
    @endif
</body>
</html>
