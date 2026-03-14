<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ImCatAnexos;

class ImCatAnexosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            [ "nombre" => "Expediente integrado por la delegación"],
            [ "nombre" => "Dictamen de verificación por autoridad(es) competente(s)"],
            [ "nombre" => "Pasaporte cancelado"],
            [ "nombre" => "Otro, especificar"],
            [ "nombre" => "En su caso, documentación complementaria presentada" ],
            [ "nombre" => "En su caso, resolución judicial"],
            [ "nombre" => "En su caso, denuncia penal ratificada (por suplantación de identidad)" ],
            [ "nombre" => "CURP Actualizada" ],
            [ "nombre" => "Probatorio de Identidad" ],
            [ "nombre" => "Probatorio de nacionalidad" ],
            [ "nombre" => "Pasaporte anterior" ],
            [ "nombre" => "Foto" ],
            [ "nombre" => "Formato de solicitud"]
        ];

        foreach ($array as  $item) {
            if( ImCatAnexos::where("nombre",$item['nombre'])->exists() == false ){
                ImCatAnexos::create($item);
            }
        }
    }
}
