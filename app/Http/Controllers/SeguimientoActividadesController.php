<?php

namespace App\Http\Controllers;

use App\Models\SupervisorTurno;
use App\Models\Turno;
use App\Models\Actividades;
use App\Models\ImagenesActividades;
use App\Models\SedesActivos;
use App\Models\SedesInsumos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SeguimientoActividadesController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $turnos = SupervisorTurno::with(['supervisor', 'sede', 'turno'])
            ->where('supervisor_id', $userId)
            ->get();
        return view('seguimiento-actividades.index', compact('turnos'));
    }

    public function obtenerActividades(Request $request)
    {
        $userId = Auth::id();
        $turnoId = $request->input('id') ?? "<script>document.write(localStorage.getItem('turno_id'))</script>";
        $sedeId = $request->input('sede_id') ?? "<script>document.write(localStorage.getItem('sede_id'))</script>";

        if (!$turnoId || !$sedeId) {
            return redirect()->route('seguimiento.actividades.index');
        }

        // Validate and store in localStorage if not exist
        echo "<script>
            if (!localStorage.getItem('turno_id') || !localStorage.getItem('sede_id')) {
                localStorage.setItem('turno_id', '$turnoId');
                localStorage.setItem('sede_id', '$sedeId');
            }
        </script>";

        $supervisorTurno = SupervisorTurno::with(['supervisor', 'sede', 'turno.actividades'])
            ->where('supervisor_id', $userId)
            ->whereHas('turno', function($query) use ($turnoId) {
                $query->where('id', $turnoId);
            })
            ->first();

        $actividadesTrue = $supervisorTurno->turno->actividades->where('estado', true)->values();
        $actividadesFalse = $supervisorTurno->turno->actividades->where('estado', false)->values();

        return view('seguimiento-actividades.actividades', compact('supervisorTurno', 'actividadesTrue', 'actividadesFalse', 'sedeId', 'turnoId'));
    }

    public function guardarCalificacion(Request $request, $id)
    {
        $actividad = Actividades::findOrFail($id);
        $actividad->calificacion = $request->input('calificacion');
        $actividad->estado = false;
        $actividad->save();

        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                $path = $file->store('evidencias', 'public');
                ImagenesActividades::create([
                    'actividad_id' => $actividad->id,
                    'imagen' => $path
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

        $sedesInsumos = SedesInsumos::with(['insumo.estados', 'sede'])
            ->where('sede_id', $sedeId)
            ->get();

        $sedesAcivos = SedesActivos::with(['activo', 'sede'])
            ->where('sede_id', $sedeId)
            ->get();
    return view('seguimiento-actividades.inventario', compact('sedesAcivos','sedesInsumos', 'sedeId'));
    }

    public function finalizarTurno(Request $request)
    {
        $userId = Auth::id();
        $turno = SupervisorTurno::where('supervisor_id', $userId)->latest()->first();
        
        if ($turno) {
            $turno->turno->observacion = $request->input('observaciones');
            $turno->turno->estado = false;
            $turno->turno->save();
        }

        return redirect()->route('inventarios.turno')->with('success', 'Turno finalizado correctamente.');
    }
}
