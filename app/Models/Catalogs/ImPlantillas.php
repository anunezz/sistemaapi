<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogs\ImCatSubCausalImpedimento;

class ImPlantillas extends Model
{
    protected $table = 'im_plantillas';
    protected $primaryKey = 'id_plantilla';
    protected $fillable = [
        'plantilla',
        'id_subcausal_impedimento',
        'bol_eliminado'
    ];

    function cat_subcausal(){
        return $this->hasOne(ImCatSubCausalImpedimento::class,'id_subcausal_impedimento','id_subcausal_impedimento');
    }

}
