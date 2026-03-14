<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ImCatAnexos;

class updateAnexosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $ImCatAnexos = ImCatAnexos::find(2);
            $ImCatAnexos->nombre = "Dictamen de verificación por autoridad(es) competente(s)";
            $ImCatAnexos->save();
    }
}
