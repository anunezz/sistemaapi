<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
   public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);


        $status = Password::sendResetLink($request->only('email'));
        return response()->json([
            'status' => $status === Password::RESET_LINK_SENT ? 'success' : 'error',
            'message' => __($status),
        ], 200);
    }
}
