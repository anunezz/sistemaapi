<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImAsignacionSolicitudes extends Model
{
    protected $table = 'im_asignacion_solicitudes';         // Nombre de la tabla
    protected $primaryKey = 'id_asignacion_solicitudes';
    protected $fillable = [
        'id_solicitud',
        'id_usuario'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
