@php
    $barberia = $turno->barberia;
    $colorPrimario = $barberia->color_primario ?? '#0f172a';
    $colorSecundario = $barberia->color_secundario ?? '#2563eb';
    $logoUrl = $barberia->logo_url;
@endphp

@extends('emails.layouts.base', [
    'subject' => 'Confirmación de turno',
    'logoUrl' => $logoUrl,
])

@section('content')
    <h1 style="color:{{ $colorPrimario }}; margin:0 0 1rem;">¡Tu turno está confirmado!</h1>

    <p style="margin:0 0 1rem; color:#0f172a;">Hola <strong>{{ $turno->cliente->nombre }}</strong>, confirmamos tu reserva en <strong>{{ $barberia->nombre }}</strong>.</p>

    @if($barberia->mensaje_bienvenida)
        <blockquote style="margin:0 0 1.5rem; padding:1rem; border-left:4px solid {{ $colorSecundario }}; background:#f8fafc; color:#0f172a;">{{ $barberia->mensaje_bienvenida }}</blockquote>
    @endif

    <div style="border:1px solid #e2e8f0; border-radius:0.75rem; padding:1rem; margin-bottom:1.25rem;">
        <p style="margin:0 0 0.5rem; font-weight:600;">Detalles del turno</p>
        <ul style="margin:0; padding-left:1rem; color:#475569;">
            <li>Fecha: {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</li>
            <li>Hora: {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}</li>
            <li>Servicio: {{ $turno->servicio->nombre }}</li>
            <li>Barbero: {{ $turno->barbero->nombre }}</li>
        </ul>
    </div>

    <p style="margin:0 0 1.5rem; color:#0f172a;">Si necesitás reprogramar, respondé este correo o contactate con la barbería.</p>

    <p style="margin:0; font-weight:600;">Gracias,<br>{{ $barberia->nombre }}</p>
@endsection

@section('subcopy')
    <div style="border-left:4px solid {{ $colorSecundario }}; padding-left:0.75rem;">
        Dirección: {{ $barberia->direccion ?? 'Sin especificar' }}<br>
        Tel: {{ $barberia->telefono ?? 'Sin especificar' }}<br>
        @if($barberia->informacion_contacto)
            {!! nl2br(e($barberia->informacion_contacto)) !!}
        @endif
    </div>
@endsection
