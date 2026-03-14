<?php

use App\Http\Controllers\FilesChunksController;

Route::middleware('auth:api')->group(function () {
    Route::post('upload/file-chunks', FilesChunksController::class)->middleware('throttle:1900,1');
});
