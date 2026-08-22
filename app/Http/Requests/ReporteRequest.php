<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_rango' => 'required|in:mes,rango',
            'mes' => 'required_if:tipo_rango,mes|nullable|date_format:Y-m',
            'fecha_desde' => 'required_if:tipo_rango,rango|nullable|date',
            'fecha_hasta' => 'required_if:tipo_rango,rango|nullable|date|after_or_equal:fecha_desde',
        ];
    }
}
