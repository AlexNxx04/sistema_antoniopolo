<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarUsuarioRequest extends FormRequest
{
    private array $provincias = [
        '01' => 'Azuay',
        '02' => 'Bolívar',
        '03' => 'Cañar',
        '04' => 'Carchi',
        '05' => 'Chimborazo',
        '06' => 'Cotopaxi',
        '07' => 'El Oro',
        '08' => 'Esmeraldas',
        '09' => 'Guayas',
        '10' => 'Imbabura',
        '11' => 'Loja',
        '12' => 'Los Ríos',
        '13' => 'Manabí',
        '14' => 'Morona Santiago',
        '15' => 'Napo',
        '16' => 'Pastaza',
        '17' => 'Pichincha',
        '18' => 'Tungurahua',
        '19' => 'Zamora Chinchipe',
        '20' => 'Galápagos',
        '21' => 'Sucumbíos',
        '22' => 'Orellana',
        '23' => 'Santo Domingo',
        '24' => 'Santa Elena',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'cedula' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                function ($attribute, $value, $fail) {
                    $codigoProvincia = substr($value, 0, 2);
                    $provinciasValidas = array_keys($this->provincias);
                    if (!in_array($codigoProvincia, $provinciasValidas)) {
                        $fail('El código de provincia (' . $codigoProvincia . ') no es válido. Debe ser uno de: 01-24');
                    }
                },
                'unique:usuarios,cedula,' . ($this->route('id') ?? 'null') . ',usuario_id',
            ],
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => [
                'nullable',
                'string',
                'regex:/^09[0-9]{8}$/',
            ],
            'correo' => 'nullable|email|max:150',
            'rol_id' => 'required|exists:roles,rol_id',
            'punto_id' => 'nullable|exists:puntos,punto_id',
            'unidad_id' => 'nullable|exists:unidades,unidad_id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['activo'] = 'required|boolean';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.size' => 'La cédula debe tener exactamente 10 dígitos.',
            'cedula.regex' => 'La cédula debe contener solo dígitos numéricos (0-9).',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            
            'telefono.regex' => 'El teléfono debe ser celular de Ecuador (09XXXXXXXX, 10 dígitos).',
            
            'nombres.required' => 'Los nombres son obligatorios.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'rol_id.required' => 'El rol es obligatorio.',
        ];
    }
}
