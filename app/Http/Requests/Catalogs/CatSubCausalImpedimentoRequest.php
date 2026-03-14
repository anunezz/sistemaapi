<?php

namespace App\Http\Requests\Catalogs;

use Illuminate\Foundation\Http\FormRequest;

class CatSubCausalImpedimentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subcausal_impedimento' => 'required|string|max:120',
            'id_causal_impedimento' => 'required|integer',
        ];
    }

    /**
     * Mensajes personalizados
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subcausal_impedimento.required' => 'Este campo es obligatorio.',
            'subcausal_impedimento.string'   => 'El valor debe ser texto.',
            'subcausal_impedimento.max'      => 'No debe exceder los 120 caracteres.',
            'id_causal_impedimento.required' => 'El causal es obligatorio.',
            'id_causal_impedimento.integer'  => 'El valor debe ser numérico.',
        ];
    }
}
