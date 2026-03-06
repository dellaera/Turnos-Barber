<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmacionTurnoMail;
use App\Mail\EstadoTurnoActualizadoMail;
use App\Models\Barberia;
use App\Models\Cliente;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TurnoController extends Controller
{
    public function index(Request $request)
    {
        $barberia = Auth::user()->barberiaActiva();
        abort_unless($barberia, 403);

        $filters = $request->only(['desde', 'hasta', 'estado', 'barbero_id', 'servicio_id']);
        $vistaActual = $request->input('vista', 'todos');

        $query = $barberia->turnos()->with(['cliente', 'servicio', 'barbero'])->latest();

        if (!empty($filters['desde'])) {
            $query->whereDate('fecha', '>=', Carbon::parse($filters['desde']));
        }
        if (!empty($filters['hasta'])) {
            $query->whereDate('fecha', '<=', Carbon::parse($filters['hasta']));
        }
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }
        if (!empty($filters['barbero_id'])) {
            $query->where('barbero_id', $filters['barbero_id']);
        }
        if (!empty($filters['servicio_id'])) {
            $query->where('servicio_id', $filters['servicio_id']);
        }

        if ($vistaActual === 'hoy') {
            $query->whereDate('fecha', Carbon::today());
        } elseif ($vistaActual === 'programados') {
            $query->where('estado', 'programado');
        } elseif ($vistaActual === 'cancelados') {
            $query->where('estado', 'cancelado');
        }

        $turnos = $query->paginate(10)->withQueryString();
        $barberos = $barberia->barberos()->orderBy('nombre')->get();
        $servicios = $barberia->servicios()->orderBy('nombre')->get();
        $estados = ['programado', 'completado', 'cancelado', 'ausente'];

        $metricasTurnos = [
            'todos' => $barberia->turnos()->count(),
            'hoy' => $barberia->turnos()->whereDate('fecha', Carbon::today())->count(),
            'programados' => $barberia->turnos()->where('estado', 'programado')->count(),
            'cancelados' => $barberia->turnos()->where('estado', 'cancelado')->count(),
        ];

        $resumenEstados = $barberia->turnos()
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('turnos.index', compact(
            'turnos',
            'barberos',
            'servicios',
            'estados',
            'filters',
            'vistaActual',
            'metricasTurnos',
            'resumenEstados'
        ));
    }

    public function actualizarEstado(Request $request, Turno $turno): RedirectResponse
    {
        $barberia = Auth::user()->barberiaActiva();
        abort_unless($barberia && $turno->barberia_id === $barberia->id, 403);

        $data = $request->validate([
            'estado' => ['required', 'in:programado,completado,cancelado,ausente'],
        ]);

        $turno->update(['estado' => $data['estado']]);

        if ($turno->cliente->email) {
            $turno->loadMissing(['barberia', 'barbero', 'servicio', 'cliente']);
            Mail::to($turno->cliente->email)->send(new EstadoTurnoActualizadoMail($turno));
        }

        return back()->with('status', 'Estado del turno actualizado');
    }

    public function disponibilidad(Request $request, Barberia $barberia)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'servicio_id' => ['required', 'exists:servicios,id'],
            'barbero_id' => ['nullable', 'exists:barberos,id'],
        ]);

        $servicio = $barberia->servicios()->findOrFail($validated['servicio_id']);
        $barbero = $validated['barbero_id']
            ? $barberia->barberos()->where('activo', true)->findOrFail($validated['barbero_id'])
            : $barberia->barberos()->where('activo', true)->firstOrFail();

        $fecha = Carbon::parse($validated['fecha'])->startOfDay();
        $inicio = (clone $fecha)->setTime(9, 0);
        $fin = (clone $fecha)->setTime(20, 0);
        $duracion = $servicio->duracion_minutos;

        $turnosTomados = Turno::where('barbero_id', $barbero->id)
            ->where('fecha', $fecha->toDateString())
            ->pluck('hora')
            ->map(fn ($hora) => Carbon::createFromFormat('H:i:s', $hora)->format('H:i'))
            ->toArray();

        $disponibles = [];
        $slot = clone $inicio;
        while ($slot->copy()->addMinutes($duracion) <= $fin) {
            $hora = $slot->format('H:i');
            if (! in_array($hora, $turnosTomados, true)) {
                $disponibles[] = $hora;
            }
            $slot->addMinutes($duracion);
        }

        return response()->json([
            'barbero' => $barbero->only(['id', 'nombre']),
            'servicio' => $servicio->only(['id', 'nombre', 'duracion_minutos']),
            'fecha' => $fecha->toDateString(),
            'disponibles' => $disponibles,
        ]);
    }

    public function reservar(Request $request, Barberia $barberia)
    {
        $validated = $request->validate([
            'servicio_id' => ['required', 'exists:servicios,id'],
            'barbero_id' => ['required', 'exists:barberos,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required'],
            'cliente.nombre' => ['required', 'string', 'max:255'],
            'cliente.telefono' => ['required', 'string', 'max:50'],
            'cliente.email' => ['nullable', 'email'],
        ]);

        $servicio = $barberia->servicios()->findOrFail($validated['servicio_id']);
        $barbero = $barberia->barberos()->where('activo', true)->findOrFail($validated['barbero_id']);
        $fecha = Carbon::parse($validated['fecha'])->toDateString();
        $hora = Carbon::createFromFormat('H:i', $validated['hora'])->format('H:i:00');

        $cliente = Cliente::firstOrCreate(
            [
                'barberia_id' => $barberia->id,
                'telefono' => $validated['cliente']['telefono'],
            ],
            [
                'nombre' => $validated['cliente']['nombre'],
                'email' => $validated['cliente']['email'] ?? null,
            ]
        );

        if (! $cliente->wasRecentlyCreated) {
            $cliente->update([
                'nombre' => $validated['cliente']['nombre'],
                'email' => $validated['cliente']['email'] ?? $cliente->email,
            ]);
        }

        $yaTomado = Turno::where('barbero_id', $barbero->id)
            ->whereDate('fecha', $fecha)
            ->whereTime('hora', $hora)
            ->exists();

        if ($yaTomado) {
            return response()->json(['message' => 'El turno ya fue tomado, elegí otro horario.'], 422);
        }

        $turno = DB::transaction(function () use ($barberia, $barbero, $servicio, $cliente, $fecha, $hora) {
            return Turno::create([
                'barberia_id' => $barberia->id,
                'barbero_id' => $barbero->id,
                'servicio_id' => $servicio->id,
                'cliente_id' => $cliente->id,
                'fecha' => $fecha,
                'hora' => $hora,
                'estado' => 'programado',
            ]);
        });

        if ($cliente->email) {
            $turno->load(['barberia', 'barbero', 'servicio', 'cliente']);
            Mail::to($cliente->email)->send(new ConfirmacionTurnoMail($turno));
        }

        return response()->json([
            'message' => 'Turno reservado correctamente',
            'turno' => $turno,
        ], 201);
    }
}
