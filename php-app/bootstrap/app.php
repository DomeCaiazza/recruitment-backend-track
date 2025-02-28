<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/',
        api: __DIR__.'/../routes/api.php'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                Log::notice('Resource not found', ['request' => $request->url()]);
                return response()->json([
                    'message' => 'Record not found'
                ], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') && $e->getCode() == 23000) {
                Log::notice('Duplicate entry', ['request' => $request->url()]);
                return response()->json([
                    'message' => 'Record already exists'
                ], 409);
            }
        });

        $exceptions->render(function (Throwable $e, Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                Log::error('Internal server error', ['request' => $request->url(), 'message' => $e->getMessage()]);
                return response()->json([
                    'message' => $e->getMessage()
                ], 500);
            }
        });   
    })->create();
