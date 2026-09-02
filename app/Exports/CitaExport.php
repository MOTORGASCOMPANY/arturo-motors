<?php

namespace App\Exports;

use App\Models\Cita;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CitaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $search;
    protected $estado;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct(?string $search = null, ?string $estado = 'todos', ?string $fechaInicio = null, ?string $fechaFin = null)
    {
        $this->search = $search;
        $this->estado = $estado;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        return Cita::with(['cliente', 'vehiculo', 'asesor'])
            ->buscar($this->search)
            ->estado($this->estado)
            ->when($this->fechaInicio, fn ($q) => $q->whereDate('fecha_cita', '>=', $this->fechaInicio))
            ->when($this->fechaFin, fn ($q) => $q->whereDate('fecha_cita', '<=', $this->fechaFin))
            ->orderBy('fecha_cita', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['#', 'Fecha', 'Cliente', 'Documento', 'Placa', 'Asesor', 'Motivo', 'Estado'];
    }

    public function map($cita): array
    {
        return [
            $cita->id,
            $cita->fecha_cita->format('d/m/Y H:i'),
            trim(($cita->cliente->nombre ?? 'N/A') . ' ' . ($cita->cliente->apellido ?? '')),
            $cita->cliente->documento ?? '—',
            $cita->vehiculo->placa ?? 'N/A',
            $cita->asesor->name ?? 'N/A',
            $cita->motivo ?? '—',
            ucfirst($cita->estado),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Row 1: Company header
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'ARTURO MOTORS — REPORTE DE CITAS');
        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => 'solid', 'color' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Row 2: Subtitle
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'Generado: ' . now()->format('d/m/Y H:i') . ' — Total: ' . $this->collection()->count() . ' cita(s)');
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '64748B']],
            'fill' => ['type' => 'solid', 'color' => ['rgb' => 'F1F5F9']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Row 3: blank spacer
        $sheet->getRowDimension(3)->setRowHeight(8);

        // Row 4: Headings
        $sheet->getRowDimension(4)->setRowHeight(28);
        $sheet->getStyle('A4:H4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => 'solid', 'color' => ['rgb' => '312E81']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '4338CA']],
            ],
        ]);

        // Data rows styling
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 4) {
            foreach (range(5, $highestRow) as $row) {
                $sheet->getRowDimension($row)->setRowHeight(22);
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'font' => ['size' => 10],
                    'fill' => ['type' => 'solid', 'color' => ['rgb' => ($row % 2 === 0) ? 'F8FAFC' : 'FFFFFF']],
                    'borders' => [
                        'bottom' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E2E8F0']],
                    ],
                    'alignment' => ['vertical' => 'center'],
                ]);
            }
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(14);

        return [];
    }

    public function title(): string
    {
        return 'Reporte de Citas';
    }
}
