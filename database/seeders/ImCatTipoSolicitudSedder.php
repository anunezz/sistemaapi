<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ImCatTipoSolicitud;

class ImCatTipoSolicitudSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = collect([
            [ 'tipo_solicitud' => "Altas" ],
            [ 'tipo_solicitud' => "Bajas" ],
            [ 'tipo_solicitud' => "Verificacion" ],
            [ 'tipo_solicitud' => "Alta de modificacion" ],
        ]);

        foreach ($array as  $item) {
            if( ImCatTipoSolicitud::where('tipo_solicitud',$item['tipo_solicitud'])->exists() == false ){
                ImCatTipoSolicitud::create($item);
            }
        }
    }
}
