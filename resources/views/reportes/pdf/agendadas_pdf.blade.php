<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Citas Agendadas</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1 { color: #20356B; text-align: center; font-size: 18px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 4px 3px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 9px; }
        tr { page-break-inside: avoid; }
        .fecha { text-align: center; font-size: 10px; margin-bottom: 10px; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    @if(!empty($membrete))
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    @php
        $showTipo = $mostrarColumnaTipo ?? true;

        $fechaTexto = '';
        $tituloFecha = '';
        if ($fecha_desde && $fecha_hasta) {
            if ($fecha_desde === $fecha_hasta) {
                $fechaTexto = 'Día: ' . \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y');
                $tituloFecha = 'Día: ' . \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y');
            } else {
                $fechaTexto = \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y');
                $tituloFecha = \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y');
            }
        } else {
            $fechaTexto = 'Todos los Registros';
            $tituloFecha = 'Todos los Registros';
        }
    @endphp

    <div class="header">
        <h1>Reporte de Citas Agendadas — {{ $tituloFecha }}</h1>
    </div>

    <div class="fecha">
        <p>
            <strong>Fecha de la Cita:</strong> {{ $fechaTexto }}
            @if($especialidadHeader)
                &nbsp;|&nbsp; <strong>Especialidad:</strong> {{ $especialidadHeader }}
            @endif
            @if($tipo_paciente)
                &nbsp;|&nbsp; <strong>Tipo:</strong> {{ $tipo_paciente === 'primera_vez' ? 'Primera Vez' : ($tipo_paciente === 'control' ? 'Sucesiva' : 'Orden Médica') }}
            @endif
            @if(!empty($medicoNombreStr))
                &nbsp;|&nbsp; <strong>Médico:</strong> {{ $medicoNombreStr }}
            @endif
        </p>
        <p>Reporte generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <tr>
            <th>N° Historia</th>
            <th>Paciente</th>
            <th>Edad</th>
            <th>Sexo</th>
            @if($showTipo)<th>Tipo de Cita</th>@endif
            <th>Procedencia</th>
            <th style="width: 35%;">Diagnóstico</th>
        </tr>
        @foreach($morbilidades as $m)
        <tr>
            <td>{{ $m->numero_expediente ?? 'Sin asignar' }}</td>
            <td>{{ $m->paciente_nombre }} {{ $m->paciente_apellido }}</td>
            <td class="text-center">{{ $m->edad ?? '—' }}</td>
            <td>{{ $m->sexo ?? '—' }}</td>
            @if($showTipo)<td>{{ $m->tipo_paciente === 'primera_vez' ? 'Primera Vez' : ($m->tipo_paciente === 'control' ? 'Sucesiva' : 'Orden Médica') }}</td>@endif
            <td>{{ $m->distrito_nombre ?? 'Ignorado' }} / {{ $m->municipio_nombre ?? 'Ignorado' }}</td>
            <td style="height: 30px;">&nbsp;</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
