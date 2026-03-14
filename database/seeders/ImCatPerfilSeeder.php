<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImCatPerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('im_cat_perfil')->insert([
                [
                    'perfil'            => 'Operador',
                    'bol_eliminado'     => false,
                    //'id_usuario_alta'   =>
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ],
                [
                    'perfil'            => 'Titular',
                    'bol_eliminado'     => false,
                    //'id_usuario_alta'   =>
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ],
                [
                    'perfil'            => 'Dictaminador',
                    'bol_eliminado'     => false,
                    //'id_usuario_alta'   =>
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ],
                [
                    'perfil'            => 'Autorizador',
                    'bol_eliminado'     => false,
                    //'id_usuario_alta'   =>
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ],
                [
                    'perfil'            => 'Dirección de Normatividad',
                    'bol_eliminado'     => false,
                    //'id_usuario_alta'   =>
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ]
            ]);
        //$path = database_path('seeders/sql/im_cat_perfil.sql');

        //if (File::exists($path)) {
            //$sql = File::get($path);
            //DB::unprepared($sql);
            //$this->command->info('SQL ejecutado desde ' . $path);

            // TODO Obtener el valor máximo de id_perfil
            //$maxId = DB::table('im_cat_perfil')->max('id_perfil');
            // TODO Sincronizar la secuencia con el valor máximo + 1
            //DB::statement("SELECT setval('im_cat_perfil_id_perfil_seq', ?, false);", [$maxId + 1]);

       /*  } else {
            $this->command->error('Archivo SQL no encontrado en: ' . $path);
        } */
    }
}
