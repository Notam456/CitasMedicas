<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuspensionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medico_id' => 'required|exists:medicos,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'suplente_id' => 'nullable|exists:medicos,id',
            'motivo' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_inicio.after_or_equal' => 'La fecha de inicio de la suspensión no puede ser anterior a hoy.',
            'fecha_fin.after_or_equal' => 'La fecha de fin de la suspensión debe ser igual o posterior a la fecha de inicio.',
        ];
    }
}
