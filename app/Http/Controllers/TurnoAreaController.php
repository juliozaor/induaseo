<?php

namespace App\Http\Controllers;

use App\Models\SupervisorTurno;
use Illuminate\Http\Request;
use App\Models\TurnoArea;

class TurnoAreaController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'turno_id' => 'required|exists:supervisor_turnos,id',
            'area_id' => 'required|exists:areas,id',
        ]);

        TurnoArea::create($validatedData);

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
