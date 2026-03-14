<?php

namespace App\Models\Catalogs;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Catalogs\ImPlantillas;

class ImCatSubCausalImpedimento extends Model
{
    protected $table = 'im_cat_subcausal_impedimento';
    protected $primaryKey = 'id_subcausal_impedimento';
    protected $appends = ['hash_id'];
    protected $fillable = [
        'id_causal_impedimento',
        'subcausal_impedimento',
        'bol_eliminado',
        'usuario_alta',
        'usuario_modificacion',
    ];

    public function getHashIdAttribute(): string
    {
        return encrypt($this->id_subcausal_impedimento);
    }

    public function cat_causal_impedimento()
    {
        return $this->hasOne(ImCatCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {
            $q->when(!empty($filters->search), function ($q) use ($filters) {
                $q->whereRaw("unaccent(subcausal_impedimento) ilike unaccent('%" . $filters->search . "%')");
            });

            $q->when(!empty($filters->id_causal_impedimento), function ($q) use ($filters) {
                $q->where('id_causal_impedimento', $filters->id_causal_impedimento);
            });
        });
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y H:i');
    }

    public function cat_plantilla(){
        return $this->hasOne(ImPlantillas::class,'id_subcausal_impedimento','id_subcausal_impedimento');
    }

}
