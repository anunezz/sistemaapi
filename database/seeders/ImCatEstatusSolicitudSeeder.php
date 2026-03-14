<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImCatEstatusSolicitudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/sql/im_cat_estatus_solicitud.sql');

        if (File::exists($path)) {
            $sql = File::get($path);
            DB::unprepared($sql);
            $this->command->info('SQL ejecutado desde ' . $path);

            // TODO Obtener el valor máximo de id_estatus_solicitud
            $maxId = DB::table('im_cat_estatus_solicitud')->max('id_estatus_solicitud');
            // TODO Sincronizar la secuencia con el valor máximo + 1
            DB::statement("SELECT setval('im_cat_estatus_solicitud_id_estatus_solicitud_seq', ?, false);", [$maxId + 1]);
        } else {
            $this->command->error('Archivo SQL no encontrado en: ' . $path);
        }
    }
}
