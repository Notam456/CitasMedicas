<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Citas</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1 { color: #20356B; text-align: center; font-size: 18px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr { page-break-inside: avoid; }
        .fecha { text-align: center; font-size: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
    @if(isset($membrete) && file_exists($membrete))
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>Reporte de Citas</h1>
    </div>

    <div class="fecha">
        <p>
            Fecha del reporte: {{ now()->format('d/m/Y H:i') }}
            @if($especialidad)
                &nbsp;|&nbsp; <strong>Especialidad:</strong> {{ $especialidad }}
            @endif
            @if($tipo_paciente)
                &nbsp;|&nbsp; <strong>Tipo:</strong> {{ $tipo_paciente === 'primera_vez' ? 'Primera Vez' : 'Sucesiva' }}
            @endif
            @if($estado)
                &nbsp;|&nbsp; <strong>Estado:</strong> {{ $estado }}
            @endif
        </p>
        <p>
            <strong>Fecha de la Cita:</strong>
            @if($fecha_desde && $fecha_hasta)
                {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}
            @else
                Todos los Registros
            @endif
            @if($fecha_registro_desde || $fecha_registro_hasta)
                &nbsp;|&nbsp; <strong>Fecha de Registro:</strong>
                @if($fecha_registro_desde && $fecha_registro_hasta)
                    {{ \Carbon\Carbon::parse($fecha_registro_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_registro_hasta)->format('d/m/Y') }}
                @elseif($fecha_registro_desde)
                    Desde {{ \Carbon\Carbon::parse($fecha_registro_desde)->format('d/m/Y') }}
                @elseif($fecha_registro_hasta)
                    Hasta {{ \Carbon\Carbon::parse($fecha_registro_hasta)->format('d/m/Y') }}
                @endif
            @endif
        </p>
    </div>

    @if($morbilidades->count())
    <table>
        <tbody>
            <tr style="background-color:#f2f2f2;font-weight:bold;">
                <td><strong>Paciente</strong></td>
                <td><strong>Cédula</strong></td>
                <td><strong>Fecha Cita</strong></td>
                <td><strong>Especialidad</strong></td>
                <td><strong>Médico</strong></td>
                <td><strong>Diagnóstico</strong></td>
                <td><strong>Fecha Registro</strong></td>
                <td><strong>Observaciones</strong></td>
            </tr>
            @foreach($morbilidades as $m)
            <tr>
                <td>{{ $m->paciente_nombre }} {{ $m->paciente_apellido }}</td>
                <td>{{ $m->paciente_cedula }}</td>
                <td>{{ \Carbon\Carbon::parse($m->fecha_cita)->format('d/m/Y') }}</td>
                <td>{{ $m->especialidad_nombre }}</td>
                <td>Dr. {{ $m->medico_nombre }} {{ $m->medico_apellido }}</td>
                <td>
                    @php
                        $diag = '';
                        if (!empty($m->patologias_nombres)) {
                            $diag = $m->patologias_nombres;
                            if ($m->diagnostico_libre) {
                                $diag .= ' - ' . $m->diagnostico_libre;
                            }
                        } else {
                            $diag = $m->diagnostico_libre ?: 'Sin diagnóstico';
                        }
                    @endphp
                    {{ $diag }}
                </td>
                <td>{{ \Carbon\Carbon::parse($m->created_at)->format('d/m/Y') }}</td>
                <td>{{ $m->cita_observacion ?: 'Asistió' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No hay registros de morbilidad.</p>
    @endif
</body>
</html>
