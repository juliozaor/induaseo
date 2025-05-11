<?php
namespace App\Exports;

use App\Models\SupervisorTurno;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\TurnosHistorialActividades;
use App\Models\SupervisorTurnosFechas;

class ActividadesExport implements FromCollection, WithHeadings
{
    protected $sede_id;
    protected $fecha_inicio;
    protected $fecha_fin;

    public function __construct($sede_id, $fecha_inicio, $fecha_fin)
    {
        $this->sede_id = $sede_id;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function collection()
    {
        // Obtener los turnos asignados con sus relaciones
        $turnosAsignados = SupervisorTurno::with(['turno', 'supervisor', 'areas.area.actividades', 'areas.actividades.actividad'])
            ->where('sede_id', $this->sede_id);

        if ($this->fecha_inicio && $this->fecha_fin) {
            $turnosAsignados->whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin]);
        }

        $historialTurnos = SupervisorTurnosFechas::with([
            'supervisorTurno', 'supervisorTurno.turno', 'supervisorTurno.supervisor',
            'supervisorTurno.areas.area.actividades', 'supervisorTurno.areas.actividades.actividad',
            'supervisorTurno.areas'
        ])->whereIn('supervisor_turno_id', $turnosAsignados->pluck('id'));

        $turnos = [];

        foreach ($historialTurnos->get() as $historialTurno) {
            $supervisorTurno = $historialTurno->supervisorTurno;

            if ($supervisorTurno) {
            $historialActividades = TurnosHistorialActividades::with(['turnoAreaActividad'])
                ->whereHas('turnoAreaActividad.turnoArea', function ($query) use ($supervisorTurno) {
                $query->where('turno_id', $supervisorTurno->id);
                })
                ->get()
                ->map(function ($historial) use ($supervisorTurno, $historialTurno) {
                return [
                    'fecha_inicio' => $historialTurno->fecha_inicio ?? '-',
                    'fecha_fin' => $historialTurno->fecha_fin ?? '-',
                    'turno' => $supervisorTurno->turno->nombre,
                    'area' => $historial->turnoAreaActividad->turnoArea->area->nombre,
                    'actividad' => $historial->turnoAreaActividad->actividad->nombre,
                    'estado' => $historial->estado ? 'No completada' : 'Completada',
                    'calificacion' => $historial->calificacion ? "{$historial->calificacion}/5" : '0/5',
                ];
                });

            $turnos = array_merge($turnos, $historialActividades->toArray());
            }
        }

        return collect($turnos);
    }

    public function headings(): array
    {
        return [
            'Fecha Inicio',
            'Fecha Fin',
            'Turno',
            'Área',
            'Actividad',
            'Estado',
            'Calificación',
        ];
    }
}
