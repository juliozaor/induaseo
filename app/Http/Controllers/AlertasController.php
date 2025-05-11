<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\SupervisorTurno;
use App\Models\Sede;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use App\Models\SupervisorTurnosFechas;
use App\Models\TurnosHistorialActividades;

class AlertasController extends Controller
{
    public function index()
    {
        return view('alertas.index');
    }

    /* public function consultar(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);
        $clienteId = $request->input('cliente_id');
        $sedeId = $request->input('sede_id');

        $query = SupervisorTurno::with(['supervisor', 'sede.cliente', 'turno', 'areas.area.actividades']);

        if ($buscar) {
            $query->whereHas('supervisor', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%$buscar%")
                  ->orWhere('apellidos', 'like', "%$buscar%");
            });
        }

        if ($clienteId) {
            $query->whereHas('sede', function ($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            });
        }

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        }

        $turnos = $query->get()->filter(function ($turno) {
            $actividadesCompletadas = $turno->areas
                ->map(function ($area) {
                    return $area->area->actividades->where('estado', false)->count();
                })->sum();
            $totalActividades = $turno->areas
                ->map(function ($area) {
                    return $area->area->actividades->count();
                })->sum();
            return $actividadesCompletadas != $totalActividades; // Only return tasks that are not completed
        });

        $paginatedTurnos = $turnos->forPage($request->page ?? 1, $registrosPorPagina);
        // dd($paginatedTurnos);
        $paginatedTurnos->transform(function ($turno) {
            $actividadesCompletadas = $turno->areas
                ->map(function ($area) {
                    return $area->area->actividades->where('estado', false)->count();
                })->sum();
            $totalActividades = $turno->areas
                ->map(function ($area) {
                    return $area->area->actividades->count();
                })->sum();
            return [
                'id' => $turno->id,
                'alerta' => false,
                'fecha' => now(),
                'turno' => $turno->fecha_inicio . ' - ' . $turno->fecha_fin,
                'nombre_turno' => $turno->turno->nombre,
                'regional' => $turno->sede->regional->nombre,
                'cliente' => $turno->sede->cliente->nombre,
                'sede' => $turno->sede->nombre,
                'actividades_completadas' => "$actividadesCompletadas/$totalActividades",
                'supervisor' => $turno->supervisor->nombres . ' ' . $turno->supervisor->apellidos,
                'celular' => $turno->supervisor->telefono,
                'observaciones' => $turno->turno->observacion,
            ];
        });

        return response()->json([
            'data' => $paginatedTurnos,
            'total' => $turnos->count(),
            'current_page' => $request->page ?? 1,
            'last_page' => ceil($turnos->count() / $registrosPorPagina),
        ]);
    } */

    public function consultar(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);
        $clienteId = $request->input('cliente_id');
        $sedeId = $request->input('sede_id');

        // Obtener los turnos asignados con sus relaciones
        $turnosAsignados = SupervisorTurno::with(['turno', 'supervisor', 'areas.area.actividades', 'areas.actividades.actividad']);

        // Filtrar por sede si se proporciona
        if ($sedeId) {
            $turnosAsignados->where('sede_id', $sedeId);
        }

        // Filtrar por cliente si se proporciona
        if ($clienteId) {
            $turnosAsignados->whereHas('sede', function ($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            });
        }

        // Filtrar por supervisor si se proporciona el término de búsqueda
        if ($buscar) {
            $turnosAsignados->whereHas('supervisor', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%$buscar%")
                    ->orWhere('apellidos', 'like', "%$buscar%");
            });
        }

        $historialTurnos = SupervisorTurnosFechas::with([
            'supervisorTurno',
            'supervisorTurno.turno',
            'supervisorTurno.supervisor',
            'supervisorTurno.areas.area.actividades',
            'supervisorTurno.areas.actividades.actividad',
            'supervisorTurno.areas.actividades.turnoHistorialActividades',
            'supervisorTurno.areas'
        ])->whereIn('supervisor_turno_id', $turnosAsignados->pluck('id'));
        /* dd($historialTurnos->get()); */
        $turnos = [];
        foreach ($historialTurnos->get() as $historialTurno) {
            $supervisorTurno = $historialTurno->supervisorTurno;
            if ($supervisorTurno && !$supervisorTurno->inicializado) {
                /* dd($supervisorTurno); */
                $actividadesCompletadas = $supervisorTurno->areas
                    ->map(function ($area) {
                        // Ensure the 'actividades' relationship is loaded and accessed correctly
                        $actividades = $area->actividades ?? collect(); // Use a fallback if the relationship is not loaded
                        return $actividades->map(function ($actividad) {
                            return $actividad->turnoHistorialActividades->where('estado', false)->count();
                        })->sum();
                    })->sum();
                $totalActividades = $supervisorTurno->areas
                    ->map(function ($area) {
                        $actividades = $area->actividades ?? collect(); // Use a fallback if the relationship is not loaded
                        return $actividades->map(function ($actividad) {
                            return $actividad->turnoHistorialActividades->count();
                        })->sum();
                    })->sum();

                $turnos[] = [
                    'id' => $supervisorTurno->id,
                    'alerta' => false,
                    'fecha' => now()->format('d-m-Y'),
                    'turno' => $historialTurno->fecha_inicio . ' - ' . $historialTurno->fecha_fin,
                    'nombre_turno' => $supervisorTurno->turno->nombre,
                    'regional' => $supervisorTurno->sede->regional->nombre,
                    'cliente' => $supervisorTurno->sede->cliente->nombre,
                    'sede' => $supervisorTurno->sede->nombre,
                    'actividades_completadas' => "$actividadesCompletadas/$totalActividades",
                    'supervisor' => $supervisorTurno->supervisor->nombres . ' ' . $supervisorTurno->supervisor->apellidos,
                    'celular' => $supervisorTurno->supervisor->telefono,
                    'observaciones' => $supervisorTurno->turno->observacion,
                ];
            }
        }

        // Paginación manual
        $currentPage = $request->page ?? 1;
        $paginatedTurnos = collect($turnos)->forPage($currentPage, $registrosPorPagina);

        return response()->json([
            'data' => $paginatedTurnos->values(),
            'total' => count($turnos),
            'current_page' => $currentPage,
            'last_page' => ceil(count($turnos) / $registrosPorPagina),
        ]);
    }
}
