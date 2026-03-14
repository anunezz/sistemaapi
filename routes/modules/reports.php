<?php

use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Reports\ReportStatisticalController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api','permission:reports'])->prefix('administration')->group(function () {
    Route::get('reports', [ReportsController::class, 'getReport']);
    Route::get('report-statistical', [ReportStatisticalController::class, 'getReport']);
});
