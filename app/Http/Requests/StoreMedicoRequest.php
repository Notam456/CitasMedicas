<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'cedula' => 'required|string|unique:medicos,cedula',
            'telefono' => 'required|string|max:20|regex:/^[\d\-\(\)\s\+]+$/',
            'especialidad_id' => 'required|exists:especialidades,id',
            'horarios' => 'nullable|array',
            'horarios.*.checked' => 'nullable|in:1',
            'horarios.*.hora_entrada' => 'required_if:horarios.*.checked,1|nullable|date_format:H:i',
            'horarios.*.hora_salida' => 'required_if:horarios.*.checked,1|nullable|date_format:H:i|after:horarios.*.hora_entrada',
        ];
    }

    public function messages(): array
    {
        return [
            'horarios.*.hora_entrada.required_if' => 'La hora de entrada es obligatoria para los días seleccionados.',
            'horarios.*.hora_salida.required_if' => 'La hora de salida es obligatoria para los días seleccionados.',
            'horarios.*.hora_salida.after' => 'La hora de salida debe ser posterior a la hora de entrada.',
            'horarios.*.hora_entrada.date_format' => 'El formato de la hora de entrada debe ser HH:MM.',
            'horarios.*.hora_salida.date_format' => 'El formato de la hora de salida debe ser HH:MM.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => mb_convert_case(trim($this->nombre ?? ''), MB_CASE_TITLE, 'UTF-8'),
            'apellido' => mb_convert_case(trim($this->apellido ?? ''), MB_CASE_TITLE, 'UTF-8'),
        ]);
    }
}
