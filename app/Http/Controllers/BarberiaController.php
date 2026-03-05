<?php

namespace App\Http\Controllers;

use App\Models\Barberia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BarberiaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $barberia = $user?->barberiaActiva();
        if ($barberia) {
            $barberia->load([
                'barberos' => fn ($q) => $q->latest(),
                'servicios' => fn ($q) => $q->latest(),
            ]);
            $barberia->loadCount(['barberos', 'servicios']);
        }

        $barberias = $user?->esAdmin()
            ? Barberia::withCount(['barberos', 'servicios'])->latest()->get()
            : collect();
        $barberos = $barberia?->barberos ?? collect();
        $servicios = $barberia?->servicios ?? collect();
        $turnos = $barberia
            ? $barberia->turnos()->with(['cliente', 'servicio', 'barbero'])->latest()->take(5)->get()
            : collect();
        $clientes = $barberia
            ? $barberia->clientes()
                ->with(['ultimoTurno.barbero', 'ultimoTurno.servicio'])
                ->latest()
                ->take(10)
                ->get()
            : collect();

        $metrics = [
            'turnos_semana' => 0,
            'turnos_hoy' => 0,
            'clientes_unicos' => 0,
            'barberos_activos' => 0,
        ];
        $proximoTurno = null;

        if ($barberia) {
            $inicioSemana = Carbon::now()->startOfWeek();
            $finSemana = Carbon::now()->endOfWeek();
            $metrics['turnos_semana'] = $barberia->turnos()->whereBetween('fecha', [$inicioSemana, $finSemana])->count();
            $metrics['turnos_hoy'] = $barberia->turnos()->whereDate('fecha', Carbon::today())->count();
            $metrics['clientes_unicos'] = $barberia->turnos()->distinct('cliente_id')->count('cliente_id');
            $metrics['barberos_activos'] = $barberia->barberos()->where('activo', true)->count();
            $proximoTurno = $barberia->turnos()
                ->with(['cliente', 'servicio'])
                ->whereDate('fecha', '>=', Carbon::today())
                ->orderBy('fecha')
                ->orderBy('hora')
                ->first();
        }

        return view('dashboard', compact('barberias', 'barberia', 'barberos', 'servicios', 'turnos', 'clientes', 'metrics', 'proximoTurno'));
    }

    public function update(Request $request)
    {
        $barberia = Auth::user()->barberia;
        abort_unless($barberia, 403);

        $data = $request->validate([
            'logo_url' => ['nullable', 'url'],
            'logo_file' => ['nullable', 'image', 'max:2048'],
            'color_primario' => ['nullable', 'string', 'max:20'],
            'color_secundario' => ['nullable', 'string', 'max:20'],
            'mensaje_bienvenida' => ['nullable', 'string'],
            'informacion_contacto' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('logos', 'public');
            $data['logo_url'] = Storage::url($path);
        }

        unset($data['logo_file']);

        $barberia->update($data);

        return back()->with('status', 'Personalización actualizada');
    }

    public function show(Barberia $barberia)
    {
        $barberia->load(['barberos' => fn ($q) => $q->where('activo', true), 'servicios']);

        return view('barberias.show', compact('barberia'));
    }
}
