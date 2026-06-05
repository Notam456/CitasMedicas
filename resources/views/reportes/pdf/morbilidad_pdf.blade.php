<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Morbilidad</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1 { color: #20356B; text-align: center; font-size: 18px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; }
        thead { display: table-row-group; }
        tr { page-break-inside: avoid; }
        .fecha { text-align: right; font-size: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
    @if(isset($membrete) && file_exists($membrete))
        <img src="{{ $membrete }}" style="width: 100%;">
    @endif

    <div class="header">
        <h1>Reporte de Morbilidad</h1>
    </div>

    <div class="fecha">
        <p>Fecha del reporte: {{ now()->format('d/m/Y H:i') }}</p>
        @if($especialidad)
            <p><strong>Especialidad:</strong> {{ $especialidad }}</p>
        @endif
        @if($fecha_desde && $fecha_hasta)
            <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}</p>
        @elseif($fecha_desde)
            <p><strong>Desde:</strong> {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }}</p>
        @elseif($fecha_hasta)
            <p><strong>Hasta:</strong> {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}</p>
        @endif
    </div>

    @if($morbilidades->count())
    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Cédula</th>
                <th>Fecha Cita</th>
                <th>Especialidad</th>
                <th>Médico</th>
                <th>Diagnóstico</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
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