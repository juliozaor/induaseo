<?php
namespace App\Exports;

use App\Models\SedesActivos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class ActivosExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $sede_id;

    public function __construct($sede_id)
    {
        $this->sede_id = $sede_id;
    }

    public function collection()
    {
        $activos = SedesActivos::with(['activo', 'estados'])
            ->where('sede_id', $this->sede_id)
            ->get()
            ->map(function ($activo) {
                return [
                    'activo' => $activo->activo->nombre_elemento,
                    'cantidad' => $activo->cantidad,
                    'estado' => $activo->estados->nombre,
                    'observacion' => $activo->observacion
                ];
            });

        return collect($activos);
    }

    public function headings(): array
    {
        return [
            'Activo',
            'Cantidad',
            'Estado',
            'Observación'
        ];
    }
}
