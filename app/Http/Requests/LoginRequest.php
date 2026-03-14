<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Traits\BinnacleTrait;
use App\Traits\UserDataTrait;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use LdapRecord\Auth\PasswordRequiredException;
use LdapRecord\Auth\UsernameRequiredException;
use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class LoginRequest extends FormRequest
{
    use UserDataTrait,BinnacleTrait;
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'username'    => 'required',
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required'    => 'El campo de usuario es requerido',
            'password.required' => 'El campo de contraseña es requerido',
        ];
    }

    public function authenticateWithLdapOrExternal(): \Illuminate\Http\JsonResponse
    {

        // if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
        //     return $this->ensureIsNotRateLimited();
        // }

        $userName = $this->get('username');
        $sessionUser = false;

        $userSystem = User::where('username', $userName)
        ->where('bol_eliminado',false)
        ->orWhere('email', $userName)
        ->first();

        if ($userSystem && $userSystem->usuario_directorio_activo == true) {
            $sessionUser = $this->startSession($userName);
        }
        else{
            $sessionUser = Auth::attempt(['email'=>$userName,'password'=>$this->get('password')]);
        }

        try {
            if ($sessionUser) {

                RateLimiter::clear($this->throttleKey());
                Auth::loginUsingId($userSystem->id);

                DB::table('oauth_access_tokens')
                    ->where('user_id', $userSystem->id)
                    ->update(['revoked' => true]);


                DB::table('oauth_access_tokens')
                    ->where('revoked', true)
                    ->delete();


                $token = $userSystem->createToken('impedimentos_token')->accessToken;
                $date = new \DateTime(datetime: "now +8 hours");

                /*TransactionsController::saveTransaction(
                    2,
                    "Ingreso al sistema con usuario: $username"
                );*/
                $this->guardarMovimiento($userSystem->id,18,1,'Se Inicio sesión en el sistema');

                return response()->json([
                    'success'       => true,
                    'authenticated' => true,
                    'user'          => $this->getSessionInfo($userSystem->id),
                    'session'       => (object)[
                        'impedimentos_token'            => $token,
                        'impedimentos_token_expiration' => $date->format('D M d Y H:i:s'),
                        'impedimentos_hash'             => encrypt($userSystem->id)
                    ]
                ], 200);

            } else {

                /*TransactionsController::saveTransaction(
                    1,
                    "Intento de ingreso al sistema con usuario: $username"
                );*/

               // RateLimiter::hit($this->throttleKey(), 600);

                return response()->json( [
                    'success'       => false,
                    'message'  => 'Datos invalidos!',
                ], 402 );
            }
        } catch (PasswordRequiredException | UsernameRequiredException $e) {
            RateLimiter::hit($this->throttleKey(), 600);

            throw ValidationException::withMessages([
                'error' => __('auth.failed'),
            ]);
        }
    }

    public function ensureIsNotRateLimited()
    {
        // event(new Lockout($this));

        // $seconds = RateLimiter::availableIn($this->throttleKey());

        // return response()->json([
        //     'messages' => 'Has excedido el límite de intentos. <br> Por favor, espera ' . $seconds . ' segundos (' . ceil($seconds / 60) . ' minutos) antes de intentar de nuevo.'
        // ],429);
        return response()->json([
            'messages' => 'Has excedido el límite de intentos. <br> Por favor, espera  segundos ( minutos) antes de intentar de nuevo.'
        ],429);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->input('username')) . '|' . $this->ip();
    }

    public function startSession($username)
    {
        $connection = Container::getConnection('default');

        if (\Config::get('app.env') == "local") { //para no activar vpn en desarrollo local
            return true;
        }

        $user = LdapUser::where([
            'samaccountname' => $username,
            'useraccountcontrol' => '512',
        ])->first();

        if ($user && $connection->auth()->attempt($user->getDn(), $this->get('password'))) {
             return true;
        }

        return false;
    }
}
