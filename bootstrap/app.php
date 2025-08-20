<?php

use App\Http\Middleware\CheckClinicIsActive;
use App\Http\Middleware\MasterMiddleware;
use App\Http\Middleware\VerificarUsuarioAtivoParaLogin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'master' => MasterMiddleware::class,
            'checkClinicIsActive' => CheckClinicIsActive::class,
            'verificarUsuarioAtivoParaLogin' => VerificarUsuarioAtivoParaLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
