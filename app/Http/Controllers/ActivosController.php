<?php

namespace App\Http\Controllers;

use App\Models\Activos;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ActivosController extends Controller
{
    public function guardar(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombreElemento' => 'required|string|max:255',
                'marca' => 'required|string|max:255',
                'serie' => 'required|string|max:255',
                'clasificacion' => 'required|exists:clasificaciones,id',
                'cantidad' => 'required|integer',
                'estadoActivo' => 'required|exists:estados,id',
                'estado' => 'required|boolean',
            ]);

            Activos::create([
                'nombre_elemento' => $request->nombreElemento,
                'marca' => $request->marca,
                'serie' => $request->serie,
                'clasificacion_id' => $request->clasificacion,
                'cantidad' => $request->cantidad,
                'estado_id' => $request->estadoActivo,
                'estado' => $request->estado,
                'creador_id' => Auth::id(),
            ]);


            return response()->json(['message' => 'Activo creado con éxito']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function actualizar(Request $request, $id)
    {
        $activo = Activos::findOrFail($id);

        try {
            $validatedData = $request->validate([
                'nombreElemento' => 'required|string|max:255',
                'marca' => 'required|string|max:255',
                'serie' => 'required|string|max:255',
                'clasificacion' => 'required|exists:clasificaciones,id',
                'cantidad' => 'required|integer',
                'estadoActivo' => 'required|exists:estados,id',
                'estado' => 'required|boolean',
            ]);

            $activo->update([
                'nombre_elemento' => $request->nombreElemento,
                'marca' => $request->marca,
                'serie' => $request->serie,
                'clasificacion_id' => $request->clasificacion,
                'cantidad' => $request->cantidad,
                'estado_id' => $request->estadoActivo,
                'estado' => $request->estado,
                'actualizador_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Activo actualizado correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function obtenerActivo(Request $request)
    {
        $id = $request->input('id');
        $activo = Activos::with('clasificacion', 'estado')->findOrFail($id);
        return response()->json($activo);
    }

    public function consultar(Request $request)
    {
        $tabla = $request->input('tabla');
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);
        $clasificacion = $request->input('clasificacion');
        $estado = $request->input('estado');

        $query = Activos::with('clasificacion', 'estado');

        if ($buscar) {
            $query->where('nombre_elemento', 'like', "%{$buscar}%")
                  ->orWhere('marca', 'like', "%{$buscar}%")
                  ->orWhere('serie', 'like', "%{$buscar}%");
        }

        if ($clasificacion) {
            $query->where('clasificacion_id', $clasificacion);
        }

        if ($estado !== null) {
            $query->where('estado', $estado);
        }

        $activos = $query->paginate($registrosPorPagina);

        return response()->json($activos);
    }
}