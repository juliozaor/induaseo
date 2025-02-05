<?php

use App\Http\Controllers\ActividadesEvidenciasController;
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
use App\Http\Controllers\GestionarInventarioController;
use App\Http\Controllers\InformacionController;
use App\Http\Controllers\InformacionNovedadesController;
use App\Http\Controllers\ReporteController;

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

/* Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest'); */

Route::get('/', function () {
    return redirect()->route('login');
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
    return redirect('/login'); // Redirige a la página de bienvenida o login después de salir
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
    Route::get('/gestionar-activos/obtener-mantenimientos', [GestionarActivosController::class, 'obtenerMantenimientos'])->name('gestionar.activos.obtenerMantenimientos');
    Route::get('/gestionar-inventario', [GestionarInventarioController::class, 'index'])->name('gestionar.inventario.index');
    Route::get('/gestionar-inventario/consultar', [GestionarInventarioController::class, 'consultar'])->name('gestionar.inventario.consultar');
    Route::post('/gestionar-inventario/guardar', [GestionarInventarioController::class, 'guardar'])->name('gestionar.inventario.guardar');
    Route::put('/gestionar-inventario/actualizar/{id}', [GestionarInventarioController::class, 'actualizar'])->name('gestionar.inventario.actualizar');
    Route::get('/gestionar-inventario/obtener', [GestionarInventarioController::class, 'obtenerInventario'])->name('gestionar.inventario.obtener');
    Route::get('/sedes', [GestionarInventarioController::class, 'getSedes'])->name('sedes.obtener');
    Route::get('/obtener-sede', [SedeController::class, 'obtenerSede'])->name('obtener.sede');
    Route::get('/inventario', [SeguimientoActividadesController::class, 'inventario'])->name('inventario');
    Route::post('/solicitar-insumo-activo', [SeguimientoActividadesController::class, 'solicitarInsumoActivo'])->name('solicitar.insumo.activo');
    Route::get('/admin/informacion', [InformacionController::class, 'index'])->name('admin.informacion.index');
    Route::post('/admin/informacion', [InformacionController::class, 'cargarInformacion'])->name('admin.informacion.cargar');
    Route::post('/informacion/guardar', [InformacionController::class, 'guardar'])->name('informacion.guardar');
    Route::get('/informacion', [InformacionController::class, 'obtenerInformacion'])->name('informacion.obtener');
    Route::put('/informacion/actualizar/{id}', [InformacionController::class, 'actualizar'])->name('informacion.actualizar');
    Route::get('/actividades-evidencias', [ActividadesEvidenciasController::class, 'index'])->name('actividades.evidencias.index');
    Route::get('/actividades-evidencias/consultar', [ActividadesEvidenciasController::class, 'consultar'])->name('actividades.evidencias.consultar');
    Route::get('/actividades-evidencias/detalle/{id}', [ActividadesEvidenciasController::class, 'getTurnoDetalle'])->name('actividades.evidencias.detalle');
    //Route::get('/informacion-novedades', [InformacionNovedadesController::class, 'index'])->name('informacion.novedades.index');
    // ...other routes...
    Route::get('/novedades', [InformacionController::class, 'obtenerNovedades'])->name('novedades.obtener');
    Route::get('/informacion', [SeguimientoActividadesController::class, 'informacion'])->name('informacion');

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/consultar', [ReporteController::class, 'consultar'])->name('reportes.consultar');
});
/*
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/supervisor/dashboard', [SupervisorController::class, 'index'])->name('supervisor.dashboard');
Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard'); */

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

Route::get('/tipos-multimedia', [InformacionController::class, 'getTiposMultimedia']);
Route::get('/categorias', [InformacionController::class, 'Categorias']);

// Add route for updating insumo quantity and observation
Route::post('/actualizar-insumo', [SeguimientoActividadesController::class, 'actualizarInsumo'])->name('actualizar.insumo');

