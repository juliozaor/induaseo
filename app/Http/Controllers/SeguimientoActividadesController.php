<?php

namespace App\Http\Controllers;

use App\Models\SupervisorTurno;
use App\Models\Turno;
use App\Models\Actividades;
use App\Models\ImagenesActividades;
use App\Models\SedesActivos;
use App\Models\SedesInsumos;
use App\Models\Estados; // Importar el modelo Estados
use App\Models\Mantenimiento; // Importar el modelo Mantenimientos
use App\Models\Activos; // Importar el modelo Activo
use App\Models\Insumos; // Importar el modelo Insumo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Usuario; // Importar el modelo Usuario
use App\Models\Sede; // Importar el modelo Sede
use App\Models\Area;
use App\Models\AreaActividad;
use App\Models\Cliente; // Importar el modelo Cliente
use App\Models\Inventario;
use App\Models\SupervisorTurnosFechas; // Import the model
use App\Models\TurnosAreasActividades; // Import the model TurnosAreasActividades
use App\Models\TurnosHistorialActividades; // Import the model TurnosHistorialActividades

class SeguimientoActividadesController extends Controller
{
    // Muestra la vista de inventario con los insumos y activos de las sedes
    public function inventario()
    {
        // Obtiene todos los insumos de las sedes
        $sedesInsumos = SedesInsumos::with('insumo.estados')->get();

        // Obtiene todos los activos de las sedes
        $sedesActivos = SedesActivos::with('activo.estados')->get();

        // Obtiene todos los estados
        $estados = Estados::all();

        // Retorna la vista con los insumos, activos, estados y mantenimientos de las sedes
        return view('seguimiento-actividades.inventario', compact('sedesInsumos', 'sedesActivos', 'estados'));
    }

    // Obtener todos los estados
    public function obtenerEstados()
    {
        $estados = Estados::all();
        return response()->json($estados);
    }

    // Guarda las observaciones y novedades de un insumo
    public function guardarObservaciones(Request $request, $id)
    {
        // Valida los datos recibidos
        $request->validate([
            'novedades' => 'required|integer',
            'observaciones' => 'nullable|string',
        ]);

        // Encuentra el insumo de la sede por su ID
        $sedesInsumo = SedesInsumos::findOrFail($id);

        // Actualiza las observaciones y novedades del insumo
        $sedesInsumo->update([
            'novedades' => $request->novedades,
            'observaciones' => $request->observaciones,
        ]);

        // Retorna una respuesta exitosa
        return response()->json(['message' => 'Observaciones guardadas exitosamente']);
    }

    // Reporta un fallo en un activo
    public function reportarFallo(Request $request, $id)
    {
        // Valida los datos recibidos
        $request->validate([
            'observaciones' => 'required|string',
        ]);

        // Encuentra el activo de la sede por su ID
        $sedeActivo = SedesActivos::findOrFail($id);

        // Actualiza las observaciones del activo y marca el fallo
        $sedeActivo->update([
            'novedades' => 2, // Código para fallo
            'observaciones' => $request->observaciones,
        ]);

        // Retorna una respuesta exitosa
        return response()->json(['message' => 'Fallo reportado exitosamente']);
    }

    public function index()
    {
        $userId = Auth::id();
        $turnos = SupervisorTurno::with(['supervisor', 'sede', 'turno'])
            ->where('supervisor_id', $userId)
            ->get();
        //dd($turnos);
        return view('seguimiento-actividades.index', compact('turnos'));
    }

