<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Movimiento de Consultas</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .fecha { text-align: center; font-size: 10px; margin-bottom: 10px; }
        h1 { color: #20356B; text-align: center; font-size: 18px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 15px; }
        thead { display: table-row-group; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: center; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; }
        th:first-child, td:first-child { width: 40%; }
        th:nth-child(2), td:nth-child(2) { width: 20%; }
        th:nth-child(3), td:nth-child(3) { width: 20%; }
        th:nth-child(4), td:nth-child(4) { width: 20%; }
        tr { page-break-inside: avoid; }
        .total-row { background-color: #c3e6cb; font-weight: bold; }
    </style>
</head>
<body>
    @if(isset($membrete) && $membrete)
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>Movimiento de Consulta Externa</h1>
      <!--  <h3>{{ $titulo }}</h3> -->
    </div>

    <div class="fecha">
        <p>
            <strong>Tipo de paciente:</strong> {{ $tipoPaciente == 'adulto' ? 'Mayores de 12 años' : 'Pediatría (12 años o menos)' }}
            &nbsp;|&nbsp; <strong>Período:</strong> {{ $fechaTexto }}
            &nbsp;|&nbsp; Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>

    @if(count($data) > 0)
        <table>
            <thead>
                <tr>
                    <th>Especialidad</th>
                    <th>Primera vez</th>
                    <th>Sucesivas</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totales = ['primera' => 0, 'sucesivas' => 0, 'total' => 0]; @endphp
                @foreach($data as $row)
                <tr>
                    <td>{{ $row['especialidad'] }}</td>
                    <td>{{ $row['primera_vez'] }}</td>
                    <td>{{ $row['sucesivas'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
                @php
                    $totales['primera'] += $row['primera_vez'];
                    $totales['sucesivas'] += $row['sucesivas'];
                    $totales['total'] += $row['total'];
                @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="1"><strong>TOTAL GENERAL</strong></td>
                    <td><strong>{{ $totales['primera'] }}</strong></td>
                    <td><strong>{{ $totales['sucesivas'] }}</strong></td>
                    <td><strong>{{ $totales['total'] }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <p>No hay datos para el período seleccionado.</p>
    @endif
</body>
</html>