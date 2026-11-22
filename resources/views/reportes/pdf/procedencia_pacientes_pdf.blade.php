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
        th:first-child, td:first-child { width: 16%; }
        th:nth-child(2), td:nth-child(2) { width: 28%; }
        th:nth-child(3), td:nth-child(3) { width: 14%; }
        th:nth-child(4), td:nth-child(4) { width: 14%; }
        th:nth-child(5), td:nth-child(5) { width: 14%; }
        th:nth-child(6), td:nth-child(6) { width: 14%; }
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
            <tr>
                <th>Distrito</th>
                <th>Municipio</th>
                <th>Citas Atendidas</th>
                <th>Citas Canceladas</th>
                <th>Citas Pendientes</th>
                <th>Total Citas</th>
            </tr>
            @foreach($reporteFinal as $distritoInfo)
                @if(count($distritoInfo['municipios']) > 0)
                    @foreach($distritoInfo['municipios'] as $municipio)
                        <tr>
                            <td class="distrito-row">{{ $distritoInfo['distrito'] }}</td>
                            <td class="text-left">{{ $municipio['nombre'] }}</td>
                            <td>{{ $municipio['atendidas'] }}</td>
                            <td>{{ $municipio['canceladas'] }}</td>
                            <td>{{ $municipio['pendientes'] }}</td>
                            <td>{{ $municipio['total'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="2"><strong>Subtotal {{ $distritoInfo['distrito'] }}</strong></td>
                        <td><strong>{{ $distritoInfo['subtotal']['atendidas'] }}</strong></td>
                        <td><strong>{{ $distritoInfo['subtotal']['canceladas'] }}</strong></td>
                        <td><strong>{{ $distritoInfo['subtotal']['pendientes'] }}</strong></td>
                        <td><strong>{{ $distritoInfo['subtotal']['total'] }}</strong></td>
                    </tr>
                @elseif($distritoInfo['subtotal']['total'] > 0)
                    <tr class="distrito-row">
                        <td>{{ $distritoInfo['distrito'] }}</td>
                        <td>&mdash;</td>
                        <td>{{ $distritoInfo['subtotal']['atendidas'] }}</td>
                        <td>{{ $distritoInfo['subtotal']['canceladas'] }}</td>
                        <td>{{ $distritoInfo['subtotal']['pendientes'] }}</td>
                        <td>{{ $distritoInfo['subtotal']['total'] }}</td>
                    </tr>
                @endif
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL GENERAL</strong></td>
                <td><strong>{{ $totalesGlobales['atendidas'] }}</strong></td>
                <td><strong>{{ $totalesGlobales['canceladas'] }}</strong></td>
                <td><strong>{{ $totalesGlobales['pendientes'] }}</strong></td>
                <td><strong>{{ $totalesGlobales['total'] }}</strong></td>
            </tr>
    </table>

    <div class="fecha">
        <p><small>* El reporte cuenta citas, no pacientes únicos: un paciente con varias citas en el período suma una por cada cita. Total = Atendidas + Canceladas + Pendientes.</small></p>
        <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Rango de fechas: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}</p>
    </div>
</body>
</html>
