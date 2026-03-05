<x-mail::message>
# ¡Tu turno está confirmado!

Hola {{ $turno->cliente->nombre }}, confirmamos tu reserva en **{{ $turno->barberia->nombre }}**.

**Detalles del turno**
- Fecha: {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}
- Hora: {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}
- Servicio: {{ $turno->servicio->nombre }}
- Barbero: {{ $turno->barbero->nombre }}

Si necesitás reprogramar, respondé este correo o contactate con la barbería.

Gracias por elegirnos,
{{ $turno->barberia->nombre }}

<x-mail::subcopy>
Dirección: {{ $turno->barberia->direccion }} | Tel: {{ $turno->barberia->telefono }}
</x-mail::subcopy>
</x-mail::message>
