@extends('layouts.app')

@section('content')
    <section class="card" style="max-width:420px; margin:0 auto;">
        <h2 style="margin-top:0;">Ingresá al panel</h2>
        <form method="POST" action="{{ route('login') }}" class="grid" style="gap:1rem;">
            @csrf
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p style="color:#dc2626; font-size:0.9rem;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <label style="display:flex; align-items:center; gap:0.5rem;">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Recordarme
            </label>

            <button type="submit" class="btn btn-primary" style="width:100%;">Entrar</button>
        </form>
    </section>
@endsection
