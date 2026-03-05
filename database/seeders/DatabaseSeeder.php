<?php

namespace Database\Seeders;

use App\Models\Barberia;
use App\Models\Barbero;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@turnosbarber.com'],
            [
                'name' => 'Demo Barbería',
                'password' => bcrypt('password'),
            ]
        );

        $barberia = Barberia::firstOrCreate(
            ['slug' => 'barberia-demo'],
            [
                'usuario_id' => $user->id,
                'nombre' => 'Barbería Demo',
                'direccion' => 'Av. Principal 123',
                'telefono' => '+54 11 5555-0000',
            ]
        );

        $barberos = collect([
            'Carlos Medina',
            'Sofía Torres',
            'Lucas Pérez',
        ])->map(fn ($nombre) => Barbero::firstOrCreate([
            'barberia_id' => $barberia->id,
            'nombre' => $nombre,
        ]));

        $servicios = [
            ['nombre' => 'Corte clásico', 'duracion' => 30, 'precio' => 8000],
            ['nombre' => 'Corte + Barba', 'duracion' => 45, 'precio' => 11000],
            ['nombre' => 'Arreglo de Barba', 'duracion' => 25, 'precio' => 6000],
        ];

        foreach ($servicios as $servicio) {
            Servicio::firstOrCreate(
                [
                    'barberia_id' => $barberia->id,
                    'nombre' => $servicio['nombre'],
                ],
                [
                    'duracion_minutos' => $servicio['duracion'],
                    'precio' => $servicio['precio'],
                ]
            );
        }
    }
}
