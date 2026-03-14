<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class ImCatTipoSolicitud extends Model
{
    protected $table = 'im_cat_tipo_solicitud'; 
    protected $primaryKey = 'id_tipo_solicitud';
    protected $fillable = [
        'tipo_solicitud',
        'bol_eliminado',
        'id_usuario_alta',
        'id_usuario_modificacion'
    ];

    protected function casts(): array
    {
        return [
            'tipo_solicitud' => CleanText::class,
        ];
    }
}
