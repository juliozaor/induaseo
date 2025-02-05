<?php

namespace App\Http\Controllers;

use App\Models\Regionales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegionalesController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);

        $query = Regionales::with(['creador', 'actualizador']);

        if ($buscar) {
            $query->where('nombre', 'like', "%{$buscar}%");
        }

        $regionales = $query->paginate($registrosPorPagina);

        return response()->json($regionales);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'boolean',
        ]);

        $regional = Regionales::create([
            'nombre' => $validated['nombre'],
            'estado' => $request->input('estado', 0)
        ]);

        return response()->json(['message' => 'Regional creada con éxito', 'regional' => $regional], 201);
    }

    public function show(Request $request)
    {
        $id = $request->input('id');
        $regional = Regionales::query()->findOrFail($id);
        return response()->json($regional);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'estado' => 'boolean',
        ]);

        $regional = Regionales::findOrFail($id);
        $regional->update([
            'nombre' => $validated['nombre'],
            'estado' => $validated['estado']
        ]);

        return response()->json(['message' => 'Regional actualizada correctamente', 'regional' => $regional]);
    }

    public function destroy($id)
    {
        $regional = Regionales::findOrFail($id);
        $regional->delete();
        return response()->json(['message' => 'Regional eliminada correctamente'], 204);
    }
}
