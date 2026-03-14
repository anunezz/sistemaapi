<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware'    => ['auth:api','permission:user_management'],
    'prefix'        => 'administration',
    'namespace'     => 'App\Http\Controllers'
], function(){

    // 🔹 SOLO LECTURA (no bloquear)
    Route::get('users',[UserController::class, 'index']);
    Route::post('users/search',[UserController::class, 'search']);
    Route::get('users/{id}/edit',[UserController::class,'edit']);
    
    // 🔴 ACCIONES (sí bloquear si está desactivado)
    Route::middleware('active.user')->group(function () {
        Route::get('users/{id}',[UserController::class,'show']);
        Route::post('users',[UserController::class,'store']);
        Route::put('users/{id}',[UserController::class,'update']);
        Route::get('users/change-status/{id}',[UserController::class,'changeStatus']);
    });

});

