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

    public function getActividadDetalle($id)
    {
        $actividad = Actividades::with('imagenes')->findOrFail($id);
        return response()->json([
            'nombre' => $actividad->nombre,
            'estado' => $actividad->estado,
            'calificacion' => $actividad->calificacion,
            'imagenes' => $actividad->imagenes->map(function ($imagen) {
                return [
                    'url' => $imagen->imagen,
                ];
            }),
        ]);
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

        $supervisorTurno = SupervisorTurno::where('turno_id', $turno_id)->first();

        if ($area_id) {
            $area = TurnoArea::with(['area', 'area.actividades', 'turno.supervisor', 'supervisorTurno'])
                ->where('area_id', $area_id)
                ->where('turno_id', $supervisorTurno->id)
                ->first();
            //dd($supervisorTurno->supervisor->nombres);
            if ($area && $area->area->actividades) {
                return response()->json([
                    'supervisor' => $supervisorTurno->supervisor->nombres . ' ' . $supervisorTurno->supervisor->apellidos,
                    'fecha' => $supervisorTurno->created_at->format('d/m/Y'),
                    'actividades' => $area->area->actividades->map(function ($actividad) {
                        return [
                            'id' => $actividad->id,
                            'nombre' => $actividad->nombre,
                            'estado' => $actividad->estado,
                            'calificacion' => $actividad->calificacion,
                        ];
                    }),
                ]);
            } else {
                return response()->json(['error' => 'No data found for the given area_id'], 404);
            }
        }

        if ($supervisorTurno) {
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
}
