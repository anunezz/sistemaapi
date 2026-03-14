<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ImpedimentTransactionController;
use App\Http\Controllers\ImPlantillaController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ApplicationTransactionController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\V1ServiceImpedimentsController;

Route::prefix('v1')->group(function () {
    Route::post('solicitud_alta_modificacion', [V1ServiceImpedimentsController::class, 'solicitud_alta_modificacion']);
    Route::post('solicitud_verificacion', [V1ServiceImpedimentsController::class, 'solicitud_verificacion']);
    Route::post('solicitud_alta', [V1ServiceImpedimentsController::class, 'solicitud_alta']);
    Route::post('consulta_de_impedimentos', [V1ServiceImpedimentsController::class, 'consulta_de_impedimentos']);
    Route::post('alerta_de_impedimentos', [V1ServiceImpedimentsController::class, 'alerta_de_impedimentos']);
    Route::post('consulta_verficacion', [V1ServiceImpedimentsController::class, 'consulta_verficacion']);
    Route::post('consulta_verficacion_rechazados', [V1ServiceImpedimentsController::class, 'consulta_verficacion_rechazados']);
});

Route::post('login', [AuthController::class,'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');

///Obetenr archivos de storage (imagenes, PDFs, videos, etc.)
Route::get('media-file/{path}', [GeneralController::class, 'getMediaFiles'])->where('path', '.*');

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class,'logout']);
    Route::get('user/{userId}', [AuthController::class,'getUserInfo']);

    //transactions
    Route::post('/transaction', [TransactionController::class, 'index'])->middleware('permission:logbook');
    Route::post('/transaction/create', [TransactionController::class, 'store'])/* ->middleware('permission:logbook') */;
    Route::post('/get_cats_binnacle', [TransactionController::class, 'get_cats_binnacle'])->middleware('permission:logbook');
    Route::post('/get_export', [TransactionController::class,'get_export'])->middleware('permission:logbook');

    //application transactions
    Route::post('/application_transaction', [ApplicationTransactionController::class, 'index'])->middleware('permission:request_log');
    Route::post('/get_cats_application_binnacle', action: [ApplicationTransactionController::class, 'get_cats_application_binnacle'])->middleware('permission:request_log');

    //impediment transactions
    Route::post('/impediment_transaction', [ImpedimentTransactionController::class, 'index'])->middleware('permission:impediment_log');
    Route::post('/get_cats_impediment_binnacle', action: [ImpedimentTransactionController::class, 'get_cats_impediment_binnacle'])->middleware('permission:impediment_log');

    Route::post('/plantillas', action: [ImPlantillaController::class, 'index'])->middleware('permission:verification_inbox');
    Route::post('/plantillas/create', action: [ImPlantillaController::class, 'store'])->middleware('permission:verification_inbox');
    Route::post(uri: '/plantillas/getPlantillaById', action: [ImPlantillaController::class, 'getPlantillaById'])->middleware('permission:verification_inbox');
    Route::post('/plantillas/update/{id}', [ImPlantillaController::class, 'update'])->middleware('permission:verification_inbox');
    Route::post('/plantillas/delete', [ImPlantillaController::class, 'delete'])->middleware('permission:verification_inbox');
    Route::post('/plantillas/get_cat_subcausal', [ImPlantillaController::class, 'get_cat_subcausal'])->middleware('permission:verification_inbox');


});
