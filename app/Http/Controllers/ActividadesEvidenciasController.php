<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\SupervisorTurno;
use App\Models\Sede;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use App\Models\TurnoArea;
use App\Exports\ActividadesExport;
use App\Models\Actividades;
use App\Models\Turno;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\SatisfaccionServicio; // Asegúrate de importar el modelo correspondiente
use App\Models\AreaActividad; // Import the AreaActividad model
use App\Models\TurnosAreasActividades; // Import the TurnosAreasActividades model

class ActividadesEvidenciasController extends Controller
{
    public function index()
    {
        $usuarioID = Auth::user()->id;
        $cliente = Usuario::find($usuarioID)->clientes->first();
        $sedes = Sede::where('cliente_id', $cliente->id)->get();
        $clientes = Cliente::all(); // Agregar esta línea para obtener todos los clientes
        return view('actividades-evidencias.index', compact('sedes', 'clientes')); // Pasar la variable $clientes a la vista
    }

    public function consultar(Request $request)
    {
        $sede_id = $request->sede_id;

        $turnos = SupervisorTurno::with(['supervisor', 'sede', 'turno', 'areas'])
            ->where('sede_id', $sede_id)
            ->get()
            ->map(function ($turno) {
                $actividadesCompletadasPorTurno = $turno->areas
                    ->map(function ($area) {
                        $actividadesCompletadasPorArea = $area->area->areasActividades->where('estado', false)->count();
                        $totalActividadesPorArea = $area->area->areasActividades->count();
                        return [
                            'actividades_completadas' => $actividadesCompletadasPorArea,
                            'total_actividades' => $totalActividadesPorArea,
                        ];
                    });

                $totalActividadesCompletadas = $actividadesCompletadasPorTurno->sum('actividades_completadas');
                $totalTotalActividades = $actividadesCompletadasPorTurno->sum('total_actividades');

                return [
                    'id' => $turno->id,
                    'fecha' => $turno->fecha_inicio,
                    'nombre_turno' => $turno->turno->nombre,
                    'regional' => $turno->sede->regional->nombre,
                    'actividades_completadas' => "$totalActividadesCompletadas/$totalTotalActividades",
                    'supervisor' => $turno->supervisor->nombres . ' ' . $turno->supervisor->apellidos,
                    'observaciones' => $turno->turno->observacion,
                ];
            });
        /* ->map(function ($turno) {
                $actividadesCompletadas = $turno->turno->actividades->where('estado', false)->count();
                $totalActividades = $turno->turno->actividades->count();
                return [
                    'id' => $turno->id,
                    'fecha' => $turno->fecha_inicio,
                    'nombre_turno' => $turno->turno->nombre,
                    'regional' => $turno->sede->regional->nombre,
                    'actividades_completadas' => "$actividadesCompletadas/$totalActividades",
                    'supervisor' => $turno->supervisor->nombres . ' ' . $turno->supervisor->apellidos,
                    'observaciones' => $turno->turno->observacion,
                ];
            }); */
        //dd($turnos);
        return response()->json($turnos);
    }

    public function getTurnoDetalle($id)
    {
        $turno = SupervisorTurno::with(['supervisor', 'sede', 'turno', 'areas'])->findOrFail($id);
        $actividades = $turno->areas->flatMap(function ($area) {
            return $area->area->areasActividades->map(function ($actividad) {
                return [
                    'id' => $actividad->actividad->id,
                    'nombre_area' => $actividad->area->nombre,
                    'nombre' => $actividad->actividad->nombre,
                    'descripcion' => $actividad->actividad->descripcion,
                    'estado' => $actividad->estado,
                    'calificacion' => $actividad->calificacion,
                ];
            });
        });

        return response()->json([
            'supervisor' => $turno->supervisor->nombres . ' ' . $turno->supervisor->apellidos,
            'sede' => $turno->sede->nombre,
            'fecha' => $turno->fecha_inicio,
            'actividades' => $actividades,
        ]);
    }

