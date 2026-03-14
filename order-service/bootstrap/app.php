<?php

    use App\Http\Middleware\VerifyInternalSignature;
    use App\Http\Middleware\EnsureUserContext;
    use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(VerifyInternalSignature::class);
        $middleware->append(EnsureUserContext::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
