<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('x-api-key');
        if(env('APP_ENV') === 'testing') {
            $expectedKey = env('API_KEY_TESTING', 'secret_test');
        }else {
            $expectedKey = env('API_KEY', 'secret');
        }


        if (!$providedKey || $providedKey !== $expectedKey) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
