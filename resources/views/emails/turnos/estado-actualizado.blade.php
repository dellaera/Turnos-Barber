@php
    $barberia = $turno->barberia;
    $colorPrimario = $barberia->color_primario ?? '#0f172a';
    $colorSecundario = $barberia->color_secundario ?? '#2563eb';
@endphp

<x-mail::message>
@if($barberia->logo_url)
<div style="text-align:center; margin-bottom:1rem;">
    <img src="{{ $barberia->logo_url }}" alt="Logo {{ $barberia->nombre }}" style="max-height:80px;">
</div>
@endif

<h1 style="color:{{ $colorPrimario }};">Actualizamos tu turno</h1>

Hola {{ $turno->cliente->nombre }}, el estado de tu turno pasó a **{{ ucfirst($turno->estado) }}**.

**Resumen**
- Barbería: {{ $barberia->nombre }}
- Servicio: {{ $turno->servicio->nombre }}
- Barbero: {{ $turno->barbero->nombre }}
- Fecha y hora: {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}

@if($barberia->mensaje_bienvenida)
> {{ $barberia->mensaje_bienvenida }}
@endif

Si necesitás más información, respondé este correo o contactate con la barbería.

Gracias,
{{ $barberia->nombre }}

<x-mail::subcopy>
<div style="border-left:4px solid {{ $colorSecundario }}; padding-left:0.75rem;">
    Dirección: {{ $barberia->direccion ?? 'Sin especificar' }}<br>
    Tel: {{ $barberia->telefono ?? 'Sin especificar' }}<br>
    @if($barberia->informacion_contacto)
        {!! nl2br(e($barberia->informacion_contacto)) !!}
    @endif
</div>
</x-mail::subcopy>
</x-mail::message>
