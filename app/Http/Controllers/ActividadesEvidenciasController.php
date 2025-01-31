<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\SupervisorTurno;
use App\Models\Sede;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class ActividadesEvidenciasController extends Controller
{
    public function index()
    {
        $usuarioID = Auth::user()->id;
        $cliente = Usuario::find($usuarioID)->clientes->first();
        $sedes = Sede::where('cliente_id', $cliente->id)->get();
        return view('actividades-evidencias.index', compact('sedes'));
    }

    public function consultar(Request $request)
    {
        $sede_id = $request->sede_id;

        $turnos = SupervisorTurno::with(['supervisor', 'sede', 'turno.actividades'])
            ->where('sede_id', $sede_id)
            ->get()
            ->map(function ($turno) {
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
            });
        return response()->json($turnos);
    }

    public function getTurnoDetalle($id)
    {
        $turno = SupervisorTurno::with(['supervisor', 'sede', 'turno.actividades'])->findOrFail($id);
        $actividades = $turno->turno->actividades->map(function ($actividad) {
            return [
                'id' => $actividad->id,
                'nombre' => $actividad->nombre,
                'descripcion' => $actividad->descripcion,
                'estado' => $actividad->estado,
                'calificacion' => $actividad->calificacion,
            ];
        });

        return response()->json([
            'supervisor' => $turno->supervisor->nombres . ' ' . $turno->supervisor->apellidos,
            'sede' => $turno->sede->nombre,
            'fecha' => $turno->fecha_inicio,
            'actividades' => $actividades,
        ]);
    }
}