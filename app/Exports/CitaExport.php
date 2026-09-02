<?php

namespace App\Exports;

use App\Models\Cita;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

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
        $lastCol = 'H';

        // Row 1: Company header
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'ARTURO MOTORS — REPORTE DE CITAS');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E293B');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setHeight(40);

        // Row 2: Subtitle
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Generado: ' . now()->format('d/m/Y H:i') . ' — Total: ' . $this->collection()->count() . ' cita(s)');
        $sheet->getStyle('A2')->getFont()->setSize(10)->getColor()->setRGB('64748B');
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setHeight(25);

        // Row 3: blank spacer
        $sheet->getRowDimension(3)->setHeight(8);

        // Row 4: Headings
        $headingStyle = $sheet->getStyle("A4:{$lastCol}4");
        $headingStyle->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('FFFFFF');
        $headingStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('312E81');
        $headingStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $headingBorders = $headingStyle->getBorders();
        $headingBorders->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $headingBorders->getAllBorders()->getColor()->setRGB('4338CA');
        $sheet->getRowDimension(4)->setHeight(28);

        // Data rows styling
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 4) {
            $dataRows = range(5, $highestRow);
            foreach ($dataRows as $row) {
                $isEven = ($row % 2 === 0);
                $rowStyle = $sheet->getStyle("A{$row}:{$lastCol}{$row}");
                $rowStyle->getFont()->setSize(10);
                $rowStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($isEven ? 'F8FAFC' : 'FFFFFF');
                $rowBorders = $rowStyle->getBorders();
                $rowBorders->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $rowBorders->getBottom()->getColor()->setRGB('E2E8F0');
                $rowStyle->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension($row)->setHeight(22);
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
