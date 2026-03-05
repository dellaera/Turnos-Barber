<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteAdminController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia, 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $barberia->clientes()->create($data);

        return back()->with('status', 'Cliente agregado correctamente');
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia && $cliente->barberia_id === $barberia->id, 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $cliente->update($data);

        return back()->with('status', 'Cliente actualizado');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia && $cliente->barberia_id === $barberia->id, 403);

        $cliente->delete();

        return back()->with('status', 'Cliente eliminado');
    }
}
