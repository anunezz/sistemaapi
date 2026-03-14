<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class ActivarOficinasDesdeTxtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datos = [
            ['id_cat_anexos' => 12, 'id_tipo_solicitud' => 1],
            ['id_cat_anexos' => 12, 'id_tipo_solicitud' => 2],
            ['id_cat_anexos' => 12, 'id_tipo_solicitud' => 3],
            ['id_cat_anexos' => 12, 'id_tipo_solicitud' => 4]
        ];

        foreach ($datos as $item) {
            $existe = DB::table('cat_anexo_tipo_solicitud')
            ->where('id_cat_anexos', $item['id_cat_anexos'])
            ->where('id_tipo_solicitud', $item['id_tipo_solicitud'])
            ->exists();

            if (!$existe) {
                DB::table('cat_anexo_tipo_solicitud')->insert([$item]);
            }
        }
    }
}