// Add route for updating activo quantity and observation
Route::post('/actualizar-activo', [SeguimientoActividadesController::class, 'actualizarActivo'])->name('actualizar.activo');

Route::get('/actividades-turno', [SeguimientoActividadesController::class, 'obtenerActividades'])->name('actividades.turno');
Route::get('/inventario-turno', [SeguimientoActividadesController::class, 'obtenerInventarios'])->name('inventarios.turno');
Route::get('/informacion-novedades', [InformacionNovedadesController::class, 'index'])->name('informacion.novedades.index');
Route::get('/informacion-novedades/buscar', [InformacionNovedadesController::class, 'buscarInformacion'])->name('informacion.novedades.buscar');

Route::post('/guardar-calificacion/{id}', [SeguimientoActividadesController::class, 'guardarCalificacion'])->name('guardarCalificacion');

Route::post('/finalizar-turno', [SeguimientoActividadesController::class, 'finalizarTurno'])->name('finalizarTurno');

Route::get('/estados', [SeguimientoActividadesController::class, 'obtenerEstados'])->name('obtener.estados');

Route::get('/obtener-observaciones/{id', [SeguimientoActividadesController::class, 'obtenerObservaciones'])->name('obtener.observaciones');

Route::get('/obtener-insumo/{id}', [SeguimientoActividadesController::class, 'obtenerInsumo'])->name('obtener.insumo');

Route::post('/actualizar/activo', [SeguimientoActividadesController::class, 'actualizarActivo'])->name('actualizar.activo');
Route::get('/obtener/activo/{id}', [SeguimientoActividadesController::class, 'obtenerActivo'])->name('obtener.activo');
Route::get('/obtener/observaciones/activo/{id}', [SeguimientoActividadesController::class, 'obtenerObservacionesActivo'])->name('obtener.observaciones.activo');

Route::post('/reportar/activo', [SeguimientoActividadesController::class, 'reportarActivo'])->name('reportar.activo');

Route::get('/mantenimientos', [SeguimientoActividadesController::class, 'obtenerMantenimientos'])->name('obtener.mantenimientos');

Route::post('/enviar-solicitud-items', [SeguimientoActividadesController::class, 'enviarSolicitudItems'])->name('enviar.solicitud.items');

Route::get('/activo-reportado/{id}', [SeguimientoActividadesController::class, 'activoReportado'])->name('activo.reportado');

Route::get('/obtener-datos-mantenimiento/{id}', [SeguimientoActividadesController::class, 'obtenerDatosMantenimiento'])->name('obtener.datos.mantenimiento');

// Add route for updating maintenance details
Route::post('/actualizar-mantenimiento', [SeguimientoActividadesController::class, 'actualizarMantenimiento'])->name('actualizar.mantenimiento');

// Add route for finalizing maintenance
Route::post('/finalizar-mantenimiento', [SeguimientoActividadesController::class, 'finalizarMantenimiento'])->name('finalizar.mantenimiento');

// Agregar ruta para obtener detalles de mantenimiento (para admin)
Route::get('/obtener-detalles-mantenimiento/{id}', [GestionarActivosController::class, 'obtenerDatosMantenimiento'])->name('obtener.detalles.mantenimiento');
Route::post('/actualizar-mantenimiento/{id}', [GestionarActivosController::class, 'actualizarMantenimiento'])->name('actualizar.mantenimiento');

// Agregar ruta para obtener novedades
Route::get('/novedades/{sedeId}', [InformacionController::class, 'obtenerNovedadesPorSede'])->name('novedades.obtener.por.sede');

// Add route to fetch categories
Route::get('/categorias', [InformacionNovedadesController::class, 'obtenerCategorias'])->name('categorias.obtener');

// Add route to fetch items
Route::get('/items', [GestionarInventarioController::class, 'obtenerItems'])->name('items.obtener');

// Add route to fetch item
Route::get('/item/{codigo}', [GestionarInventarioController::class, 'obtenerItem'])->name('item.obtener');
