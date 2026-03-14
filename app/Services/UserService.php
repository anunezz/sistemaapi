<?php

namespace App\Services;

use App\Models\ImUsuarioPerfil;
use App\Models\User;
use App\Notifications\UserRegistered;
use App\Traits\BinnacleTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Illuminate\Support\Str;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class UserService
{
    use BinnacleTrait;
    private $password;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->password='Password@01';
    }

    public function index(array $filter)
    {
        return User::with('usuarioPerfil.perfil')->search($filter['search'])->paginate($filter['rowsPerPage']);
    }

    public function search(array $search)
    {
        try {

            $data['username'] = $search['username'];

            $user_db = User::where('username', $data["username"])->first();

            $status = 'already_exists';

            $userLdap = LdapUser::where([
                'samaccountname' => $data['username'],
                'useraccountcontrol' => '512'
            ])->first();

            if( $userLdap && $userLdap != null ){
                if( isset($userLdap->samaccountname) ){
                    if( $userLdap->samaccountname[0] !== $data['username'] ){
                        $userLdap = null;
                    }
                }
            }

            $user = [];
            if ($userLdap == null) {
                $status = 'not_found';
            }

            if ($userLdap && $user_db == null) {
                $status                 = 'found';
                $surnames               = explode(' ', $userLdap->sn[0]);
                $user['username']       = $data['username'];
                $user['email']          = $data['username'] . '@sre.gob.mx';
                $user['name']           = $userLdap->givenname[0];
                $user['first_name']     = $surnames[0];
                $user['second_name']    = isset($surnames[1]) ? $surnames[1] : '';
            }

            return response()->json([
                'status' => $status,
                'user' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error'], 400);
        }
    }

   public function store(array $userData)
{
    try {
        DB::beginTransaction();


$username = strtolower(trim(Str::before($userData['email'], '@')));

// validar duplicado
if (
    User::whereRaw('LOWER(username) = ?', [$username])->exists()
) {
    return response()->json([
        'status' => 'duplicate',
        'message' => 'El usuario ya está registrado'
    ], 422);
}

        $userStore = new User;
        $userStore->fill($userData);
        $userStore->id_usuario_alta = Auth::user()->id;
        $userStore->username = $username;
        $userStore->email = strtolower($userData['email']);
        $userStore->password = Hash::make($this->password);
        $userStore->save();


        $userStore->permissions()->sync($userData['permissions']);


        $imUsuarioPerfil = new ImUsuarioPerfil;
        $imUsuarioPerfil->id_usuario = $userStore->id;
        $imUsuarioPerfil->id_perfil = $userData['id_perfil'];
        $imUsuarioPerfil->usuario_alta = Auth::user()->id;
        $imUsuarioPerfil->save();


        if ($userStore->usuario_directorio_activo == false) {
            $userStore->notify(new UserRegistered($this->password));
        }

        $this->guardarMovimiento(
            Auth::user()->id,
            12,
            3,
            'Se creó el usuario "' . $userStore->username . '" con ID ' . $userStore->id
        );

        DB::commit();

        return response()->json(['status' => 'saved'], 200);

    } catch (Throwable $e) {
        DB::rollBack();
        \Log::error($e);

        return response()->json(['status' => 'error'], 400);
    }
}


    public function show($id)
    {
        $user = User::with('usuarioPerfil','permissionIds')->find(decrypt($id));

        $user->permissions = $user->permissionIds()->pluck('id_permission');

        return response()->json([
                    'data' => $user,
            ], 200);
    }

    public function edit($id)
    {
        $user = User::with('usuarioPerfil','permissionIds')->find(decrypt($id));

        $user->permissions = $user->permissionIds()->pluck('id_permission');

        return response()->json([
                    'data' => $user,
            ], 200);
    }

    public function update($id, $userData)
    {
        try {
            DB::beginTransaction();

            $userUpdate = User::find(decrypt($id));
            $userUpdate->fill($userData);
            $userUpdate->username = Str::before($userData['email'], '@');
            $userUpdate->id_usuario_modificacion = Auth::user()->id;
            $userUpdate->save();

            $userUpdate->permissions()->sync($userData['permissions']);

            $imUsuarioPerfilUpdate = ImUsuarioPerfil::where('id_usuario', $userUpdate->id)->first();

            if ($imUsuarioPerfilUpdate) {
                $imUsuarioPerfilUpdate['id_perfil'] = $userData['id_perfil'];
                $imUsuarioPerfilUpdate->usuario_modificacion = Auth::user()->id;
                $imUsuarioPerfilUpdate->save();
            }

            $this->guardarMovimiento(Auth::user()->id,12,2,'Se actualizó el usuario "'.$userUpdate->username. '" con ID '.$userUpdate->id);
            DB::commit();
            return response()->json(['status' => 'updated'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error'], 400);
        }
    }

    public function changeStatus($id)
    {
        $userStatus = User::find(decrypt($id));
        $userStatusText = !$userStatus->bol_eliminado ? 'desactivo' : 'activo';
        $userStatus->bol_eliminado = !$userStatus->bol_eliminado;
        $userStatus->save();
        $this->guardarMovimiento(Auth::user()->id,12,2,'Se '.$userStatusText.' el estatus de el usuario "'.$userStatus->username.'" con ID'. $userStatus->id);
        return response()->json([
            'status' => $userStatus->bol_eliminado === true ? 'disabled' : 'enabled',
        ], 200);
    }
}
