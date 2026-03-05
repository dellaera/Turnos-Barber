<?php

namespace App\Http\Controllers;

use App\Models\Barbero;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarberoAdminController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia, 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $barberia->barberos()->create($data);

        return back()->with('status', 'Barbero creado correctamente');
    }

    public function update(Request $request, Barbero $barbero): RedirectResponse
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia && $barbero->barberia_id === $barberia->id, 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $barbero->update([
            'nombre' => $data['nombre'],
            'activo' => $data['activo'] ?? false,
        ]);

        return back()->with('status', 'Barbero actualizado');
    }

    public function destroy(Barbero $barbero): RedirectResponse
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia && $barbero->barberia_id === $barberia->id, 403);

        $barbero->delete();

        return back()->with('status', 'Barbero eliminado');
    }
}
