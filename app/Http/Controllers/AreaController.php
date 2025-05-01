<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Actividades;
use Exception;
use Illuminate\Validation\ValidationException;

use function PHPUnit\Framework\isNan;

class AreaController extends Controller
{
    // Listar todas las áreas con filtros opcionales
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);
        $clienteId = $request->input('cliente_id');
        $sedeId = $request->input('sede_id');
        $estado = $request->input('estado');

        $query = Area::with(['sede.cliente', 'actividades', 'creador', 'actualizador']);

        if ($buscar) {
            $query->where('nombre', 'like', "%{$buscar}%");
        }

        if ($clienteId) {
            $query->whereHas('sede.cliente', function ($q) use ($clienteId) {
                $q->where('id', $clienteId);
            });
        }

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        }

        if ($estado) {
            $query->where('estado', $estado == 1 ? 1 : 0);
        }

        $areas = $query->paginate($registrosPorPagina);

        return response()->json($areas);
    }

    // Almacenar una nueva área
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cliente' => 'required|exists:clientes,id',
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
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    // Mostrar detalles de un área específica
    public function show(Request $request)
    {
        $id = $request->input('id');
        $area = Area::with(['sede.cliente', 'creador', 'actualizador'])->findOrFail($id);
        return response()->json($area);
    }

    // Actualizar un área existente
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'sede' => 'sometimes|required|exists:sedes,id',
            'estado' => 'boolean',
        ]);

        $area = Area::findOrFail($id);
        try {
            //code...
            $area->update([
                'nombre' => $validated['nombre'],
                'sede_id' => $validated['sede'],
                'estado' => $validated['estado'],
                'actualizador_id' => Auth::id(),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el área', 'error' => $th->getMessage()], 500);
        }

        return response()->json(['message' => 'Área actualizada correctamente', 'area' => $area]);
    }

    // Eliminar un área
    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json(['message' => 'Área eliminada correctamente']);
    }

    // Obtener tareas para un área específica
    public function obtenerTareas($areaId)
    {
        // $tareas = Tarea::where('area_id', $areaId)->get();
        // return response()->json($tareas);
    }

    // Almacenar una nueva tarea para un área
    public function guardarTarea(Request $request)
    {
        // $validated = $request->validate([
        //     'area_id' => 'required|exists:areas,id',
        //     'nombre' => 'required|string|max:255',
        //     'descripcion' => 'nullable|string|max:255',
        // ]);

        // $tarea = Tarea::create([
        //     'area_id' => $validated['area_id'],
        //     'nombre' => $validated['nombre'],
        //     'descripcion' => $validated['descripcion'],
        // ]);

        // return response()->json(['message' => 'Tarea creada con éxito', 'tarea' => $tarea]);
    }

    // Eliminar una tarea
    public function eliminarTarea($id)
    {
        // $tarea = Tarea::findOrFail($id);
        // $tarea->delete();
        // return response()->json(['message' => 'Tarea eliminada correctamente']);
    }

    // Obtener actividades para un área específica
    public function obtenerActividades($areaId)
    {
        try {
            $actividades = Area::findOrFail($areaId)->actividades()->get();
            return response()->json($actividades);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    // Eliminar una actividad
    public function eliminarActividad($id)
    {
        $actividad = Actividades::findOrFail($id);
        $actividad->delete();
        return response()->json(['message' => 'Actividad eliminada correctamente.']);
    }

    // Almacenar una nueva actividad para un área
    public function guardarActividad(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'area_id' => 'required|exists:areas,id',
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:255',
            ]);

            $actividad = Actividades::create([
                'area_id' => $request->area_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            return response()->json(['message' => 'Actividad creada con éxito', 'actividad' => $actividad]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    // Verificar si un nombre de área ya existe en una ubicación específica
    public function verificarNombre(Request $request)
    {
        $nombre = $request->input('nombre');
        $sedeId = $request->input('sede_id');
        $exists = Area::where('nombre', $nombre)->where('sede_id', $sedeId)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function obtenerAreasPorSede($sedeId)
    {
        $areas = Area::where('sede_id', $sedeId)->get();
        return response()->json($areas);
    }
}
