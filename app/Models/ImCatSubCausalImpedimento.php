<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class ImCatSubCausalImpedimento extends Model
{
    protected $table = 'im_cat_subcausal_impedimento';
    //im_cat_subcausal_impedimento

    protected function casts(): array
    {
        return [
            'subcausal_impedimento' => CleanText::class,
        ];
    }
}
