<?php

namespace App\Http\Controllers;

use App\Models\Insumos;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InsumosController extends Controller
{
    public function guardar(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombreInsumo' => 'required|string|max:255',
                'marcaInsumo' => 'required|string|max:255',
                'codigoInsumo' => 'required|string|max:255',
                'clasificacionInsumo' => 'required|exists:clasificaciones,id',
                'cantidadInsumo' => 'required|integer',
                'estadoInsumo' => 'required|exists:estados,id',
                'estado' => 'required|boolean',
                'proveedorInsumo' => 'required|string|max:255',
                'telefonoProveedorInsumo' => 'required|string|max:255',
            ]);

            

            $insumo = new Insumos();
            $insumo->nombre_elemento = $request->input('nombreInsumo');
            $insumo->marca = $request->input('marcaInsumo');
            $insumo->codigo = $request->input('codigoInsumo');
            $insumo->clasificacion_id = $request->input('clasificacionInsumo');
            $insumo->cantidad = $request->input('cantidadInsumo');
            $insumo->estado_id = $request->input('estadoInsumo');
            $insumo->estado = $request->input('estado');
            $insumo->proveedor = $request->input('proveedorInsumo');
            $insumo->telefono_proveedor = $request->input('telefonoProveedorInsumo');
            $insumo->creador_id = Auth::id();
            $insumo->save();

            return response()->json(['message' => 'Insumo creado exitosamente']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Error al guardar el insumo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al guardar el insumo'], 500);
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'nombreInsumo' => 'required|string|max:255',
                'marcaInsumo' => 'required|string|max:255',
                'codigoInsumo' => 'required|string|max:255',
                'clasificacionInsumo' => 'required|exists:clasificaciones,id',
                'cantidadInsumo' => 'required|integer',
                'estadoInsumo' => 'required|exists:estados,id',
                'estado' => 'required|boolean',
                'proveedorInsumo' => 'required|string|max:255',
                'telefonoProveedorInsumo' => 'required|string|max:255',
            ]);

            $insumo = Insumos::findOrFail($id);
            $insumo->nombre_elemento = $request->input('nombreInsumo');
            $insumo->marca = $request->input('marcaInsumo');
            $insumo->codigo = $request->input('codigoInsumo');
            $insumo->clasificacion_id = $request->input('clasificacionInsumo');
            $insumo->cantidad = $request->input('cantidadInsumo');
            $insumo->estado_id = $request->input('estadoInsumo');
            $insumo->estado = $request->input('estado');
            $insumo->proveedor = $request->input('proveedorInsumo');
            $insumo->telefono_proveedor = $request->input('telefonoProveedorInsumo');
            $insumo->actualizador_id = Auth::id();
            $insumo->save();

            return response()->json(['message' => 'Insumo actualizado exitosamente']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Error al actualizar el insumo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el insumo'], 500);
        }
    }

    public function obtener(Request $request)
    {
        try {
            $id = $request->input('id');
            $insumo = Insumos::with(['clasificacion', 'estado', 'creador', 'actualizador'])->findOrFail($id);
            return response()->json($insumo);
        } catch (Exception $e) {
            Log::error('Error al obtener el insumo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener el insumo'], 500);
        }
    }

    public function consultar(Request $request)
    {
        try {
            $buscar = $request->input('buscar');
            $registrosPorPagina = $request->input('registros_por_pagina', 10);
            $clasificacion = $request->input('clasificacion');
            $estado = $request->input('estado');

            $query = Insumos::with(['clasificacion', 'estado']);

            if ($buscar) {
                $query->where('nombre_elemento', 'like', "%{$buscar}%")
                      ->orWhere('marca', 'like', "%{$buscar}%")
                      ->orWhere('codigo', 'like', "%{$buscar}%");
            }

            if ($clasificacion) {
                $query->where('clasificacion_id', $clasificacion);
            }

            if ($estado !== null) {
                $query->where('estado_id', $estado);
            }

            $insumos = $query->paginate($registrosPorPagina);

            return response()->json($insumos);
        } catch (Exception $e) {
            Log::error('Error al consultar los insumos: ' . $e->getMessage());
            return response()->json(['error' => 'Error al consultar los insumos'], 500);
        }
    }
}