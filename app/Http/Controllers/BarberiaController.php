<?php

namespace App\Http\Controllers;

use App\Models\Barberia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BarberiaController extends Controller
{
    public function index(Request $request)
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

        $clientesBusqueda = $request->input('clientes_buscar');

        $barberias = $user?->esAdmin()
            ? Barberia::withCount(['barberos', 'servicios'])->latest()->get()
            : collect();
        $barberos = $barberia?->barberos ?? collect();
        $servicios = $barberia?->servicios ?? collect();
        $turnos = $barberia
            ? $barberia->turnos()
                ->with(['cliente', 'servicio', 'barbero'])
                ->orderByDesc('fecha')
                ->orderByDesc('hora')
                ->take(5)
                ->get()
            : collect();

        $clientes = collect();
        $clienteMetrics = [
            'total' => 0,
            'nuevos_30' => 0,
            'activos_30' => 0,
        ];

        if ($barberia) {
            $clientesQuery = $barberia->clientes()
                ->with(['ultimoTurno.barbero', 'ultimoTurno.servicio'])
                ->withCount(['turnos as turnos_completados_count' => function ($q) {
                    $q->where('estado', 'completado');
                }])
                ->latest();

            if ($clientesBusqueda) {
                $clientesQuery->where(function ($query) use ($clientesBusqueda) {
                    $like = "%{$clientesBusqueda}%";
                    $query->where('nombre', 'like', $like)
                        ->orWhere('telefono', 'like', $like)
                        ->orWhere('email', 'like', $like);

                    if (ctype_digit($clientesBusqueda)) {
                        $query->orWhere('id', intval($clientesBusqueda));
                    }
                });
            }

            $clientes = $clientesQuery->paginate(8)->withQueryString();

            $clienteMetrics['total'] = $barberia->clientes()->count();
            $clienteMetrics['nuevos_30'] = $barberia->clientes()
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count();
            $clienteMetrics['activos_30'] = $barberia->turnos()
                ->where('fecha', '>=', Carbon::now()->subDays(30))
                ->distinct('cliente_id')
                ->count('cliente_id');
        }

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
                ->with(['cliente', 'servicio', 'barbero'])
                ->where('estado', 'programado')
                ->where(function ($q) {
                    $hoy = Carbon::today();
                    $ahora = Carbon::now()->format('H:i:s');
                    $q->whereDate('fecha', '>', $hoy)
                        ->orWhere(function ($sub) use ($hoy, $ahora) {
                            $sub->whereDate('fecha', $hoy)
                                ->whereTime('hora', '>=', $ahora);
                        });
                })
                ->orderBy('fecha')
                ->orderBy('hora')
                ->first();
        }

        return view('dashboard', compact(
            'barberias',
            'barberia',
            'barberos',
            'servicios',
            'turnos',
            'clientes',
            'metrics',
            'proximoTurno',
            'clienteMetrics',
            'clientesBusqueda'
        ));
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
