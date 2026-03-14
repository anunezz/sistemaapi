<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CatTiposTransaccion;


class CatTransactionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = [
            [ "name" => "Ingreso" ],
            [ "name" => "Actualizacion" ],
            [ "name" => "Creación" ],
            [ "name" => "Eliminación" ],
            [ "name" => "Consulta" ],
            [ "name" => "Habilitar" ],
            [ "name" => "Deshabilitar"],
            [ "name" => "Finalizado"],
            [ "name" => "Enviado"],
            [ "name" => "Salida"],
            [ "name" => "Descargar documento"],
            [ "name" => "Asignación"],
            [ "name" => "Desasignar"]
        ];

        foreach ( $transactions as $item) {
            if( CatTiposTransaccion::where("name",$item["name"])->exists() == false ){
                CatTiposTransaccion::create( $item );
            }
        }

    }
}
