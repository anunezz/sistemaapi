<?php

use App\Http\Controllers\Catalogs\CatCausalImpedimentoController;
use App\Http\Controllers\Catalogs\CatEntidadFederativaController;
use App\Http\Controllers\Catalogs\CatEstatusSolicitudController;
use App\Http\Controllers\Catalogs\CatGeneralGeneroController;
use App\Http\Controllers\Catalogs\CatMunicipioController;
use App\Http\Controllers\Catalogs\CatOficinasController;
use App\Http\Controllers\Catalogs\CatPaisController;
use App\Http\Controllers\Catalogs\CatPerfilController;
use App\Http\Controllers\Catalogs\CatSubCausalImpedimentoController;
use App\Http\Controllers\Catalogs\CatTipoSolicitudController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CatalogController;

Route::group([
    'middleware'    => ['auth:api'],
    'prefix'        => 'administration',
    'as'            => 'administration::',
    'namespace'     => 'App\Http\Controllers'
], function(){
        Route::get('catalog',[CatalogController::class, 'index']);
});


Route::middleware(['auth:api','permission:catalog_management'])->prefix('administration/catalogs')->group(function () {
    Route::resource('subcausal-impedimentos', CatSubCausalImpedimentoController::class);
    Route::resource('causal-impedimentos', CatCausalImpedimentoController::class);
    Route::resource('entidad-federativa', CatEntidadFederativaController::class);
    Route::resource('estatus-solicitud', CatEstatusSolicitudController::class);
    Route::resource('general-genero', CatGeneralGeneroController::class);
    Route::resource('municipio', CatMunicipioController::class);
    Route::resource('oficinas', CatOficinasController::class);
    Route::resource('pais', CatPaisController::class);
    Route::resource('perfil', CatPerfilController::class);
    Route::resource('tipo-solicitud', CatTipoSolicitudController::class);
});
Route::post('/cat-users', [UserController::class, 'catUsers']);


