<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;

class ImCatEstatusVerificacion extends Model
{
    protected $table = 'im_cat_estatus_verificacion';
    protected $primaryKey = 'id_estatus_verificacion';
    // protected $appends = ['hash_id'];
    protected $fillable = [
        'estatus',
        'bol_estatus',
        'bol_eliminado'
    ];
}
