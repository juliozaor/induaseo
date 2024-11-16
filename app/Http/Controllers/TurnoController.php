<?php

namespace App\Http\Controllers;

use App\Models\Actividades;
use App\Models\Turno;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class TurnoController extends Controller
{
    public function guardar(Request $request)
    {
        
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'frecuencia' => 'required|exists:frecuencias,id',
                'detalleFrecuencia' => 'required|string|max:255',
                'estado' => 'required|boolean',
            ]);

            $turno = Turno::create([
                'nombre' => $request->nombre,
                'frecuencia_id' => $request->frecuencia,
                'frecuencia_cantidad' => $request->detalleFrecuencia,
                'estado' => $request->estado,
                'creador_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Turno creado con éxito', 'turno' => $turno]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function actualizar(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);

        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'frecuencia' => 'required|exists:frecuencias,id',
                'detalleFrecuencia' => 'required|string|max:255',
                'estado' => 'required|boolean',
            ]);

            $turno->update([
                'nombre' => $request->nombre,
                'frecuencia_id' => $request->frecuencia,
                'frecuencia_cantidad' => $request->detalleFrecuencia,
                'estado' => $request->estado,
                'actualizador_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Turno actualizado correctamente.', 'turno' => $turno]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function obtenerTurno(Request $request)
    {
        $id = $request->input('id');
        $turno = Turno::with(['frecuencia', 'actividades', 'creador', 'actualizador'])->findOrFail($id);
        return response()->json($turno);
    }

    public function obtenerTurnos(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);

        $query = Turno::with(['frecuencia', 'actividades', 'creador', 'actualizador']);

        if ($buscar) {
            $query->where('nombre', 'like', "%{$buscar}%");
        }

        $turnos = $query->paginate($registrosPorPagina);

        return response()->json($turnos);
    }

    public function obtenerActividades($turnoId)
    {
        
        try {
            $actividades = Actividades::where('turno_id', $turnoId)->get();
            return response()->json($actividades);
        } catch (Exception $e) {
            return response()->json([]);
        }
    }

    public function eliminarActividad($id)
    {
        $actividad = Actividades::findOrFail($id);
        $actividad->delete();
        return response()->json(['message' => 'Actividad eliminada correctamente.']);
    }

    public function guardarActividad(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'turno_id' => 'required|exists:turnos,id',
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:255',
            ]);
            
            $actividad = Actividades::create([
                'turno_id' => $request->turno_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);
            
            
            return response()->json(['message' => 'Actividad creada con éxito', 'actividad' => $actividad]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
