<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EjecutarController;

Route::group([
   // 'middleware'    => ['auth:api'],
    'prefix'        => 'ejecuatr',
    'namespace'     => 'App\Http\Controllers'
], function(){
    Route::get('comandos', [EjecutarController::class,'comandos']);
});

