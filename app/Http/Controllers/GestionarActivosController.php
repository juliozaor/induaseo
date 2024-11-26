<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\SedesActivos;
use App\Models\ImagenSedeActivo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
        //$estado = $request->input('estado');

        $query = SedesActivos::with(['sede.cliente', 'activo', 'estados', 'creador','actualizador']);

       /*  if ($buscar) {
            $query->where('direccion', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', function($q) use ($buscar) {
                      $q->where('nombre', 'like', "%{$buscar}%");
                  })
                  ->orWhereHas('ciudad', function($q) use ($buscar) {
                      $q->where('nombre', 'like', "%{$buscar}%");
                  });
        } */

       /*  if ($cliente) {
            $query->where('cliente_id', $cliente);
        }

        if ($estado !== null) {
            $query->where('estado', $estado);
        } */

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
                $file->move(public_path('imagenes'), $filename);
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
        $activo = SedesActivos::with('sede.cliente', 'activo', 'estados', 'creador','actualizador', 'imagenes')->findOrFail($id);
        return response()->json($activo);
    }
}