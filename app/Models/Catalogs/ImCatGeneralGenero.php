<?php

namespace App\Models\Catalogs;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ImCatGeneralGenero extends Model
{
    protected $table = 'im_cat_general_genero';
    protected $appends = ['hash_id'];
    protected $fillable = [
        'id_genero',
        'genero',
        'bol_eliminado',
        'usuario_alta',
        'usuario_modificacion',
    ];

    public function getHashIdAttribute(): string
    {
        return encrypt($this->id);
    }

    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {

            $q->when(!empty($filters->search), function ($q) use ($filters) {
                $q->whereRaw("unaccent(genero) ilike unaccent('%" . $filters->search . "%')");
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
