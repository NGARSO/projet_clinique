<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        // CORS pour Angular (localhost:4200)
        $middleware->alias([
            'cors' => HandleCors::class,
        ]);
        // Nous n'avons pas besoin d'aliaser 'auth:api', Laravel le fait déjà nativement !
    })
    ->withExceptions(function (\Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        // Force le retour en JSON pour les erreurs d'authentification sur l'API (évite l'erreur Route [login])
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Non authentifié. Token manquant ou invalide.'], 401);
            }
        });
    })
    ->create();