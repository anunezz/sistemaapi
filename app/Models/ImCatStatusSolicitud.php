<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class ImCatStatusSolicitud extends Model
{
    protected $table = 'im_cat_estatus_solicitud'; 
    protected $primaryKey = 'id_estatus_solicitud'; // <--- IMPORTANTE

    protected function casts(): array
    {
        return [
            'estatus_solicitud' => CleanText::class,
        ];
    }
}
