<?php

namespace App\Http\Controllers;

use App\Models\SupervisorTurno;
use App\Models\Turno;
use App\Models\Actividades;
use App\Models\ImagenesActividades;
use App\Models\SedesActivos;
use App\Models\SedesInsumos;
use App\Models\Estados; // Importar el modelo Estados
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SeguimientoActividadesController extends Controller
{
    // Muestra la vista de inventario con los insumos y activos de las sedes
    public function inventario()
    {
        // Obtiene todos los insumos de las sedes
        $sedesInsumos = SedesInsumos::with('insumo.estados')->get();

        // Obtiene todos los activos de las sedes
        $sedesActivos = SedesActivos::with('activo.estados')->get();

        // Obtiene todos los estados
        $estados = Estados::all();

        // Retorna la vista con los insumos, activos y estados de las sedes
        return view('seguimiento-actividades.inventario', compact('sedesInsumos', 'sedesActivos', 'estados'));
    }

    // Obtener todos los estados
    public function obtenerEstados()
    {
        $estados = Estados::all();
        return response()->json($estados);
    }

    // Guarda las observaciones y novedades de un insumo
    public function guardarObservaciones(Request $request, $id)
    {
        // Valida los datos recibidos
        $request->validate([
            'novedades' => 'required|integer',
            'observaciones' => 'nullable|string',
        ]);

        // Encuentra el insumo de la sede por su ID
        $sedesInsumo = SedesInsumos::findOrFail($id);

        // Actualiza las observaciones y novedades del insumo
        $sedesInsumo->update([
            'novedades' => $request->novedades,
            'observaciones' => $request->observaciones,
        ]);

        // Retorna una respuesta exitosa
        return response()->json(['message' => 'Observaciones guardadas exitosamente']);
    }

    // Reporta un fallo en un activo
    public function reportarFallo(Request $request, $id)
    {
        // Valida los datos recibidos
        $request->validate([
            'observaciones' => 'required|string',
        ]);

        // Encuentra el activo de la sede por su ID
        $sedeActivo = SedesActivos::findOrFail($id);

        // Actualiza las observaciones del activo y marca el fallo
        $sedeActivo->update([
            'novedades' => 2, // Código para fallo
            'observaciones' => $request->observaciones,
        ]);

        // Retorna una respuesta exitosa
        return response()->json(['message' => 'Fallo reportado exitosamente']);
    }

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
        /* dd($sedesInsumos); */
        $sedesActivos = SedesActivos::with(['activo.estados', 'sede'])
            ->where('sede_id', $sedeId)
            ->get();
        /* dd($sedesActivos); */
        return view('seguimiento-actividades.inventario', compact('sedesActivos', 'sedesInsumos', 'sedeId'));
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

    public function actualizarInsumo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sedes_insumos,id',
            'estado_id' => 'required|exists:estados,id',
            'observacion' => 'nullable|string',
        ]);

        $sedesInsumo = SedesInsumos::find($request->id);
        $sedesInsumo->observacion = $request->observacion;
        $sedesInsumo->save();

        $insumo = $sedesInsumo->insumo;
        $insumo->estado_id = $request->estado_id;
        $insumo->save();

        return response()->json(['message' => 'Insumo actualizado correctamente']);
    }

    // Obtener detalles de un insumo
    public function obtenerInsumo($id)
    {
        $sedesInsumo = SedesInsumos::with('insumo.estados')->findOrFail($id);
        return response()->json([
            'insumo' => [
                'estado_id' => $sedesInsumo->insumo->estado_id
            ]
        ]);
    }

    public function obtenerObservaciones($id)
    {
        $sedesInsumo = SedesInsumos::find($id);
        if ($sedesInsumo) {
            return response()->json(['observacion' => $sedesInsumo->observacion]);
        } else {
            return response()->json(['error' => 'Insumo no encontrado'], 404);
        }
    }
}
