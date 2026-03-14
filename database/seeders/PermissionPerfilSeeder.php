<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionPerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permission_perfil')->insert([
                [
                    'id_perfil'     => '1',
                    'id_permission' => '1',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '2',
                    'id_permission' => '1',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '2',
                    'id_permission' => '2',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '2',
                    'id_permission' => '3',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '3',
                    'id_permission' => '4',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '3',
                    'id_permission' => '5',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                // [
                //     'id_perfil'     => '3',
                //     'id_permission' => '6',
                //     'created_at'    => Carbon::now(),
                //     'updated_at'    => Carbon::now(),
                // ],
                [
                    'id_perfil'     => '3',
                    'id_permission' => '7',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '3',
                    'id_permission' => '8',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '3',
                    'id_permission' => '17',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '3',
                    'id_permission' => '19',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                // [
                //     'id_perfil'     => '3',
                //     'id_permission' => '18',
                //     'created_at'    => Carbon::now(),
                //     'updated_at'    => Carbon::now(),
                // ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '1',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '2',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '3',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '4',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '5',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '7',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '8',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '9',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
/*                 [
                    'id_perfil'     => '4',
                    'id_permission' => '9',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ], */
                [
                    'id_perfil'     => '4',
                    'id_permission' => '10',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '11',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '12',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '13',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '14',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                 [
                    'id_perfil'     => '4',
                    'id_permission' => '15',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                 [
                    'id_perfil'     => '4',
                    'id_permission' => '16',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                 [
                    'id_perfil'     => '4',
                    'id_permission' => '17',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],

                // [
                //     'id_perfil'     => '4',
                //     'id_permission' => '18',
                //     'created_at'    => Carbon::now(),
                //     'updated_at'    => Carbon::now(),
                // ],
                [
                    'id_perfil'     => '5',
                    'id_permission' => '9',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                [
                    'id_perfil'     => '5',
                    'id_permission' => '11',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                // [
                //     'id_perfil'     => '5',
                //     'id_permission' => '18',
                //     'created_at'    => Carbon::now(),
                //     'updated_at'    => Carbon::now(),
                // ],
                [
                    'id_perfil'     => '4',
                    'id_permission' => '24',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ],
                // [
                //     'id_perfil'     => '5',
                //     'id_permission' => '20',
                //     'created_at'    => Carbon::now(),
                //     'updated_at'    => Carbon::now(),
                // ],
                /*
                [
                    'id_perfil'     => '',
                    'id_permission' => '',
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ], */
            ]);
    }
}
