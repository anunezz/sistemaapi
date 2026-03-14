<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlertaImpedimentosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'curp'       => ['nullable','string','size:18','regex:/^[A-Z]{4}\d{6}[HM][A-Z]{5}[0-9A-Z]\d$/'],
            'nombres'    => ['required','string','max:100','regex:/^[\p{L}\p{M} ]+$/u'],
            'primer_apellido'  => ['required','string','max:100','regex:/^[\p{L}\p{M} ]+$/u'],
            'segundo_apellido' => ['nullable','string','max:100','regex:/^[\p{L}\p{M} ]+$/u'],
            'fecha_nacimiento' => ['required','date','before:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'curp.size'           => 'La CURP debe tener 18 caracteres.',
            'curp.regex'          => 'La CURP no tiene un formato válido.',
            'nombres.required'    => 'El nombre es obligatorio.',
            'nombres.regex'       => 'El nombre solo puede contener letras y espacios.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.regex'    => 'El primer apellido solo puede contener letras y espacios.',
            'segundo_apellido.regex'   => 'El segundo apellido solo puede contener letras y espacios.',
            'fecha_nacimiento.required'=> 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before'  => 'La fecha de nacimiento debe ser anterior a hoy.'
        ];
    }
}
