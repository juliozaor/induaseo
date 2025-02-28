<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use App\Models\Informacion;
use App\Models\TipoMultimedias;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\SupervisorTurno;

class InformacionController extends Controller
{
    public function index()
    {
        return view('admin.informacion.index');

    }

    public function cargarInformacion(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);

        $informacion = Informacion::query()
        ->with('tipoMultimedia', 'categoria', 'sede.cliente')
            ->when($buscar, function ($query, $buscar) {
                return $query->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            })
            ->paginate($registrosPorPagina);

        return response()->json($informacion);
    }

    public function guardar(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string|max:255',
                'url' => 'nullable|file|mimes:pdf,mp4,png,jpg,jpeg|max:20480',
                'tipo_multimedia_id' => 'required|exists:tipo_multimedias,id',
                'categoria_id' => 'required|exists:categorias,id',
                'sede_id' => 'nullable|exists:sedes,id',
            ]);

            if ($request->hasFile('url')) {
                $file = $request->file('url');
                $path = $file->storeAs('recursos', $file->getClientOriginalName(), 'public');
                $validatedData['url'] = $path;
            }

            $validatedData['fecha'] = now(); // Set current date

            Informacion::create($validatedData);

            return response()->json(['message' => 'Información creada con éxito']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function obtenerInformacion(Request $request)
    {
        $id = $request->input('id');
        $informacion = Informacion::with('tipoMultimedia', 'categoria', 'sede.cliente')->findOrFail($id);
        return response()->json($informacion);
    }

    public function actualizar(Request $request, $id)
    {
        $informacion = Informacion::findOrFail($id);

        try {
            $validatedData = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string|max:255',
                'url' => 'nullable|file|mimes:pdf,mp4,png,jpg,jpeg|max:20480',
                'tipo_multimedia_id' => 'required|exists:tipo_multimedias,id',
                'categoria_id' => 'required|exists:categorias,id',
                'sede_id' => 'nullable|exists:sedes,id',
            ]);

            if ($request->hasFile('url')) {
                $file = $request->file('url');
                $path = $file->storeAs('recursos', $file->getClientOriginalName(), 'public');
                $validatedData['url'] = $path;
            }

            $informacion->update($validatedData);

            return response()->json(['message' => 'Información actualizada correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function obtenerNovedades()
    {
        $novedades = Informacion::where('tipo_multimedia_id', 'tipoMultimedia')->get();
        return response()->json($novedades);
    }

    public function obtenerNovedadesPorSede($sedeId)
    {
        $novedades = Informacion::where('sede_id', $sedeId)
            ->with('tipoMultimedia') // Ensure the relationship is loaded
            ->get();

        return response()->json($novedades);
    }

    public function getTiposMultimedia()
    {
        $tiposMultimedia = TipoMultimedias::all();
        return response()->json($tiposMultimedia);
    }

    public function Categorias()
    {
        $categorias = Categorias::all();
        return response()->json($categorias);
    }

    public function informacion()
    {
        $userId = Auth::id();
        $turnos = SupervisorTurno::with(['supervisor', 'sede', 'turno'])
            ->where('supervisor_id', $userId)
            ->get();
        return view('seguimiento-actividades.informacion', compact('turnos'));
    }

    public function destroy($id)
    {
        $informacion = Informacion::findOrFail($id);
        $informacion->delete();

        return response()->json(['message' => 'Información eliminada con éxito.']);
    }

}
