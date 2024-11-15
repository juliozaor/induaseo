<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Paises;
use App\Models\SectoresEconomico;
use Exception;
use Illuminate\Http\Request;

class MaestrasController extends Controller
{
    public function index()
    {
        $paises = Paises::all();
        $sectorEconomico = SectoresEconomico::all();
        $tablasMaestras = ['clientes']; // Agregar más tablas maestras aquí si es necesario
        return view('admin.maestras.index', compact('tablasMaestras', 'paises', 'sectorEconomico'));
    }

    public function consultar(Request $request)
    {
        
        try {
            $tabla = $request->input('tabla');

        if ($tabla === 'clientes') {
            $query = Cliente::with(['tipoDocumento', 'ciudad.pais', 'sectorEconomico', 'creador', 'actualizador']);

            // Filtro de búsqueda
            if ($request->has('buscar')) {
                $buscar = $request->input('buscar');
                $query->where('nombre', 'like', "%$buscar%")
                    ->orWhere('numero_documento', 'like', "%$buscar%")
                    ->orWhereHas('ciudad', fn($q) => $q->where('nombre', 'like', "%$buscar%"))
                    ->orWhereHas('tipoDocumento', fn($q) => $q->where('nombre', 'like', "%$buscar%"));
            }

            // Cantidad de registros por página
            $registrosPorPagina = $request->input('registros_por_pagina', 10);

            // Obtener datos paginados
            $clientes = $query->paginate($registrosPorPagina);

            return response()->json($clientes);
        }

        return response()->json(['error' => 'Tabla no encontrada'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al crear el cliente'], 500);
        }
        
    }
}