    public function obtenerActividades(Request $request)
    {   /* dd($request->all()); */
        $userId = Auth::id();
        $turnoId = $request->input('id') ?? "<script>document.write(localStorage.getItem('turno_id'))</script>";
        $sedeId = $request->input('sede_id') ?? "<script>document.write(localStorage.getItem('sede_id'))</script>";
        $inicioTurno = $request->input('inicio_turno');
        $fechaInicial = $request->input('fecha_inicial');
        /* dd($turnoId, $sedeId, $fechaInicial); */
        if (!$turnoId || !$sedeId) {
            return redirect()->route('seguimiento.actividades.index');
        }

        // Validar y almacenar en localStorage si no existe
        echo "<script>
            if (!localStorage.getItem('turno_id') || !localStorage.getItem('sede_id')) {
                localStorage.setItem('turno_id', '$turnoId');
                localStorage.setItem('sede_id', '$sedeId');
            }
        </script>";

        // Actualizar la fecha inicial del turno
        SupervisorTurno::where('supervisor_id', $userId)
            ->where('turno_id', $turnoId)
            ->where('sede_id', $sedeId)
            ->update(['fecha_inicio' => $fechaInicial]);

        $turnoAsignado = SupervisorTurno::where('supervisor_id', $userId)
            ->where('turno_id', $turnoId)
            ->where('sede_id', $sedeId)
            ->get();
        $turnoAsignadoId = $turnoAsignado->first()->id;
        $estadoInicializado = $turnoAsignado->first()->inicializado;
        /* dd($turnoId, $sedeId,$turnoAsignadoId); */

        $supervisorTurno = SupervisorTurno::with(['supervisor', 'sede', 'turno', 'areas'])
            ->where('supervisor_id', $userId)
            ->where('id', $turnoAsignadoId)
            ->first();
        /* dd($turnoId, $sedeId,$turnoAsignadoId,$supervisorTurno); */
        $actividadesTrue = collect();   // Actividades pendientes
        $actividadesFalse = collect();  // Actividades finalizadas
        /* dd($estadoInicializado); */
        if (!$estadoInicializado) {
            // Guardar la fecha inicial en la tabla supervisor_turnos_fechas
            SupervisorTurnosFechas::create([
                'supervisor_turno_id' => $turnoAsignadoId, // Usar el ID correcto
                'fecha_inicio' => $fechaInicial,
                'fecha_fin' => null // Asumiendo que fecha_fin es nulo inicialmente
            ]);

            foreach ($supervisorTurno->areas as $area) {
                foreach ($area->actividades as $areaActividad) {
                    if (!$areaActividad->estado) {
                        $areaActividad->estado = true;
                        $areaActividad->calificacion = null;
                        $areaActividad->save();
                    }
                }
            }
            // Cambiar el estado a inicializado
            SupervisorTurno::where('supervisor_id', $userId)
                ->where('turno_id', $turnoId)
                ->where('sede_id', $sedeId)
                ->update(['inicializado' => true]);
        }

        foreach ($supervisorTurno->areas as $area) {
            /* dd($area->area->areasActividades, $area->actividades); */
            foreach ($area->actividades as $areaActividad) {
                if ($areaActividad->estado) {
                    $actividadesTrue->push($areaActividad->actividad);
                } else {
                    $actividadesFalse->push($areaActividad->actividad);
                }
            }
        }
        /* dd($actividadesTrue, $actividadesFalse); */
        return view('seguimiento-actividades.actividades', compact('supervisorTurno', 'actividadesTrue', 'actividadesFalse', 'sedeId', 'turnoId'));
    }

