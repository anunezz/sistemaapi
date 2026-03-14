<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Catalogs\ImCatEstatusVerificacion;

class ImCatEstatusVerificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = collect([
            [ "estatus" => "Pendiente", "bol_estatus" => 0 ],
            [ "estatus" => "No autorizado", "bol_estatus" => 1 ],
            [ "estatus" => "Autorizado", "bol_estatus" => 2  ]
        ]);

        foreach ($array as  $item) {
            if( ImCatEstatusVerificacion::where('estatus',$item['estatus'])->where('bol_estatus',$item['bol_estatus'])->exists() == false ){
                ImCatEstatusVerificacion::create($item);
            }
        }
    }
}
