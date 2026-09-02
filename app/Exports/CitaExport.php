<?php

namespace App\Exports;

use App\Models\Cita;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CitaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithCustomStartCell
{
    protected ?string $search;
    protected ?string $estado;
    protected ?string $fechaInicio;
    protected ?string $fechaFin;

    protected ?Collection $cachedCollection = null;

    public function __construct(?string $search = null, ?string $estado = 'todos', ?string $fechaInicio = null, ?string $fechaFin = null)
    {
        $this->search = $search;
        $this->estado = $estado;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        if ($this->cachedCollection === null) {
            $this->cachedCollection = Cita::with(['cliente', 'vehiculo', 'asesor'])
                ->buscar($this->search)
                ->estado($this->estado)
                ->when($this->fechaInicio, fn ($q) => $q->whereDate('fecha_cita', '>=', $this->fechaInicio))
                ->when($this->fechaFin, fn ($q) => $q->whereDate('fecha_cita', '<=', $this->fechaFin))
                ->orderBy('fecha_cita', 'desc')
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
        return ['#', 'Fecha', 'Cliente', 'Documento', 'Placa', 'Asesor', 'Estado'];
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
            ucfirst($cita->estado),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'G';
        $total = $this->collection()->count();

        // Row 1: Company header
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'ARTURO MOTORS — REPORTE DE CITAS');
        $sheet->getRowDimension(1)->setRowHeight(38);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 15, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '14233F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Row 2: Subtitle
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Generado el ' . now()->format('d/m/Y') . ' a las ' . now()->format('H:i') . ' hrs.  ·  Total: ' . $total . ' cita(s)');
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 10, 'color' => ['rgb' => '64748B']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F7F9FC']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Row 3: spacer
        $sheet->getRowDimension(3)->setRowHeight(6);

        // Row 4: Headings
        $sheet->getRowDimension(4)->setRowHeight(26);
        $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
            'font' => ['name' => 'Calibri', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2E5286']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '1C3057']],
            ],
        ]);

        // Data rows
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

                // Estado column (G): color by value
                $estadoCell = "G{$row}";
                $estadoValor = strtolower((string) $sheet->getCell($estadoCell)->getValue());
                $estadoColors = match ($estadoValor) {
                    'pendiente' => ['font' => 'A16207', 'fill' => 'FEF9C3'],
                    'aceptada'  => ['font' => '15803D', 'fill' => 'DCFCE7'],
                    'rechazada' => ['font' => 'DC2626', 'fill' => 'FEE2E2'],
                    'cancelada' => ['font' => '475569', 'fill' => 'F1F5F9'],
                    default     => null,
                };
                if ($estadoColors) {
                    $sheet->getStyle($estadoCell)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => $estadoColors['font']]],
                        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $estadoColors['fill']]],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ]);
                }
            }
        }

        // Closing row
        $closingRow = $highestRow + 2;
        $sheet->mergeCells("A{$closingRow}:{$lastCol}{$closingRow}");
        $sheet->setCellValue("A{$closingRow}", 'Fin del reporte — ' . $total . ' registro(s) listado(s)');
        $sheet->getStyle("A{$closingRow}")->applyFromArray([
            'font' => ['name' => 'Calibri', 'italic' => true, 'size' => 8.5, 'color' => ['rgb' => '94A3B8']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(14);

        // Freeze headers
        $sheet->freezePane('A5');

        return [];
    }

    public function title(): string
    {
        return 'Reporte de Citas';
    }
}
