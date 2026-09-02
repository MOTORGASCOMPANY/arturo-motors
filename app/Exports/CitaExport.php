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
        return Cita::with(['cliente', 'vehiculo', 'asesor', 'serviceOrder.service'])
            ->buscar($this->search)
            ->estado($this->estado)
            ->when($this->fechaInicio, fn ($q) => $q->whereDate('fecha_cita', '>=', $this->fechaInicio))
            ->when($this->fechaFin, fn ($q) => $q->whereDate('fecha_cita', '<=', $this->fechaFin))
            ->orderBy('fecha_cita', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['#', 'Fecha', 'Cliente', 'Documento', 'Placa', 'Asesor', 'Servicio', 'Motivo', 'Estado'];
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
            $cita->serviceOrder->service->nombre ?? '—',
            $cita->motivo ?? '—',
            ucfirst($cita->estado),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'I';

        // Row 1: Company header
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'ARTURO MOTORS — REPORTE DE CITAS');
        $sheet->getStyle("A1:{$lastCol}1")->apply([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => Fill::FILL_SOLID, 'color' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setHeight(40);

        // Row 2: Subtitle
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Generado: ' . now()->format('d/m/Y H:i') . ' — Total: ' . $this->collection()->count() . ' cita(s)');
        $sheet->getStyle("A2:{$lastCol}2")->apply([
            'font' => ['size' => 10, 'color' => ['rgb' => '64748B']],
            'fill' => ['type' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F1F5F9']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setHeight(25);

        // Row 3: blank spacer
        $sheet->getRowDimension(3)->setHeight(8);

        // Row 4: Headings
        $sheet->getStyle("A4:{$lastCol}4")->apply([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => Fill::FILL_SOLID, 'color' => ['rgb' => '312E81']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '4338CA']],
            ],
        ]);
        $sheet->getRowDimension(4)->setHeight(28);

        // Data rows styling
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 4) {
            $dataRows = range(5, $highestRow);
            foreach ($dataRows as $row) {
                $isEven = ($row % 2 === 0);
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->apply([
                    'font' => ['size' => 10],
                    'fill' => ['type' => Fill::FILL_SOLID, 'color' => ['rgb' => $isEven ? 'F8FAFC' : 'FFFFFF']],
                    'borders' => [
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
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
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(14);

        return [];
    }

    public function title(): string
    {
        return 'Reporte de Citas';
    }
}
