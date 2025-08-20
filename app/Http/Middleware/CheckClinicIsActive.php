<?php

namespace App\Http\Middleware;

use App\Models\Clinica;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckClinicIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $clinic = Clinica::find(Auth::user()->clinica_id);


        if ($clinic->status == 'inativo') {
            Auth::logout(); // Desloga o usuário
            return redirect('/login')->with('error', 'Clínica Desativada');
        }

        return $next($request);
    }
}
