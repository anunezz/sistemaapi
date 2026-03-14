<?php

namespace App\Traits;

use App\Models\User;

trait UserDataTrait
{
    public function getSessionInfo ($userId)
    {
        $user = User::with('usuarioPerfil.perfil:id_perfil,perfil','oficina:id_oficina,cad_oficina,correo_electronico','permissionIds.permission')->find($userId);
        $permissions=[];
        if($user && $user->permissionIds){
            foreach ($user->permissionIds as $key => $id_permission) {
                array_push($permissions,$id_permission->permission->name);
            }
        }
        return [
            'hash_id'       => $user->hash_id,
            'full_name'     => $user->full_name,
            'email'         => $user->email,
            'name'          => $user->name,
            'first_name'    => $user->first_name,
            'second_name'   => $user->second_name,
            'perfil'        => $user->usuarioPerfil->perfil,
            'oficina'       => $user->oficina,
            'permissions'   => $permissions
        ];
    }
}
