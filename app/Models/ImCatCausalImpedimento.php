<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class ImCatCausalImpedimento extends Model
{
    protected $table = 'im_cat_causal_impedimento'; 

    protected function casts(): array
    {
        return [
            'causal_impedimento' => CleanText::class,
        ];
    }
}
