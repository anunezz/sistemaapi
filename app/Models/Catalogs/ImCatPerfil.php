<?php

namespace App\Models\Catalogs;

use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ImCatPerfil extends Model
{
    protected $table = 'im_cat_perfil';
    protected $primaryKey = 'id_perfil';
    protected $appends = ['hash_id'];
    protected $fillable = [
        'perfil',
        'bol_eliminado',
        'usuario_alta',
        'usuario_modificacion',
    ];

    public function getHashIdAttribute(): string
    {
        return encrypt($this->id_perfil);
    }

    public function scopeSearch($query, $filters)
    {
        return $query->where(function ($q) use ($filters) {

            $q->when(!empty($filters->search), function ($q) use ($filters) {
                $q->whereRaw("unaccent(perfil) ilike unaccent('%" . $filters->search . "%')");
            });
        });
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_perfil', 'id_perfil', 'id_permission')->select('id');
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
