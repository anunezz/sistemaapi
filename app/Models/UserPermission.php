<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserPermission extends Pivot
{
    protected $table = "user_permission";

    protected $fillable=['id_usuario','id_permission'];

    public function permission(){
        return $this->hasOne(Permission::class,'id','id_permission');
    }
}
