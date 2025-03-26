<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\SupervisorTurno;
use App\Models\Sede;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class AlertasController extends Controller
{
    public function index()
    {
        return view('alertas.index');
    }

    public function consultar(Request $request)
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
    }
}
