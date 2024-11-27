<?php

use App\Http\Controllers\AreaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\MaestrasController;
use App\Http\Controllers\SedeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SupervisorTurnoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivosController;
use App\Http\Controllers\GestionarActivosController;
use App\Http\Controllers\InsumosController;
use App\Http\Controllers\SeguimientoActividadesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\UserController;

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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/maestras', [MaestrasController::class, 'index'])->name('admin.maestras.index');
    Route::get('/asignar-turnos', [SupervisorTurnoController::class, 'index'])->name('asignar.turnos');
    Route::get('/gestionar-activos', [GestionarActivosController::class, 'index'])->name('gestionar.index');
    Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/seguimiento-actividades', [SeguimientoActividadesController::class, 'index'])->name('seguimiento.actividades.index');
    Route::get('/asignar-turnos/consultar', [SupervisorTurnoController::class, 'consultar'])->name('asignar.turnos.consultar');
    Route::get('/gestionar-activos/consultar', [GestionarActivosController::class, 'consultar'])->name('gestionar.activos.consultar');
    // ...other routes...
});

Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/supervisor/dashboard', [SupervisorController::class, 'index'])->name('supervisor.dashboard');
Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard');

Route::get('/paises', [MaestrasController::class, 'obtenerPaises'])->name('obtener.paises');
Route::get('/ciudades', [MaestrasController::class, 'obtenerCiudades'])->name('obtener.ciudades');
Route::get('/clientes-select', [MaestrasController::class, 'obtenerClientes'])->name('obtener.clientes');
Route::get('/sectores-economicos', [MaestrasController::class, 'obtenerSectoresEconomicos'])->name('obtener.sectoresEconomicos');
Route::get('/regionales', [MaestrasController::class, 'obtenerRegionales'])->name('obtener.regionales');

Route::get('/maestras/clientes', [MaestrasController::class, 'clientes'])->name('maestras.clientes');

Route::post('/clientes/guardar', [ClientesController::class, 'guardar'])->name('clientes.guardar');
Route::get('/clientes', [ClientesController::class, 'obtenerCliente'])->name('clientes.obtener');
Route::put('/clientes/actualizar/{id}', [ClientesController::class, 'actualizarCliente'])->name('clientes.actualizar');

Route::get('/maestras/sedes', [MaestrasController::class, 'clientes'])->name('maestras.sedes');
Route::post('/sedes/guardar', [SedeController::class, 'guardar'])->name('sedes.guardar');
Route::get('/sedes', [SupervisorTurnoController::class, 'getSedes'])->name('sedes.obtener');
Route::put('/sedes/actualizar/{id}', [SedeController::class, 'actualizar'])->name('sedes.actualizar');

Route::get('/maestras/turnos', [MaestrasController::class, 'clientes'])->name('maestras.turnos');
Route::post('/turnos/guardar', [TurnoController::class, 'guardar'])->name('turnos.guardar');
Route::get('/turno', [TurnoController::class, 'obtenerTurno'])->name('turno.obtener');
Route::put('/turnos/actualizar/{id}', [TurnoController::class, 'actualizar'])->name('turnos.actualizar');

Route::get('/frecuencias', [MaestrasController::class, 'obtenerFrecuencias'])->name('obtener.frecuencias');
Route::get('/actividades/{turnoId}', [TurnoController::class, 'obtenerActividades'])->name('obtener.actividades');
Route::delete('/actividades/{id}', [TurnoController::class, 'eliminarActividad'])->name('eliminar.actividad');
Route::post('/actividades', [TurnoController::class, 'guardarActividad'])->name('guardar.actividad');

Route::get('/turnos', [TurnoController::class, 'obtenerTurnos'])->name('turnos.obtener');

