<?php

namespace App\Http\Controllers;

use App\Models\Ciudades;
use App\Models\Cliente;
use App\Models\Frecuencia;
use App\Models\Paises;
use App\Models\Regionales;
use App\Models\SectoresEconomico;
use App\Models\Sede;
use App\Models\Turno;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaestrasController extends Controller
{
    public function index()
    {
        $tablasMaestras = ['clientes','sedes', 'turnos']; // Agregar más tablas maestras aquí si es necesario
        return view('admin.maestras.index', compact('tablasMaestras'));
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
            } elseif ($tabla === 'sedes') {
                $query = Sede::with(['cliente', 'ciudad.pais', 'creador', 'actualizador', 'regional']);

               
                // Filtro de búsqueda
                if ($request->has('buscar')) {
                    $buscar = $request->input('buscar');
                    $query->where('direccion', 'like', "%$buscar%")
                        ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%$buscar%"))
                        ->orWhereHas('ciudad', fn($q) => $q->where('nombre', 'like', "%$buscar%"));
                }

                // Cantidad de registros por página
                $registrosPorPagina = $request->input('registros_por_pagina', 10);

                // Obtener datos paginados
                $sedes = $query->paginate($registrosPorPagina);
                return response()->json($sedes);
            } elseif ($tabla === 'turnos') {
                $query = Turno::with(['creador', 'actualizador']);

                // Filtro de búsqueda
                if ($request->has('buscar')) {
                    $buscar = $request->input('buscar');
                    $query->where('nombre', 'like', "%$buscar%");
                }

                // Cantidad de registros por página
                $registrosPorPagina = $request->input('registros_por_pagina', 10);

                // Obtener datos paginados
                $turnos = $query->paginate($registrosPorPagina);
                return response()->json($turnos);
            }

            return response()->json(['error' => 'Tabla no encontrada'], 404);
        } catch (Exception $e) {
            Log::error('Error en consultar: ' . $e->getMessage());
            return response()->json(['error' => 'Error al consultar los datos'], 500);
        }
    }

    public function clientes(Request $request)
    {
        $tabla = $request->get('tabla');
        if ($tabla === 'clientes') {
            return view('admin.maestras.clientes', compact('tabla'));
        } elseif ($tabla === 'sedes') {
            return view('admin.maestras.sedes', compact('tabla'));
        } elseif ($tabla === 'turnos') {
            return view('admin.maestras.turnos', compact('tabla'));
        }
        return response()->json(['error' => 'Tabla no encontrada'], 404);
    }

    public function obtenerClientes(Request $request)
    {
        $clientes = Cliente::all();

        return response()->json($clientes);
    }

    public function obtenerPaises(Request $request)
    {
        $paises = Paises::all();

        return response()->json($paises);
    }

    public function obtenerRegionales(Request $request)
    {
        $regionales = Regionales::all();

        return response()->json($regionales);
    }

    public function obtenerCiudades(Request $request)
    {
        $pais_id = $request->input('pais');
        // Busca las ciudades que pertenecen al país seleccionado
        $ciudades = Ciudades::where('pais_id', $pais_id)->get(['id', 'nombre']);

        // Devuelve las ciudades como respuesta JSON
        return response()->json($ciudades);
    }

    public function obtenerSectoresEconomicos(Request $request)
    {
        $sectores = SectoresEconomico::all();
        return response()->json($sectores);
    }

    public function obtenerFrecuencias(Request $request)
    {
        $frecuencias = Frecuencia::all();

        return response()->json($frecuencias);
    }
}
