<?php

use App\Http\Middleware\CheckUserActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Finder\Finder;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Cargar rutas modulares desde el directorio routes/modules
            Route::prefix('api')
                ->middleware('api')
                ->group(function () {
                    collect(
                        Finder::create()->in(base_path('routes/modules'))->name('*.php')
                    )->each(function ($file) {
                        include $file->getRealPath();
                    });
                });

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'permission' => \App\Http\Middleware\AccessPermission::class,
        'active.user' => CheckUserActive::class,
    ]);

    $middleware->append([
        \App\Http\Middleware\AESCryptMiddleware::class,
        \App\Http\Middleware\SecureHeadersMiddleware::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
