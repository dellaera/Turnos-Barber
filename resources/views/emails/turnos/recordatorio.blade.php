@php
    $barberia = $turno->barberia;
    $colorPrimario = $barberia->color_primario ?? '#0f172a';
    $colorSecundario = $barberia->color_secundario ?? '#2563eb';
    $logoUrl = $barberia->logo_url;
    $fecha = $turno->fecha_hora->translatedFormat('d \d\e M');
    $hora = $turno->fecha_hora->format('H:i');
@endphp

@extends('emails.layouts.base', [
    'subject' => 'Recordatorio de turno',
    'logoUrl' => $logoUrl,
])

@section('content')
    <p style="margin:0 0 1rem; color:#0f172a;">Hola <strong>{{ $turno->cliente->nombre }}</strong>, te recordamos tu turno en <strong>{{ $barberia->nombre }}</strong>.</p>

    <div style="border:1px solid #e2e8f0; border-radius:0.75rem; padding:1rem; margin-bottom:1.25rem;">
        <p style="margin:0 0 0.5rem; font-weight:600;">Detalles</p>
        <ul style="margin:0; padding-left:1rem; color:#475569;">
            <li>Fecha: {{ $fecha }}</li>
            <li>Hora: {{ $hora }}</li>
            <li>Servicio: {{ $turno->servicio->nombre }}</li>
            <li>Barbero: {{ $turno->barbero->nombre }}</li>
        </ul>
    </div>

    <p style="margin:0 0 1.5rem; color:#0f172a;">Si necesitás reprogramar o cancelar, respondé este mensaje o escribinos por WhatsApp.</p>

    <p style="margin:0; font-weight:600;">¡Te esperamos!<br>{{ $barberia->nombre }}</p>
@endsection

@section('subcopy')
    <div style="border-left:4px solid {{ $colorSecundario }}; padding-left:0.75rem;">
        Dirección: {{ $barberia->direccion ?? 'Sin especificar' }}<br>
        Tel: {{ $barberia->telefono ?? 'Sin especificar' }}
    </div>
@endsection
