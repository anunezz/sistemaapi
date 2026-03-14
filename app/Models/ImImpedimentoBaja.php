<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Model;

class ImImpedimentoBaja extends Model
{
    protected $table = 'im_impedimento_baja';
    protected $primaryKey = 'id_secuencial_baja';
    protected $fillable = [
        'id_impedimento',
        'fecha_elaboracion',
        'id_oficina',
        'id_estatus_impedimento_baja',
        'fechado',
        'contenido',
        'motivo_levantamiento',
        'descrpción_levantamiento',
        'anexo_expediente_integrado',
        'anexo_dictamen_verificacion',
        'documentacion_complementaria',
        'resolucion_juficial',
        'denuncia_penal_ratificada',
        'otro_documento_baja',
        'bol_eliminado',
        'id_usuario_alta',
        'id_usuario_modificacion'
    ];

    protected function casts(): array
    {
        return [
            'motivo_levantamiento' => CleanText::class,
            'anexo_expediente_integrado' => CleanText::class,
            'anexo_dictamen_verificacion' => CleanText::class,
            'documentacion_complementaria' => CleanText::class,
            'resolucion_juficial' => CleanText::class,
            'denuncia_penal_ratificada' => CleanText::class,
            'otro_documento_baja' => CleanText::class,
        ];
    }


    function cat_status()
    {
        return $this->belongsTo(ImCatStatusSolicitud::class, 'id_estatus_impedimento_baja', 'id_estatus_solicitud');
    }

    public function cat_user_alta()
    {
        return $this->hasOne(User::class,'id','id_usuario_alta');
    }

}
