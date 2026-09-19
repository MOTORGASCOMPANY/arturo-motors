<?php

namespace App\Exports;

use App\Models\ServiceOrder;
use App\Models\ItemSerializado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConversionesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private $filtros;
    private $rows = [];

    public function __construct(array $filtros = [])
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $query = ServiceOrder::with([
            'cliente',
            'vehiculo',
            'service',
            'tecnico',
            'items.producto.categoria',
            'items.kitPadre.producto',
        ])->whereIn('estado', ['en_conversion', 'conversion_completada']);

        if (!empty($this->filtros['sede_id'])) {
            $query->whereHas('items', fn ($q) => $q->where('sede_id', (int) $this->filtros['sede_id']));
        }

        if (!empty($this->filtros['estado'])) {
            $query->where('estado', $this->filtros['estado']);
        }

        if (!empty($this->filtros['desde'])) {
            $query->where('fecha_inicio_conversion', '>=', $this->filtros['desde']);
        }

        if (!empty($this->filtros['hasta'])) {
            $query->where('fecha_inicio_conversion', '<=', $this->filtros['hasta'] . ' 23:59:59');
        }

        $ordenes = $query->orderByDesc('fecha_inicio_conversion')->get();

        $this->rows = $ordenes->map(function ($o) {
            $kitPadre = $o->items->first(fn ($i) => $i->kit_padre_id === null && ($i->producto->categoria->es_kit ?? false));
            $hijos = $o->items->where('kit_padre_id', '!=', null);
            $instalados = $hijos->where('estado', 'instalado');

            // Componentes instalados por nombre
            $componentesInstalados = $instalados->map(fn ($i) => $i->producto->nombre)->implode(', ');

            // Piezas con cantidad
            $piezasCantidad = $instalados->filter(fn ($i) => ($i->atributos['tipo'] ?? '') === 'cantidad')
                ->map(fn ($i) => $i->producto->nombre)
                ->implode(', ');

            return [
                'orden_id' => $o->id,
                'cliente' => trim($o->cliente->nombre . ' ' . $o->cliente->apellido),
                'placa' => $o->vehiculo->placa ?? 'N/A',
                'vehiculo' => trim(($o->vehiculo->marca ?? '') . ' ' . ($o->vehiculo->modelo ?? '')),
                'servicio' => $o->service->nombre ?? 'N/A',
                'tecnico' => $o->tecnico->name ?? 'N/A',
                'kit' => $kitPadre?->producto->nombre ?? 'N/A',
                'generacion' => $kitPadre?->producto->atributos['generacion'] ?? '',
                'total_componentes' => $hijos->count(),
                'instalados' => $instalados->count(),
                'componentes' => $componentesInstalados,
                'piezas_cantidad' => $piezasCantidad,
                'reportes' => $o->reportesPendientes()->count(),
                'estado' => $o->estado === 'conversion_completada' ? 'Completada' : 'En proceso',
                'inicio' => $o->fecha_inicio_conversion?->format('d/m/Y H:i'),
                'fin' => $o->fecha_fin_conversion?->format('d/m/Y H:i'),
                'duracion_horas' => $o->fecha_inicio_conversion && $o->fecha_fin_conversion
                    ? round($o->fecha_inicio_conversion->diffInHours($o->fecha_fin_conversion), 1) . 'h'
                    : '—',
            ];
        });

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Orden',
            'Cliente',
            'Placa',
            'Vehículo',
            'Servicio',
            'Técnico',
            'Kit',
            'Generación',
            'Total Componentes',
            'Instalados',
            'Componentes Instalados',
            'Piezas por Cantidad',
            'Reportes',
            'Estado',
            'Inicio',
            'Fin',
            'Duración',
        ];
    }

    public function map($row): array
    {
        return [
            $row['orden_id'],
            $row['cliente'],
            $row['placa'],
            $row['vehiculo'],
            $row['servicio'],
            $row['tecnico'],
            $row['kit'],
            $row['generacion'],
            $row['total_componentes'],
            $row['instalados'],
            $row['componentes'],
            $row['piezas_cantidad'],
            $row['reportes'],
            $row['estado'],
            $row['inicio'],
            $row['fin'],
            $row['duracion_horas'],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
        $sheet->getStyle('A1:Q1')->getFont()->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:Q1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:Q1')->getFill()->setStartColor(new Color('1e40af'));

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(50);
        $sheet->getColumnDimension('L')->setWidth(40);
    }

    public function title(): string
    {
        return 'Conversiones GNV';
    }
}
