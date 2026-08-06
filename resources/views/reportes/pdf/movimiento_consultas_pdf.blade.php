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
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: center; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; }
        @if(!$especialidadSeleccionada)
        th:first-child, td:first-child { width: 30%; }
        @endif
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
    </div>

    <div class="fecha">
        <p>
            <strong>Edad:</strong> {{ $tipoPaciente }}
            &nbsp;|&nbsp; <strong>Especialidad:</strong> {{ $especialidadNombre }}
            &nbsp;|&nbsp; <strong>Período:</strong> {{ $fechaTexto }}
            &nbsp;|&nbsp; Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>

    @if(count($data) > 0)
        <table>
            <tr>
                @if(!$especialidadSeleccionada)
                    <th>Especialidad</th>
                @endif
                @foreach($columnas as $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
            @if($especialidadSeleccionada)
                <tr>
                    @foreach($columnas as $clave => $label)
                        <td>{{ $totales[$clave] }}</td>
                    @endforeach
                </tr>
            @else
                @foreach($data as $row)
                <tr>
                    <td>{{ $row['especialidad'] }}</td>
                    @foreach($columnas as $clave => $label)
                        <td>{{ $row[$clave] }}</td>
                    @endforeach
                </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>TOTAL GENERAL</strong></td>
                    @foreach($columnas as $clave => $label)
                        <td><strong>{{ $totales[$clave] }}</strong></td>
                    @endforeach
                </tr>
            @endif
        </table>
    @else
        <p>No hay datos para el período seleccionado.</p>
    @endif
</body>
</html>
