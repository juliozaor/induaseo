<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class SedeController extends Controller
{
    public function guardar(Request $request)
    {
        
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'cliente' => 'required|exists:clientes,id',
                'ciudad' => 'required|exists:ciudades,id',
                'direccion' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'horarioInicio' => 'required|date_format:H:i',
                'horarioFin' => 'required|date_format:H:i',
                'estado' => 'required|boolean',
                'ciudad' => 'required|exists:ciudades,id',
                'regional' => 'required|exists:regionales,id',
            ]);
            Sede::create([
                'nombre' => $request->nombre,
                'cliente_id' => $request->cliente,
                'ciudad_id' => $request->ciudad,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'horario_inicio' => $request->horarioInicio,
                'horario_fin' => $request->horarioFin,
                'estado' => $request->estado,
                'regional_id' => $request->regional,
                'creador_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Sede creada con éxito']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function actualizar(Request $request, $id)
    {
        $sede = Sede::findOrFail($id);

        // Remove seconds from horarioInicio and horarioFin
        $request->merge([
            'horarioInicio' => substr($request->horarioInicio, 0, 5),
            'horarioFin' => substr($request->horarioFin, 0, 5),
        ]);
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'cliente' => 'required|exists:clientes,id',
                'ciudad' => 'required|exists:ciudades,id',
                'direccion' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'horarioInicio' => 'required|date_format:H:i',
                'horarioFin' => 'required|date_format:H:i',
                'estado' => 'required|boolean',
                'regional' => 'required|exists:regionales,id',
            ]);

            $sede->update([
                'nombre' => $request->nombre,
                'cliente_id' => $request->cliente,
                'ciudad_id' => $request->ciudad,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'horario_inicio' => $request->horarioInicio,
                'horario_fin' => $request->horarioFin,
                'estado' => $request->estado,
                'regional_id' => $request->regional,
                'actualizador_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Sede actualizada correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function obtenerSede(Request $request)
    {
        $id = $request->input('id');
        $sede = Sede::with('cliente', 'ciudad', 'regional')->findOrFail($id);
        return response()->json($sede);
    }

    public function consultar(Request $request)
    {
        $tabla = $request->input('tabla');
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);
        $cliente = $request->input('cliente');
        $estado = $request->input('estado');

        $query = Sede::with('cliente', 'ciudad.pais');

        if ($buscar) {
            $query->where('direccion', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', function($q) use ($buscar) {
                      $q->where('nombre', 'like', "%{$buscar}%");
                  })
                  ->orWhereHas('ciudad', function($q) use ($buscar) {
                      $q->where('nombre', 'like', "%{$buscar}%");
                  });
        }

        if ($cliente) {
            $query->where('cliente_id', $cliente);
        }

        if ($estado !== null) {
            $query->where('estado', $estado);
        }

        $sedes = $query->paginate($registrosPorPagina);

        return response()->json($sedes);
    }
}
