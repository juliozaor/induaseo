<?php

namespace App\Http\Controllers;

use App\Models\Informacion;
use Illuminate\Support\Facades\Auth;
use App\Models\SupervisorTurno;
use Illuminate\Http\Request;
use App\Models\Categorias; // Add this import

class InformacionNovedadesController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $turnos = SupervisorTurno::with(['supervisor', 'sede', 'turno'])
            ->where('supervisor_id', $userId)
            ->get();
        return view('seguimiento-actividades.informacion', compact('turnos'));
    }

    public function buscarInformacion(Request $request)
    {
        $buscar = $request->input('buscar');

        $informacion = Informacion::query()
            ->when($buscar, function ($query, $buscar) {
                return $query->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            })
            ->with('tipoMultimedia') // Ensure the relationship is loaded
            ->get();

        return response()->json($informacion);
    }

    public function obtenerCategorias()
    {
        $categorias = Categorias::all();
        return response()->json($categorias);
    }
}
