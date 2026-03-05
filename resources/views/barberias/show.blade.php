@extends('layouts.app')

@section('content')
    <section class="card">
        <a href="{{ route('dashboard') }}" style="color:#64748b; font-size:0.9rem">← Volver al dashboard</a>
        <h2 style="margin:0.3rem 0 0;">{{ $barberia->nombre }}</h2>
        <p style="margin:0; color:#475569;">{{ $barberia->direccion }} · Tel: {{ $barberia->telefono }}</p>

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

                    <button type="submit" class="btn btn-primary" style="margin-top:1rem; width:100%;">
                        Consultar disponibilidad
                    </button>
                </form>

                <div id="disponibilidad" style="margin-top:1.5rem;"></div>
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

                    <button type="submit" class="btn btn-primary" style="margin-top:1.5rem; width:100%;">
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
const horaSelect = document.getElementById('hora');
const mensaje = document.getElementById('mensaje');
const turnoServicio = document.getElementById('turno_servicio_id');
const turnoBarbero = document.getElementById('turno_barbero_id');
const turnoFecha = document.getElementById('turno_fecha');

formDisponibilidad?.addEventListener('submit', async (e) => {
    e.preventDefault();
    disponibilidadBox.innerHTML = 'Buscando horarios disponibles…';
    mensaje.textContent = '';

    const params = new URLSearchParams(new FormData(formDisponibilidad));
    const url = '{{ route('turnos.disponibilidad', $barberia) }}' + '?' + params.toString();

    const response = await fetch(url);
    if (!response.ok) {
        disponibilidadBox.innerHTML = 'No pudimos obtener disponibilidad. Revisá los datos.';
        return;
    }

    const data = await response.json();
    turnoServicio.value = data.servicio.id;
    turnoBarbero.value = data.barbero.id;
    turnoFecha.value = data.fecha;

    if (!data.disponibles.length) {
        disponibilidadBox.innerHTML = 'No hay turnos disponibles para esa fecha.';
        horaSelect.innerHTML = '<option value="" disabled selected>Sin horarios</option>';
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
});

const formTurno = document.getElementById('form-turno');
formTurno?.addEventListener('submit', async (e) => {
    e.preventDefault();
    mensaje.textContent = 'Reservando turno…';

    const body = new FormData(formTurno);
    const response = await fetch('{{ route('turnos.reservar', $barberia) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body,
    });

    const data = await response.json();
    if (!response.ok) {
        mensaje.style.color = '#dc2626';
        mensaje.textContent = data.message ?? 'No pudimos reservar el turno.';
        return;
    }

    mensaje.style.color = '#16a34a';
    mensaje.textContent = '¡Turno reservado correctamente!';
    formTurno.reset();
    horaSelect.innerHTML = '<option value="" disabled selected>Primero consultá la disponibilidad</option>';
});
</script>
@endpush