    public function guardarCalificacion(Request $request, $turnosAreasId, $actividadId)
    {
        $actividad = Actividades::findOrFail($actividadId);
        //$actividad->calificacion = $request->input('calificacion');
        $actividad->save();

        $actividadArea = TurnosAreasActividades::where('actividad_id', $actividadId)
            ->where('turnos_areas_id', $turnosAreasId)
            ->firstOrFail();
        $actividadArea->estado = false;
        $actividadArea->calificacion = $request->input('calificacion');
        $actividadArea->save();

        // Crear un registro en la tabla turnosHistorialActividades
        TurnosHistorialActividades::create([
            'turnos_areas_actividades_id' => $actividadArea->id,
            'estado' => false,
            'calificacion' => $request->input('calificacion')
        ]);

        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->move(public_path('evidencias'), $filename);
                $imagePath = 'evidencias/' . $filename;
                ImagenesActividades::create([
                    'actividad_id' => $actividadArea->id,
                    'imagen' => $imagePath
                ]);
            }
        }

        return redirect()->back()->with('success', 'Actividad actualizada correctamente.');
    }

    public function obtenerInventarios(Request $request)
    {
        $sedeId = $request->input('sede_id') ?? "<script>document.write(localStorage.getItem('sede_id'))</script>";
        if (!$sedeId) {
            return redirect()->route('seguimiento.actividades.index');
        }
        // Obtener todos los insumos de la sede
        $inventario = Inventario::with(['item', 'item.estados', 'sede'])
            ->where('sede_id', $sedeId)
            ->get();

        $sedesInsumos = $inventario->groupBy('id')->map(function ($items, $id) {
            return [
                'id' => $id,
                'cantidad' => $items->first()->cantidad, // Obtiene el campo 'cantidad' directamente del inventario
                'insumos' => $items->pluck('item')
            ];
        });
        /* dd($sedeId, $sedesInsumos); */

        // Obtener todos los activos de la sede
        $activos = SedesActivos::with(['activo', 'activo.estados', 'sede', 'imagenes'])
            ->where('sede_id', $sedeId)
            ->get();

        $sedesActivos = $activos->map(function ($activo) {
            $imagen = $activo->imagenes->first(); // Obtener la primera imagen asociada al activo
            return [
                'id' => $activo->id,
                'nombre' => $activo->activo->nombre_elemento,
                'serie' => $activo->activo->serie,
                'estado_id' => $activo->estado_id,
                'estado' => $activo->activo->estados->nombre,
                'observacion' => $activo->observacion,
                'imagen' => $imagen ? $imagen->imagen : null, // Agregar el campo 'imagen'
            ];
        });
        /* dd($sedesActivos); */

        // Obtiene los mantenimientos con estado_id = 3
        $mantenimientos = Mantenimiento::with('sedeActivo.activo')
            ->where('estado_id', 3)
            ->get();
        /* dd($mantenimientos); */

        // Obtiene todos los insumos
        $insumos = Insumos::all();

        // Obtiene todos los activos
        $activos = Activos::all();
        // dd($activos);
        return view(
            'seguimiento-actividades.inventario',
            compact('sedesActivos', 'sedesInsumos', 'sedeId', 'mantenimientos', 'insumos', 'activos')
        );
    }

    public function obtenerMantenimientos(Request $request)
    {
        $sedeId = $request->input('sede_id');
        $mantenimientos = Mantenimiento::with(['sedes_activos.activo', 'sedes_activos.estados'])
            ->where('estado_id', 2)
            ->when($sedeId, function ($query, $sedeId) {
                return $query->whereHas('sedes_activos', function ($query) use ($sedeId) {
                    $query->where('sede_id', $sedeId);
                });
            })
            ->get();

        // Verificar que las relaciones existan antes de devolver los datos
        $mantenimientos = $mantenimientos->filter(function ($mantenimiento) {
            return $mantenimiento->sedeActivo && $mantenimiento->sedeActivo->activo;
        })->values(); // Ensure the collection is re-indexed

        return response()->json($mantenimientos);
    }

    public function finalizarTurno(Request $request)
    {
        $userId = Auth::id();
        $turnoId = $request->input('id');
        $sedeId = $request->input('sede_id');
        $fechaFinal = $request->input('fecha_final');

        /* dd($turnoId, $sedeId, $fechaFinal); */

        // Actualizar la fecha final del turno
        SupervisorTurno::where('supervisor_id', $userId)
            ->where('turno_id', $turnoId)
            ->where('sede_id', $sedeId)
            ->update([
                'fecha_fin' => $fechaFinal,
                'inicializado' => false,
            ]);

        $turnoAsignado = SupervisorTurno::where('supervisor_id', $userId)
            ->where('turno_id', $turnoId)
            ->where('sede_id', $sedeId)
            ->first();
        $turnoAsignadoId = $turnoAsignado->id;

        // Verificar si existe un registro con fecha_fin nulo
        $registroFecha = SupervisorTurnosFechas::where('supervisor_turno_id', $turnoAsignadoId)
            ->whereNull('fecha_fin')
            ->orderBy('id', 'desc')
            ->first();

        if ($registroFecha) {
            // Actualizar la fecha_fin del registro existente
            $registroFecha->update(['fecha_fin' => $fechaFinal]);
        }

        $turno = SupervisorTurno::where('supervisor_id', $userId)
            ->where('turno_id', $turnoId)
            ->where('sede_id', $sedeId)
            ->latest()
            ->first();

        if ($turno) {
            $turno->turno->observacion = $request->input('observaciones');
            //$turno->turno->estado = false;
            $turno->turno->save();

            // Verificar si hay actividades pendientes
            $actividadesPendientes = TurnosAreasActividades::whereHas('turnoArea', function ($query) use ($turno) {
                $query->where('turno_id', $turno->id);
            })->where('estado', true)->exists();

            if ($actividadesPendientes) {
                $actividadesPendientes = TurnosAreasActividades::whereHas('turnoArea', function ($query) use ($turno) {
                    $query->where('turno_id', $turno->id);
                })->where('estado', true)->get();

                foreach ($actividadesPendientes as $actividadPendiente) {
                    TurnosHistorialActividades::create([
                        'turnos_areas_actividades_id' => $actividadPendiente->id,
                        'estado' => true,
                        'calificacion' => null,
                    ]);
                }
            }
        }

        return redirect()->route('seguimiento.actividades.index')->with('success', 'Turno finalizado correctamente.');
    }

    public function actualizarInsumo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sedes_insumos,id',
            'estado_id' => 'required|exists:estados,id',
            'observacion' => 'nullable|string',
        ]);

        $sedesInsumo = SedesInsumos::find($request->id);
        $sedesInsumo->observacion = $request->observacion;
        $sedesInsumo->save();

        $insumo = $sedesInsumo->insumo;
        $insumo->estado_id = $request->estado_id;
        $insumo->save();

        return response()->json(['message' => 'Insumo actualizado correctamente']);
    }

    // Obtener detalles de un insumo
    public function obtenerInsumo($id)
    {
        $sedesInsumo = Inventario::with('item.estados')->findOrFail($id);
        /* dd($sedesInsumo); */
        return response()->json([
            'insumo' => [
                'estado_id' => $sedesInsumo->estado_id,
                'observacion' => $sedesInsumo->item->observacion
            ]
        ]);
    }

    public function obtenerObservaciones($id)
    {
        $sedesInsumo = SedesInsumos::find($id);
        if ($sedesInsumo) {
            return response()->json(['observacion' => $sedesInsumo->observacion]);
        } else {
            return response()->json(['error' => 'Insumo no encontrado'], 404);
        }
    }

    public function actualizarActivo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sedes_activos,id',
            'estado_id' => 'required|exists:estados,id',
            'observacion' => 'nullable|string',
        ]);

        $activo = SedesActivos::find($request->id);
        $activo->estado_id = $request->estado_id;
        $activo->observacion = $request->observacion;
        $activo->save();

        $activoModel = $activo->activo;
        $activoModel->estado_id = $request->estado_id;
        $activoModel->save();

        return response()->json(['message' => 'Activo actualizado correctamente']);
    }

    public function obtenerActivo($id)
    {
        $activo = SedesActivos::with('activo')->find($id);
        if ($activo) {
            return response()->json(['activo' => $activo]);
        }

        return response()->json(['message' => 'Activo no encontrado'], 404);
    }

    public function obtenerObservacionesActivo($id)
    {
        $activo = SedesActivos::find($id);
        if ($activo) {
            return response()->json(['observacion' => $activo->observacion]);
        }

        return response()->json(['message' => 'Activo no encontrado'], 404);
    }

    // Función para reportar un activo
    public function reportarActivo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sedes_activos,id',
            'estado_id' => 'required|exists:estados,id',
            'observacion' => 'required|string',
        ]);

        $activo = SedesActivos::find($request->id);
        if ($activo) {
            // Actualizar el estado y la observación del activo en sedes_activos
            $activo->estado_id = $request->estado_id;
            $activo->observacion = $request->observacion;
            $activo->save();

            // Actualizar el estado del activo en activos
            $activoModel = $activo->activo;
            $activoModel->estado_id = $request->estado_id;
            $activoModel->save();

            // Crear un nuevo registro en la tabla de mantenimientos
            Mantenimiento::create([
                'estado_id' => $request->estado_id,
                'sede_activo_id' => $activo->id,
                'observaciones_reportadas' => $request->observacion,
                'estado' => 1,
                'creador_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Activo reportado y registrado en mantenimientos correctamente']);
        }

        return response()->json(['message' => 'Activo no encontrado'], 404);
    }

    // Función para enviar la solicitud de inventario por correo
    public function enviarSolicitudItems(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.nombre' => 'required|string',
            'items.*.cantidad' => 'required|integer',
            'items.*.tipo' => 'required|string|in:insumo,activo',
        ]);

        // Obtener los correos de los usuarios con rol de Administrador
        $administradores = Usuario::whereHas('roles', function ($query) {
            $query->where('name', 'Administrador');
        })->pluck('email');
        $sedes = Sede::with('cliente')->find($request->input('sedeId'));
        $cliente = $sedes->cliente->nombre;
        // Datos del correo
        $data = [
            'items' => $request->items,
            'usuario' => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
            'sede' => $sedes->nombre,
            'cliente' => $sedes->cliente->nombre,
        ];

        // Enviar el correo a cada administrador
        foreach ($administradores as $email) {
            Mail::send('emails.solicitud', $data, function ($message) use ($email) {
                $message->to($email)
                    ->subject('Solicitud de inventario');
            });
        }

        return response()->json(['message' => 'Solicitud de inventario enviada correctamente']);
    }

    public function activoReportado($id)
    {
        $reportado = SedesActivos::where('id', $id)
            ->where('estado_id', 3)
            ->exists();
        return response()->json(['reportado' => $reportado]);
    }

    public function obtenerDatosMantenimiento($id)
    {
        $mantenimiento = Mantenimiento::find($id);
        if ($mantenimiento) {
            return response()->json([
                'observaciones_reportadas' => $mantenimiento->observaciones_reportadas,
                'mtto_programado' => $mantenimiento->mtto_programado
            ]);
        }

        return response()->json(['message' => 'Mantenimiento no encontrado'], 404);
    }

    public function actualizarMantenimiento(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:mantenimientos,id',
            'mtto_programado' => 'required|date_format:Y-m-d',
            'observaciones_reportadas' => 'nullable|string',
        ]);

        $mantenimiento = Mantenimiento::find($request->id);
        $mantenimiento->mtto_programado = $request->mtto_programado;
        $mantenimiento->observaciones_reportadas = $request->observaciones_reportadas;
        $mantenimiento->save();

        // Update the observation in the sedes_activos table
        $sedeActivo = SedesActivos::find($mantenimiento->sede_activo_id);
        $sedeActivo->observacion = $request->observaciones_reportadas;
        $sedeActivo->save();

        return response()->json(['message' => 'Mantenimiento programado correctamente']);
    }

    public function finalizarMantenimiento(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:mantenimientos,id',
            'estado_id' => 'required|exists:estados,id',
            'observaciones' => 'nullable|string',
        ]);

        $mantenimiento = Mantenimiento::find($request->id);
        $mantenimiento->estado_id = $request->estado_id;
        $mantenimiento->observaciones_reportadas = $request->observaciones;
        $mantenimiento->estado = 2; // Assuming 2 is the finalized state
        $mantenimiento->ultimo_mtto = $mantenimiento->mtto_programado; // Save the date in ultimo_mtto
        $mantenimiento->save();

        $sedeActivo = SedesActivos::find($mantenimiento->sede_activo_id);
        $sedeActivo->estado_id = $request->estado_id;
        $sedeActivo->observacion = $request->observaciones;
        $sedeActivo->save();

        $activo = Activos::find($sedeActivo->activo_id);
        $activo->estado_id = $request->estado_id;
        $activo->save();

        return response()->json(['message' => 'Mantenimiento finalizado correctamente']);
    }
}
