@extends('layouts.app')

@section('content')
    @php
        $colorPrimario = $barberia->color_primario ?? '#0f172a';
        $colorSecundario = $barberia->color_secundario ?? '#2563eb';
        $logoUrl = $barberia->logo_url
            ? (\Illuminate\Support\Str::startsWith($barberia->logo_url, ['http://', 'https://'])
                ? $barberia->logo_url
                : asset($barberia->logo_url))
            : null;
    @endphp

    <section class="page-hero" style="background:linear-gradient(120deg, {{ $colorPrimario }}, {{ $colorSecundario }});">
        <div style="display:flex; flex-wrap:wrap; gap:2rem; align-items:center;">
            <div style="display:flex; gap:1rem; align-items:center;">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo {{ $barberia->nombre }}" style="height:96px; width:96px; border-radius:1.5rem; object-fit:cover; background:#fff; padding:0.6rem;">
                @endif
                <div>
                    <h2 style="margin:0; font-size:2.4rem;">{{ $barberia->nombre }}</h2>
                    <p style="margin:0.4rem 0; color:#e2e8f0; font-size:1.05rem;">{{ $barberia->direccion }} · Tel: <a href="tel:{{ preg_replace('/[^\d+]/', '', $barberia->telefono) }}" style="color:#bae6fd;">{{ $barberia->telefono }}</a></p>
                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                        <span class="badge" style="background:rgba(255,255,255,0.15);">Turnos online 24/7</span>
                        <span class="badge" style="background:rgba(255,255,255,0.15);">Equipo certificado</span>
                    </div>
                </div>
            </div>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#reservar" style="background:#fff; color:{{ $colorPrimario }};">Reservar turno</a>
                <a class="btn" style="background:rgba(255,255,255,0.18); color:#fff; border:1px solid rgba(255,255,255,0.35);" href="https://wa.me/{{ preg_replace('/[^\d]/', '', $barberia->telefono) }}" target="_blank">Contactar por WhatsApp</a>
            </div>
        </div>
        @if($barberia->mensaje_bienvenida)
            <p style="margin-top:1.5rem; font-size:1.1rem; max-width:640px;">{{ $barberia->mensaje_bienvenida }}</p>
        @endif
        @if($barberia->informacion_contacto)
            <p style="margin:0; opacity:0.9;">{!! nl2br(e($barberia->informacion_contacto)) !!}</p>
        @endif
    </section>

    <section class="card" style="margin-bottom:2rem;">
        <div class="section-heading">
            <div>
                <h2 style="margin:0;">Servicios destacados</h2>
                <p style="color:#475569; margin:0;">Elegí el que mejor se adapte a vos.</p>
            </div>
        </div>
        <div class="grid grid-2">
            @foreach($barberia->servicios as $servicio)
                <article style="border:1px solid #e2e8f0; border-radius:1rem; padding:1.25rem;">
                    <h3 style="margin:0;">{{ $servicio->nombre }}</h3>
                    <p style="color:#64748b; margin:0.25rem 0;">Duración: {{ $servicio->duracion_minutos }} min</p>
                    @if($servicio->precio)
                        <p style="margin:0; font-weight:600; color:{{ $colorPrimario }};">${{ number_format($servicio->precio, 0, ',', '.') }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="card" style="margin-bottom:2rem;">
        <div class="section-heading">
            <div>
                <h2 style="margin:0;">¿Cómo funciona?</h2>
                <p style="color:#475569; margin:0;">Reservar es súper simple.</p>
            </div>
        </div>
        <div class="grid grid-2" style="gap:1.5rem;">
            <article style="background:#eff6ff; border-radius:1rem; padding:1.25rem;">
                <h3 style="margin-top:0;">1. Elegí servicio y barbero</h3>
                <p style="color:#475569;">Seleccioná el servicio y a tu barbero favorito. El sistema te muestra su agenda real.</p>
            </article>
            <article style="background:#ecfccb; border-radius:1rem; padding:1.25rem;">
                <h3 style="margin-top:0;">2. Confirmá la reserva</h3>
                <p style="color:#475569;">Completá tus datos y recibí la confirmación al instante. Te recordamos por mail.</p>
            </article>
        </div>
    </section>

    <section id="reservar" class="card" style="margin-bottom:2rem;">
        <div class="section-heading">
            <div>
                <h2 style="margin:0;">Reservá tu turno</h2>
                <p style="color:#475569; margin:0;">Tu lugar queda confirmado en segundos.</p>
            </div>
        </div>
        <div class="grid grid-2" style="margin-top:1rem;">
            <div>
                <h3>1. Elegí servicio y barbero</h3>
                <form id="form-disponibilidad">
                    <label for="servicio_id">Servicio</label>
                    <select id="servicio_id" name="servicio_id" required>
                        <option value="" selected disabled>Seleccionar…</option>
                        @foreach($barberia->servicios as $servicio)
                            <option value="{{ $servicio->id }}" data-duracion="{{ $servicio->duracion_minutos }}">
                                {{ $servicio->nombre }} ({{ $servicio->duracion_minutos }} min)
                            </option>
                        @endforeach
                    </select>

                    <label for="barbero_id" style="margin-top:1rem;">Barbero</label>
                    <select id="barbero_id" name="barbero_id">
                        @foreach($barberia->barberos as $barbero)
                            <option value="{{ $barbero->id }}">{{ $barbero->nombre }}</option>
                        @endforeach
                    </select>

                    <label for="fecha" style="margin-top:1rem;">Fecha</label>
                    <input type="date" id="fecha" name="fecha" required min="{{ now()->toDateString() }}">

                    <button type="submit" class="btn btn-primary" id="btn-disponibilidad" style="margin-top:1rem; width:100%;">
                        Consultar disponibilidad
                    </button>
                </form>

                <div id="disponibilidad" style="margin-top:1.5rem;"></div>
                <p id="disponibilidad-error" style="color:#dc2626; font-weight:600;"></p>
            </div>

            <div>
                <h3>2. Confirmá el turno</h3>
                <form id="form-turno">
                    <input type="hidden" name="servicio_id" id="turno_servicio_id">
                    <input type="hidden" name="barbero_id" id="turno_barbero_id">
                    <input type="hidden" name="fecha" id="turno_fecha">
                    <label for="hora">Hora disponible</label>
                    <select id="hora" name="hora" required>
                        <option value="" disabled selected>Primero consultá la disponibilidad</option>
                    </select>

                    <label style="margin-top:1rem;" for="cliente_nombre">Nombre</label>
                    <input type="text" id="cliente_nombre" name="cliente[nombre]" required>

                    <label style="margin-top:1rem;" for="cliente_telefono">Teléfono</label>
                    <input type="text" id="cliente_telefono" name="cliente[telefono]" required>

                    <label style="margin-top:1rem;" for="cliente_email">Email (opcional)</label>
                    <input type="email" id="cliente_email" name="cliente[email]">

                    <button type="submit" class="btn btn-primary" id="btn-reservar" style="margin-top:1.5rem; width:100%;">
                        Reservar turno
                    </button>
                </form>
                <p id="mensaje" style="margin-top:1rem; font-weight:600;"></p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
const formDisponibilidad = document.getElementById('form-disponibilidad');
const disponibilidadBox = document.getElementById('disponibilidad');
const disponibilidadError = document.getElementById('disponibilidad-error');
const horaSelect = document.getElementById('hora');
const mensaje = document.getElementById('mensaje');
const turnoServicio = document.getElementById('turno_servicio_id');
const turnoBarbero = document.getElementById('turno_barbero_id');
const turnoFecha = document.getElementById('turno_fecha');
const btnDisponibilidad = document.getElementById('btn-disponibilidad');
const btnReservar = document.getElementById('btn-reservar');

const parseJsonResponse = async (response) => {
    const contentType = response.headers.get('content-type') ?? '';
    if (contentType.includes('application/json')) {
        return response.json();
    }

    const text = await response.text();
    throw new Error(text?.trim() || 'Recibimos una respuesta inesperada del servidor. Intentalo de nuevo.');
};

const setMensaje = (texto, tipo = 'info', target = mensaje) => {
    if (!target) return;
    const colores = {
        info: '#0ea5e9',
        error: '#dc2626',
        success: '#16a34a',
    };
    target.style.color = colores[tipo] ?? '#0f172a';
    target.textContent = texto;
};

formDisponibilidad?.addEventListener('submit', async (e) => {
    e.preventDefault();
    disponibilidadBox.innerHTML = 'Buscando horarios disponibles…';
    disponibilidadError.textContent = '';
    mensaje.textContent = '';
    btnDisponibilidad.disabled = true;

    const params = new URLSearchParams(new FormData(formDisponibilidad));
    const url = '{{ route('turnos.disponibilidad', $barberia) }}' + '?' + params.toString();
    let data;

    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        if (!response.ok) {
            throw new Error('No pudimos obtener disponibilidad.');
        }
        data = await parseJsonResponse(response);
    } catch (error) {
        disponibilidadBox.innerHTML = '';
        setMensaje(error.message, 'error', disponibilidadError);
        horaSelect.innerHTML = '<option value="" disabled selected>Sin horarios</option>';
        btnDisponibilidad.disabled = false;
        return;
    }

    turnoServicio.value = data.servicio.id;
    turnoBarbero.value = data.barbero.id;
    turnoFecha.value = data.fecha;

    if (!data.disponibles.length) {
        disponibilidadBox.innerHTML = 'No hay turnos disponibles para esa fecha.';
        horaSelect.innerHTML = '<option value="" disabled selected>Sin horarios</option>';
        btnDisponibilidad.disabled = false;
        return;
    }

    disponibilidadBox.innerHTML = `Barbero: <strong>${data.barbero.nombre}</strong><br>Servicio: <strong>${data.servicio.nombre}</strong>`;
    horaSelect.innerHTML = '<option value="" disabled selected>Elegí un horario</option>';
    data.disponibles.forEach(h => {
        const option = document.createElement('option');
        option.value = h;
        option.textContent = h;
        horaSelect.appendChild(option);
    });
    btnDisponibilidad.disabled = false;
});

const formTurno = document.getElementById('form-turno');
formTurno?.addEventListener('submit', async (e) => {
    e.preventDefault();
    setMensaje('Reservando turno…', 'info');
    btnReservar.disabled = true;

    const body = new FormData(formTurno);
    try {
        const response = await fetch('{{ route('turnos.reservar', $barberia) }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body,
        });

        const data = await parseJsonResponse(response);
        if (!response.ok) {
            const errors = data.errors ? Object.values(data.errors).flat().join(' ') : data.message;
            throw new Error(errors || 'No pudimos reservar el turno.');
        }

        setMensaje('¡Turno reservado correctamente!', 'success');
        formTurno.reset();
        horaSelect.innerHTML = '<option value="" disabled selected>Primero consultá la disponibilidad</option>';
        disponibilidadBox.innerHTML = '';
    } catch (error) {
        setMensaje(error.message, 'error');
    } finally {
        btnReservar.disabled = false;
    }
});
</script>
@endpush
