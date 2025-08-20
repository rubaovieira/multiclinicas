<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class MasterMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->perfil === 'master') {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Acesso não autorizado.');
    }
} 