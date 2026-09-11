<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GanttExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function collection()
    {
        return collect([
            // Módulo Almacén
            ['modulo' => 'Almacén', 'funcionalidad' => 'Seedear productos GNV', 'inicio' => '2026-09-01', 'fin' => '2026-09-01', 'estado' => 'Completado', 'dias' => 1],
            ['modulo' => 'Almacén', 'funcionalidad' => 'Recepciones - Crear', 'inicio' => '2026-09-02', 'fin' => '2026-09-02', 'estado' => 'Completado', 'dias' => 1],
            ['modulo' => 'Almacén', 'funcionalidad' => 'Recepciones - Listado', 'inicio' => '2026-09-02', 'fin' => '2026-09-02', 'estado' => 'Completado', 'dias' => 1],
            ['modulo' => 'Almacén', 'funcionalidad' => 'Stock - Ver', 'inicio' => '2026-09-03', 'fin' => '2026-09-03', 'estado' => 'Completado', 'dias' => 1],
            ['modulo' => 'Almacén', 'funcionalidad' => 'Kits - Completar', 'inicio' => '2026-09-03', 'fin' => '2026-09-03', 'estado' => 'Completado', 'dias' => 1],
            ['modulo' => 'Almacén', 'funcionalidad' => 'Traslados - Crear', 'inicio' => '2026-09-04', 'fin' => '2026-09-04', 'estado' => 'Completado', 'dias' => 1],
            ['modulo' => 'Almacén', 'funcionalidad' => 'Reporte con gráficos', 'inicio' => '2026-09-08', 'fin' => '2026-09-08', 'estado' => 'Completado', 'dias' => 1],
            
            // Módulo Conversiones
            ['modulo' => 'Conversiones', 'funcionalidad' => 'Asignar Equipos (por kit)', 'inicio' => '2026-09-04', 'fin' => '2026-09-05', 'estado' => 'Completado', 'dias' => 2],
            ['modulo' => 'Conversiones', 'funcionalidad' => 'Realizar (búsqueda automática)', 'inicio' => '2026-09-05', 'fin' => '2026-09-06', 'estado' => 'Completado', 'dias' => 2],
            ['modulo' => 'Conversiones', 'funcionalidad' => 'Registrar Series', 'inicio' => '2026-09-06', 'fin' => '2026-09-06', 'estado' => 'Completado', 'dias' => 1],
            
            // Base de Datos
            ['modulo' => 'Base de Datos', 'funcionalidad' => 'Seeder datos de prueba', 'inicio' => '2026-09-08', 'fin' => '2026-09-08', 'estado' => 'Completado', 'dias' => 1],
            
            // Exportaciones
            ['modulo' => 'Exportaciones', 'funcionalidad' => 'PDF con gráficos', 'inicio' => '2026-09-08', 'fin' => '2026-09-08', 'estado' => 'Completado', 'dias' => 1],
            ['modulo' => 'Exportaciones', 'funcionalidad' => 'Excel con gráficos', 'inicio' => '2026-09-08', 'fin' => '2026-09-08', 'estado' => 'Completado', 'dias' => 1],
            
            // UI/UX
            ['modulo' => 'UI/UX', 'funcionalidad' => 'Sidebar actualizado', 'inicio' => '2026-09-04', 'fin' => '2026-09-04', 'estado' => 'Completado', 'dias' => 1],
            ['modulo' => 'UI/UX', 'funcionalidad' => 'Rutas actualizadas', 'inicio' => '2026-09-04', 'fin' => '2026-09-04', 'estado' => 'Completado', 'dias' => 1],
            
            // Limpieza
            ['modulo' => 'Limpieza', 'funcionalidad' => 'Eliminar pantallas obsoletas', 'inicio' => '2026-09-08', 'fin' => '2026-09-08', 'estado' => 'Completado', 'dias' => 1],
        ]);
    }

    public function headings(): array
    {
        return ['Módulo', 'Funcionalidad', 'Inicio', 'Fin', 'Estado', 'Días'];
    }

    public function map($item): array
    {
        return [
            $item['modulo'],
            $item['funcionalidad'],
            $item['inicio'],
            $item['fin'],
            $item['estado'],
            $item['dias'],
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        // Header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFont()->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A1:F1')->getFont()->setSize(12);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:F1')->getFill()->setStartColor(new Color('1e40af'));

        // Ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(8);

        // Colores por módulo
        $modulos = [
            'Almacén' => 'dbeafe',
            'Conversiones' => 'dcfce7',
            'Base de Datos' => 'fef3c7',
            'Exportaciones' => 'f3e8ff',
            'UI/UX' => 'ffe4e6',
            'Limpieza' => 'e0e7ff',
        ];

        for ($row = 2; $row <= 17; $row++) {
            $modulo = $sheet->getCell("A{$row}")->getValue();
            if (isset($modulos[$modulo])) {
                $sheet->getStyle("A{$row}:F{$row}")->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle("A{$row}:F{$row}")->getFill()->setStartColor(new Color($modulos[$modulo]));
            }
        }

        // Agregar gráfico de Gantt
        $this->addGanttChart($sheet);
    }

    private function addGanttChart(Worksheet $sheet): void
    {
        // Datos para el gráfico
        $funcionalidades = [
            'Seedear productos', 'Recepciones', 'Stock Ver', 'Kits Completar',
            'Traslados', 'Reporte', 'Asignar Equipos', 'Realizar',
            'Registrar Series', 'Seeder', 'PDF', 'Excel',
            'Sidebar', 'Rutas', 'Eliminar obs.'
        ];
        
        $dias = [1, 1, 1, 1, 1, 1, 2, 2, 1, 1, 1, 1, 1, 1, 1];

        // Escribir datos auxiliares
        $sheet->setCellValueByColumnAndRow(8, 1, 'Funcionalidad');
        $sheet->setCellValueByColumnAndRow(9, 1, 'Días');
        $sheet->getColumnDimension('H')->setVisible(false);
        $sheet->getColumnDimension('I')->setVisible(false);

        for ($i = 0; $i < count($funcionalidades); $i++) {
            $sheet->setCellValueByColumnAndRow(8, $i + 2, $funcionalidades[$i]);
            $sheet->setCellValueByColumnAndRow(9, $i + 2, $dias[$i]);
        }

        // Crear gráfico de barras horizontal (simula Gantt)
        $chart = new Chart('gantt');
        $chart->setTitle(new Title('Diagrama Gantt - Funcionalidades'));
        $chart->setTopLeftPosition('A19');
        $chart->setBottomRightPosition('F35');

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_BARS,
            range(0, count($dias) - 1)
        );
        $series->setTitle(new Title('Días'));
        $series->setValues(new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Sheet1'!\$I\$2:\$I\$" . (count($dias) + 1)));
        $series->setCategoryAxisLabels(new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Sheet1'!\$H\$2:\$H\$" . (count($dias) + 1)));
        $series->setFillColor(new Color('3b82f6'));

        $plotArea = new PlotArea(null, [$series]);
        $chart->setPlotArea($plotArea);

        $sheet->addChart($chart);
    }

    public function title(): string
    {
        return 'Diagrama Gantt';
    }
}