Route::post('/admin/usuarios', [UsuarioController::class, 'cargarUsuarios'])->name('usuarios.cargar');
Route::post('/usuarios/guardar', [UsuarioController::class, 'guardar'])->name('usuarios.guardar');
Route::get('/usuarios', [UsuarioController::class, 'obtenerUsuario'])->name('usuarios.obtener');
Route::put('/usuarios/actualizar/{id}', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.actualizar');

Route::get('/roles', [UsuarioController::class, 'obtenerRoles'])->name('roles.obtener');
Route::get('/tipos-documentos', [UsuarioController::class, 'obtenerTiposDocumentos'])->name('tipos.documentos.obtener');

Route::get('/supervisores', [SupervisorTurnoController::class, 'getSupervisores']);
Route::get('/asignar-turnos/{id}', [SupervisorTurnoController::class, 'getTurno']);
Route::put('/asignar-turnos/actualizar/{id}', [SupervisorTurnoController::class, 'actualizar']);
Route::get('/asignar-turnos/tareas/{id}', [SupervisorTurnoController::class, 'getTareas'])->name('asignar.turnos.tareas');

Route::get('/areas', [AreaController::class, 'index'])->name('areas.index');
Route::post('/areas/guardar', [AreaController::class, 'store'])->name('areas.store');
Route::get('/area', [AreaController::class, 'show'])->name('areas.show');
Route::put('/areas/actualizar/{id}', [AreaController::class, 'update'])->name('areas.update');
Route::delete('/areas/{id}', [AreaController::class, 'destroy'])->name('areas.destroy');
Route::get('/tareas/{areaId}', [AreaController::class, 'obtenerTareas'])->name('areas.tareas');
Route::post('/tareas', [AreaController::class, 'guardarTarea'])->name('areas.tareas.store');
Route::delete('/tareas/{id}', [AreaController::class, 'eliminarTarea'])->name('areas.tareas.destroy');


Route::post('/asignar-turnos/guardar', [SupervisorTurnoController::class, 'guardar'])->name('asignar.turnos.guardar');
Route::post('/asignar-turnos/validar', [SupervisorTurnoController::class, 'validarAsignacion'])->name('asignar.turnos.validar');

Route::post('/activos/guardar', [ActivosController::class, 'guardar'])->name('activos.guardar');
Route::put('/activos/actualizar/{id}', [ActivosController::class, 'actualizar'])->name('activos.actualizar');
Route::get('/activo', [ActivosController::class, 'obtenerActivo'])->name('activo.obtener');
Route::get('/activos', [ActivosController::class, 'obtenerActivos'])->name('activos.obtener');

Route::post('/gestionar-activos/guardar', [GestionarActivosController::class, 'guardar'])->name('gestionar.activos.guardar');
Route::put('/gestionar-activos/actualizar/{id}', [GestionarActivosController::class, 'actualizar'])->name('gestionar.activos.actualizar');
Route::get('/gestionar-activo/consultar', [GestionarActivosController::class, 'obtenerActivo'])->name('gestionar.activo.obtener');

Route::post('/admin/maestras/consultar', [MaestrasController::class, 'consultar'])->name('maestras.consultar');

Route::get('/clasificaciones', [MaestrasController::class, 'obtenerClasificaciones'])->name('obtener.clasificaciones');
Route::get('/estados', [MaestrasController::class, 'obtenerEstados'])->name('obtener.estados');

// Add routes for insumos
Route::get('admin/maestras/insumos', [MaestrasController::class, 'clientes'])->name('maestras.insumos');
Route::post('/insumos/guardar', [InsumosController::class, 'guardar'])->name('insumos.guardar');
Route::put('/insumos/actualizar/{id}', [InsumosController::class, 'actualizar'])->name('insumos.actualizar');
Route::get('/insumos', [InsumosController::class, 'obtener'])->name('insumos.obtener');


// Add routes for clientes and sedes
Route::get('/get-clientes', [GestionarActivosController::class, 'getClientes']);
Route::get('/get-sedes', [GestionarActivosController::class, 'getSedes']);

Route::get('/home', [HomeController::class, 'index'])->name('home');
