<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class ImPersonaPadre extends Model
{
    protected $table = 'im_persona_padres';
    protected $primaryKey = 'id_persona';
    protected $fillable = [
        'id_persona',
        'nombres_padre',
        'primer_apellido_padre',
        'segundo_apellido_padre',
        'nombres_madre',
        'primer_apellido_madre',
        'segundo_apellido_madre',
        'bol_eliminado',
        // 'fec_alta',
        // 'fec_modificacion',
        'id_usuario_alta',
        'id_usuario_modificacion'
    ];
    protected function casts(): array
    {
        return [
            'nombres_padre' => CleanText::class,
            'primer_apellido_padre' => CleanText::class,
            'segundo_apellido_padre' => CleanText::class,
            'nombres_madre' => CleanText::class,
            'primer_apellido_madre' => CleanText::class,
            'segundo_apellido_madre' => CleanText::class,
        ];
    }

}
