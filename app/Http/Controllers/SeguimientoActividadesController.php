<?php

namespace App\Http\Controllers;

use App\Models\SupervisorTurno;
use Illuminate\Http\Request;

class SeguimientoActividadesController extends Controller
{
    public function index()
    {
        $turnos = SupervisorTurno::with(['supervisor', 'sede', 'turno'])
            ->where('supervisor_id', 2)
            ->get();
        return view('seguimiento-actividades.index', compact('turnos'));
    }

}
