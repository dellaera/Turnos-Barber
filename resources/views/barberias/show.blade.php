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
    <section class="card" style="border-top:6px solid {{ $colorPrimario }};">
        <a href="{{ route('dashboard') }}" style="color:#64748b; font-size:0.9rem">← Volver al dashboard</a>

        <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center; margin:1rem 0 0;">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo {{ $barberia->nombre }}" style="height:72px; object-fit:contain;">
            @endif
            <div>
                <h2 style="margin:0; color:{{ $colorPrimario }};">{{ $barberia->nombre }}</h2>
                <p style="margin:0.2rem 0 0; color:#475569;">{{ $barberia->direccion }} · Tel: {{ $barberia->telefono }}</p>
            </div>
        </div>

        @if($barberia->mensaje_bienvenida)
            <div style="background:{{ $colorSecundario }}15; border-radius:1rem; padding:1rem 1.25rem; margin-top:1.25rem; color:{{ $colorPrimario }};">
                {{ $barberia->mensaje_bienvenida }}
            </div>
        @endif

        @if($barberia->informacion_contacto)
            <div style="margin-top:1rem; color:#475569;">
                <strong>Información adicional:</strong><br>
                {!! nl2br(e($barberia->informacion_contacto)) !!}
            </div>
        @endif

        <div class="grid grid-2" style="margin-top:2rem;">
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
