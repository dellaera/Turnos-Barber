@extends('layouts.app')

@section('page_header')
    <section class="card">
        <div class="section-heading">
            <div>
                <h2 style="margin:0;">Turnos</h2>
                <p style="color:#475569; margin:0;">Administrá la agenda con filtros, estados y acciones rápidas.</p>
            </div>
            <a class="btn" style="background:#f1f5f9;" href="{{ route('dashboard') }}">← Volver al dashboard</a>
        </div>
    </section>
@endsection

@section('content')
    <section class="card">
        <form method="GET" class="grid grid-2" style="gap:1rem;">
            <div>
                <label>Desde</label>
                <input type="date" name="desde" value="{{ $filters['desde'] ?? '' }}">
            </div>
            <div>
                <label>Hasta</label>
                <input type="date" name="hasta" value="{{ $filters['hasta'] ?? '' }}">
            </div>
            <div>
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado }}" {{ ($filters['estado'] ?? '') === $estado ? 'selected' : '' }}>
                            {{ ucfirst($estado) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Barbero</label>
                <select name="barbero_id">
                    <option value="">Todos</option>
                    @foreach($barberos as $barbero)
                        <option value="{{ $barbero->id }}" {{ ($filters['barbero_id'] ?? '') == $barbero->id ? 'selected' : '' }}>
                            {{ $barbero->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Servicio</label>
                <select name="servicio_id">
                    <option value="">Todos</option>
                    @foreach($servicios as $servicio)
                        <option value="{{ $servicio->id }}" {{ ($filters['servicio_id'] ?? '') == $servicio->id ? 'selected' : '' }}>
                            {{ $servicio->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; align-items:flex-end; gap:0.75rem;">
                <button class="btn btn-primary" style="flex:1;">Filtrar</button>
                <a href="{{ route('turnos.index') }}" class="btn" style="background:#e2e8f0; color:#0f172a;">Limpiar</a>
            </div>
        </form>
    </section>

    <section class="card">
        @if($turnos->isEmpty())
            <p>No hay turnos con los criterios seleccionados.</p>
        @else
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Barbero</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                            <th>Cambiar estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($turnos as $turno)
                            <tr style="border-bottom:1px solid #e2e8f0;">
                                <td style="padding:0.7rem 0;">
                                    <strong>{{ $turno->cliente->nombre }}</strong><br>
                                    <small style="color:#64748b;">{{ $turno->cliente->telefono }}</small>
                                </td>
                                <td>{{ $turno->servicio->nombre }}</td>
                                <td>{{ $turno->barbero->nombre }}</td>
                                <td>{{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}</td>
                                <td>
                                    <span style="background:#e0f2fe; color:#0369a1; padding:0.2rem 0.6rem; border-radius:999px; font-size:0.85rem;">
                                        {{ ucfirst($turno->estado) }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('turnos.actualizar-estado', $turno) }}" style="display:flex; gap:0.5rem; align-items:center;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="estado" style="flex:1;">
                                            @foreach($estados as $estado)
                                                <option value="{{ $estado }}" {{ $turno->estado === $estado ? 'selected' : '' }}>
                                                    {{ ucfirst($estado) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary">Actualizar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:1rem;">
                {{ $turnos->links() }}
            </div>
        @endif
    </section>
@endsection
