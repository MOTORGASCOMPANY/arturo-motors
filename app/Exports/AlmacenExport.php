<?php

namespace App\Exports;

use App\Models\Producto;
use App\Models\Sede;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AlmacenExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private $sedes;
    private $rows = [];

    public function __construct()
    {
        $this->sedes = Sede::activas()->orderBy('id')->get();
    }

    public function collection()
    {
        $productos = Producto::with('categoria')->where('activo', true)->get();

        $this->rows = $productos->map(function ($p) {
            $porSede = [];
            foreach ($this->sedes as $s) {
                $porSede[$s->nombre] = $p->stockSueltoEnSede($s->id);
            }
            return [
                'producto' => $p,
                'por_sede' => $porSede,
                'total' => array_sum($porSede),
            ];
        })->filter(fn ($row) => $row['total'] > 0)->values();

        return $this->rows;
    }

    public function headings(): array
    {
        $headings = ['Producto', 'Categoría'];
        foreach ($this->sedes as $s) {
            $headings[] = $s->nombre;
        }
        $headings[] = 'Total';
        return $headings;
    }

    public function map($row): array
    {
        $data = [
            $row['producto']->nombre,
            $row['producto']->categoria->nombre ?? 'N/A',
        ];
        foreach ($this->sedes as $s) {
            $data[] = $row['por_sede'][$s->nombre] ?? 0;
        }
        $data[] = $row['total'];
        return $data;
    }

    public function styles(Worksheet $sheet): void
    {
        $lastCol = chr(64 + 2 + $this->sedes->count() + 1);

        // Header
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setColor(new Color('FFFFFF'));
        $sheet->getStyle("A1:{$lastCol}1")->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()->setStartColor(new Color('4287f5'));

        // Widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);

        // Charts
        $this->addBarChart($sheet);
        $this->addPieChart($sheet);
    }

    private function addBarChart(Worksheet $sheet): void
    {
        $dataCount = $this->rows->count();
        if ($dataCount === 0) return;

        $colors = ['3b82f6', '10b981', 'f59e0b', '8b5cf6'];
        $startCol = 60;
        $sheetName = $sheet->getTitle();

        // ── Datos auxiliares ──
        // Fila 1: encabezados de sedes
        // Fila 2+: datos
        for ($i = 0; $i < $dataCount; $i++) {
            $sheet->setCellValueByColumnAndRow($startCol, $i + 2, $this->rows[$i]['producto']->nombre);
        }
        foreach ($this->sedes as $idx => $sede) {
            $col = $startCol + 1 + $idx;
            $sheet->setCellValueByColumnAndRow($col, 1, $sede->nombre);
            for ($i = 0; $i < $dataCount; $i++) {
                $sheet->setCellValueByColumnAndRow($col, $i + 2, $this->rows[$i]['por_sede'][$sede->nombre] ?? 0);
            }
        }

        // ── Referencias ──
        $catLetter = self::colLetter($startCol);
        $catCell = "'{$sheetName}'!\${$catLetter}\$2:\${$catLetter}\$" . ($dataCount + 1);

        // X-axis labels (categories = product names)
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $catCell, null, $dataCount),
        ];

        // Data series (one per sede)
        $dataSeriesValues = [];
        $dataSeriesLabels = [];
        foreach ($this->sedes as $idx => $sede) {
            $col = $startCol + 1 + $idx;
            $valLetter = self::colLetter($col);
            $valCell = "'{$sheetName}'!\${$valLetter}\$2:\${$valLetter}\$" . ($dataCount + 1);

            $dsv = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valCell, null, $dataCount);
            $dsv->setFillColor($colors[$idx % count($colors)]);
            $dataSeriesValues[] = $dsv;

            // Label = sede name (single cell)
            $labelCell = "'{$sheetName}'!\${$valLetter}\$1";
            $dataSeriesLabels[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $labelCell, null, 1);
        }

        // ── Build DataSeries ──
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        // ── PlotArea + Legend + Title ──
        $plotArea = new PlotArea(null, [$series]);
        $legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);
        $title = new Title('Stock por Sede');

        // ── Chart ──
        $chart = new Chart(
            'barras_sedes',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP
        );
        $chart->setTopLeftPosition('A' . ($dataCount + 4));
        $chart->setBottomRightPosition('H' . ($dataCount + 20));

        $sheet->addChart($chart);
    }

    private function addPieChart(Worksheet $sheet): void
    {
        $productos = Producto::with('categoria')->where('activo', true)->get();
        $stockPorCategoria = $productos->map(function ($p) {
            $total = collect($this->sedes)->sum(fn ($s) => $p->stockSueltoEnSede($s->id));
            return ['cat' => $p->categoria->nombre, 'total' => $total];
        })->filter(fn ($r) => $r['total'] > 0)
          ->groupBy('cat')
          ->map(fn ($g) => $g->sum('total'))
          ->sortDesc()
          ->toArray();

        if (empty($stockPorCategoria)) return;

        $cats = array_keys($stockPorCategoria);
        $vals = array_values($stockPorCategoria);
        $count = count($cats);
        $colors = ['3b82f6', '10b981', 'f59e0b', 'ef4444', '8b5cf6', 'ec4899'];
        $startCol = 70;
        $sheetName = $sheet->getTitle();

        for ($i = 0; $i < $count; $i++) {
            $sheet->setCellValueByColumnAndRow($startCol, $i + 2, $cats[$i]);
            $sheet->setCellValueByColumnAndRow($startCol + 1, $i + 2, $vals[$i]);
        }

        $catLetter = self::colLetter($startCol);
        $valLetter = self::colLetter($startCol + 1);
        $catCell = "'{$sheetName}'!\${$catLetter}\$2:\${$catLetter}\$" . ($count + 1);
        $valCell = "'{$sheetName}'!\${$valLetter}\$2:\${$valLetter}\$" . ($count + 1);

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $catCell, null, $count),
        ];

        $dsv = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valCell, null, $count);

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, $count - 1),
            [],
            $xAxisTickValues,
            [$dsv]
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, true);
        $title = new Title('Stock por Categoría');

        $chart = new Chart(
            'pastel_categorias',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP
        );
        $chart->setTopLeftPosition('A' . ($this->rows->count() + 22));
        $chart->setBottomRightPosition('H' . ($this->rows->count() + 38));

        $sheet->addChart($chart);
    }

    private static function colLetter(int $col): string
    {
        $letter = '';
        while ($col > 0) {
            $col--;
            $letter = chr(65 + ($col % 26)) . $letter;
            $col = intdiv($col, 26);
        }
        return $letter;
    }

    public function title(): string
    {
        return 'Reporte de Almacén';
    }
}
