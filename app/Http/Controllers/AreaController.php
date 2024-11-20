<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);

        $query = Area::with([ 'sede.cliente', 'creador', 'actualizador']);

        if ($buscar) {
            $query->where('nombre', 'like', "%{$buscar}%");
        }

        $areas = $query->paginate($registrosPorPagina);

        return response()->json($areas);
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'sede' => 'required|exists:sedes,id',
            'estado' => 'boolean',
        ]);

        $area = Area::create([
            'nombre' => $validated['nombre'],
            'sede_id' => $validated['sede'],
            'estado' => $request->input('estado', 0), // Default to 0 if not provided
            'creador_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Área creada con éxito', 'area' => $area], 201);
    }

    public function show(Request $request)
    {
        $id = $request->input('id');
        $area = Area::with(['sede.cliente', 'creador', 'actualizador'])->findOrFail($id);
        return response()->json($area);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'sede' => 'sometimes|required|exists:sedes,id',
            'estado' => 'boolean',
        ]);

        $area = Area::findOrFail($id);
        $area->update([
            'nombre' => $validated['nombre'],
            'sede_id' => $validated['sede'],
            'estado' => $validated['estado'],
            'actualizador_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Área actualizada correctamente', 'area' => $area]);
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json(['message' => 'Área eliminada correctamente'], 204);
    }

    public function obtenerTareas($areaId)
    {
        $tareas = Tarea::where('area_id', $areaId)->get();
        return response()->json($tareas);
    }

    public function guardarTarea(Request $request)
    {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $tarea = Tarea::create([
            'area_id' => $validated['area_id'],
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
        ]);

        return response()->json(['message' => 'Tarea creada con éxito', 'tarea' => $tarea]);
    }

    public function eliminarTarea($id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->delete();
        return response()->json(['message' => 'Tarea eliminada correctamente']);
    }
}