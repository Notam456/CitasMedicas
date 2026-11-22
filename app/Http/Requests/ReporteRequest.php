<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza los valores vacios que envian los selects dinamicos para que
     * las reglas "nullable" no rechacen cadenas vacias.
     */
    protected function prepareForValidation(): void
    {
        foreach (['tipo_paciente', 'especialidad_id', 'mes', 'fecha_desde', 'fecha_hasta'] as $campo) {
            if ($this->has($campo) && $this->input($campo) === '') {
                $this->merge([$campo => null]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'tipo_rango' => 'required|in:mes,rango',
            'mes' => 'required_if:tipo_rango,mes|nullable|date_format:Y-m',
            'fecha_desde' => 'required_if:tipo_rango,rango|nullable|date',
            'fecha_hasta' => 'required_if:tipo_rango,rango|nullable|date|after_or_equal:fecha_desde',
            'tipo_paciente' => 'nullable|in:adulto,pediatria,todas',
            'especialidad_id' => 'nullable|integer|exists:especialidades,id',
        ];
    }
}
