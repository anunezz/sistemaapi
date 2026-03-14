<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioPerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('im_usuario_perfil')->insert([
			[
                'id_usuario'                => 1,
                'id_perfil'                 => 4,
                'bol_eliminado'             => false,
                'usuario_alta'              => 1,
                'usuario_modificacion'      => '',
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
			],
            [
                'id_usuario'                => 2,
                'id_perfil'                 => 4,
                'bol_eliminado'             => false,
                'usuario_alta'              => 1,
                'usuario_modificacion'      => '',
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
			],
            [
                'id_usuario'                => 3,
                'id_perfil'                 => 4,
                'bol_eliminado'             => false,
                'usuario_alta'              => 1,
                'usuario_modificacion'      => '',
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
			],
            [
                'id_usuario'                => 4,
                'id_perfil'                 => 4,
                'bol_eliminado'             => false,
                'usuario_alta'              => 1,
                'usuario_modificacion'      => '',
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
			],
            [
                'id_usuario'                => 5,
                'id_perfil'                 => 4,
                'bol_eliminado'             => false,
                'usuario_alta'              => 1,
                'usuario_modificacion'      => '',
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
			],
            [
                'id_usuario'                => 6,
                'id_perfil'                 => 4,
                'bol_eliminado'             => false,
                'usuario_alta'              => 1,
                'usuario_modificacion'      => '',
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
			],
            [
                'id_usuario'                => 7,
                'id_perfil'                 => 4,
                'bol_eliminado'             => false,
                'usuario_alta'              => 1,
                'usuario_modificacion'      => '',
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
			],
            [
                'id_usuario'                => 8,
                'id_perfil'                 => 4,
                'bol_eliminado'             => false,
                'usuario_alta'              => 1,
                'usuario_modificacion'      => '',
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
			],
            [
                'id_usuario'                => 9,
                'id_perfil'                 => 4,
                'bol_eliminado'             => false,
                'usuario_alta'              => 1,
                'usuario_modificacion'      => '',
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
			],
        ]);
    }
}
