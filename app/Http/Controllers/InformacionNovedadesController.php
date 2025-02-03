<?php

namespace App\Http\Controllers;

use App\Models\Informacion;
use Illuminate\Http\Request;

class InformacionNovedadesController extends Controller
{
    public function index()
    {
        return view('seguimiento-actividades.informacion');
    }

    public function buscarInformacion(Request $request)
    {
        $buscar = $request->input('buscar');

        $informacion = Informacion::query()
            ->when($buscar, function ($query, $buscar) {
                return $query->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            })
            ->get();

        return response()->json($informacion);
    }
}
