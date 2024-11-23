<?php

namespace App\Http\Controllers;

use App\Models\Activos;
use App\Models\Ciudades;
use App\Models\Cliente;
use App\Models\Frecuencia;
use App\Models\Paises;
use App\Models\Regionales;
use App\Models\SectoresEconomico;
use App\Models\Sede;
use App\Models\Turno;
use App\Models\Area;
use App\Models\Clasificaciones;
use App\Models\Estados;
use App\Models\Insumos;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaestrasController extends Controller
{
    public function index()
    {
        $tablasMaestras = ['clientes', 'sedes', 'turnos', 'areas', 'activos', 'insumos']; 
        return view('admin.maestras.index', compact('tablasMaestras'));
    }

    public function consultar(Request $request)
    {
        try {
            $tabla = $request->input('tabla');

            if ($tabla === 'clientes') {
                $clientes = Cliente::with(['tipoDocumento', 'ciudad.pais', 'sectorEconomico', 'creador', 'actualizador'])->get();
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
            } elseif ($tabla === 'areas') {
                $query = Area::with(['cliente', 'sede', 'creador', 'actualizador']);

                // Filtro de búsqueda
                if ($request->has('buscar')) {
                    $buscar = $request->input('buscar');
                    $query->where('nombre', 'like', "%$buscar%");
                }

                // Cantidad de registros por página
                $registrosPorPagina = $request->input('registros_por_pagina', 10);

                // Obtener datos paginados
                $areas = $query->paginate($registrosPorPagina);
                return response()->json($areas);
            } elseif ($tabla === 'activos') {
                $query = Activos::with(['clasificacion', 'estado', 'creador', 'actualizador']);

                // Filtro de búsqueda
                if ($request->has('buscar')) {
                    $buscar = $request->input('buscar');
                    $query->where('nombre_elemento', 'like', "%$buscar%")
                        ->orWhere('marca', 'like', "%$buscar%")
                        ->orWhere('serie', 'like', "%$buscar%");
                }

                // Cantidad de registros por página
                $registrosPorPagina = $request->input('registros_por_pagina', 10);

                // Obtener datos paginados
                $activos = $query->paginate($registrosPorPagina);
                return response()->json($activos);
            } elseif ($tabla === 'insumos') {
                
                $query = Insumos::with(['clasificacion', 'estado', 'creador', 'actualizador']);

                // Filtro de búsqueda
                if ($request->has('buscar')) {
                    $buscar = $request->input('buscar');
                    $query->where('nombre_elemento', 'like', "%$buscar%");
                }

                // Cantidad de registros por página
                $registrosPorPagina = $request->input('registros_por_pagina', 10);

                // Obtener datos paginados
                $insumos = $query->paginate($registrosPorPagina);
                
                return response()->json($insumos);
            }

            return response()->json(['error' => 'Tabla no encontrada'], 404);
        } catch (Exception $e) {
            Log::error('Error en consultar: ' . $e->getMessage());
            return response()->json(['error' => 'Error al consultar los datos :'. $e->getMessage()], 500);
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
        } elseif ($tabla === 'areas') {
            return view('admin.maestras.areas', compact('tabla'));
        } elseif ($tabla === 'activos') {
            return view('admin.maestras.activos', compact('tabla'));
        } elseif ($tabla === 'insumos') { // Add this block
            return view('admin.maestras.insumos', compact('tabla'));
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

    public function obtenerClasificaciones(Request $request)
    {
        $clasificaciones = Clasificaciones::all();
        return response()->json($clasificaciones);
    }

    public function obtenerEstados(Request $request)
    {
        $estados = Estados::all();
        return response()->json($estados);
    }
}
