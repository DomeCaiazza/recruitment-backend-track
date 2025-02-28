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
        $expectedKey = env('API_KEY', 'secret');

        if (!$providedKey || $providedKey !== $expectedKey) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
