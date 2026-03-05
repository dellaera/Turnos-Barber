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

        if ($user && $user->esAdmin() && ! $user->barberiaActiva()) {
            return redirect()->route('dashboard')->with('status', 'Elegí una barbería para continuar.');
        }

        if (! $user?->barberiaActiva()) {
            abort(403);
        }

        return $next($request);
    }
}
