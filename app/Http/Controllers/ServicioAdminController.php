<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicioAdminController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia, 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'duracion_minutos' => ['required', 'integer', 'min:5', 'max:240'],
            'precio' => ['nullable', 'numeric', 'min:0'],
        ]);

        $barberia->servicios()->create($data);

        return back()->with('status', 'Servicio creado correctamente');
    }

    public function update(Request $request, Servicio $servicio): RedirectResponse
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia && $servicio->barberia_id === $barberia->id, 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'duracion_minutos' => ['required', 'integer', 'min:5', 'max:240'],
            'precio' => ['nullable', 'numeric', 'min:0'],
        ]);

        $servicio->update($data);

        return back()->with('status', 'Servicio actualizado');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia && $servicio->barberia_id === $barberia->id, 403);

        $servicio->delete();

        return back()->with('status', 'Servicio eliminado');
    }
}
