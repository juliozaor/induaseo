<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\SupervisorTurno;
use App\Models\Sede;
use App\Models\SedesActivos;
use App\Models\TurnoArea;
use App\Models\AreaActividad;
use App\Models\Area;
use App\Models\Usuario;
use App\Models\SatisfaccionServicio;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActividadesExport;
use App\Exports\ActivosExport;
use App\Exports\InsumosExport;
use App\Models\Inventario;

class ReporteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('reportes.index', compact('clientes'));
    }

    public function consultar(Request $request)
    {
        $sede_id = $request->sede_id;
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;

        // Obtener los turnos asignados con sus relaciones
        $turnosAsignados = SupervisorTurno::with(['turno', 'supervisor', 'areas.area.actividades'])
            ->where('sede_id', $sede_id);

        // Filtrar por rango de fechas si se proporcionan
        if ($fecha_inicio && $fecha_fin) {
            $turnosAsignados->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_fin]);
        }

        // Mapear los turnos asignados para devolver la estructura deseada
        $turnos = $turnosAsignados->get()->map(function ($turnoAsignado) {
            $actividadesCompletadas = $turnoAsignado->areas->map(function ($area) use ($turnoAsignado) {
                return $area->area->actividades->where('estado', false)->map(function ($actividad) use ($turnoAsignado, $area) {
                    return [
                        'fecha' => $turnoAsignado->fecha_inicio,
                        'area' => $area->area->nombre,
                        'actividad' => $actividad->nombre,
                        'estado' => 'completada'
                    ];
                });
            })->flatten(1);

            $actividadesIncompletadas = $turnoAsignado->areas->map(function ($area) use ($turnoAsignado) {
                return $area->area->actividades->where('estado', true)->map(function ($actividad) use ($turnoAsignado, $area) {
                    return [
                        'fecha' => $turnoAsignado->fecha_inicio,
                        'area' => $area->area->nombre,
                        'actividad' => $actividad->nombre,
                        'estado' => 'incompleta'
                    ];
                });
            })->flatten(1);

            return $actividadesCompletadas->merge($actividadesIncompletadas);
        })->flatten(1);

        return response()->json($turnos);
    }

    public function consultarTurnos(Request $request)
    {
        $sede_id = $request->sede_id;

        $turnos = SupervisorTurno::with(['turno'])
            ->where('sede_id', $sede_id)
            ->get()
            ->map(function ($supervisorTurno) {
                $satisfaccion = SatisfaccionServicio::where('supervisor_turnos_id', $supervisorTurno->id)->first();
                return [
                    'supervisor_turno_id' => $supervisorTurno->id,
                    'id' => $supervisorTurno->turno->id,
                    'nombre' => $supervisorTurno->turno->nombre,
                    'satisfaccion' => $satisfaccion ? $satisfaccion->promedio : null,
                    'total_encuestas' => $satisfaccion ? $satisfaccion->total_encuestas : null,
                ];
            });

        return response()->json($turnos);
    }

    public function consultarAreas(Request $request)
    {
        $turno_id = $request->turno_id;

        $supervisorTurno = SupervisorTurno::where('turno_id', $turno_id)->first();

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

        return response()->json([]);
    }

    public function consultarActividades(Request $request)
    {
        $area_id = $request->area_id;

        $actividades = AreaActividad::with(['actividad'])
            ->where('area_id', $area_id)
            ->get()
            ->map(function ($areaActividad) {
                return [
                    'id' => $areaActividad->actividad->id,
                    'nombre' => $areaActividad->actividad->nombre,
                ];
            });

        return response()->json($actividades);
    }

    public function activos(Request $request)
    {
        $sede_id = $request->sede_id;

        $query = SedesActivos::with(['activo', 'estados'])
            ->where('sede_id', $sede_id);

        $activos = $query->get()->map(function ($activo) {
            return [
                'activo' => $activo->activo->nombre_elemento,
                'cantidad' => $activo->cantidad,
                'estado' => $activo->estados->nombre,
                'observacion' => $activo->observacion
            ];
        });

        return response()->json($activos);
    }

    public function insumos(Request $request)
    {
        $sede_id = $request->sede_id;

        $query = Inventario::with(['item', 'estados'])
            ->where('sede_id', $sede_id);
        /* dd($query->get()); */
        $insumos = $query->get()->map(function ($insumo) {
            return [
                'insumo' => $insumo->item->nombre_elemento,
                'cantidad' => $insumo->cantidad,
                'estado' => $insumo->estados->nombre
            ];
        });

        return response()->json($insumos);
    }

    public function exportarActividades(Request $request)
    {
        $sede_id = $request->sede_id;
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;

        return Excel::download(new ActividadesExport($sede_id, $fecha_inicio, $fecha_fin), 'actividades.xlsx');
    }

    public function exportarActivos(Request $request)
    {
        $sede_id = $request->sede_id;

        return Excel::download(new ActivosExport($sede_id), 'activos.xlsx');
    }
    public function exportarInsumos(Request $request)
    {
        $sede_id = $request->sede_id;

        return Excel::download(new InsumosExport($sede_id), 'insumos.xlsx');
    }
}
