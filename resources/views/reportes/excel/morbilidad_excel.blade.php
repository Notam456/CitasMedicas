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

<p>
    @if($especialidad)
        <strong>Especialidad:</strong> {{ $especialidad }}<br>
    @endif
    @if($fecha_desde && $fecha_hasta)
        <strong>Período:</strong> {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}<br>
    @elseif($fecha_desde)
        <strong>Desde:</strong> {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }}<br>
    @elseif($fecha_hasta)
        <strong>Hasta:</strong> {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}<br>
    @endif
    <strong>Reporte generado:</strong> {{ now()->format('d/m/Y H:i:s') }}
</p>
