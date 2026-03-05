<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarberiaController;
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
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/barberias/{barberia}/reservar', [BarberiaController::class, 'show'])->name('barberias.show');

Route::get('/barberias/{barberia}/disponibilidad', [TurnoController::class, 'disponibilidad'])->name('turnos.disponibilidad');
Route::post('/barberias/{barberia}/reservar', [TurnoController::class, 'reservar'])->name('turnos.reservar');
