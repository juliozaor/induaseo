<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\SupervisorTurno;
use App\Models\Sede;
use App\Models\Usuario;

class SupervisorTurnoController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('asignar-turnos.index', compact('clientes'));
    }

    public function consultar(Request $request)
    {
        $turnos = SupervisorTurno::with(['supervisor', 'sede', 'turno'])
            ->where('sede_id', $request->sede_id)
            ->get()
            ->map(function ($turno) {
                $turno->turno->actividades_count = $turno->turno->actividades()->count();
                return $turno;
            });

        return response()->json($turnos);
    }

    public function guardar(Request $request)
    {
        $validatedData = $request->validate([
            'supervisor_id' => 'required|exists:usuarios,id',
            'sede_id' => 'required|exists:sedes,id',
            'turno_id' => 'required|exists:turnos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        SupervisorTurno::create($validatedData);

        return response()->json(['message' => 'Turno asignado con éxito']);
    }

    public function actualizar(Request $request, $id)
    {
        $validatedData = $request->validate([
            'supervisor_id' => 'required|exists:usuarios,id',
            'sede_id' => 'required|exists:sedes,id',
            'turno_id' => 'required|exists:turnos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $turno = SupervisorTurno::findOrFail($id);
        $turno->update($validatedData);

        return response()->json(['message' => 'Turno actualizado con éxito']);
    }

    public function getSedes(Request $request)
    {
        $sedes = Sede::where('cliente_id', $request->cliente_id)->get();
        return response()->json($sedes);
    }

    public function getSupervisores()
    {
        $supervisores = Usuario::whereHas('roles', function($query) {
            $query->where('name', 'supervisor');
        })->get();
        return response()->json($supervisores);
    }

    public function getTurno($id)
    {
        $turno = SupervisorTurno::with(['supervisor', 'sede', 'turno'])->findOrFail($id);
        return response()->json($turno);
    }

    public function getTareas($id)
    {
        $turno = SupervisorTurno::with(['supervisor', 'sede', 'turno.actividades'])->findOrFail($id);
        return response()->json([
            'supervisor' => $turno->supervisor,
            'sede' => $turno->sede,
            'tareas' => $turno->turno->actividades
        ]);
    }

    public function validarAsignacion(Request $request)
    {
        $exists = SupervisorTurno::where('supervisor_id', $request->supervisor_id)
            ->where('sede_id', $request->sede_id)
            ->where('turno_id', $request->turno_id)
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}