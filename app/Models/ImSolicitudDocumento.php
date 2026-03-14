<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class ImSolicitudDocumento extends Model
{
    protected $table = 'im_solicitud_documento';         // Nombre de la tabla
    protected $primaryKey = 'id_solicitud_documento';
    protected $fillable = [
        'id_solicitud',
        'id_cat_anexos',
        'identificador_documento',
        'fecha_documento',
        'url_documento',
        'bol_eliminado',
        'observaciones',
        'id_usuario_alta',
        'id_usuario_modificacion',
    ];

    protected function casts(): array
    {
        return [
            'identificador_documento' => CleanText::class,
            'url_documento' => CleanText::class,

        ];
    }




    protected $appends = ['aux'];

    public function getAuxAttribute()
    {
        return false;
    }

    function cat_anexo(){
        return $this->hasOne(ImCatAnexos::class,'id_cat_anexos','id_cat_anexos');
    }

}
