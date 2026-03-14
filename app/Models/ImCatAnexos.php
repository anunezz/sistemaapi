<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class ImCatAnexos extends Model
{
    protected $table = 'im_cat_anexos';         // Nombre de la tabla
    protected $primaryKey = 'id_cat_anexos';
    protected $fillable = [
        'nombre'
    ];

    protected function casts(): array
    {
        return [
            'nombre' => CleanText::class,
        ];
    }

    protected $appends = ['aux','observaciones'];

    public function getAuxAttribute()
    {
        return false;
    }

    public function getObservacionesAttribute()
    {
        return null;
    }

    public function types_of_requests(){
        return $this->belongsToMany(
            ImCatTipoSolicitud::class,
            'cat_anexo_tipo_solicitud',
            'id_cat_anexos',
            'id_tipo_solicitud'
        )->distinct();
    }
}
