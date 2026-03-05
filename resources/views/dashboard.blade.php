@extends('layouts.app')

@section('content')
    <section class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
            <div>
                <h2 style="margin:0;">Barberías</h2>
                <p style="color:#475569;">Listado de barberías dadas de alta en el sistema.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn" style="background:#f1f5f9; color:#0f172a;">Cerrar sesión</button>
            </form>
        </div>

        @if($barberias->isEmpty())
            <p>No hay barberías aún. Podés crearlas desde seeds o un panel de administración.</p>
        @else
            <div class="grid grid-2" style="margin-top:1.5rem;">
                @foreach($barberias as $barberia)
                    <article style="border:1px solid #e2e8f0; border-radius:1rem; padding:1rem; background:#fff;">
                        <h3 style="margin:0;">{{ $barberia->nombre }}</h3>
                        <p style="margin:0.35rem 0; color:#64748b;">{{ $barberia->direccion }}</p>
                        <p style="margin:0; font-size:0.95rem; color:#475569;">
                            {{ $barberia->barberos_count }} barberos · {{ $barberia->servicios_count }} servicios
                        </p>
                        <div style="margin-top:1rem;">
                            <a class="btn btn-primary" href="{{ route('barberias.show', $barberia) }}">
                                Ver página pública
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="card" style="margin-top:2rem;">
        <h2 style="margin-top:0;">Turnos recientes</h2>
        <p style="color:#475569;">Últimos turnos reservados en tu barbería.</p>

        @php
            $turnos = \App\Models\Turno::with(['cliente', 'servicio', 'barbero'])
                ->latest()
                ->take(5)
                ->get();
        @endphp

        @if($turnos->isEmpty())
            <p>No hay turnos registrados.</p>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left; border-bottom:1px solid #e2e8f0;">
                            <th style="padding:0.75rem 0;">Cliente</th>
                            <th>Servicio</th>
                            <th>Barbero</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($turnos as $turno)
                            <tr style="border-bottom:1px solid #e2e8f0;">
                                <td style="padding:0.65rem 0;">
                                    <strong>{{ $turno->cliente->nombre }}</strong><br>
                                    <span style="color:#64748b; font-size:0.9rem;">{{ $turno->cliente->telefono }}</span>
                                </td>
                                <td>{{ $turno->servicio->nombre }}</td>
                                <td>{{ $turno->barbero->nombre }}</td>
                                <td>{{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}</td>
                                <td><span style="background:#e0f2fe; color:#0369a1; padding:0.2rem 0.6rem; border-radius:999px; font-size:0.85rem;">{{ ucfirst($turno->estado) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
