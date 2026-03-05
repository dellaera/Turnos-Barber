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

    @if($barberia)
        <section class="card" style="margin-top:2rem;">
            <h2 style="margin-top:0;">Personalización</h2>
            <p style="color:#475569;">Mostrá tu marca en la página pública.</p>

            <form method="POST" action="{{ route('barberia.update') }}" class="grid grid-2" style="gap:1rem;">
                @csrf
                @method('PATCH')
                <div>
                    <label for="logo_url">Logo (URL)</label>
                    <input type="url" id="logo_url" name="logo_url" value="{{ old('logo_url', $barberia->logo_url) }}" placeholder="https://...logo.png">
                </div>
                <div>
                    <label for="color_primario">Color primario</label>
                    <input type="text" id="color_primario" name="color_primario" value="{{ old('color_primario', $barberia->color_primario) }}" placeholder="#2563eb">
                </div>
                <div>
                    <label for="color_secundario">Color secundario</label>
                    <input type="text" id="color_secundario" name="color_secundario" value="{{ old('color_secundario', $barberia->color_secundario) }}" placeholder="#3b82f6">
                </div>
                <div>
                    <label for="mensaje_bienvenida">Mensaje de bienvenida</label>
                    <textarea id="mensaje_bienvenida" name="mensaje_bienvenida" rows="3">{{ old('mensaje_bienvenida', $barberia->mensaje_bienvenida) }}</textarea>
                </div>
                <div class="grid" style="gap:0.5rem; grid-column:span 2;">
                    <label for="informacion_contacto">Información de contacto</label>
                    <textarea id="informacion_contacto" name="informacion_contacto" rows="3" placeholder="WhatsApp, dirección, instrucciones...">{{ old('informacion_contacto', $barberia->informacion_contacto) }}</textarea>
                </div>
                <div style="grid-column:span 2; text-align:right;">
                    <button class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </section>
    @endif

    <section class="card" style="margin-top:2rem;">
        <h2 style="margin-top:0;">Turnos recientes</h2>
        <p style="color:#475569;">Últimos turnos reservados en tu barbería.</p>

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

    @if($barberia)
        <section class="card" style="margin-top:2rem;">
            <h2 style="margin-top:0;">Barberos</h2>
            <p style="color:#475569;">Gestioná el equipo de tu barbería.</p>

            <form method="POST" action="{{ route('barberos.store') }}" class="grid grid-2" style="gap:1rem; margin-bottom:1.5rem;">
                @csrf
                <div>
                    <label for="nuevo_barbero">Nombre</label>
                    <input type="text" id="nuevo_barbero" name="nombre" placeholder="Nuevo barbero" required>
                </div>
                <div style="display:flex; align-items:flex-end;">
                    <button class="btn btn-primary" style="width:100%;">Agregar barbero</button>
                </div>
            </form>

            @if($barberos->isEmpty())
                <p>No hay barberos cargados.</p>
            @else
                <div class="grid grid-2">
                    @foreach($barberos as $barbero)
                        <article style="border:1px solid #e2e8f0; border-radius:1rem; padding:1rem; background:#fff;">
                            <form method="POST" action="{{ route('barberos.update', $barbero) }}" class="grid" style="gap:0.75rem;">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{ $barbero->nombre }}" required>
                                </div>
                                <label style="display:flex; gap:0.5rem; align-items:center;">
                                    <input type="checkbox" name="activo" value="1" {{ $barbero->activo ? 'checked' : '' }}>
                                    Activo
                                </label>
                                <div style="display:flex; gap:0.5rem;">
                                    <button class="btn btn-primary" style="flex:1;">Guardar</button>
                                    <button form="delete-barbero-{{ $barbero->id }}" class="btn" style="flex:1; background:#fee2e2; color:#991b1b;">Eliminar</button>
                                </div>
                            </form>
                            <form id="delete-barbero-{{ $barbero->id }}" method="POST" action="{{ route('barberos.destroy', $barbero) }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="card" style="margin-top:2rem;">
            <h2 style="margin-top:0;">Servicios</h2>
            <p style="color:#475569;">Configurá la lista de servicios disponibles.</p>

            <form method="POST" action="{{ route('servicios.store') }}" class="grid grid-2" style="gap:1rem; margin-bottom:1.5rem;">
                @csrf
                <div>
                    <label for="servicio_nombre">Nombre</label>
                    <input type="text" id="servicio_nombre" name="nombre" placeholder="Nuevo servicio" required>
                </div>
                <div>
                    <label for="servicio_duracion">Duración (min)</label>
                    <input type="number" id="servicio_duracion" name="duracion_minutos" min="5" max="240" required>
                </div>
                <div>
                    <label for="servicio_precio">Precio (opcional)</label>
                    <input type="number" step="0.01" id="servicio_precio" name="precio" placeholder="0">
                </div>
                <div style="display:flex; align-items:flex-end;">
                    <button class="btn btn-primary" style="width:100%;">Agregar servicio</button>
                </div>
            </form>

            @if($servicios->isEmpty())
                <p>No hay servicios configurados.</p>
            @else
                <div class="grid grid-2">
                    @foreach($servicios as $servicio)
                        <article style="border:1px solid #e2e8f0; border-radius:1rem; padding:1rem; background:#fff;">
                            <form method="POST" action="{{ route('servicios.update', $servicio) }}" class="grid" style="gap:0.75rem;">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{ $servicio->nombre }}" required>
                                </div>
                                <div>
                                    <label>Duración (min)</label>
                                    <input type="number" name="duracion_minutos" min="5" max="240" value="{{ $servicio->duracion_minutos }}" required>
                                </div>
                                <div>
                                    <label>Precio</label>
                                    <input type="number" step="0.01" name="precio" value="{{ $servicio->precio }}">
                                </div>
                                <div style="display:flex; gap:0.5rem;">
                                    <button class="btn btn-primary" style="flex:1;">Guardar</button>
                                    <button form="delete-servicio-{{ $servicio->id }}" class="btn" style="flex:1; background:#fee2e2; color:#991b1b;">Eliminar</button>
                                </div>
                            </form>
                            <form id="delete-servicio-{{ $servicio->id }}" method="POST" action="{{ route('servicios.destroy', $servicio) }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
@endsection
