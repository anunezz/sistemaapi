<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class CatTiposTransaccion extends Model
{

    protected $fillable = [
        'name',
        'isActive'
    ];
    
    protected $table = 'im_cat_tipos_transacciones';
    
    function transaccion(){
        return $this->belongsTo(Transaccion::class);
    }

    protected function casts(): array
    {
        return [
            'name' => CleanText::class,
        ];
    }

}
