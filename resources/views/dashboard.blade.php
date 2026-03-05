@extends('layouts.app')

@section('content')
    <section class="card">
        <h2 style="margin-top:0;">Barberías</h2>
        <p style="color:#475569;">Listado de barberías dadas de alta en el sistema.</p>

        @if($barberias->isEmpty())
            <p>No hay barberías aún. Podés crearlas desde seeds o un panel de administración.</p>
        @else
            <div class="grid grid-2" style="margin-top:1.5rem;">
                @foreach($barberias as $barberia)
                    <article style="border:1px solid #e2e8f0; border-radius:1rem; padding:1rem; background:#fff;">
                        <h3 style="margin:0;">{{ $barberia->nombre }}</h3>
                        <p style="margin:0.35rem 0; color:#64748b;">{{ $barberia->direccion }}</p>
                        <p style="margin:0; font-size:0.95rem; color:#475569;">
                            {{ $barberia->barberos_count }} barberos · {{ $barberia->servicios_count }} servicios
                        </p>
                        <div style="margin-top:1rem;">
                            <a class="btn btn-primary" href="{{ route('barberias.show', $barberia) }}">
                                Ver página pública
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
