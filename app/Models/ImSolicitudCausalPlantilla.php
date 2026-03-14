<?php

namespace App\Models;

use App\Models\Catalogs\ImPlantillas;
use Illuminate\Database\Eloquent\Model;

class ImSolicitudCausalPlantilla extends Model
{
    protected $table = 'im_solicitud_causal_plantillas';
    protected $fillable = ['solicitud_causal_id','plantilla_id'];

    public function solicitudCausal()
    {
        return $this->belongsTo(ImSolicitudCausal::class, 'solicitud_causal_id');
    }
    public function plantilla()
    {
        return $this->belongsTo(ImPlantillas::class, 'id_plantilla');
    }
}
