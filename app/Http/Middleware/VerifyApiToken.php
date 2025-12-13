<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        // Check database first, fallback to .env for backward compatibility
        $expectedToken = \App\Models\Setting::get('api_token') ?? env('API_TOKEN');

        if (!$token || !$expectedToken || $token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing bearer token.',
            ], 401);
        }

        return $next($request);
    }
}
