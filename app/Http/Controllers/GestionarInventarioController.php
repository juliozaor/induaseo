<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Sede;
use App\Models\Inventario;
use App\Models\ImagenInventario;
use App\Models\Insumo;
use App\Models\Insumos;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Activos;

class GestionarInventarioController extends Controller
{
    // Método para mostrar la vista principal de gestión de inventario
    public function index()
    {
        $clientes = Cliente::all();
        return view('gestionar-inventario.index', compact('clientes'));
    }

    // Método para consultar los inventarios con paginación
    public function consultar(Request $request)
    {
        //dd($request->all());
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);

        $inventarioQuery = Inventario::with(['sede.cliente', 'item', 'estados', 'creador', 'actualizador']);

        if ($buscar) {
            $inventarioQuery->whereHas('item', function ($q) use ($buscar) {
                    $q->where('nombre_elemento', 'like', "%{$buscar}%");
                })
                ->orWhereHas('sede', function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%");
                })
                ->orWhereHas('sede.cliente', function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%");
                });
        }

        $inventarios = $inventarioQuery->paginate($registrosPorPagina);

        // Obtener los elementos de la paginación
        $inventariosData = $inventarios->items();

        // Mapear los datos de los inventarios
        $inventariosData = array_map(function ($inventario) {
            return [
                'id' => $inventario->id,
                'nombre' => $inventario->item->nombre_elemento,
                'cantidad' => $inventario->cantidad,
                'sede' => $inventario->sede->nombre,
                'cliente' => $inventario->sede->cliente->nombre,
                'creado_por' => $inventario->creador->nombres ?? 'N/A',
                'ultima_actualizacion' => $inventario->updated_at->format('d-m-Y'),
                'editado_por' => $inventario->actualizador->nombres ?? 'N/A',
            ];
        }, $inventariosData);

        // Devolver los datos en formato JSON
        return response()->json([
            'data' => $inventariosData,
            'total' => $inventarios->total(),
            'per_page' => $inventarios->perPage(),
            'current_page' => $inventarios->currentPage(),
            'last_page' => $inventarios->lastPage(),
        ]);
    }

    // Método para guardar un nuevo inventario
    public function guardar(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validatedData = $request->validate([
                'clienteSelect' => 'required|exists:clientes,id',
                'sedeSelect' => 'required|exists:sedes,id',
                'itemSelect' => 'required|exists:insumos,id',
                'cantidadInput' => 'required|integer|min:1',
                'imagenesInput' => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            // Crear un nuevo inventario
            $inventario = Inventario::create([
                'sede_id' => $request->sedeSelect,
                'insumo_id' => $request->itemSelect,
                'cantidad' => $request->cantidadInput,
                'estado_id' => 1,
                'estado' => 1,
                'creador_id' => Auth::id(),
            ]);
            if ($request->hasFile('imagenesInput')) {
                $file = $request->file('imagenesInput');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->move(public_path('imagenes'), $filename);
                $imagePath = 'imagenes/' . $filename;
                ImagenInventario::create([
                    'inventario_id' => $inventario->id,
                    'imagen' => $imagePath,
                ]);
                // Actualizar la imagen en la tabla de insumos
                $insumo = Insumos::find($request->itemSelect);
                $insumo->update(['imagen' => $imagePath]);
            }

            return response()->json(['message' => 'Inventario guardado exitosamente']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    // Método para actualizar un inventario existente
    public function actualizar(Request $request, $id)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'clienteSelect' => 'required|exists:clientes,id',
            'sedeSelect' => 'required|exists:sedes,id',
            'itemSelect' => 'required|exists:insumos,id',
            'cantidadInput' => 'required|integer|min:1',
            'imagenesInput' => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Buscar el inventario por ID
        $inventario = Inventario::find($id);
        // Actualizar los datos del inventario
        $inventario->update([
            'sede_id' => $request->sedeSelect,
            'insumo_id' => $request->itemSelect,
            'cantidad' => $request->cantidadInput,
            'estado_id' => 1,
            'estado' => 1,
            'actualizador_id' => Auth::id(),
        ]);

        if ($request->hasFile('imagenesInput')) {
            $file = $request->file('imagenesInput');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->move(public_path('imagenes'), $filename);
            $imagePath = 'imagenes/' . $filename;
            ImagenInventario::create([
                'inventario_id' => $inventario->id,
                'imagen' => $imagePath,
            ]);
            // Actualizar la imagen en la tabla de insumos
            $insumo = Insumos::find($request->itemSelect);
            $insumo->update(['imagen' => $imagePath]);
        }

        return response()->json(['message' => 'Inventario actualizado exitosamente', 'inventario' => $inventario]);
    }

    // Método para obtener los datos de un inventario específico
    public function obtenerInventario(Request $request)
    {
        $id = $request->input('id');
        $inventario = Inventario::with('sede.cliente', 'item', 'estados', 'creador', 'actualizador')->findOrFail($id);
        return response()->json($inventario);
    }

    public function getSedes(Request $request)
    {
        $clienteId = $request->input('cliente_id');
        $sedes = Sede::where('cliente_id', $clienteId)->get();
        return response()->json($sedes);
    }

    public function obtenerItem($id)
    {
        //dd($id);
        $itemInsumo = Insumos::with('imagenes')->findOrFail($id);

        if (!$itemInsumo) {
            return response()->json(['error' => 'Articulo no encontrado'], 404);
        }

        return response()->json($itemInsumo);
    }

    public function obtenerItems()
    {
        $insumos = Insumos::all();
        $itemsInsumos = $insumos->map(function ($item) {
            return [
                'id' => $item->id,
                'nombre_elemento' => $item->nombre_elemento,
            ];
        });

        return response()->json($itemsInsumos);
    }

    public function destroy($id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->delete();

        return response()->json(['message' => 'Inventario eliminado con éxito.']);
    }
}
