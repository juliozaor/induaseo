<?php
namespace App\Exports;

use App\Models\Inventario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class InsumosExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $sede_id;

    public function __construct($sede_id)
    {
        $this->sede_id = $sede_id;
    }

    public function collection()
    {
        $insumos = Inventario::with(['item', 'estados'])
            ->where('sede_id', $this->sede_id)
            ->get()
            ->map(function ($Insumo) {
                return [
                    'insumo' => $Insumo->item->nombre_elemento,
                    'cantidad' => $Insumo->cantidad,
                ];
            });

        return collect($insumos);
    }

    public function headings(): array
    {
        return [
            'Insumo',
            'Cantidad',
        ];
    }
}
