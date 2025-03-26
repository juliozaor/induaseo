<?php

namespace App\Http\Controllers;

use App\Models\SupervisorTurno;
use Illuminate\Http\Request;
use App\Models\TurnoArea;
use App\Models\AreaActividad; // Import the AreaActividad model
use App\Models\TurnosAreasActividades;

class TurnoAreaController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'turno_id' => 'required|exists:supervisor_turnos,id',
            'area_id' => 'required|exists:areas,id',
        ]);

        $turnoArea = TurnoArea::create($validatedData);

        // Get all activities of the area through AreaActividad
        $actividades = AreaActividad::where('area_id', $validatedData['area_id'])->get();

        // Create a record for each activity in turnos_areas_actividades
        foreach ($actividades as $actividad) {
            TurnosAreasActividades::create([
                'turnos_areas_id' => $turnoArea->id,
                'actividad_id' => $actividad->actividad_id,
            ]);
        }

        return response()->json(['message' => 'Área asignada al turno con éxito']);
    }

    public function destroy($id)
    {
        $turnoArea = TurnoArea::where('turno_id', $id)->firstOrFail();
        $turnoArea->delete();

        $supervisorTurno = SupervisorTurno::where('id', $id)->firstOrFail();
        $supervisorTurno->delete();

        return response()->json(['message' => 'Asignación del turno eliminada con éxito']);
    }

    public function destroyByArea($turnoId, $areaId)
    {
        $turnoArea = TurnoArea::where('turno_id', $turnoId)->where('area_id', $areaId)->firstOrFail();
        $turnoArea->delete();

        return response()->json(['message' => 'Área eliminada del turno con éxito.']);
    }
}
