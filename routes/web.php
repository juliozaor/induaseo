<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\MaestrasController;
use App\Http\Controllers\SedeController;
use Illuminate\Support\Facades\Auth;

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

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome')->middleware('guest');


Route::get('/', function () {
    return redirect()->route('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'login']);

Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');

Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Ruta para logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/welcome'); // Redirige a la página de bienvenida o login después de salir
})->name('logout');

Route::get('/admin/maestras', [MaestrasController::class, 'index'])->name('admin.maestras.index');
Route::post('/admin/maestras/consultar', [MaestrasController::class, 'consultar'])->name('maestras.consultar');

Route::get('/paises', [MaestrasController::class, 'obtenerPaises'])->name('obtener.paises');
Route::get('/ciudades', [MaestrasController::class, 'obtenerCiudades'])->name('obtener.ciudades');
Route::get('/clientes-select', [MaestrasController::class, 'obtenerClientes'])->name('obtener.clientes');
Route::get('/sectores-economicos', [MaestrasController::class, 'obtenerSectoresEconomicos'])->name('obtener.sectoresEconomicos');

Route::get('/maestras/clientes', [MaestrasController::class, 'clientes'])->name('maestras.clientes');

Route::post('/clientes/guardar', [ClientesController::class, 'guardar'])->name('clientes.guardar');
Route::get('/clientes', [ClientesController::class, 'obtenerCliente'])->name('clientes.obtener');
Route::put('/clientes/actualizar/{id}', [ClientesController::class, 'actualizarCliente'])->name('clientes.actualizar');

Route::get('/maestras/sedes', [MaestrasController::class, 'clientes'])->name('maestras.sedes');
Route::post('/sedes/guardar', [SedeController::class, 'guardar'])->name('sedes.guardar');
Route::get('/sedes', [SedeController::class, 'obtenerSede'])->name('sedes.obtener');
Route::put('/sedes/actualizar/{id}', [SedeController::class, 'actualizar'])->name('sedes.actualizar');


