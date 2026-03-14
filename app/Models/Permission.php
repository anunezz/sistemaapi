<?php

namespace App\Models;

use App\Casts\CleanText;
use App\Models\Catalogs\ImCatPerfil;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'parent_id',
    ];

     protected function casts(): array
    {
        return [
            'name' => CleanText::class,
            'display_name' => CleanText::class,

        ];
    }


    /**
     * Get the parent permission.
     */
    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }

    /**
     * Get the child permissions.
     */
    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }

    /**
     * The roles that belong to the permission.
     */
    public function perfil()
    {
        return $this->belongsToMany(ImCatPerfil::class, 'permission_perfil');
    }
}
