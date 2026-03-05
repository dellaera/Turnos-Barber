<?php

namespace App\Http\Controllers;

use App\Models\Barberia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminBarberiaController extends Controller
{
    public function seleccionar(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user && $user->esAdmin(), 403);

        $data = $request->validate([
            'barberia_id' => ['required', 'exists:barberias,id'],
        ]);

        session(['barberia_admin_id' => $data['barberia_id']]);

        return redirect()->route('dashboard')->with('status', 'Barbería seleccionada');
    }
}
