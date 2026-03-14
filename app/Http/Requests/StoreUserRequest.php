<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name'                      => ['required','regex:(^([a-zA-Z0-9ÁÉÍÓÚáéíóúñÑÄËÏÖÜäëïöü´\-_.,\s]+)+$)','max:80'],
            'first_name'                => ['required','regex:(^([a-zA-Z0-9ÁÉÍÓÚáéíóúñÑÄËÏÖÜäëïöü´\-_.,\s]+)+$)','max:80'],
            'second_name'               => ['nullable','regex:(^([a-zA-Z0-9ÁÉÍÓÚáéíóúñÑÄËÏÖÜäëïöü´\-_.,\s]+)+$)','max:80'],
            'email'                     => ['required','string','email','max:255',Rule::unique('users', 'email')],
            'usuario_directorio_activo' => ['nullable','boolean'],
            'id_oficina'                => ['required', 'numeric', Rule::exists('im_cat_oficina', 'id_oficina')],
            'id_perfil'                 => ['required','numeric',Rule::exists('im_cat_perfil','id_perfil')],
            'puesto'                    => ['required', 'string'],
            'permissions'               => ['array'],
            'permissions.*'             => ['exists:permissions,id']
        ];
    }

    public function messages(): array
    {
        return [
            'required'          => 'El campo :attribute es requerido',
            'email'             => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
			'unique'            => 'El campo :attribute ya se encuentra registrado',
			'numeric'           => 'El campo :attribute debe ser un número.',
			'max'               => [
				'numeric'       => 'El campo :attribute no debe ser mayor a :max.',
				'file'          => 'El archivo :attribute no debe pesar más de :max kilobytes.',
				'string'        => 'El campo :attribute no debe contener más de :max caracteres.',
				'array'         => 'El campo :attribute no debe contener más de :max elementos.',
			                    ],
			'regex'             => 'El formato del campo :attribute es inválido.',
            'string'            => 'El campo :attribute debe ser una cadena de caracteres.',
            'boolean'           => 'El campo :attribute debe ser verdadero o falso.',
            'array'             => 'El campo :attribute debe tener al menos un elemento',
            'date'              => 'El campo :attribute debe ser una fecha',
            'after_or_equal'    => 'El campo :attribute debe ser una fecha después o igual a :date',
            'numeric'           => 'El campo :attribute debe ser números',
            'file'              => 'El campo :attribute debe ser un archivo.',
            'min'               => [
                'numeric'       => 'El campo :attribute debe tener al menos :min.',
                'file'          => 'El campo :attribute debe tener al menos :min kilobytes.',
                'string'        => 'El campo :attribute debe tener al menos :min caracteres.',
                'array'         => 'El campo :attribute debe tener al menos :min elementos.',
                                ],
            'between'           => [
                'numeric'       => 'El campo :attribute debe estar entre :min - :max.',
                'file'          => 'El campo :attribute debe estar entre :min - :max kilobytes.',
                'string'        => 'El campo :attribute debe estar entre :min - :max caracteres.',
                'array'         => 'El campo :attribute debe tener entre :min y :max elementos.',
            ],
            'exists'            => 'El campo :attribute seleccionado no es válido.',
        ];
    }

    public function attributes()
    {
        return [
            'name'                      => 'Nombre',
            'first_name'                => 'Apellido Paterno',
            'second_name'               => 'Apellido Materno',
            'email'                     => 'Correo Electrónico',
            'usuario_directorio_activo' => 'Es Cancillería',
            'id_oficina'                => 'Oficina',
            'id_perfil'                 => 'Perfil',
            'permissions'               => 'Permisos'
        ];
    }
}
