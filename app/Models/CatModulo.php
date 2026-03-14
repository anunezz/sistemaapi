<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class CatModulo extends Model
{

    protected $fillable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'name' => CleanText::class,
        ];
    }

    protected $table = 'im_cat_modulos';
    function transaccion(){
        return $this->belongsTo(Transaccion::class);
    }

}
