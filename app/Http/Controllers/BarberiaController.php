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

    public function show(Barberia $barberia)
    {
        $barberia->load(['barberos' => fn ($q) => $q->where('activo', true), 'servicios']);

        return view('barberias.show', compact('barberia'));
    }
}
