<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureBarberiaSeleccionada
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->barberiaActiva()) {
            $mensaje = $user->esAdmin()
                ? 'Elegí una barbería para continuar.'
                : 'Necesitás tener una barbería asignada para acceder a esta sección.';

            return redirect()->route('dashboard')->with('error', $mensaje);
        }

        return $next($request);
    }
}
