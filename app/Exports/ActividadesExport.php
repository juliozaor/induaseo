<?php
namespace App\Exports;

use App\Models\SupervisorTurno;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

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
        $turnosAsignados = SupervisorTurno::with(['turno', 'supervisor', 'areas.area.actividades'])
            ->where('sede_id', $this->sede_id);

        if ($this->fecha_inicio && $this->fecha_fin) {
            $turnosAsignados->whereBetween('fecha_inicio', [$this->fecha_inicio, $this->fecha_fin]);
        }

        $turnos = $turnosAsignados->get()->map(function ($turnoAsignado) {
            $actividadesCompletadas = $turnoAsignado->areas->map(function ($area) use ($turnoAsignado) {
                return $area->area->actividades->where('estado', false)->map(function ($actividad) use ($turnoAsignado, $area) {
                    return [
                        'fecha' => $turnoAsignado->fecha_inicio,
                        'area' => $area->area->nombre,
                        'actividad' => $actividad->nombre,
                        'estado' => 'completada'
                    ];
                });
            })->flatten(1);

            $actividadesIncompletadas = $turnoAsignado->areas->map(function ($area) use ($turnoAsignado) {
                return $area->area->actividades->where('estado', true)->map(function ($actividad) use ($turnoAsignado, $area) {
                    return [
                        'fecha' => $turnoAsignado->fecha_inicio,
                        'area' => $area->area->nombre,
                        'actividad' => $actividad->nombre,
                        'estado' => 'incompleta'
                    ];
                });
            })->flatten(1);

            return $actividadesCompletadas->merge($actividadesIncompletadas);
        })->flatten(1);

        return collect($turnos);
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Área',
            'Actividad',
            'Estado'
        ];
    }
}
