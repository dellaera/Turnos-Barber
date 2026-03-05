@php
    $barberia = $turno->barberia;
    $colorPrimario = $barberia->color_primario ?? '#0f172a';
    $colorSecundario = $barberia->color_secundario ?? '#2563eb';
@endphp

<div style="font-family: 'Inter', sans-serif; background:#f1f5f9; padding:2rem;">
    <div style="max-width:600px; margin:0 auto; background:#fff; border-radius:1rem; padding:2rem; box-shadow:0 20px 40px rgba(15,23,42,0.08);">
        @if($barberia->logo_url)
            <div style="text-align:center; margin-bottom:1.5rem;">
                <img src="{{ $barberia->logo_url }}" alt="Logo {{ $barberia->nombre }}" style="max-height:80px;">
            </div>
        @endif

        <h1 style="color:{{ $colorPrimario }}; margin:0 0 1rem;">Actualizamos tu turno</h1>

        <p style="margin:0 0 1rem; color:#0f172a;">Hola <strong>{{ $turno->cliente->nombre }}</strong>, el estado de tu turno pasó a <strong>{{ ucfirst($turno->estado) }}</strong>.</p>

        <div style="border:1px solid #e2e8f0; border-radius:0.75rem; padding:1rem; margin-bottom:1.25rem;">
            <p style="margin:0 0 0.5rem; font-weight:600;">Resumen</p>
            <ul style="margin:0; padding-left:1rem; color:#475569;">
                <li>Barbería: <strong>{{ $barberia->nombre }}</strong></li>
                <li>Servicio: {{ $turno->servicio->nombre }}</li>
                <li>Barbero: {{ $turno->barbero->nombre }}</li>
                <li>Fecha y hora: {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}</li>
            </ul>
        </div>

        @if($barberia->mensaje_bienvenida)
            <blockquote style="margin:0 0 1.5rem; padding:1rem; border-left:4px solid {{ $colorSecundario }}; background:#f8fafc; color:#0f172a;">{{ $barberia->mensaje_bienvenida }}</blockquote>
        @endif

        <p style="margin:0 0 1.5rem; color:#0f172a;">Si necesitás más información, respondé este correo o contactate con la barbería.</p>

        <p style="margin:0; font-weight:600;">Gracias,<br>{{ $barberia->nombre }}</p>
    </div>

    <div style="max-width:600px; margin:1rem auto 0; font-size:0.9rem; color:#475569;">
        <div style="border-left:4px solid {{ $colorSecundario }}; padding-left:0.75rem;">
            Dirección: {{ $barberia->direccion ?? 'Sin especificar' }}<br>
            Tel: {{ $barberia->telefono ?? 'Sin especificar' }}<br>
            @if($barberia->informacion_contacto)
                {!! nl2br(e($barberia->informacion_contacto)) !!}
            @endif
        </div>
    </div>
</div>
