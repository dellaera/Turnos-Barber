<?php

namespace App\Http\Controllers;

use App\Models\Barberia;
use App\Models\Cliente;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TurnoController extends Controller
{
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
            ['telefono' => $validated['cliente']['telefono']],
            [
                'nombre' => $validated['cliente']['nombre'],
                'email' => $validated['cliente']['email'] ?? null,
            ]
        );

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
                'estado' => 'reservado',
            ]);
        });

        return response()->json([
            'message' => 'Turno reservado correctamente',
            'turno' => $turno,
        ], 201);
    }
}
