@php
    $showEsp = empty($especialidad);
    $showEstado = empty($estado);
    $showTipo = empty($tipo_paciente);
    $showFechaCita = $mostrarFechaCita ?? (empty($fecha_desde) || empty($fecha_hasta) || $fecha_desde !== $fecha_hasta);
    $showFechaRegistro = empty($fecha_registro_desde) || empty($fecha_registro_hasta) || $fecha_registro_desde !== $fecha_registro_hasta;
    $totalCols = 6 + ($showEsp ? 1 : 0) + ($showFechaCita ? 1 : 0) + ($showTipo ? 1 : 0) + ($showEstado ? 1 : 0) + ($showFechaRegistro ? 1 : 0);

    $tituloFecha = '';
    if ($fecha_desde && $fecha_hasta) {
        if ($fecha_desde === $fecha_hasta) {
            $tituloFecha = 'Día: ' . \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y');
        } else {
            $tituloFecha = \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y');
        }
    } else {
        $tituloFecha = 'Todos los Registros';
    }
@endphp
<table>
    <tr>
        <td colspan="{{ $totalCols }}" style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">
            Reporte de Citas — {{ $tituloFecha }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $totalCols }}" style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;">
            @if($especialidad)
                <strong> Especialidad: </strong> {{ $especialidad }} &nbsp;|&nbsp;
            @endif
            @if($tipo_paciente)
                <strong> Tipo: </strong> {{ $tipo_paciente === 'primera_vez' ? 'Primera Vez' : ($tipo_paciente === 'control' ? 'Sucesiva' : 'Orden Médica') }} &nbsp;|&nbsp;
            @endif
            @if($estado)
                <strong> Estado: </strong> {{ $estado }} &nbsp;|&nbsp;
            @endif
            <strong> Fecha de la Cita: </strong>
            @if($fecha_desde && $fecha_hasta)
                @if($fecha_desde === $fecha_hasta)
                    Día: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }}
                @else
                    {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}
                @endif
            @else
                Todos los Registros
            @endif
            @if($fecha_registro_desde || $fecha_registro_hasta)
                &nbsp;|&nbsp; <strong> Fecha de Registro: </strong>
                @if($fecha_registro_desde && $fecha_registro_hasta)
                    @if($fecha_registro_desde === $fecha_registro_hasta)
                        Día: {{ \Carbon\Carbon::parse($fecha_registro_desde)->format('d/m/Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($fecha_registro_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_registro_hasta)->format('d/m/Y') }}
                    @endif
                @elseif($fecha_registro_desde)
                    Desde {{ \Carbon\Carbon::parse($fecha_registro_desde)->format('d/m/Y') }}
                @elseif($fecha_registro_hasta)
                    Hasta {{ \Carbon\Carbon::parse($fecha_registro_hasta)->format('d/m/Y') }}
                @endif
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="{{ $totalCols }}" style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">
            Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">N° Historia</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Cédula</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Paciente</th>
        @if($showEsp)<th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Especialidad</th>@endif
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Médico</th>
        @if($showFechaCita)<th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Fecha Cita</th>@endif
        @if($showTipo)<th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Tipo</th>@endif
        @if($showEstado)<th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Estado</th>@endif
        @if($showFechaRegistro)<th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Fecha Registro</th>@endif
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Observación</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Diagnóstico</th>
    </tr>
    @foreach($morbilidades as $index => $m)
    <tr>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->numero_expediente ?? 'Sin asignar' }}</td>
        <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->paciente_cedula }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->paciente_nombre }} {{ $m->paciente_apellido }}</td>
        @if($showEsp)<td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->especialidad_nombre }}</td>@endif
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">Dr. {{ $m->medico_nombre }} {{ $m->medico_apellido }}</td>
        @if($showFechaCita)<td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ \Carbon\Carbon::parse($m->fecha_cita)->format('d/m/Y') }}</td>@endif
        @if($showTipo)<td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->tipo_paciente === 'primera_vez' ? 'Primera Vez' : ($m->tipo_paciente === 'control' ? 'Sucesiva' : 'Orden Médica') }}</td>@endif
        @if($showEstado)<td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->estado }}</td>@endif
        @if($showFechaRegistro)<td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ \Carbon\Carbon::parse($m->created_at)->format('d/m/Y') }}</td>@endif
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->cita_observacion ?: '—' }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">
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
</table>
