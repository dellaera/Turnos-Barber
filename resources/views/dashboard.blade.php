@extends('layouts.app')

@section('content')
    @unless($barberia)
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
                    @foreach($barberias as $barberiaItem)
                        <article style="border:1px solid #e2e8f0; border-radius:1rem; padding:1rem; background:#fff;">
                            <h3 style="margin:0;">{{ $barberiaItem->nombre }}</h3>
                            <p style="margin:0.35rem 0; color:#64748b;">{{ $barberiaItem->direccion }}</p>
                            <p style="margin:0; font-size:0.95rem; color:#475569;">
                                {{ $barberiaItem->barberos_count }} barberos · {{ $barberiaItem->servicios_count }} servicios
                            </p>
                            <div style="margin-top:1rem;">
                                <a class="btn btn-primary" href="{{ route('barberias.show', $barberiaItem) }}">
                                    Ver página pública
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @endunless

    @if($barberia)
        <section class="page-hero">
            <div style="display:flex; flex-wrap:wrap; gap:1.5rem; align-items:center;">
                <div style="display:flex; gap:1rem; align-items:center;">
                    @if($barberia->logo_url)
                        <img src="{{ $barberia->logo_url }}" alt="Logo {{ $barberia->nombre }}" style="height:72px; width:72px; border-radius:1rem; object-fit:cover; background:#fff; padding:0.4rem;">
                    @endif
                    <div>
                        <div class="badge">{{ $barberia->barberos->count() }} barberos • {{ $barberia->servicios->count() }} servicios</div>
                        <h2>{{ $barberia->nombre }}</h2>
                        <p style="margin:0.35rem 0; color:#cbd5f5;">{{ $barberia->direccion }} · {{ $barberia->telefono }}</p>
                    </div>
                </div>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('turnos.index') }}">Ver agenda</a>
                    <a class="btn" style="background:#e2e8f0; color:#0f172a;" href="{{ route('barberias.show', $barberia) }}" target="_blank">Página pública</a>
                    <a class="btn" style="background:#334155; color:#e2e8f0;" href="#personalizacion">Editar marca</a>
                </div>
            </div>
        </section>

        <div class="dashboard-layout">
            <div class="dashboard-column">
                <section class="card">
                    <div class="section-heading">
                        <div>
                            <h2 style="margin:0;">Resumen operativo</h2>
                            <p style="color:#475569; margin:0;">Un vistazo rápido a tu semana.</p>
                        </div>
                        @if($proximoTurno)
                            <span class="badge">Próximo turno: {{ \Carbon\Carbon::parse($proximoTurno->fecha)->translatedFormat('d M') }} {{ \Carbon\Carbon::parse($proximoTurno->hora)->format('H:i') }}</span>
                        @endif
                    </div>
                    <div class="stats-grid">
                        <article class="stat-card">
                            <span>Turnos esta semana</span>
                            <strong>{{ $metrics['turnos_semana'] }}</strong>
                        </article>
                        <article class="stat-card">
                            <span>Turnos hoy</span>
                            <strong>{{ $metrics['turnos_hoy'] }}</strong>
                        </article>
                        <article class="stat-card">
                            <span>Clientes únicos</span>
                            <strong>{{ $metrics['clientes_unicos'] }}</strong>
                        </article>
                        <article class="stat-card">
                            <span>Barberos activos</span>
                            <strong>{{ $metrics['barberos_activos'] }}</strong>
                        </article>
                    </div>
                    @if($proximoTurno)
                        <div style="margin-top:1.5rem;">
                            <h3 style="margin-bottom:0.5rem;">Agenda inmediata</h3>
                            <ul class="timeline">
                                <li>
                                    <div>
                                        <strong>{{ $proximoTurno->cliente->nombre }}</strong> — {{ $proximoTurno->servicio->nombre }}<br>
                                        <span style="color:#94a3b8;">{{ \Carbon\Carbon::parse($proximoTurno->fecha)->translatedFormat('d M, H:i') }} con {{ $proximoTurno->barbero->nombre }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @endif
                </section>

                <section id="turnos-recientes" class="card">
                    <div class="section-heading">
                        <div>
                            <h2 style="margin:0;">Turnos recientes</h2>
                            <p style="color:#475569; margin:0;">Últimos movimientos de tu agenda.</p>
                        </div>
                        <a href="{{ route('turnos.index') }}" class="btn" style="background:#e2e8f0; color:#0f172a;">Ver más</a>
                    </div>

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
            </div>

            <div class="dashboard-column">
                <section id="personalizacion" class="card">
                    <h2 style="margin-top:0;">Marca y comunicación</h2>
                    <p style="color:#475569;">Todo lo que tus clientes ven en la web y en los correos.</p>

                    <div style="background:#eff6ff; border-radius:0.85rem; padding:0.85rem 1rem; margin-bottom:1rem; color:#1d4ed8;">
                        Actualizá logos, colores y mensajes para personalizar la landing pública y las notificaciones.
                    </div>

                    <form method="POST" action="{{ route('barberia.update') }}" class="grid grid-2" style="gap:1rem;" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="logo_url">Logo (URL)</label>
                            <input type="url" id="logo_url" name="logo_url" value="{{ old('logo_url', $barberia->logo_url) }}" placeholder="https://...logo.png">
                        </div>
                        <div>
                            <label for="logo_file">Logo (archivo)</label>
                            <input type="file" id="logo_file" name="logo_file" accept="image/*">
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

                <section id="barberos" class="card">
                    <h2 style="margin-top:0;">Equipo de barberos</h2>
                    <p style="color:#475569;">Sumá o editá perfiles en minutos.</p>

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
                                    <form id="delete-barbero-{{ $barbero->id }}" method="POST" action="{{ route('barberos.destroy', $barbero) }}" onsubmit="return confirm('¿Eliminar al barbero {{ $barbero->nombre }}? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section id="servicios" class="card">
                    <h2 style="margin-top:0;">Catálogo de servicios</h2>
                    <p style="color:#475569;">Actualizá precios y duraciones para sincronizar la agenda.</p>

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
                                    <form id="delete-servicio-{{ $servicio->id }}" method="POST" action="{{ route('servicios.destroy', $servicio) }}" onsubmit="return confirm('¿Eliminar el servicio {{ $servicio->nombre }}? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </div>
    @endif
@endsection
