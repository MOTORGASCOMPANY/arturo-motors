<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FiseReporteExport
{
    public function __construct(private array $datos)
    {
    }

    public function spreadsheet(): Spreadsheet
    {
        $d = $this->datos;
        $ss = new Spreadsheet();
        $hoja = $ss->getActiveSheet();
        $hoja->setTitle('REPORTE FISE');

        $fillSeccion = 'FFBDD6EE';
        $nombreDe = fn ($cliente) => $cliente
            ? ($cliente->nombre_completo ?? trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')))
            : '—';

        $hoja->setCellValue('A1', 'REPORTE FISE');
        $hoja->mergeCells('A1:G1');
        $hoja->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $hoja->setCellValue('A2', 'Del ' . $d['desde']->format('d/m/Y') . ' al ' . $d['hasta']->format('d/m/Y'));
        $hoja->mergeCells('A2:G2');

        $fila = 4;

        $seccion = function (string $titulo) use (&$fila, $hoja, $fillSeccion) {
            $hoja->setCellValue("A{$fila}", $titulo);
            $hoja->mergeCells("A{$fila}:G{$fila}");
            $hoja->getStyle("A{$fila}")->getFont()->setBold(true);
            $hoja->getStyle("A{$fila}:G{$fila}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($fillSeccion);
            $fila++;
        };

        $encabezado = function (array $cols) use (&$fila, $hoja, $fillSeccion) {
            foreach ($cols as $i => $texto) {
                $hoja->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . $fila, $texto);
            }
            $ultima = Coordinate::stringFromColumnIndex(count($cols));
            $hoja->getStyle("A{$fila}:{$ultima}{$fila}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillSeccion]],
                'borders' => ['allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['argb' => 'FFB0B0B0'],
                ]],
                'alignment' => ['horizontal' => 'center'],
            ]);
            $fila++;
        };

        $datos = function (array $vals, array $montos = []) use (&$fila, $hoja) {
            foreach ($vals as $i => $v) {
                $col = Coordinate::stringFromColumnIndex($i + 1);
                $hoja->setCellValue($col . $fila, $v);
                if (in_array($i, $montos, true)) {
                    $hoja->getStyle($col . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
                }
            }
            $fila++;
        };

        // Indicadores
        $seccion('INDICADORES');
        $encabezado(['Indicador', 'Valor']);
        $datos(['Solicitudes totales', $d['totalSolicitudes']]);
        $datos(['Solicitudes aprobadas', $d['solicitudesAprobadas']]);
        $datos(['Solicitudes rechazadas', $d['solicitudesRechazadas']]);
        $datos(['Solicitudes pendientes', $d['solicitudesPendientes']]);
        $datos(['Tasa de aprobación', $d['tasaAprobacion'] . '%']);
        $datos(['Pagos registrados', $d['totalPagos']]);
        $datos(['Monto total FISE', (float) $d['montoTotalFise']], [1]);
        $datos(['Monto pagado FISE', (float) $d['montoPagadoFise']], [1]);
        $datos(['Saldo pendiente FISE', (float) $d['saldoPendiente']], [1]);
        $datos(['Ingresos en caja (FISE)', (float) $d['ingresosCajaFise']], [1]);
        $fila++;

        // Solicitudes
        $seccion('SOLICITUDES DEL PERÍODO');
        $encabezado(['Fecha', 'Cliente', 'Vehículo', 'Estado', 'Observaciones']);
        if ($d['solicitudes']->isEmpty()) {
            $datos(['Sin solicitudes en el rango seleccionado.']);
        } else {
            foreach ($d['solicitudes'] as $s) {
                $datos([
                    $s->created_at->format('d/m/Y'),
                    $nombreDe($s->cliente),
                    $s->vehiculo?->placa ?? '—',
                    strtoupper($s->estado ?? '—'),
                    $s->observaciones ?? '',
                ]);
            }
        }
        $fila++;

        // Pagos
        $seccion('PAGOS DEL PERÍODO');
        $encabezado(['Fecha', 'Orden', 'Cliente', 'Técnico', 'Monto total', 'Monto pagado', 'Estado']);
        if ($d['pagos']->isEmpty()) {
            $datos(['Sin pagos en el rango seleccionado.']);
        } else {
            foreach ($d['pagos'] as $p) {
                $datos([
                    $p->created_at->format('d/m/Y'),
                    '#' . $p->service_order_id,
                    $nombreDe($p->serviceOrder?->cliente),
                    $p->pagadoPor?->name ?? '—',
                    (float) $p->monto_total,
                    (float) $p->monto_pagado,
                    strtoupper($p->estado ?? '—'),
                ], [4, 5]);
            }
        }
        $fila++;

        // Pagos por técnico
        $seccion('PAGOS POR TÉCNICO');
        $encabezado(['Técnico', 'Operaciones', 'Monto total', 'Monto pagado']);
        if ($d['pagosPorTecnico']->isEmpty()) {
            $datos(['Sin pagos con técnico asignado.']);
        } else {
            foreach ($d['pagosPorTecnico'] as $tecnico => $row) {
                $datos([$tecnico, (int) $row['cantidad'], (float) $row['monto_total'], (float) $row['monto_pagado']], [2, 3]);
            }
        }

        foreach (['A' => 34, 'B' => 26, 'C' => 18, 'D' => 14, 'E' => 26, 'F' => 16, 'G' => 14] as $col => $ancho) {
            $hoja->getColumnDimension($col)->setWidth($ancho);
        }

        return $ss;
    }
}
