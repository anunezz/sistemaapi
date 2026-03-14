<?php

namespace App\Models;

use App\Models\Catalogs\ImCatSubCausalImpedimento;
use Illuminate\Database\Eloquent\Model;

class ImSolicitudCausal extends Model
{
    protected $table = 'im_solicitud_causales';
    protected $fillable = ['solicitud_id','id_causal_impedimento','id_subcausal_impedimento'];

    public function solicitud()
    {
        return $this->belongsTo(ImSolicitud::class, 'solicitud_id');
    }
    public function causal()
    {
        return $this->belongsTo(ImCatCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }
    public function subcausal()
    {
        return $this->belongsTo(ImCatSubCausalImpedimento::class, 'id_subcausal_impedimento', 'id_subcausal_impedimento');
    }
}
