<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if ($roles == 'admin' && auth()->user()->isAdmin) {
            return $next($request);
        } else if ($roles == 'public' && auth()->user()->isPublic) {
            return $next($request);
        } else if ($roles == 'verifikator' && auth()->user()->isVerifikator) {
            return $next($request);
        } else if ($roles == 'inputer' && auth()->user()->isInputer) {
            return $next($request);
        } else {
            return $request->expectsJson() ?
                ResponseFormatter::error([
                    'message' => 'Anda tidak memiliki akses untuk aksi tersebut',
                ], 'Forbidden', 403) :
                redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses untuk aksi tersebut');
        }
    }
}
