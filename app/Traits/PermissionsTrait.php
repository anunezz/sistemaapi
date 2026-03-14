<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait PermissionsTrait
{
    private function hasPermission($permission){
        $user = User::find(Auth::user()->id);
        return $user->hasPermission($permission) ? true : false;
    }

    private function responseDenied(){
        return response()->json(['status'=>'no autorizado'],403);
    }
}
