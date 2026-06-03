<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Procedencia de Pacientes</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .fecha {
            text-align: right;
            font-size: 10px;
            margin-bottom: 10px;
        }
        h1 {
            color: #20356B;
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 15px;
        }
        /* Evitar que se repita el encabezado en cada página */
        thead {
            display: table-row-group;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .distrito-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .subtotal-row {
            background-color: #d1ecf1;
            font-weight: bold;
            page-break-inside: avoid;
        }
        .total-row {
            background-color: #c3e6cb;
            font-weight: bold;
            page-break-inside: avoid;
        }
        .text-left {
            text-align: left;
        }
        /* Anchos de columna */
        th:first-child, td:first-child { width: 18%; }
        th:nth-child(2), td:nth-child(2) { width: 32%; }
        th:nth-child(3), td:nth-child(3) { width: 15%; }
        th:nth-child(4), td:nth-child(4) { width: 15%; }
        th:nth-child(5), td:nth-child(5) { width: 20%; }
        /* Evitar que las filas se partan en dos páginas */
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    </style>
</head>
<body>
    @if($membrete)
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>Reporte de Procedencia de Pacientes</h1>
        <h3>{{ $titulo }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Distrito</th>
                <th>Municipio</th>
                <th>Citas Agendadas</th>
                <th>Citas Atendidas</th>
                <th>Total Pacientes*</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = ['agendadas' => 0, 'atendidas' => 0, 'todos' => 0];
            @endphp
            @foreach($reporteFinal as $distritoInfo)
                @foreach($distritoInfo['municipios'] as $municipio)
                    <tr>
                        <td class="distrito-row">{{ $distritoInfo['distrito'] }}</td>
                        <td class="text-left">{{ $municipio['nombre'] }}</td>
                        <td>{{ $municipio['agendadas'] }}</td>
                        <td>{{ $municipio['atendidas'] }}</td>
                        <td>{{ $municipio['total'] }}</td>
                    </tr>
                @endforeach
                @if(count($distritoInfo['municipios']) > 0)
                    <tr class="subtotal-row">
                        <td colspan="2"><strong>Subtotal {{ $distritoInfo['distrito'] }}</strong></td>
                        <td><strong>{{ $distritoInfo['subtotal']['agendadas'] }}</strong></td>
                        <td><strong>{{ $distritoInfo['subtotal']['atendidas'] }}</strong></td>
                        <td><strong>{{ $distritoInfo['subtotal']['total'] }}</strong></td>
                    </tr>
                @endif
                @php
                    $grandTotal['agendadas'] += $distritoInfo['subtotal']['agendadas'];
                    $grandTotal['atendidas'] += $distritoInfo['subtotal']['atendidas'];
                    $grandTotal['todos'] += $distritoInfo['subtotal']['total'];
                @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL GENERAL</strong></td>
                <td><strong>{{ $grandTotal['agendadas'] }}</strong></td>
                <td><strong>{{ $grandTotal['atendidas'] }}</strong></td>
                <td><strong>{{ $grandTotal['todos'] }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="fecha">
        <p><small>* Total Pacientes: pacientes únicos con al menos una cita (Agendada o Atendida) en el período.</small></p>
        <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Rango de fechas: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}</p>
    </div>
</body>
</html>