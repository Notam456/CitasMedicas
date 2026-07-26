<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Citas</title>
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
        $showEsp = empty($especialidad);
        $showEstado = empty($estado);
        $showTipo = empty($tipo_paciente);
        $showFechaCita = $mostrarFechaCita ?? (empty($fecha_desde) || empty($fecha_hasta) || $fecha_desde !== $fecha_hasta);
        $showFechaRegistro = empty($fecha_registro_desde) || empty($fecha_registro_hasta) || $fecha_registro_desde !== $fecha_registro_hasta;

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

        $fechaRegTexto = '';
        if ($fecha_registro_desde && $fecha_registro_hasta) {
            if ($fecha_registro_desde === $fecha_registro_hasta) {
                $fechaRegTexto = 'Día: ' . \Carbon\Carbon::parse($fecha_registro_desde)->format('d/m/Y');
            } else {
                $fechaRegTexto = \Carbon\Carbon::parse($fecha_registro_desde)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($fecha_registro_hasta)->format('d/m/Y');
            }
        } elseif ($fecha_registro_desde) {
            $fechaRegTexto = 'Desde ' . \Carbon\Carbon::parse($fecha_registro_desde)->format('d/m/Y');
        } elseif ($fecha_registro_hasta) {
            $fechaRegTexto = 'Hasta ' . \Carbon\Carbon::parse($fecha_registro_hasta)->format('d/m/Y');
        }
    @endphp

    <div class="header">
        <h1>Reporte de Citas — {{ $tituloFecha }}</h1>
    </div>

    <div class="fecha">
        <p>
            <strong>Fecha de la Cita:</strong> {{ $fechaTexto }}
            @if($especialidad)
                &nbsp;|&nbsp; <strong>Especialidad:</strong> {{ $especialidad }}
            @endif
            @if($tipo_paciente)
                &nbsp;|&nbsp; <strong>Tipo:</strong> {{ $tipo_paciente === 'primera_vez' ? 'Primera Vez' : ($tipo_paciente === 'control' ? 'Sucesiva' : 'Orden Médica') }}
            @endif
            @if($estado)
                &nbsp;|&nbsp; <strong>Estado:</strong> {{ $estado }}
            @endif
            @if($fechaRegTexto)
                &nbsp;|&nbsp; <strong>Fecha de Registro:</strong> {{ $fechaRegTexto }}
            @endif
        </p>
        <p>Reporte generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Historia</th>
                <th>Cédula</th>
                <th>Paciente</th>
                @if($showEsp)<th>Especialidad</th>@endif
                <th>Médico</th>
                @if($showFechaCita)<th>Fecha Cita</th>@endif
                @if($showTipo)<th>Tipo</th>@endif
                @if($showEstado)<th>Estado</th>@endif
                @if($showFechaRegistro)<th>Fecha Registro</th>@endif
                <th>Observación</th>
                <th>Diagnóstico</th>
            </tr>
        </thead>
        <tbody>
            @foreach($morbilidades as $m)
            <tr>
                <td>{{ $m->numero_expediente ?? 'Sin asignar' }}</td>
                <td>{{ $m->paciente_cedula }}</td>
                <td>{{ $m->paciente_nombre }} {{ $m->paciente_apellido }}</td>
                @if($showEsp)<td>{{ $m->especialidad_nombre }}</td>@endif
                <td>Dr. {{ $m->medico_nombre }} {{ $m->medico_apellido }}</td>
                @if($showFechaCita)<td class="text-center">{{ \Carbon\Carbon::parse($m->fecha_cita)->format('d/m/Y') }}</td>@endif
                @if($showTipo)<td>{{ $m->tipo_paciente === 'primera_vez' ? 'Primera Vez' : ($m->tipo_paciente === 'control' ? 'Sucesiva' : 'Orden Médica') }}</td>@endif
                @if($showEstado)<td>{{ $m->estado }}</td>@endif
                @if($showFechaRegistro)<td class="text-center">{{ \Carbon\Carbon::parse($m->created_at)->format('d/m/Y') }}</td>@endif
                <td>{{ $m->cita_observacion ?: '—' }}</td>
                <td>
                    @php
                        $diag = '';
                        if ($m->estado === 'Agendada') {
                            $diag = 'SIN OBSERVACION, PENDIENTE POR ATENDER';
                        } elseif (!empty($m->patologias_nombres)) {
                            $diag = $m->patologias_nombres;
                            if ($m->diagnostico_libre) {
                                $diag .= ' - ' . $m->diagnostico_libre;
                            }
                        } elseif ($m->diagnostico_libre) {
                            $diag = $m->diagnostico_libre;
                        } elseif ($m->estado === 'Cancelada') {
                            $diag = 'SIN OBSERVACION, ESTUVO AGENDADA';
                        } else {
                            $diag = '—';
                        }
                    @endphp
                    {{ $diag }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
