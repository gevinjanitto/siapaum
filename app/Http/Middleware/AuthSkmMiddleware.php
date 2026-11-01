<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSkmMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Define the fixed token (you can also use env('FIXED_TOKEN') if stored in .env)
        $fixedToken = config('app.skm_api_key');

        // Extract the token from the Authorization header
        $authorizationHeader = $request->header('Authorization');
        if ($authorizationHeader && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            $token = $matches[1];

            // Check if the token matches the fixed token
            if ($token === $fixedToken) {
                return $next($request);
            }
        }

        // Return a 401 Unauthorized response if the token is invalid
        return ResponseFormatter::error([
            'message' => 'Valid API key is required',
        ], 'Unauthorized', 401);
    }
}
