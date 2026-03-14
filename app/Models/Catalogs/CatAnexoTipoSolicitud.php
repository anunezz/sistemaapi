<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;

class CatAnexoTipoSolicitud extends Model
{
    protected $table = 'cat_anexo_tipo_solicitud';
    protected $fillable = [
        'id_cat_anexos',
        'id_tipo_solicitud'
    ];
}
