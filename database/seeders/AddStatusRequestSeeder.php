<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ImCatStatusSolicitud;

class AddStatusRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [ "id_estatus_solicitud" => 300, "estatus_solicitud" => "Por rechazar" ]
        ];

        foreach ($data as $item) {
            if( ImCatStatusSolicitud::where("estatus_solicitud",$item['estatus_solicitud'])->where('id_estatus_solicitud',$item['id_estatus_solicitud'])->exists() == false ){
                ImCatStatusSolicitud::create($item);
            }
        }


    }
}
