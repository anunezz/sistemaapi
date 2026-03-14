<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\ValidatesBase64;

class SolicitudAltaRequest extends FormRequest
{
    use ValidatesBase64;
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
            'id_oficina' => [
                'required','integer',
                Rule::exists('im_cat_oficina', 'id_oficina')
                ->where('bol_activo', true)
                ->where('id_tipo_oficina',  $this->id_tipo_oficina ?? 2)
            ],
            'curp'       => ['nullable','string','size:18','regex:/^[A-Z]{4}\d{6}[HM][A-Z]{5}[0-9A-Z]\d$/'],
            'nombres'    => ['required','string','max:100','regex:/^[\p{L}\p{M} ]+$/u'],
            'primer_apellido'  => ['required','string','max:100','regex:/^[\p{L}\p{M} ]+$/u'],
            'segundo_apellido' => ['nullable','string','max:100','regex:/^[\p{L}\p{M} ]+$/u'],
            'fecha_nacimiento' => ['required','date','before:today'],
            'entidad_federativa_nacimiento' => ['nullable','string','max:100','regex:/^[\p{L}\p{M}\d\s\.\,\#\-\°\/]+$/u'],
            'foto' => $this->base64ImageRule(3), // MB
            'probatorio_nacionalidad' => $this->base64PdfRule(100),
            'probatorio_identidad' => $this->base64PdfRule(100),
        ];
    }

    public function messages(): array
    {
        return [
            'id_oficina.required' => 'La oficina es obligatoria.',
            'id_oficina.integer'  => 'El identificador de oficina debe ser numérico.',
            'id_oficina.exists'   => 'La oficina seleccionada no es válida o no está activa.',
            'curp.size'           => 'La CURP debe tener 18 caracteres.',
            'curp.regex'          => 'La CURP no tiene un formato válido.',
            'nombres.required'    => 'El nombre es obligatorio.',
            'nombres.regex'       => 'El nombre solo puede contener letras y espacios.',
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.regex'    => 'El primer apellido solo puede contener letras y espacios.',
            'segundo_apellido.regex'   => 'El segundo apellido solo puede contener letras y espacios.',
            'fecha_nacimiento.required'=> 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before'  => 'La fecha de nacimiento debe ser anterior a hoy.',
            'foto.*' => 'La imagen no cumple con el formato base64 válido o excede el tamaño permitido.',
            'probatorio_nacionalidad.*' => 'El pdf no cumple con el formato base64 válido o excede el tamaño permitido.',
            'probatorio_identidad.*' => 'El pdf no cumple con el formato base64 válido o excede el tamaño permitido.'
        ];
    }
}
