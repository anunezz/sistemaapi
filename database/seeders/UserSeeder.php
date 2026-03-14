<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                //id = 1
                'username'                  =>'pdominguez',
                'name'                      => 'Pedro',
                'first_name'                => 'Domínguez',
                'second_name'               => 'Díaz',
                'email'                     => 'pdominguez@sre.gob.mx',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 264,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ],
            [
                //id = 2
                'username'                  => 'adriann',
                'name'                      => 'Adrián',
                'first_name'                => 'Núñez',
                'second_name'               => 'Alanís',
                'email'                     => 'adriann@sre.gob.mx',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 264,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ],
            [
                //id = 3
                'username'                  => 'icastillo',
                'name'                      => 'Irving',
                'first_name'                => 'Castillo',
                'second_name'               => 'Pérez',
                'email'                     => 'icastillo@sre.gob.mx',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 264,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ],
            [
                //id = 4
                'username'                  => 'jmendozap',
                'name'                      => 'José Fabián',
                'first_name'                => 'Mendoza',
                'second_name'               => 'Pérez',
                'email'                     => 'jmendozap@sre.gob.mx',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 264,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ],
            [
                //id = 5
                'username'                  => 'jbarbosac',
                'name'                      => 'José Luis',
                'first_name'                => 'Barbosa',
                'second_name'               => 'Cepeda',
                'email'                     => 'jbarbosac@sre.gob.mx',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 264,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ],
            [
                //id = 6
                'username'                  => 'prosales',
                'name'                      => 'Pedro Arturo',
                'first_name'                => 'Rosales',
                'second_name'               => 'Simonín',
                'email'                     => 'prosales@sre.gob.mx',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 264,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ],
            [
                //id = 7
                'username'                  => 'mcordero',
                'name'                      => 'Marco Antonio',
                'first_name'                => 'Cordero',
                'second_name'               => 'Miranda',
                'email'                     => 'mcordero@sre.gob.mx',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 264,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ],
            [
                //id = 8
                'username'                  => 'mbautistag',
                'name'                      => 'Maximiliano',
                'first_name'                => 'Bautista',
                'second_name'               => 'Gutiérrez',
                'email'                     => 'mbautistag@sre.gob.mx',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 264,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ],
            [
                //id = 9
                'username'                  => 'asotelo',
                'name'                      => 'Ángel Daniel',
                'first_name'                => 'Sotelo',
                'second_name'               => 'Sánchez',
                'email'                     => 'asotelo@sre.gob.mx',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 264,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),]
            /*[
                //id = 10
                'username'                  => '',
                'name'                      => '',
                'first_name'                => '',
                'second_name'               => '',
                'email'                     => '',
                'usuario_directorio_activo' => true,
                'id_oficina'                => 4,
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now(),
            ], */
        ];

        foreach ($users as $us) {
            $user = User::create($us);
        }
    }
}
