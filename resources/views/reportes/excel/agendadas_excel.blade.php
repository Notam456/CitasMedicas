@php
    $showTipo = $mostrarColumnaTipo ?? true;
    $totalCols = 7 + ($showTipo ? 1 : 0);

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
            Reporte de Citas Agendadas — {{ $tituloFecha }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $totalCols }}" style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;">
            @if($especialidadHeader)
                <strong> Especialidad: </strong> {{ $especialidadHeader }} &nbsp;|&nbsp;
            @endif
            @if($tipo_paciente)
                <strong> Tipo: </strong> {{ $tipo_paciente === 'primera_vez' ? 'Primera Vez' : ($tipo_paciente === 'control' ? 'Sucesiva' : 'Orden Médica') }} &nbsp;|&nbsp;
            @endif
            @if(!empty($medicoNombreStr))
                <strong> Médico: </strong> {{ $medicoNombreStr }} &nbsp;|&nbsp;
            @endif
            <strong> Fecha: </strong> {{ $tituloFecha }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $totalCols }}" style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">
            Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
    <tr>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">N° Historia</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Paciente</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Hist. Traída</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Edad</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Sexo</th>
        @if($showTipo)<th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Tipo de Cita</th>@endif
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Procedencia</th>
        <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Diagnóstico</th>
    </tr>
    @foreach($morbilidades as $index => $m)
    <tr>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }}; text-align: center;">{{ $m->numero_expediente ?? 'Sin asignar' }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->paciente_nombre }} {{ $m->paciente_apellido }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }}; text-align: center;">{{ $m->historia_traida ? 'TH' : 'FH' }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }}; text-align: center;">{{ $m->edad ?? '—' }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }}; text-align: center;">{{ $m->sexo ?? '—' }}</td>
        @if($showTipo)<td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }}; text-align: center;">{{ $m->tipo_paciente === 'primera_vez' ? 'Primera Vez' : ($m->tipo_paciente === 'control' ? 'Sucesiva' : 'Orden Médica') }}</td>@endif
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $m->distrito_nombre ?? 'Ignorado' }} / {{ $m->municipio_nombre ?? 'Ignorado' }}</td>
        <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">&nbsp;</td>
    </tr>
    @endforeach
</table>
