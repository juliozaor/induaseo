<?php

namespace App\Http\Controllers;

use App\Models\Ciudades;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ClientesController extends Controller
{
    public function index()
    {
        $tablasMaestras = ['clientes']; // Agrega más tablas maestras si es necesario
        return view('admin.maestras.clientes', compact('tablasMaestras'));
    }

    public function consultar(Request $request)
    {
        // Obtener los datos de clientes con sus relaciones
        $clientes = Cliente::with(['ciudad.pais', 'tipoDocumento', 'sectorEconomico'])->get();

        return response()->json($clientes);
    }

    public function guardar(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tipoIdentificacion' => 'required|exists:tipos_documentos,id',
                'numeroIdentificacion' => 'required|string|unique:clientes,numero_documento',
                'nombre' => 'required|string|max:255',
                'pais' => 'required|exists:paises,id',
                'ciudad' => 'required|exists:ciudades,id',
                'direccion' => 'nullable|string|max:255',
                'correo' => 'nullable|email|max:255',
                'celular' => 'nullable|numeric',
                'estado' => 'required|boolean',
                'sectorEconomico' => 'required|exists:sectores_economicos,id',
            ]);
            
            Cliente::create([
                'tipo_documento_id' => $request->tipoIdentificacion,
                'numero_documento' => $request->numeroIdentificacion,
                'nombre' => $request->nombre,
                'pais_id' => $request->pais,
                'ciudad_id' => $request->ciudad,
                'direccion' => $request->direccion,
                'correo' => $request->correo,
                'celular' => $request->celular,
                'estado' => $request->estado,
                'sector_economico_id' => $request->sectorEconomico,
                'creador_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Cliente creado con éxito']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function obtenerCiudades(Request $request)
    {
        $pais_id = $request->input('pais');
        // Busca las ciudades que pertenecen al país seleccionado
        $ciudades = Ciudades::where('pais_id', $pais_id)->get(['id', 'nombre']);

        // Devuelve las ciudades como respuesta JSON
        return response()->json($ciudades);
    }

    public function obtenerCliente(Request $request)
    {
        $id = $request->input('id');
        $cliente = Cliente::with('tipoDocumento', 'ciudad.pais', 'sectorEconomico')->findOrFail($id);
        return response()->json($cliente);
    }

    public function actualizarCliente(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        try {
            $validatedData = $request->validate([
                'tipoIdentificacion' => 'required|exists:tipos_documentos,id',
                'numeroIdentificacion' => 'required|string|unique:clientes,numero_documento,' . $cliente->id,
                'nombre' => 'required|string|max:255',
                'pais' => 'required|exists:paises,id',
                'ciudad' => 'required|exists:ciudades,id',
                'direccion' => 'nullable|string|max:255',
                'correo' => 'nullable|email|max:255',
                'celular' => 'nullable|numeric',
                'estado' => 'required|boolean',
                'sectorEconomico' => 'required|exists:sectores_economicos,id',
            ]);

            

           /*  $validatedData['actualizador_id'] = Auth::id(); */

           /*  $cliente->update($validatedData); */

            $cliente->update([
                'tipo_documento_id' => $request->tipoIdentificacion,
                'numero_documento' => $request->numeroIdentificacion,
                'nombre' => $request->nombre,
                'pais_id' => $request->pais,
                'ciudad_id' => $request->ciudad,
                'direccion' => $request->direccion,
                'correo' => $request->correo,
                'celular' => $request->celular,
                'estado' => $request->estado,
                'sector_economico_id' => $request->sectorEconomico,
                'actualizador_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Cliente actualizado correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
