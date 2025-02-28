<?php

namespace App\Http\Controllers;

use App\Models\Actividades;
use Illuminate\Http\Request;

class ActividadesController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);

        $query = Actividades::query();

        if ($buscar) {
            $query->where('nombre', 'like', "%{$buscar}%");
        }

        $actividades = $query->paginate($registrosPorPagina);

        return response()->json($actividades);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|boolean',
        ]);

        $actividad = Actividades::create([
            'nombre' => $validated['nombre'],
            'estado' => $request->input('estado', 0)
        ]);

        return response()->json(['message' => 'Actividad creada con éxito', 'actividad' => $actividad], 201);
    }

    public function show(Request $request)
    {
        $id = $request->input('id');
        $actividad = Actividades::query()->findOrFail($id);
        //dd($actividad);
        if (!$actividad) {
            return response()->json(['error' => 'Actividad no encontrada'], 404);
        }
        return response()->json($actividad);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'estado' => 'required|boolean',
        ]);

        $actividad = Actividades::findOrFail($id);
        $actividad->update([
            'nombre' => $validated['nombre'],
            'estado' => $validated['estado']
        ]);

        return response()->json(['message' => 'Actividad actualizada correctamente', 'actividad' => $actividad]);
    }

    public function destroy($id)
    {
        $actividad = Actividades::findOrFail($id);
        $actividad->delete();

        return response()->json(['message' => 'Actividad eliminada exitosamente']);
    }

    public function verificarNombre(Request $request)
    {
        $nombre = strtolower($request->query('nombre'));
        $id = $request->query('id');

        $query = Actividades::whereRaw('LOWER(nombre) = ?', [$nombre]);
        if ($id) {
            $query->where('id', '!=', $id);
        }

        $exists = $query->exists();

        return response()->json(['exists' => $exists]);
    }
}
