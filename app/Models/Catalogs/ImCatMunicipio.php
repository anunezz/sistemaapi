<?php

namespace App\Models\Catalogs;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ImCatMunicipio extends Model
{
    protected $table = 'im_cat_municipio';
    protected $primaryKey = 'id_municipio';
    protected $appends = ['hash_id'];
    protected $fillable = [
        'municipio',
        'id_entidad_federativa',
        'id_pais',
        'bol_eliminado',
        'usuario_alta',
        'usuario_modificacion',
    ];

    public function getHashIdAttribute(): string
    {
        return encrypt($this->id_municipio);
    }

    public function cat_entidad_federativa()
    {
        return $this->hasOne(ImCatEntidadFederativa::class, 'id_entidad_federativa', 'id_entidad_federativa');
    }

    public function cat_pais()
    {
        return $this->hasOne(ImCatPais::class, 'id_pais', 'id_pais');
    }

    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {

            $q->when(!empty($filters->search), function ($q) use ($filters) {
                $q->whereRaw("unaccent(municipio) ilike unaccent('%" . $filters->search . "%')");
            });
        });
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i');
    }
}
