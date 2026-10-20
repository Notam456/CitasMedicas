<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cedula_tipo' => 'required|in:V,E',
            'cedula' => 'required|string|min:7|max:20|regex:/^[0-9]+$/',
            'numero_expediente' => 'nullable|regex:/^\d{2}-\d{2}-\d{2}$/',
            'rif' => 'nullable|string|max:20',
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15|regex:/^[\d\-\(\)\s\+]+$/',
            'estado_id' => 'required|numeric|exists:estados,id',
            'municipio_id' => 'required|numeric|exists:municipios,id',
            'parroquia_id' => 'required|numeric|exists:parroquias,id',
            'direccion' => 'nullable|string|max:255',
            'sexo' => 'required|in:Masculino,Femenino',
            'calendario_id' => 'required|numeric|exists:calendarios,id',
            'fecha_cita' => 'required|date|after_or_equal:today',
            'observacion' => 'nullable|string',
            'especialidad_id' => 'required|exists:especialidades,id',
            // Algunas especialidades no tienen médico fijo ("Cualquier médico"),
            // por eso médico es opcional; el día seleccionado (calendario_id) es
            // la garantía real de que hay disponibilidad.
            'medico_id' => 'nullable|string',
            'tipo_paciente' => 'required|string|in:primera_vez,control,orden_medica',
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
