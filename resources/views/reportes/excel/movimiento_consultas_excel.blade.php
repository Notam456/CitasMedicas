@php $colspan = count($columnas) + ($especialidadSeleccionada ? 0 : 1); @endphp
<table>
    <tr>
        <td colspan="{{ $colspan }}" style="font-size: 18px; font-weight: bold; text-align: center; padding: 12px; background-color: #1B5E20; color: #FFFFFF; border: none;">
            Movimiento de Consulta Externa
        </td>
    </tr>
    <tr>
        <td colspan="{{ $colspan }}" style="font-size: 11px; padding: 8px 12px; background-color: #E8F5E9; border: none;">
            <strong>Edad:</strong> {{ $tipoPaciente }} &nbsp;|&nbsp; <strong>Especialidad:</strong> {{ $especialidadNombre }} &nbsp;|&nbsp; <strong>Período:</strong> {{ $fechaTexto }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $colspan }}" style="font-size: 9px; padding: 4px 12px; color: #666666; border: none;">
            Reporte generado: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
    <tr>
        @if(!$especialidadSeleccionada)
            <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">Especialidad</th>
        @endif
        @foreach($columnas as $label)
            <th style="background-color: #2E7D32; color: #FFFFFF; font-weight: bold; font-size: 12px; padding: 10px; text-align: center; border: 1px solid #1B5E20;">{{ $label }}</th>
        @endforeach
    </tr>
    @if($especialidadSeleccionada)
        <tr>
            @foreach($columnas as $clave => $label)
                <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9;">{{ $totales[$clave] }}</td>
            @endforeach
        </tr>
    @else
        @foreach($data as $index => $row)
        <tr>
            <td style="padding: 8px; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row['especialidad'] }}</td>
            @foreach($columnas as $clave => $label)
                <td style="padding: 8px; text-align: center; border: 1px solid #C8E6C9; background-color: {{ $index % 2 == 0 ? '#F1F8E9' : '#FFFFFF' }};">{{ $row[$clave] }}</td>
            @endforeach
        </tr>
        @endforeach
        <tr style="font-weight: bold; background-color: #A5D6A7;">
            <td style="padding: 10px; border: 1px solid #81C784; font-weight: bold;">TOTAL GENERAL</td>
            @foreach($columnas as $clave => $label)
                <td style="padding: 10px; text-align: center; border: 1px solid #81C784; font-weight: bold;">{{ $totales[$clave] }}</td>
            @endforeach
        </tr>
    @endif
</table>
