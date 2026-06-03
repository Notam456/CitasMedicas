<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Médicos</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            margin: 20px;
            font-size: 11px;
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
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            word-break: normal;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        /* Anchos de columna redistribuidos (sin ID) */
        th:first-child, td:first-child { width: 20%; }   /* Nombres */
        th:nth-child(2), td:nth-child(2) { width: 20%; } /* Apellidos */
        th:nth-child(3), td:nth-child(3) { width: 18%; } /* Cédula */
        th:nth-child(4), td:nth-child(4) { width: 18%; } /* Teléfono */
        th:nth-child(5), td:nth-child(5) { width: 24%; } /* Especialidad */
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
        @if($especialidad)
            <h1>Médicos de {{ $especialidad->nombre }}</h1>
        @else
            <h1>Todos los Médicos</h1>
        @endif
    </div>

    <div class="fecha">
        <p>Fecha del reporte: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if($medicos->count())
        <table>
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Especialidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicos as $medico)
                <tr>
                    <td>{{ $medico->nombre }}</td>
                    <td>{{ $medico->apellido }}</td>
                    <td>{{ $medico->cedula }}</td>
                    <td>{{ $medico->telefono }}</td>
                    <td>{{ $medico->especialidad->nombre ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay médicos registrados para esta especialidad.</p>
    @endif
</body>
</html>