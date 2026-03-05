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
        header h1 {
            margin: 0;
            font-size: 1.25rem;
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
    </style>
    @stack('styles')
</head>
<body>
    <header>
        <h1>Turnos Barber</h1>
    </header>
    <main>
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