    public function getActividadDetalle(Request $request)
    {
        $turno_id = $request->turno_id;
        $area_id = $request->area_id;
        $sede_id = $request->sede_id;
        $actividad_id = $request->actividad_id;
        /* dd($turno_id, $area_id, $sede_id); */
        $supervisorTurno = SupervisorTurno::where('turno_id', $turno_id)
            ->where('sede_id', $sede_id)
            ->first();
        $area = TurnoArea::with(['area', 'area.actividades', 'area.areasActividades', 'turno.supervisor', 'supervisorTurno'])
            ->where('area_id', $area_id)
            ->where('turno_id', $supervisorTurno->id)
            ->first();
        $areaActividades = TurnosAreasActividades::with(['actividad'])->where('turnos_areas_id', $area->id)->get();
        $actividadDetalles = $areaActividades->firstWhere('actividad.id', $actividad_id);

        if ($actividadDetalles) {
            $actividadDetalles = [
            'nombre' => $actividadDetalles->actividad->nombre,
            'estado' => $actividadDetalles->estado,
            'calificacion' => $actividadDetalles->calificacion,
            'imagenes' => $actividadDetalles->imagenes->map(function ($imagen) {
                return [
                'url' => $imagen->imagen,
                ];
            }),
            ];
        }

        return response()->json($actividadDetalles);
        /* $actividad = Actividades::with('imagenes')->findOrFail($id);
        return response()->json([
            'nombre' => $actividad->nombre,
            'estado' => $actividad->estado,
            'calificacion' => $actividad->calificacion,
            'imagenes' => $actividad->imagenes->map(function ($imagen) {
                return [
                    'url' => $imagen->imagen,
                ];
            }),
        ]); */
    }

    public function exportarActividades(Request $request)
    {
        $sede_id = $request->sede_id;
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;

        return Excel::download(new ActividadesExport($sede_id, $fecha_inicio, $fecha_fin), 'actividades.xlsx');
    }

    public function obtenerConsolidado(Request $request)
    {
        $turno_id = $request->turno_id;
        $area_id = $request->area_id;
        $sede_id = $request->sede_id;

        $supervisorTurno = SupervisorTurno::where('turno_id', $turno_id)
            ->where('sede_id', $sede_id)
            ->first();

        if ($area_id) {
            $area = TurnoArea::with(['area', 'area.actividades','area.areasActividades', 'turno.supervisor', 'supervisorTurno'])
                ->where('area_id', $area_id)
                ->where('turno_id', $supervisorTurno->id)
                ->first();
            $areaActividades = TurnosAreasActividades::with(['actividad'])->where('turnos_areas_id', $area->id)->get();
            if ($area && $areaActividades) {
                return response()->json([
                    'supervisor' => $supervisorTurno->supervisor->nombres . ' ' . $supervisorTurno->supervisor->apellidos,
                    'fecha' => $supervisorTurno->created_at->format('d/m/Y'),
                    'actividades' => $areaActividades->map(function ($areaActividad) {
                        return [
                            'id' => $areaActividad->actividad->id,
                            'nombre' => $areaActividad->actividad->nombre,
                            'estado' => $areaActividad->estado,
                            'calificacion' => $areaActividad->calificacion,
                        ];
                    }),
                ]);
            } else {
                return response()->json(['error' => 'No data found for the given area_id'], 404);
            }
        }

        if ($supervisorTurno) {
            /* dd($supervisorTurno->id); */
            $areas = TurnoArea::with(['area'])
                ->where('turno_id', $supervisorTurno->id)
                ->get()
                ->map(function ($turnoArea) {
                    return [
                        'id' => $turnoArea->area->id,
                        'nombre' => $turnoArea->area->nombre,
                    ];
                });

            return response()->json($areas);
        }

        return response()->json(['error' => 'No data found for the given turno_id'], 404);
    }

    public function guardarSatisfaccion(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'supervisor_turnos_id' => 'required|exists:supervisor_turnos,id',
            'total_puntos' => 'required|integer|min:0|max:100', // Ajusta el rango según sea necesario
        ]);

        // Buscar o crear un registro en la tabla satisfaccion_servicio
        $satisfaccion = SatisfaccionServicio::firstOrNew([
            'supervisor_turnos_id' => $request->supervisor_turnos_id,
        ]);

        // Incrementar el acumulador y actualizar los puntos
        $satisfaccion->total_puntos += $request->total_puntos;
        $satisfaccion->total_encuestas = ($satisfaccion->total_encuestas ?? 0) + 1;
        $satisfaccion->save();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Satisfacción guardada exitosamente.'], 200);
    }
}
