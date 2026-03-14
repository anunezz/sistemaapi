<?php

namespace App\Models\Catalogs;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ImCatOficina extends Model
{
    protected $table = 'im_cat_oficina';
    protected $primaryKey = 'id_oficina';
    protected $appends = ['hash_id'];
    protected $fillable = [
        'cad_oficina',
        'nombre_corto',
        'correo_electronico',
        'id_oficina_padre',
        'id_tipo_oficina',
        'id_pais',
        'id_jurisdiccion',
        'bol_eliminado',
        'bol_activo',
        'usuario_alta',
        'usuario_modificacion',
        'id_oficina_suet',
    ];

    public function getHashIdAttribute(): string
    {
        return encrypt($this->id_oficina);
    }

    public function cat_pais()
    {
        return $this->hasOne(ImCatPais::class, 'id_pais', 'id_pais');
    }

    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {

            $q->when(!empty($filters->search), function ($q) use ($filters) {
                $q->whereRaw("unaccent(nombre_corto) ilike unaccent('%" . $filters->search . "%')");
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

    /**
     * Filtra oficinas por tipo, activas y no eliminadas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $tipo El id del tipo de oficina (por defecto 2)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorTipoActivas($query, $tipo = 2)
    {
        return $query->where('id_tipo_oficina', $tipo)
                    ->where('bol_activo', true);
    }
}
