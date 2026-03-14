<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Catalogs\CatAnexoTipoSolicitud;

class CatAnexoTipoSolicitudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = collect([
            ['id_cat_anexos' => 1, 'id_tipo_solicitud' => 1],
            ['id_cat_anexos' => 2, 'id_tipo_solicitud' => 1],
            ['id_cat_anexos' => 3, 'id_tipo_solicitud' => 1],
            ['id_cat_anexos' => 4, 'id_tipo_solicitud' => 1],
            ['id_cat_anexos' => 9, 'id_tipo_solicitud' => 1],
            ['id_cat_anexos' => 12, 'id_tipo_solicitud' => 1],
            ['id_cat_anexos' => 13, 'id_tipo_solicitud' => 1],

            ['id_cat_anexos' => 1, 'id_tipo_solicitud' => 2],
            ['id_cat_anexos' => 2, 'id_tipo_solicitud' => 2],
            ['id_cat_anexos' => 5, 'id_tipo_solicitud' => 2],
            ['id_cat_anexos' => 6, 'id_tipo_solicitud' => 2],
            ['id_cat_anexos' => 7, 'id_tipo_solicitud' => 2],
            ['id_cat_anexos' => 4, 'id_tipo_solicitud' => 2],
            ['id_cat_anexos' => 12, 'id_tipo_solicitud' => 2],
            ['id_cat_anexos' => 13, 'id_tipo_solicitud' => 2],

            ['id_cat_anexos' => 1, 'id_tipo_solicitud' => 3],
            ['id_cat_anexos' => 4, 'id_tipo_solicitud' => 3],
            ['id_cat_anexos' => 12, 'id_tipo_solicitud' => 3],
            ['id_cat_anexos' => 13, 'id_tipo_solicitud' => 3],

            ['id_cat_anexos' => 8, 'id_tipo_solicitud' => 4],
            ['id_cat_anexos' => 9, 'id_tipo_solicitud' => 4],
            ['id_cat_anexos' => 10, 'id_tipo_solicitud' => 4],
            ['id_cat_anexos' => 11, 'id_tipo_solicitud' => 4],
            ['id_cat_anexos' => 12, 'id_tipo_solicitud' => 4],
            ['id_cat_anexos' => 13, 'id_tipo_solicitud' => 4]
        ]);

        foreach ($data as $item) {
            if( CatAnexoTipoSolicitud::where('id_cat_anexos',$item['id_cat_anexos'])->where('id_tipo_solicitud',$item['id_tipo_solicitud'])->exists() == false ){
                CatAnexoTipoSolicitud::create($item);
            }
        }
    }
}
