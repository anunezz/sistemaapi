<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Traits\BinnacleTrait;
use App\Traits\UserDataTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use UserDataTrait, BinnacleTrait;
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            return $request->authenticateWithLdapOrExternal();
        }
        catch ( \Exception $e ){
            \Log::error($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],500);
        }

    }

    public function logout(): JsonResponse
    {
        try {
            /*TransactionsController::saveTransaction(
                3,
                "Sesión finalizada"
            );*/

            Auth::guard('web')->logout();

            DB::table('oauth_access_tokens')
                ->where('user_id', Auth::user()->getAuthIdentifier())
                ->update(['revoked' => true]);

                $this->guardarMovimiento(Auth::user()->id,19,10,'Se termino la sesión en el sistema');
                return response()->json([
                'authenticated' => false
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error. Intenta de nuevo.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getUserInfo($userId): JsonResponse
    {
        try {
            if (Auth::user()->getAuthIdentifier() !== decrypt($userId)) {

                Auth::guard('web')->logout();

                DB::table('oauth_access_tokens')
                    ->where('user_id', Auth::user()->getAuthIdentifier())
                    ->update(['revoked' => true]);

                return response()->json([
                    'authenticated' => false
                ], 401);
            }

            return response()->json([
                'user' => $this->getSessionInfo(decrypt($userId))
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error. Intenta de nuevo.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
