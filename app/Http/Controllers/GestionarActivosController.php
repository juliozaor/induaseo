<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\SedesActivos;
use App\Models\ImagenSedeActivo;
use App\Models\Mantenimiento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\error;

class GestionarActivosController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('gestionar-activos.index', compact('clientes'));
    }

    public function consultar(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);
        $estado = $request->input('estado_id');
        $sedeId = $request->input('sede_id');

        $query = SedesActivos::with(['sede.cliente', 'activo', 'estados', 'creador', 'actualizador']);

        if ($buscar) {
            $query->whereHas('activo', function($q) use ($buscar) {
                $q->where('nombre_elemento', 'like', "%{$buscar}%");
            });
        }

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        }

        if ($estado !== null) {
            $query->where('estado_id', $estado);
        }

        $activos = $query->paginate($registrosPorPagina);

        return response()->json($activos);
    }

    public function guardar(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'activoSelect' => 'required|exists:activos,id',
                'cantidad' => 'required|integer|min:1',
                'estado' => 'required|boolean',
                'estadoActivo' => 'required|exists:estados,id',
                'imagenesInput' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $activo = SedesActivos::create([
                'sede_id' => $request->sede_id,
                'activo_id' => $request->activoSelect,
                'numero_serie' => $request->codigoInput,
                'cantidad' => $request->cantidad,
                'estado_id' => $request->estadoActivo,
                'estado' => $request->estado,
                'creador_id' => Auth::id(),
            ]);

            if ($request->hasFile('imagenesInput')) {
                $file = $request->file('imagenesInput');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('imagenes', $filename, 'public');
                ImagenSedeActivo::create([
                    'sede_activo_id' => $activo->id,
                    'imagen' => 'imagenes/' . $filename,
                ]);
            }

            return response()->json(['message' => 'Activo guardado exitosamente']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function actualizar(Request $request, $id)
    {

        $validatedData = $request->validate([
            'activoSelect' => 'required|exists:activos,id',
            'cantidad' => 'required|integer|min:1',
            'estado' => 'required|boolean',
            'estadoActivo' => 'required|exists:estados,id',
            'imagenesInput' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $activo = SedesActivos::findOrFail($id);

        $activo->update([
            'sede_id' => $request->sede_id,
            'activo_id' => $request->activoSelect,
            'numero_serie' => $request->codigoInput,
            'cantidad' => $request->cantidad,
            'estado_id' => $request->estadoActivo,
            'estado' => $request->estado,
            'actualizador_id' => Auth::id(),
        ]);


        if ($request->hasFile('imagenesInput')) {
            // Delete old images
            ImagenSedeActivo::where('sede_activo_id', $activo->id)->delete();
            Storage::disk('public')->delete($activo->imagenes->pluck('imagen')->toArray());

            $file = $request->file('imagenesInput');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('imagenes'), $filename);
            ImagenSedeActivo::create([
                'sede_activo_id' => $activo->id,
                'imagen' => 'imagenes/' . $filename,
            ]);
        }

        return response()->json(['message' => 'Activo actualizado exitosamente', 'activo' => $activo]);
    }

    public function obtenerActivo(Request $request)
    {
        $id = $request->input('id');
        $activo = SedesActivos::with('sede.cliente', 'activo', 'estados', 'creador', 'actualizador', 'imagenes')->findOrFail($id);
        return response()->json($activo);
    }

    public function obtenerMantenimientos(Request $request)
    {
        try {
            $sedeId = $request->input('sede_id');
            $estadoId = $request->input('estado_id');
            $registrosPorPagina = $request->input('registros_por_pagina', 10);
            $buscar = $request->input('buscar');

            $mantenimientos = Mantenimiento::with(['estado', 'creador', 'actualizador', 'sedes_activos.activo', 'sedes_activos.sede.cliente'])
                ->whereHas('sedes_activos', function ($query) use ($sedeId) {
                    $query->where('sede_id', $sedeId);
                })
                ->when($estadoId, function ($query, $estadoId) {
                    return $query->where('estado_id', $estadoId);
                })
                ->when($buscar, function ($query, $buscar) {
                    return $query->whereHas('sedes_activos.activo', function ($q) use ($buscar) {
                        $q->where('nombre_elemento', 'like', "%{$buscar}%");
                    });
                })
                ->paginate($registrosPorPagina);

            return response()->json($mantenimientos->items());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching mantenimientos: ' . $e->getMessage()], 500);
        }
    }

    public function obtenerDatosMantenimiento($id)
    {
        $mantenimiento = Mantenimiento::with(['estado', 'sedes_activos.activo', 'sedes_activos.sede.cliente'])
            ->findOrFail($id);
        return response()->json($mantenimiento);
    }

    public function actualizarMantenimiento(Request $request, $id)
    {
        $validatedData = $request->validate([
            'observaciones' => 'required|string',
        ]);

        $mantenimiento = Mantenimiento::findOrFail($id);
        $mantenimiento->update([
            'observaciones' => $request->observaciones,
            'actualizador_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Mantenimiento actualizado exitosamente']);
    }
}
