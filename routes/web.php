<?php

use App\Http\Controllers\AdminBarberiaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarberoAdminController;
use App\Http\Controllers\BarberiaController;
use App\Http\Controllers\ServicioAdminController;
use App\Http\Controllers\TurnoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BarberiaController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/admin/barberia', [AdminBarberiaController::class, 'seleccionar'])->name('admin.barberia.seleccionar');

    Route::post('/barberos', [BarberoAdminController::class, 'store'])->name('barberos.store');
    Route::patch('/barberos/{barbero}', [BarberoAdminController::class, 'update'])->name('barberos.update');
    Route::delete('/barberos/{barbero}', [BarberoAdminController::class, 'destroy'])->name('barberos.destroy');

    Route::post('/servicios', [ServicioAdminController::class, 'store'])->name('servicios.store');
    Route::patch('/servicios/{servicio}', [ServicioAdminController::class, 'update'])->name('servicios.update');
    Route::delete('/servicios/{servicio}', [ServicioAdminController::class, 'destroy'])->name('servicios.destroy');

    Route::patch('/barberia', [BarberiaController::class, 'update'])->name('barberia.update');

    Route::get('/turnos', [TurnoController::class, 'index'])->name('turnos.index');
    Route::patch('/turnos/{turno}', [TurnoController::class, 'actualizarEstado'])->name('turnos.actualizar-estado');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/barberias/{barberia}/reservar', [BarberiaController::class, 'show'])->name('barberias.show');

Route::get('/barberias/{barberia}/disponibilidad', [TurnoController::class, 'disponibilidad'])->name('turnos.disponibilidad');
Route::post('/barberias/{barberia}/reservar', [TurnoController::class, 'reservar'])->name('turnos.reservar');
