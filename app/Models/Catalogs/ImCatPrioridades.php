<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;

class ImCatPrioridades extends Model
{
    protected $table = 'im_cat_prioridades';
    protected $primaryKey = 'id_prioridad';
    protected $fillable = [
        'prioridad'
    ];
}
