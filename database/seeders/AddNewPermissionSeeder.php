<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddNewPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $exists = DB::table('permission_perfil')
            ->where('id_perfil', 3)
            ->where('id_permission', 19)
            ->exists();

        if (!$exists) {
            DB::table('permission_perfil')->insert([
                'id_perfil'     => 3,
                'id_permission' => 19,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ]);
        }
    }
}
