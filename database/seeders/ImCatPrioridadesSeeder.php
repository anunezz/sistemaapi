<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Catalogs\ImCatPrioridades;

class ImCatPrioridadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            "Alta",
            "Media",
            "Baja"
        ];

        foreach ($data as $item) {
            ImCatPrioridades::create([
                "prioridad" => $item
            ]);
        }
    }
}
