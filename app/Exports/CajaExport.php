<?php

namespace App\Exports;

use App\Models\SesionCaja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CajaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithCustomStartCell
{
    protected $desde;
    protected $hasta;
    protected ?Collection $cachedCollection = null;

    public function __construct(string $desde, string $hasta)
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
    }

    public function collection()
    {
        if ($this->cachedCollection === null) {
            $this->cachedCollection = SesionCaja::with('abiertaPor')
                ->whereBetween('abierta_en', [$this->desde . ' 00:00:00', $this->hasta . ' 23:59:59'])
                ->orderByDesc('abierta_en')
                ->get();
        }
        return $this->cachedCollection;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return ['#', 'Fecha', 'Cajero', 'Apertura (S/)', 'Esperado (S/)', 'Cierre (S/)', 'Diferencia (S/)', 'Estado'];
    }

    public function map($sesion): array
    {
        return [
            $sesion->id,
            $sesion->abierta_en->format('d/m/Y H:i'),
            strtoupper($sesion->abiertaPor?->name ?? 'N/A'),
            $sesion->monto_apertura,
            $sesion->monto_esperado,
            $sesion->monto_cierre,
            $sesion->diferencia,
            ucfirst($sesion->estado),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'H';
        $total = $this->collection()->count();

        // Title
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'ARTURO MOTORS — REPORTE DE CAJA');
        $sheet->getRowDimension(1)->setRowHeight(36);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '14233F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Subtitle
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Generado el ' . now()->format('d/m/Y') . ' a las ' . now()->format('H:i') . ' hrs.  ·  Total: ' . $total . ' sesión(es)');
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 9, 'color' => ['rgb' => '64748B']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F7F9FC']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Spacer
        $sheet->getRowDimension(3)->setRowHeight(6);

        // Header row (row 4 = startCell)
        $sheet->getRowDimension(4)->setRowHeight(24);
        $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
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
                $sheet->getRowDimension($row)->setRowHeight(18);
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'font' => ['name' => 'Calibri', 'size' => 9, 'color' => ['rgb' => '1C2D42']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $isEven ? 'F7F9FC' : 'FFFFFF']],
                    'borders' => [
                        'bottom' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E2E8F0']],
                    ],
                    'alignment' => ['vertical' => 'center'],
                ]);

                // Diferencia: rojo si != 0, verde si == 0
                $diffCell = "G{$row}";
                $diffVal = $sheet->getCell($diffCell)->getValue();
                if ($diffVal !== null && $diffVal != 0) {
                    $sheet->getStyle($diffCell)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']],
                    ]);
                } elseif ($diffVal !== null && $diffVal == 0) {
                    $sheet->getStyle($diffCell)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '15803D']],
                    ]);
                }

                // Estado badge
                $estadoCell = "H{$row}";
                $estadoValor = strtolower((string) $sheet->getCell($estadoCell)->getValue());
                $estadoColors = match ($estadoValor) {
                    'abierta' => ['font' => '15803D', 'fill' => 'DCFCE7'],
                    'cerrada' => ['font' => '475569', 'fill' => 'F1F5F9'],
                    default => null,
                };
                if ($estadoColors) {
                    $sheet->getStyle($estadoCell)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => $estadoColors['font']]],
                        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $estadoColors['fill']]],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ]);
                }

                // Moneda
                foreach (['D', 'E', 'F', 'G'] as $col) {
                    $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                }
            }
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(12);

        $sheet->freezePane('A5');

        return [];
    }

    public function title(): string
    {
        return 'Reporte de Caja';
    }
}
