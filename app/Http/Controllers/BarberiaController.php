<?php

namespace App\Http\Controllers;

use App\Models\Barberia;

class BarberiaController extends Controller
{
    public function index()
    {
        $barberias = Barberia::withCount(['barberos', 'servicios'])->latest()->get();

        return view('dashboard', compact('barberias'));
    }

    public function show(Barberia $barberia)
    {
        $barberia->load(['barberos' => fn ($q) => $q->where('activo', true), 'servicios']);

        return view('barberias.show', compact('barberia'));
    }
}
