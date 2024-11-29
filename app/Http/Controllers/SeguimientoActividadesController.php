<?php

namespace App\Http\Controllers;

use App\Models\SupervisorTurno;
use App\Models\Turno;
use App\Models\Actividades;
use App\Models\ImagenesActividades;
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
        $id = $request->input('id');
        $supervisorTurno = SupervisorTurno::with(['supervisor', 'sede', 'turno.actividades'])
            ->where('supervisor_id', $userId)
            ->whereHas('turno', function($query) use ($id) {
                $query->where('id', $id);
            })
            ->first();

        $actividadesTrue = $supervisorTurno->turno->actividades->where('estado', true)->values();
        $actividadesFalse = $supervisorTurno->turno->actividades->where('estado', false)->values();

        return view('seguimiento-actividades.actividades', compact('supervisorTurno', 'actividadesTrue', 'actividadesFalse'));
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
}
