<?php

namespace App\Models\Catalogs;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ImCatPais extends Model
{
    protected $table = 'im_cat_pais';
    protected $primaryKey = 'id_pais';
    protected $appends = ['hash_id'];
    protected $fillable = [
        'idalpha2',
        'idalpha3',
        'cad_nombre_es',
        'bol_eliminado',
        'usuario_alta',
        'usuario_modificacion',
    ];

    public function getHashIdAttribute(): string
    {
        return encrypt($this->id_pais);
    }

    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {

            $q->when(!empty($filters->search), function ($q) use ($filters) {
                $q->whereRaw("unaccent(idalpha2) ilike unaccent('%" . $filters->search . "%')")
                    ->orWhereRaw("unaccent(idalpha2) ilike unaccent('%" . $filters->search . "%')")
                    ->orWhereRaw("unaccent(cad_nombre_es) ilike unaccent('%" . $filters->search . "%')");
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
