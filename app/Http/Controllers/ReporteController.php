<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\SupervisorTurno;
use App\Models\Sede;
use App\Models\SedesActivos;
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

        $query = SupervisorTurno::with(['supervisor', 'sede', 'turno.actividades'])
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