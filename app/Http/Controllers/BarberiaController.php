<?php

namespace App\Http\Controllers;

use App\Models\Barberia;
use Illuminate\Support\Facades\Auth;

class BarberiaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $barberia = $user?->barberia()->with(['barberos' => fn ($q) => $q->latest(), 'servicios' => fn ($q) => $q->latest()])->first();

        $barberias = $barberia ? collect([$barberia->loadCount(['barberos', 'servicios'])]) : Barberia::withCount(['barberos', 'servicios'])->latest()->get();
        $barberos = $barberia?->barberos ?? collect();
        $servicios = $barberia?->servicios ?? collect();
        $turnos = $barberia
            ? $barberia->turnos()->with(['cliente', 'servicio', 'barbero'])->latest()->take(5)->get()
            : collect();

        return view('dashboard', compact('barberias', 'barberia', 'barberos', 'servicios', 'turnos'));
    }

    public function update()
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia, 403);

        $data = request()->validate([
            'logo_url' => ['nullable', 'url'],
            'color_primario' => ['nullable', 'string', 'max:20'],
            'color_secundario' => ['nullable', 'string', 'max:20'],
            'mensaje_bienvenida' => ['nullable', 'string'],
            'informacion_contacto' => ['nullable', 'string'],
        ]);

        $barberia->update($data);

        return back()->with('status', 'Personalización actualizada');
    }

    public function show(Barberia $barberia)
    {
        $barberia->load(['barberos' => fn ($q) => $q->where('activo', true), 'servicios']);

        return view('barberias.show', compact('barberia'));
    }
}
