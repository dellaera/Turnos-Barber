<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Turnos Barber' }}</title>
    <style>
        :root {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #0f172a;
            background: #f8fafc;
        }
        body {
            margin: 0;
            min-height: 100vh;
            background: #f8fafc;
        }
        header {
            background: #0f172a;
            color: #fff;
            padding: 1rem 2rem;
        }
        header .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        header h1 {
            margin: 0;
            font-size: 1.25rem;
        }
        nav {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        nav a {
            color: #e2e8f0;
            font-weight: 600;
        }
        nav form {
            margin: 0;
        }
        nav button {
            background: transparent;
            border: 1px solid rgba(226, 232, 240, 0.4);
            border-radius: 999px;
            color: #e2e8f0;
            padding: 0.4rem 1rem;
            cursor: pointer;
        }
        nav select {
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.4);
            background: rgba(15, 23, 42, 0.25);
            color: #e2e8f0;
            padding: 0.35rem 0.85rem;
            font-weight: 600;
        }
        main {
            max-width: 960px;
            margin: 2rem auto;
            padding: 0 1.5rem 3rem;
        }
        .card {
            background: #fff;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        }
        .page-hero {
            background: linear-gradient(120deg, #0f172a, #1e293b);
            color: #e2e8f0;
            border-radius: 1.25rem;
            padding: 2rem 2.5rem;
            box-shadow: 0 20px 35px rgba(15, 23, 42, 0.4);
            margin-bottom: 2rem;
        }
        .page-hero h2 {
            margin: 0.25rem 0 0;
            font-size: 2rem;
        }
        .page-hero .hero-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.15);
            color: #e2e8f0;
            font-size: 0.9rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 1rem;
        }
        .stat-card {
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 1rem;
            padding: 1.25rem;
        }
        .stat-card span {
            display: block;
            font-size: 0.9rem;
            color: #cbd5f5;
        }
        .stat-card strong {
            font-size: 2rem;
            font-weight: 700;
        }
        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .timeline {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .timeline li {
            display: flex;
            gap: 0.75rem;
            padding: 0.75rem 0;
        }
        .timeline li::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #2563eb;
            margin-top: 0.4rem;
        }
        a {
            color: #2563eb;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            border-radius: 999px;
            padding: 0.65rem 1.5rem;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-primary {
            background: linear-gradient(120deg, #2563eb, #3b82f6);
            color: #fff;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 15px 25px rgba(37, 99, 235, 0.35);
        }
        .grid {
            display: grid;
            gap: 1.25rem;
        }
        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.35rem;
        }
        input, select {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid #cbd5f5;
            padding: 0.65rem 0.85rem;
            font-size: 1rem;
            background: #fff;
        }
        .alert {
            padding: 0.85rem 1.25rem;
            border-radius: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $currentUser = Auth::user();
        $barberiaActivaNav = $currentUser?->barberiaActiva();
        $adminBarberiasNav = $currentUser?->esAdmin()
            ? \App\Models\Barberia::orderBy('nombre')->get()
            : collect();
    @endphp
    <header>
        <div class="top-bar">
            <h1>Turnos Barber</h1>
            <nav>
                <a href="{{ route('dashboard') }}">Dashboard</a>
                @auth
                    <a href="{{ route('turnos.index') }}">Turnos</a>
                    @if(Auth::user()->barberia)
                        <a href="{{ route('barberias.show', Auth::user()->barberia) }}" target="_blank">Vista pública</a>
                    @endif
                    @if($currentUser?->esAdmin())
                        <form method="POST" action="{{ route('admin.barberia.seleccionar') }}">
                            @csrf
                            <select name="barberia_id" onchange="this.form.submit()">
                                <option value="">Seleccionar barbería…</option>
                                @foreach($adminBarberiasNav as $barberiaNav)
                                    <option value="{{ $barberiaNav->id }}" {{ $barberiaActivaNav && $barberiaActivaNav->id === $barberiaNav->id ? 'selected' : '' }}>
                                        {{ $barberiaNav->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Cerrar sesión</button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>
    <main>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0; padding-left:1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
