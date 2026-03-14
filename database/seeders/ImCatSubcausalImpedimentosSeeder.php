<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Catalogs\ImCatSubCausalImpedimento;

class ImCatSubcausalImpedimentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $data = [
            ['id_causal_impedimento' => 1 ,'subcausal_impedimento' => 'EJEMPLO 1'],
            ['id_causal_impedimento' => 1 ,'subcausal_impedimento' => 'EJEMPLO 2'],
            ['id_causal_impedimento' => 1 ,'subcausal_impedimento' => 'EJEMPLO 3'],
            ['id_causal_impedimento' => 2 ,'subcausal_impedimento' => 'EJEMPLO 4'],
            ['id_causal_impedimento' => 2 ,'subcausal_impedimento' => 'EJEMPLO 5'],
            ['id_causal_impedimento' => 2 ,'subcausal_impedimento' => 'EJEMPLO 6'],
            ['id_causal_impedimento' => 3 ,'subcausal_impedimento' => 'EJEMPLO 7'],
            ['id_causal_impedimento' => 3 ,'subcausal_impedimento' => 'EJEMPLO 7'],
        ];

        foreach ($data as $item) {
            ImCatSubCausalImpedimento::create($item);
        }
    }
}
