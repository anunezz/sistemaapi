<?php

namespace App\Http\Requests\Catalogs;

use Illuminate\Foundation\Http\FormRequest;

class CatCausalImpedimentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para causal
     */
    public function rules(): array
    {
        return [
            'causal_impedimento' => 'required|string|max:300',
        ];
    }

    /**
     * Mensajes personalizados
     */
    public function messages(): array
    {
        return [
            'causal_impedimento.required' => 'Este campo es obligatorio.',
            'causal_impedimento.string'   => 'El valor debe ser texto.',
            'causal_impedimento.max'      => 'No debe exceder los 300 caracteres.',
        ];
    }
}
