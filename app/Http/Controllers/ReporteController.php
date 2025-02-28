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

        $areaSede = Area::with(['actividades'])->where('sede_id',$sede_id);

        $actividades = $areaSede->get()->map(function ($area) {
            $actividadesCompletadas = $area->actividades->where('estado', false)->count();
            $actividadesIncompletas = $area->actividades->where('estado', true)->count();
            return [
                'Area' => $area->nombre,
                'actividades_completadas' => $actividadesCompletadas,
                'actividades_incompletadas' => $actividadesIncompletas,
            ];
        });
        dd($actividades);
        $query = SupervisorTurno::with(['supervisor', 'sede', 'areas', 'turno'])
            ->where('sede_id', $sede_id);

        if ($fecha_inicio && $fecha_fin) {
            $query->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_fin]);
        }

        $turnos = $query->get()->map(function ($turno) {
            $actividadesCompletadas = $turno->turno->actividades->where('estado', false)->count();
            $actividadesIncompletas = $turno->turno->actividades->where('estado', true)->count();
            return [
                'fecha' => $turno->fecha_inicio,
                'actividades_completadas' => $actividadesCompletadas,
                'actividades_incompletadas' => $actividadesIncompletas,
            ];
        });

        return response()->json($turnos);
    }

    public function consultarTurnos(Request $request)
    {
        $sede_id = $request->sede_id;

        $turnos = SupervisorTurno::with(['turno'])
            ->where('sede_id', $sede_id)
            ->get()
            ->map(function ($supervisorTurno) {
                return [
                    'id' => $supervisorTurno->turno->id,
                    'nombre' => $supervisorTurno->turno->nombre,
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
}
