<?php

namespace App\Exports;

use App\Models\Comprobante;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiciosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithCustomStartCell
{
    protected $desde;
    protected $hasta;
    protected $tipoServicio;

    protected ?Collection $cachedCollection = null;

    public function __construct(string $desde, string $hasta, string $tipoServicio = 'todos')
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->tipoServicio = $tipoServicio;
    }

    public function collection()
    {
        if ($this->cachedCollection === null) {
            $query = Comprobante::whereBetween('created_at', [$this->desde . ' 00:00:00', $this->hasta . ' 23:59:59'])
                ->with(['serviceOrder.service', 'serviceOrder.tecnico', 'serviceOrder.cliente']);

            if ($this->tipoServicio !== 'todos') {
                $query->whereHas('serviceOrder.service', fn ($s) => $s->where('tipo', $this->tipoServicio));
            }

            $this->cachedCollection = $query->orderByDesc('created_at')->get();
        }
        return $this->cachedCollection;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return ['#', 'Fecha', 'Cliente', 'Servicio', 'Tipo', 'Técnico', 'Monto (S/)'];
    }

    public function map($comprobante): array
    {
        return [
            $comprobante->id,
            $comprobante->created_at->format('d/m/Y H:i'),
            trim(($comprobante->serviceOrder?->cliente->nombre ?? 'N/A') . ' ' . ($comprobante->serviceOrder?->cliente->apellido ?? '')),
            $comprobante->serviceOrder?->service->nombre ?? 'N/A',
            ucfirst($comprobante->serviceOrder?->service->tipo ?? 'simple'),
            $comprobante->serviceOrder?->tecnico?->name ?? 'N/A',
            $comprobante->monto,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'G';
        $total = $this->collection()->count();
        $totalMonto = $this->collection()->sum('monto');

        // Title
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'ARTURO MOTORS — REPORTE DE SERVICIOS');
        $sheet->getRowDimension(1)->setRowHeight(38);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 15, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '14233F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Subtitle
        $tipoLabel = $this->tipoServicio !== 'todos' ? '  ·  Tipo: ' . ucfirst($this->tipoServicio) : '';
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Generado el ' . now()->format('d/m/Y') . ' a las ' . now()->format('H:i') . ' hrs.  ·  Total: ' . $total . ' orden(es)  ·  S/ ' . number_format($totalMonto, 2) . $tipoLabel);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 10, 'color' => ['rgb' => '64748B']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F7F9FC']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Spacer
        $sheet->getRowDimension(3)->setRowHeight(6);

        // Header row (row 4 = startCell)
        $sheet->getRowDimension(4)->setRowHeight(26);
        $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2E5286']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '1C3057']],
            ],
        ]);

        // Data rows (start at row 5)
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 4) {
            foreach (range(5, $highestRow) as $row) {
                $isEven = ($row % 2 === 0);
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'font' => ['name' => 'Calibri', 'size' => 10, 'color' => ['rgb' => '1C2D42']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $isEven ? 'F7F9FC' : 'FFFFFF']],
                    'borders' => [
                        'bottom' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E2E8F0']],
                    ],
                    'alignment' => ['vertical' => 'center'],
                ]);

                // Tipo badge
                $tipoCell = "E{$row}";
                $tipoValor = strtolower((string) $sheet->getCell($tipoCell)->getValue());
                $tipoColors = match ($tipoValor) {
                    'simple' => ['font' => '1D4ED8', 'fill' => 'DBEAFE'],
                    'conversion' => ['font' => '92400E', 'fill' => 'FEF3C7'],
                    default => null,
                };
                if ($tipoColors) {
                    $sheet->getStyle($tipoCell)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => $tipoColors['font']]],
                        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $tipoColors['fill']]],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ]);
                }

                // Moneda
                $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            }
        }

        // Footer
        $closingRow = $highestRow + 2;
        $sheet->mergeCells("A{$closingRow}:{$lastCol}{$closingRow}");
        $sheet->setCellValue("A{$closingRow}", 'Fin del reporte — ' . $total . ' orden(es) cobrada(s)  ·  Total: S/ ' . number_format($totalMonto, 2));
        $sheet->getStyle("A{$closingRow}")->applyFromArray([
            'font' => ['name' => 'Calibri', 'italic' => true, 'size' => 8.5, 'color' => ['rgb' => '94A3B8']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(14);

        $sheet->freezePane('A5');

        return [];
    }

    public function title(): string
    {
        return 'Reporte de Servicios';
    }
}
