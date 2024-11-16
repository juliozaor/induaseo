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
                'cliente' => 'required|exists:clientes,id',
                'ciudad' => 'required|exists:ciudades,id',
                'direccion' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'horarioInicio' => 'required|date_format:H:i',
                'horarioFin' => 'required|date_format:H:i',
                'estado' => 'required|boolean',
            ]);

            Sede::create([
                'cliente_id' => $request->cliente,
                'ciudad_id' => $request->ciudad,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'horario_inicio' => $request->horarioInicio,
                'horario_fin' => $request->horarioFin,
                'estado' => $request->estado,
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

        try {
            $validatedData = $request->validate([
                'cliente' => 'required|exists:clientes,id',
                'ciudad' => 'required|exists:ciudades,id',
                'direccion' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'horarioInicio' => 'required|date_format:H:i',
                'horarioFin' => 'required|date_format:H:i',
                'estado' => 'required|boolean',
            ]);

            $sede->update([
                'cliente_id' => $request->cliente,
                'ciudad_id' => $request->ciudad,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'horario_inicio' => $request->horarioInicio,
                'horario_fin' => $request->horarioFin,
                'estado' => $request->estado,
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
        $sede = Sede::with('cliente', 'ciudad')->findOrFail($id);
        return response()->json($sede);
    }
}
