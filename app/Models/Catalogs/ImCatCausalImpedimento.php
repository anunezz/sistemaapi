<?php

namespace App\Models\Catalogs;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ImCatCausalImpedimento extends Model
{
    protected $table = 'im_cat_causal_impedimento';
    protected $primaryKey = 'id_causal_impedimento';
    protected $appends = ['hash_id'];
    protected $fillable = [
        'causal_impedimento',
        'bol_eliminado',
        'validate_high',
        'id_usuario_alta',
        'id_usuario_modificacion',
    ];

    public function getHashIdAttribute(): string
    {
        return encrypt($this->id_causal_impedimento);
    }

    public function cat_subcausal_impedimento()
    {
        return $this->hasMany(ImCatSubCausalImpedimento::class, 'id_causal_impedimento', 'id_causal_impedimento');
    }

    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {
            $q->when(!empty($filters->search), function ($q) use ($filters) {

                $q->whereRaw("unaccent(causal_impedimento) ilike unaccent('%" . $filters->search . "%')");
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
}
