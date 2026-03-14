<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class ImImpedimentoDocumento extends Model
{
    protected $table = 'im_impedimento_documento';         // Nombre de la tabla
    protected $primaryKey = 'id_impedimento_documento';
    protected $fillable = [
        'id_impedimento',
        'id_cat_anexos',
        'identificador_documento',
        'fecha_documento',
        'url_documento',
        'bol_eliminado',
        'observaciones',
        'id_usuario_alta',
        'id_usuario_modificacion',
    ];

    protected $appends = ['aux'];

    protected function casts(): array
    {
        return [
            'identificador_documento' => CleanText::class,
            'url_documento' => CleanText::class,
        ];
    }


    public function getAuxAttribute()
    {
        return false;
    }

    function cat_anexo(){
        return $this->hasOne(ImCatAnexos::class,'id_cat_anexos','id_cat_anexos');
    }
}
