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

        $query = Inventario::with(['sede.cliente', 'item', 'estado', 'creador', 'actualizador']);

        if ($buscar) {
            $query->where('nombre', 'like', "%{$buscar}%")
                  ->orWhereHas('sede', function($q) use ($buscar) {
                      $q->where('nombre', 'like', "%{$buscar}%");
                  })
                  ->orWhereHas('sede.cliente', function($q) use ($buscar) {
                      $q->where('nombre', 'like', "%{$buscar}%");
                  });
        }

        $inventarios = $query->paginate($registrosPorPagina);

        // Obtener los elementos de la paginación
        $inventariosData = $inventarios->items();

        // Mapear los datos de los inventarios
        $inventariosData = array_map(function ($inventario) {
            return [
                'id' => $inventario->id,
                'nombre' => $inventario->item->nombre,
                'cantidad' => $inventario->cantidad,
                'sede' => $inventario->sede->nombre,
                'cliente' => $inventario->sede->cliente->nombre,
                'creado_por' => $inventario->creador->nombres,
                'ultima_actualizacion' => $inventario->updated_at->format('d-m-Y'),
                'editado_por' => $inventario->actualizador->nombres,
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
                'itemSelect' => 'required|exists:insumos,id',
                'cantidad' => 'required|integer|min:1',
                'estado' => 'required|boolean',
                'estadoInventario' => 'required|exists:estados,id',
                'imagenesInput' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'sedeSelect' => 'required|array|min:1',
                'sedeSelect.*' => 'exists:sedes,id',
            ]);

            // Crear un nuevo inventario para cada sede seleccionada
            foreach ($request->sedeSelect as $sedeId) {
                $inventario = Inventario::create([
                    'sede_id' => $sedeId,
                    'item_id' => $request->itemSelect,
                    'cantidad' => $request->cantidad,
                    'estado_id' => $request->estadoInventario,
                    'estado' => $request->estado,
                    'creador_id' => Auth::id(),
                ]);

                // Guardar la imagen del inventario si existe
                if ($request->hasFile('imagenesInput')) {
                    $file = $request->file('imagenesInput');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('imagenes'), $filename);
                    ImagenInventario::create([
                        'inventario_id' => $inventario->id,
                        'imagen' => 'imagenes/' . $filename,
                    ]);
                }
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
            'itemSelect' => 'required|exists:insumos,id',
            'cantidad' => 'required|integer|min:1',
            'estado' => 'required|boolean',
            'estadoInventario' => 'required|exists:estados,id',
            'imagenesInput' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sedeSelect' => 'required|array|min:1',
            'sedeSelect.*' => 'exists:sedes,id',
        ]);

        // Buscar el inventario por ID
        $inventario = Inventario::findOrFail($id);

        // Actualizar los datos del inventario
        $inventario->update([
            'sede_id' => $request->sedeSelect[0], // Asignar la primera sede seleccionada
            'item_id' => $request->itemSelect,
            'cantidad' => $request->cantidad,
            'estado_id' => $request->estadoInventario,
            'estado' => $request->estado,
            'actualizador_id' => Auth::id(),
        ]);

        // Actualizar la imagen del inventario si existe
        if ($request->hasFile('imagenesInput')) {
            ImagenInventario::where('inventario_id', $inventario->id)->delete();
            Storage::disk('public')->delete($inventario->imagenes->pluck('imagen')->toArray());

            $file = $request->file('imagenesInput');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('imagenes'), $filename);
            ImagenInventario::create([
                'inventario_id' => $inventario->id,
                'imagen' => 'imagenes/' . $filename,
            ]);
        }

        return response()->json(['message' => 'Inventario actualizado exitosamente', 'inventario' => $inventario]);
    }

    // Método para obtener los datos de un inventario específico
    public function obtenerInventario(Request $request)
    {
        $id = $request->input('id');
        $inventario = Inventario::with('sede.cliente', 'item', 'estado', 'creador', 'actualizador', 'imagenes')->findOrFail($id);
        return response()->json($inventario);
    }

    public function getSedes(Request $request)
    {
        $clienteId = $request->input('cliente_id');
        $sedes = Sede::where('cliente_id', $clienteId)->get();
        return response()->json($sedes);
    }

    public function obtenerItem($codigo)
    {
        $itemInsumo = Insumos::where('codigo', $codigo)->first();
        $itemActivo = Activos::findOrFail($codigo);

        if ($itemInsumo) {
            $item = $itemInsumo;
        } elseif ($itemActivo) {
            $item = $itemActivo;
        } else {
            return response()->json(['error' => 'Articulo no encontrado'], 404);
        }

        return response()->json([
            'numero_serie' => $item instanceof Activos ? $item->serie : $item->codigo,
            'cantidad_disponible' => $item->cantidad,
        ]);
    }

    public function obtenerItems()
    {
        $insumos = Insumos::all();
        $activos = Activos::all();
        $itemsInsumos = $insumos->map(function ($item) {
            return [
            'id' => $item->codigo,
            'nombre_elemento' => $item->nombre_elemento,
            ];
        });
        $itemsActivos = $activos->map(function ($item) {
            return [
            'id' => $item->id,
            'nombre_elemento' => $item->nombre_elemento,
            ];
        });
        $items = $itemsInsumos->merge($itemsActivos);
        //dd($items);

        return response()->json($items);
    }
}
