<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ConsultaVerificacionRequest extends FormRequest
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
            'id_solicitud' => [
                'required',
                'integer',
                Rule::exists('im_solicitud', 'id_solicitud')
                    ->where(function ($query) {
                        $query->where('bol_eliminado', false)
                            ->where('id_tipo_solicitud', 3)
                            ->where('id_solicitud',$this->id_solicitud);
                    }),
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_solicitud.required' => 'El id_solicitud es obligatoria.',
            'id_solicitud.integer'  => 'El id_solicitud debe ser numérico.',
            'id_solicitud.exists'   => 'El id_solicitud seleccionada no es válida o no está activa.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors'  => $validator->errors(), // { campo: [mensajes...] }
            ], 422)
        );
    }
}
