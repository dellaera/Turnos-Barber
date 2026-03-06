<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Turnos Barber' }}</title>
</head>
<body style="font-family:'Inter',Arial,sans-serif; background:#f1f5f9; margin:0; padding:0;">
<div style="max-width:600px; margin:0 auto; padding:2rem 1rem;">
    <div style="background:#fff; border-radius:1rem; padding:2rem; box-shadow:0 20px 40px rgba(15,23,42,0.08);">
        @isset($logoUrl)
            <div style="text-align:center; margin-bottom:1.25rem;">
                <img src="{{ $logoUrl }}" alt="Logo" style="max-height:80px;">
            </div>
        @endisset

        @yield('content')
    </div>

    <div style="max-width:600px; margin:1.25rem auto 0; font-size:0.9rem; color:#475569;">
        @yield('subcopy')
    </div>
</div>
</body>
</html>
