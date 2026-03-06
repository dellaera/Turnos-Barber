@extends('layouts.app')

@section('page_header')
    @if($barberia)
        <section class="page-hero">
            <div class="hero-grid">
                <div class="hero-info">
                    <div style="display:flex; gap:1rem; align-items:center;">
                        @if($barberia->logo_url)
                            <img src="{{ $barberia->logo_url }}" alt="Logo {{ $barberia->nombre }}" style="height:78px; width:78px; border-radius:1rem; object-fit:cover; background:#fff; padding:0.45rem;">
                        @endif
                        <div>
                            <h2 style="margin:0;">{{ $barberia->nombre }}</h2>
                            <p style="margin:0.25rem 0 0; color:#cbd5f5; font-size:0.95rem;">Panel interno de gestión</p>
                        </div>
                    </div>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="{{ route('turnos.index') }}">Ver agenda completa</a>
                        <a class="btn" style="background:#e2e8f0; color:#0f172a;" href="{{ route('barberias.show', $barberia) }}" target="_blank">Página pública</a>
                    </div>
                </div>
                <div class="hero-summary">
                    <div class="badge" style="align-self:flex-start;">{{ $barberia->barberos->count() }} barberos • {{ $barberia->servicios->count() }} servicios</div>
                    <div class="hero-metrics">
                        <article>
                            <span>Turnos hoy</span>
                            <strong>{{ $metrics['turnos_hoy'] }}</strong>
                        </article>
                        <article>
                            <span>Clientes únicos</span>
                            <strong>{{ $metrics['clientes_unicos'] }}</strong>
                        </article>
                        <article>
                            <span>Barberos activos</span>
                            <strong>{{ $metrics['barberos_activos'] }}</strong>
                        </article>
                    </div>
                    @if($proximoTurno)
                        @php
                            $proximoTurnoFecha = \Carbon\Carbon::parse($proximoTurno->fecha.' '.$proximoTurno->hora);
                        @endphp
                        <div style="font-size:0.95rem;">
                            <p style="margin:0; color:#cbd5f5;">Próximo turno</p>
                            <strong>{{ $proximoTurno->cliente->nombre }}</strong> · {{ $proximoTurnoFecha->translatedFormat('d M, H:i') }} con {{ $proximoTurno->barbero->nombre }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @else
        <section class="card">
            <h2 style="margin:0;">Seleccioná una barbería</h2>
            <p style="color:#475569; margin:0.25rem 0 0;">Elegí qué barbería querés administrar para acceder a las herramientas.</p>
        </section>
    @endif
@endsection

@section('content')
    @unless($barberia)
        <section class="card">
            @if($barberias->isEmpty())
                <p>No hay barberías aún. Podés crearlas desde seeds o un panel de administración.</p>
            @else
                <div class="grid grid-2">
                    @foreach($barberias as $barberiaItem)
                        <article class="subcard">
                            <h3 style="margin:0;">{{ $barberiaItem->nombre }}</h3>
                            <p style="margin:0.35rem 0; color:#64748b;">{{ $barberiaItem->direccion }}</p>
                            <p style="margin:0; font-size:0.95rem; color:#475569;">
                                {{ $barberiaItem->barberos_count }} barberos · {{ $barberiaItem->servicios_count }} servicios
                            </p>
                            <div style="margin-top:1rem;">
                                <a class="btn btn-primary" href="{{ route('barberias.show', $barberiaItem) }}" target="_blank">
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
        <div id="dashboard-panels" class="panel-container">
            <div class="panel-nav" id="dashboard-panel-tabs">
                <button class="active" data-panel-target="panel-resumen">Operaciones</button>
                <button data-panel-target="panel-turnos">Turnos recientes</button>
                <button data-panel-target="panel-clientes">Clientes</button>
                <button data-panel-target="panel-marca">Marca</button>
                <button data-panel-target="panel-equipo">Equipo</button>
                <button data-panel-target="panel-servicios">Servicios</button>
            </div>

            <div class="panel active" id="panel-resumen">
                <section class="card">
                    <div class="section-heading">
                        <div>
                            <h2 style="margin:0;">Resumen operativo</h2>
                            <p style="color:#475569; margin:0;">Un vistazo rápido a tu semana.</p>
                        </div>
                        @if($proximoTurno)
                            @php
                                $agendaTurnoFecha = \Carbon\Carbon::parse($proximoTurno->fecha.' '.$proximoTurno->hora);
                            @endphp
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
                                        <span style="color:#94a3b8;">{{ $agendaTurnoFecha->translatedFormat('d M, H:i') }} con {{ $proximoTurno->barbero->nombre }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @endif
                </section>
            </div>

            <div class="panel" id="panel-clientes">
                <section class="card">
                    <div class="section-heading">
                        <div>
                            <h2 style="margin:0;">Clientes</h2>
                            <p style="color:#475569; margin:0;">Contactos recurrentes y su actividad reciente.</p>
                        </div>
                        <div style="display:flex; gap:0.45rem; flex-wrap:wrap;">
                            <span class="badge" style="background:rgba(37,99,235,0.15); color:#1d4ed8;">Total: {{ $clienteMetrics['total'] }}</span>
                            <span class="badge" style="background:rgba(16,185,129,0.15); color:#047857;">Nuevos 30d: {{ $clienteMetrics['nuevos_30'] }}</span>
                            <span class="badge" style="background:rgba(251,146,60,0.2); color:#c2410c;">Activos 30d: {{ $clienteMetrics['activos_30'] }}</span>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('dashboard') }}" style="margin-bottom:1rem;">
                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                            <div style="flex:2; min-width:220px;">
                                <label for="clientes_buscar">Buscar</label>
                                <input type="text" id="clientes_buscar" name="clientes_buscar" value="{{ $clientesBusqueda }}" placeholder="Nombre, teléfono o email">
                            </div>
                            <div style="display:flex; align-items:flex-end; gap:0.5rem;">
                                <button class="btn btn-primary">Filtrar</button>
                                @if($clientesBusqueda)
                                    <a href="{{ route('dashboard') }}#panel-clientes" class="btn" style="background:#e2e8f0; color:#0f172a;">Limpiar</a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('clientes.store') }}" class="grid grid-2" style="gap:1rem; margin-bottom:1.25rem;">
                        @csrf
                        <div>
                            <label for="cliente_nombre">Nombre</label>
                            <input type="text" id="cliente_nombre" name="nombre" placeholder="Nuevo cliente" required>
                        </div>
                        <div>
                            <label for="cliente_telefono">Teléfono</label>
                            <input type="text" id="cliente_telefono" name="telefono" placeholder="Ej: +54 11..." required>
                        </div>
                        <div>
                            <label for="cliente_email">Email (opcional)</label>
                            <input type="email" id="cliente_email" name="email" placeholder="cliente@mail.com">
                        </div>
                        <div style="display:flex; align-items:flex-end;">
                            <button class="btn btn-primary" style="width:100%;">Agregar cliente</button>
                        </div>
                    </form>

                    @if($clientes->isEmpty())
                        <p>No hay clientes registrados todavía.</p>
                    @else
                        <div class="grid grid-2">
                            @foreach($clientes as $cliente)
                                <article class="subcard" style="display:flex; flex-direction:column; gap:0.75rem;">
                                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                        <span class="pill" style="background:#e0f2fe; color:#0369a1;">Turnos completados: {{ $cliente->turnos_completados_count }}</span>
                                        @if($cliente->ultimoTurno)
                                            <span class="pill">Última visita {{ \Carbon\Carbon::parse($cliente->ultimoTurno->fecha)->translatedFormat('d M') }}</span>
                                        @else
                                            <span class="pill" style="background:#fee2e2; color:#b91c1c;">Sin visitas</span>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('clientes.update', $cliente) }}" class="grid" style="gap:0.75rem;">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label>Nombre</label>
                                            <input type="text" name="nombre" value="{{ $cliente->nombre }}" required>
                                        </div>
                                        <div>
                                            <label>Teléfono</label>
                                            <input type="text" name="telefono" value="{{ $cliente->telefono }}" required>
                                        </div>
                                        <div>
                                            <label>Email</label>
                                            <input type="email" name="email" value="{{ $cliente->email }}">
                                        </div>
                                        <p style="margin:0; color:#475569; font-size:0.9rem;">
                                            @if($cliente->ultimoTurno)
                                                Último turno: {{ \Carbon\Carbon::parse($cliente->ultimoTurno->fecha)->translatedFormat('d M') }} · {{ \Carbon\Carbon::parse($cliente->ultimoTurno->hora)->format('H:i') }} con {{ $cliente->ultimoTurno->barbero->nombre }}
                                            @else
                                                Sin turnos registrados aún.
                                            @endif
                                        </p>
                                        <div style="display:flex; gap:0.5rem;">
                                            <button class="btn btn-primary" style="flex:1;">Guardar</button>
                                            <button form="delete-cliente-{{ $cliente->id }}" class="btn" style="flex:1; background:#fee2e2; color:#991b1b;">Eliminar</button>
                                        </div>
                                    </form>
                                    <form id="delete-cliente-{{ $cliente->id }}" method="POST" action="{{ route('clientes.destroy', $cliente) }}" onsubmit="return confirm('¿Eliminar al cliente {{ $cliente->nombre }}? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </article>
                            @endforeach
                        </div>
                        <div style="margin-top:1rem;">
                            {{ $clientes->withQueryString()->links() }}
                        </div>
                    @endif
                </section>
            </div>

            <div class="panel" id="panel-turnos">
                <section class="card">
                    <div class="section-heading">
                        <div>
                            <h2 style="margin:0;">Turnos recientes</h2>
                            <p style="color:#475569; margin:0;">Últimos movimientos de tu agenda (solo reservas recientes).</p>
                        </div>
                        <a href="{{ route('turnos.index') }}" class="btn btn-primary" style="background:#f97316; border:none;">Ir a la agenda completa</a>
                    </div>

                    @if($turnos->isEmpty())
                        <p>No hay turnos registrados.</p>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($turnos as $turno)
                                        <tr>
                                            <td>
                                                <strong>{{ $turno->cliente->nombre }}</strong><br>
                                                <span style="color:#64748b; font-size:0.9rem;">{{ $turno->cliente->telefono }}</span>
                                            </td>
                                            <td>{{ $turno->servicio->nombre }}</td>
                                            <td>{{ $turno->barbero->nombre }}</td>
                                            <td>{{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}</td>
                                            <td><span class="pill">{{ ucfirst($turno->estado) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>

            <div class="panel" id="panel-marca">
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
            </div>

            <div class="panel" id="panel-equipo">
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
            </div>

            <div class="panel" id="panel-servicios">
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

@if($barberia)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const buttons = document.querySelectorAll('#dashboard-panel-tabs button');
                const panels = document.querySelectorAll('#dashboard-panels .panel');
                buttons.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const target = btn.getAttribute('data-panel-target');
                        buttons.forEach((b) => b.classList.remove('active'));
                        panels.forEach((panel) => panel.classList.remove('active'));
                        btn.classList.add('active');
                        const panel = document.getElementById(target);
                        panel?.classList.add('active');
                    });
                });
            });
        </script>
    @endpush
@endif
