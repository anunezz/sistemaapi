<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CatModulo;


class CatModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            ["name" => "Inicio" ], //1
            ["name" => "Solicitudes"], //2
            ["name" => "Autorización de solicitudes"], //3
            ["name" => "Validación de altas"], //4
            ["name" => "Validación de bajas"], //5
            ["name" => "Verificaciones"], //6
            ["name" =>"Autorización de alta de impedimentos" ], //7
            ["name" =>"Autorización de baja de impedimentos" ], //8
            ["name" => "Respuesta de impedimentos"], //9
            ["name" => "Administración catálogos"], //10
            ["name" => "Generación de reportes"], //11
            ["name" => "Administración de usuarios"], //12   
            ["name" => "Consulta de impedimentos"], //13
            ["name" => "Consulta de bitácora"], //14
            ["name" => "Reporte estadistico de impedimentos"], //15
            ["name" => "Bitácora solicitudes"], //16
            ["name" => "Bitácora impedimentos"], //17
            ["name" => "Inicio de sesión"], //18
            ["name" => "Termino de sesión"], //19
            ["name" => "Autorización de rechazos"], //20
            ["name" => "Validación de alta de modificación"], //21
            ["name" => "Validación modificación"], //22
            ["name" => "Asignación de trabajo"], //23

        ];

        foreach ( $modules as $item) {
            CatModulo::create( $item );
        }

    }
}
