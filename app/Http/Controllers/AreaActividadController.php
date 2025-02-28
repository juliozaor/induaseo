<?php

namespace App\Http\Controllers;

use App\Models\AreaActividad;
use Illuminate\Http\Request;

class AreaActividadController extends Controller
{
    // Almacenar una nueva relación entre área y actividad
    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'actividad_id' => 'required|exists:actividades,id',
        ]);

        $areaActividad = AreaActividad::create($validated);

        return response()->json(['message' => 'Actividad agregada con éxito', 'areaActividad' => $areaActividad], 201);
    }

    // Eliminar una relación entre área y actividad
    public function destroy($id)
    {
        $areaActividad = AreaActividad::findOrFail($id);
        $areaActividad->delete();

        return response()->json(['message' => 'Actividad eliminada con éxito']);
    }
}
