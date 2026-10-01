<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Plantilla "INVENTARIO 2026".
 *
 * Hoja 1 "INVENTARIO 2026": bloques REDUCTOR (A-K) y TANQUE (M-V) lado a lado.
 * Hoja 2 "EQUIPOS IGT": equipos por kit + subtabla COMPONENTES DE KITS.
 * Hoja 3 "KITS Y SERIALIZADOS": items serializados, serializados en kits y kits instalados.
 */
class Inventario2026Export
{
    private const FILL_HEADER = 'FFBDD6EE';

    public function __construct(private ?int $filtroSede = null)
    {
    }

    public function spreadsheet(): Spreadsheet
    {
        $ss = new Spreadsheet();

        $inventario = $ss->getActiveSheet();
        $inventario->setTitle('INVENTARIO 2026');
        $this->hojaInventario($inventario);

        $equipos = $ss->createSheet();
        $equipos->setTitle('EQUIPOS IGT');
        $this->hojaEquiposIgt($equipos);

        $kits = $ss->createSheet();
        $kits->setTitle('KITS Y SERIALIZADOS');
        $this->hojaKitsSerializados($kits);

        // Impresión: A4 horizontal, 1 página de ancho por hoja
        foreach ($ss->getAllSheets() as $sh) {
            $setup = $sh->getPageSetup();
            $setup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $setup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $setup->setFitToPage(true);
            $setup->setFitToWidth(1);
            $setup->setFitToHeight(0);
        }

        return $ss;
    }

    // ────────────────────────────────────────────────────────────
    //  HOJA 1 — INVENTARIO 2026 (bloques REDUCTOR / TANQUE)
    // ────────────────────────────────────────────────────────────
    private function hojaInventario(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sh): void
    {
        $reductores = $this->itemsPorCategoria(3);
        $tanques    = $this->itemsPorCategoria(4);

        $anchos = ['A'=>11.6,'B'=>9.3,'C'=>11.9,'D'=>10.9,'E'=>13,'F'=>11.3,'G'=>3.4,'H'=>12.3,
                   'I'=>11.3,'J'=>13,'K'=>13,'L'=>8.4,'M'=>11.7,'N'=>8.7,'O'=>12.7,'P'=>10.3,
                   'Q'=>13,'R'=>10.3,'S'=>7.7,'T'=>7.6,'U'=>13,'V'=>13];
        foreach ($anchos as $col => $w) {
            $sh->getColumnDimension($col)->setWidth($w);
        }

        // Encabezado fila 1 (verticales) + fila 2 — SIN fecha de certificación
        $h1 = [
            'A' => 'FECHA DE LEGADA', 'B' => 'PLACA', 'C' => 'TALLER', 'D' => 'FECHA ENTREGA',
            'E' => 'COMPONENTE', 'K' => 'PERTENECE A KIT',
            'M' => 'FECHA DE LEGADA', 'N' => 'PLACA', 'O' => 'TALLER', 'P' => 'FECHA ENTREGA',
            'Q' => 'COMPONENTE', 'V' => 'PERTENECE A KIT',
        ];
        $h2 = [
            'F' => 'SERIE', 'H' => 'MARCA', 'I' => 'GENERACION', 'J' => 'PRODUCE',
            'R' => 'SERIE', 'S' => 'MARCA', 'T' => 'CAPAC.', 'U' => 'PRODUCE',
        ];
        foreach ($h1 as $c => $v) $sh->setCellValue($c . '1', $v);
        foreach ($h2 as $c => $v) $sh->setCellValue($c . '2', $v);
        foreach (['A','B','C','D','E','K','M','N','O','P','Q','V'] as $c) {
            $sh->mergeCells($c . '1:' . $c . '2');
        }
        $sh->setCellValue('F1', 'REDUCTOR');
        $sh->mergeCells('F1:J1');
        $sh->setCellValue('R1', 'TANQUE');
        $sh->mergeCells('R1:U1');

        $maxFilas = max(count($reductores), count($tanques), 1);
        $secuencia = [];
        for ($i = 0; $i < $maxFilas; $i++) {
            $r = 3 + $i;
            if (isset($reductores[$i])) {
                $it = $reductores[$i];
                $a  = $this->attrs($it);
                $f  = $a['recepcion_fecha'] ?? '';
                $nro = ($secuencia['L'][$f] = ($secuencia['L'][$f] ?? 0) + 1);
                $sh->setCellValue("A{$r}", $this->fecha($f));
                $sh->setCellValueExplicit("B{$r}", (string) ($it->placa ?? '---'), DataType::TYPE_STRING);
                $sh->setCellValue("C{$r}", $it->sede);
                $sh->setCellValue("D{$r}", $this->fecha($it->fecha_fin_conversion));
                $sh->setCellValue("E{$r}", $it->prod);
                $sh->setCellValueExplicit("F{$r}", (string) ($it->serie ?? ($a['serie'] ?? '---')), DataType::TYPE_STRING);
                $sh->setCellValue("G{$r}", $nro);
                $sh->setCellValue("H{$r}", $a['marca'] ?? '---');
                $sh->setCellValue("I{$r}", $a['generacion'] ?? '---');
                $sh->setCellValue("J{$r}", $this->produce($a));
                $sh->setCellValue("K{$r}", $it->kit_padre_id ? 'Si' : 'No');
            }
            if (isset($tanques[$i])) {
                $it = $tanques[$i];
                $a  = $this->attrs($it);
                $f  = $a['recepcion_fecha'] ?? '';
                $sh->setCellValue("M{$r}", $this->fecha($f));
                $sh->setCellValueExplicit("N{$r}", (string) ($it->placa ?? '---'), DataType::TYPE_STRING);
                $sh->setCellValue("O{$r}", $it->sede);
                $sh->setCellValue("P{$r}", $this->fecha($it->fecha_fin_conversion));
                $sh->setCellValue("Q{$r}", $it->prod);
                $sh->setCellValueExplicit("R{$r}", (string) ($it->serie ?? ($a['serie'] ?? '---')), DataType::TYPE_STRING);
                $sh->setCellValue("S{$r}", $a['marca'] ?? '---');
                $sh->setCellValue("T{$r}", $a['capacidad'] ?? '---');
                $sh->setCellValue("U{$r}", $this->produce($a));
                $sh->setCellValue("V{$r}", $it->kit_padre_id ? 'Si' : 'No');
            }
        }

        // Merges verticales de lote (FECHA DE LEGADA repetida), como la plantilla
        $this->mergeLotes($sh, 'A', $maxFilas);
        $this->mergeLotes($sh, 'M', $maxFilas);

        $ultima = 2 + $maxFilas;
        $this->estiloHeader($sh, "A1:V2");
        $this->estiloDatos($sh, "A3:V{$ultima}");
        foreach (['A', 'D', 'M', 'P'] as $c) {
            $sh->getStyle("{$c}3:{$c}{$ultima}")->getNumberFormat()->setFormatCode('@');
        }
        $sh->freezePane('A3');
    }

    // ────────────────────────────────────────────────────────────
    //  HOJA 2 — EQUIPOS IGT (equipos por kit + COMPONENTES DE KITS)
    // ────────────────────────────────────────────────────────────
    private function hojaEquiposIgt(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sh): void
    {
        $equipos    = $this->equiposDeKits();
        $componentes = $this->componentesDeKits();

        $anchos = ['A'=>11.4,'B'=>13.3,'C'=>15.7,'D'=>14.6,'E'=>3,'F'=>12,'G'=>15,'H'=>13,
                   'K'=>11,'L'=>14,'M'=>10];
        foreach ($anchos as $col => $w) {
            $sh->getColumnDimension($col)->setWidth($w);
        }

        $h1 = ['A'=>'FECHA DE LLEGADA','B'=>'PLACA','C'=>'TALLER','D'=>'FECHA DE ENTREGA',
               'F'=>'EQUIPO','K'=>'FECHA','L'=>'PRODUCTO','M'=>'CANTIDAD'];
        $h2 = ['F'=>'MARCA','G'=>'SERIE','H'=>'PRODUCE'];
        foreach ($h1 as $c => $v) $sh->setCellValue($c . '1', $v);
        foreach ($h2 as $c => $v) $sh->setCellValue($c . '2', $v);
        foreach (['A','B','C','D','K','L','M'] as $c) {
            $sh->mergeCells($c . '1:' . $c . '2');
        }
        $sh->mergeCells('F1:H1');

        // Filas de equipos (hijos serializados de kits) — columna A mergeada por kit
        $kitActual = null;
        $iniMerge  = 3;
        for ($i = 0; $i < count($equipos); $i++) {
            $r = 3 + $i;
            $e = $equipos[$i];
            $a = $this->attrs($e);
            $sh->setCellValue("A{$r}", $this->fecha($e->recepcion ?? ''));
            $sh->setCellValueExplicit("B{$r}", (string) ($e->placa ?: '---'), DataType::TYPE_STRING);
            $sh->setCellValue("C{$r}", $e->taller);
            $sh->setCellValue("D{$r}", $this->fecha($e->entrega));
            $sh->setCellValue("F{$r}", $a['marca'] ?? '---');
            $sh->setCellValueExplicit("G{$r}", (string) ($e->serie ?? ($a['serie'] ?? '---')), DataType::TYPE_STRING);
            $sh->setCellValue("H{$r}", $this->produce($a));

            if ($kitActual !== $e->kit_id) {
                if ($kitActual !== null && $r - 1 > $iniMerge) {
                    $sh->mergeCells("A{$iniMerge}:A" . ($r - 1));
                }
                $kitActual = $e->kit_id;
                $iniMerge  = $r;
            }
        }
        if ($kitActual !== null && (3 + count($equipos) - 1) > $iniMerge) {
            $sh->mergeCells("A{$iniMerge}:A" . (2 + count($equipos)));
        }

        // Subtabla derecha: COMPONENTES DE KITS (FECHA / PRODUCTO / CANTIDAD)
        for ($i = 0; $i < count($componentes); $i++) {
            $r = 3 + $i;
            $sh->setCellValue("K{$r}", $this->fecha($componentes[$i]->fecha));
            $sh->setCellValue("L{$r}", $componentes[$i]->producto);
            $sh->setCellValue("M{$r}", $componentes[$i]->cantidad);
        }

        $maxFila = max(count($equipos), count($componentes), 1) + 2;
        $this->estiloHeader($sh, 'A1:M2');
        $this->estiloDatos($sh, "A3:M{$maxFila}");
        foreach (['A', 'D', 'K'] as $c) {
            $sh->getStyle("{$c}3:{$c}{$maxFila}")->getNumberFormat()->setFormatCode('@');
        }
        $sh->freezePane('A3');
    }

    // ────────────────────────────────────────────────────────────
    //  HOJA 3 — KITS Y SERIALIZADOS (3 subtablas)
    // ────────────────────────────────────────────────────────────
    private function hojaKitsSerializados(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sh): void
    {
        $anchos = ['A'=>18,'B'=>28,'C'=>16,'D'=>22,'E'=>16,'F'=>16,'G'=>14,'H'=>16,'I'=>14];
        foreach ($anchos as $col => $w) {
            $sh->getColumnDimension($col)->setWidth($w);
        }

        $fila = 1;

        // ── 1) ITEMS SERIALIZADOS ──
        $serializados = $this->todosSerializados();
        $fila = $this->subtabla(
            $sh, $fila, 'ITEMS SERIALIZADOS',
            ['COMPONENTE', 'SERIE', 'MARCA', 'GENERACION / CAPAC.', 'PRODUCE', 'ESTADO'],
            $serializados->isEmpty()
                ? [['sin cantidad', '', '', '', '', '']]
                : $serializados->map(fn ($it) => [
                    $it->prod,
                    $it->serie ?? ($this->attrs($it)['serie'] ?? '---'),
                    ($this->attrs($it)['marca'] ?? '') !== '' ? $this->attrs($it)['marca'] : '---',
                    ($this->attrs($it)['generacion'] ?? $this->attrs($it)['capacidad'] ?? '') !== ''
                        ? ($this->attrs($it)['generacion'] ?? $this->attrs($it)['capacidad']) : '---',
                    $this->produce($this->attrs($it)),
                    $it->estado,
                ])->toArray()
        ) + 1;

        // ── 2) ITEMS SERIALIZADOS EN KITS ──
        $enKits = $serializados->filter(fn ($it) => $it->kit_id !== null)->values();
        $fila = $this->subtabla(
            $sh, $fila, 'ITEMS SERIALIZADOS EN KITS',
            ['COMPONENTE', 'SERIE', 'MARCA', 'GENERACION / CAPAC.', 'PRODUCE', 'KIT', 'PLACA', 'FECHA ENTREGA', 'ESTADO'],
            $enKits->isEmpty()
                ? [['sin datos', '', '', '', '', '', '', '', '']]
                : $enKits->map(fn ($it) => [
                    $it->prod,
                    $it->serie ?? ($this->attrs($it)['serie'] ?? '---'),
                    ($this->attrs($it)['marca'] ?? '') !== '' ? $this->attrs($it)['marca'] : '---',
                    ($this->attrs($it)['generacion'] ?? $this->attrs($it)['capacidad'] ?? '') !== ''
                        ? ($this->attrs($it)['generacion'] ?? $this->attrs($it)['capacidad']) : '---',
                    $this->produce($this->attrs($it)),
                    $it->kit_nombre ?? 'sin kit',
                    $it->placa ?: '---',
                    $this->fecha($it->entrega),
                    $it->estado,
                ])->toArray()
        ) + 1;

        // ── 3) KITS INSTALADOS ──
        $instalados = $this->kitsInstalados();
        $this->subtabla(
            $sh, $fila, 'KITS INSTALADOS',
            ['KIT', 'CLIENTE', 'DNI', 'VEHÍCULO', 'SERVICIO', 'DÓNDE', 'CUÁNDO', 'ESTADO', 'PRECIO FINAL'],
            $instalados->isEmpty()
                ? [['sin datos', '', '', '', '', '', '', '', '']]
                : $instalados->map(fn ($k) => [
                    $k->kit_nombre,
                    trim(($k->cli_nombre ?? '') . ' ' . ($k->cli_apellido ?? '')) ?: '---',
                    $k->documento ?? '---',
                    ($k->placa ? $k->placa . ' — ' . trim(($k->marca ?? '') . ' ' . ($k->modelo ?? '')) : '---')
                        . ($k->color ? " ({$k->color})" : ''),
                    $k->servicio ?? '---',
                    $k->sede,
                    'Recep. ' . $this->fecha($k->recepcion)
                        . ($k->fecha_inicio ? ' · Inicio ' . date('d-m-y H:i', strtotime($k->fecha_inicio)) : '')
                        . ($k->fecha_fin ? ' · Entrega ' . date('d-m-y H:i', strtotime($k->fecha_fin)) : ''),
                    $k->estado_orden ?? 'sin orden',
                    $k->precio_final !== null ? 'S/ ' . $k->precio_final : '---',
                ])->toArray()
        );
    }

    /**
     * Escribe una subtabla con título + encabezado + filas. Devuelve la próxima fila libre.
     */
    private function subtabla(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sh, int $fila, string $titulo, array $headers, array $filas): int
    {
        $ultimaCol = Coordinate::stringFromColumnIndex(count($headers));

        $sh->setCellValue("A{$fila}", $titulo);
        $sh->getStyle("A{$fila}")->getFont()->setBold(true)->setSize(12);
        $fila++;

        $filaHeader = $fila;
        foreach ($headers as $idx => $h) {
            $sh->setCellValueByColumnAndRow($idx + 1, $fila, $h);
        }
        $this->estiloHeader($sh, "A{$filaHeader}:{$ultimaCol}{$filaHeader}");

        foreach ($filas as $datos) {
            $fila++;
            foreach ($datos as $idx => $valor) {
                $coord = Coordinate::stringFromColumnIndex($idx + 1) . $fila;
                if (is_int($valor) || is_float($valor)) {
                    $sh->setCellValue($coord, $valor);
                } else {
                    // Seriales, DNI, placas: siempre texto (evita notación científica)
                    $sh->setCellValueExplicit($coord, (string) $valor, DataType::TYPE_STRING);
                }
            }
        }
        $this->estiloDatos($sh, "A" . ($filaHeader + 1) . ":{$ultimaCol}{$fila}");

        return $fila + 2; // deja una fila en blanco
    }

    // ────────────────────────────────────────────────────────────
    //  CONSULTAS (solo lectura, respetan filtro de sede)
    // ────────────────────────────────────────────────────────────

    /** Items serializados de una categoría (3=Reductores, 4=Tanques). */
    private function itemsPorCategoria(int $categoriaId): array
    {
        return $this->baseSerializados()
            ->where('p.categoria_id', $categoriaId)
            ->orderBy('i.id')
            ->get()
            ->toArray();
    }

    /** Todos los items serializados (con datos de kit si tienen). */
    private function todosSerializados()
    {
        return $this->baseSerializados()
            ->leftJoin('items_serializados as k', 'k.id', 'i.kit_padre_id')
            ->leftJoin('productos as kp', 'kp.id', 'k.producto_id')
            ->leftJoin('service_orders as ko', 'ko.id', 'k.service_order_id')
            ->leftJoin('vehiculos as kv', 'kv.id', 'ko.vehiculo_id')
            ->addSelect('k.id as kit_id', 'kp.nombre as kit_nombre', 'kv.placa as kit_placa',
                'ko.fecha_fin_conversion as kit_entrega')
            ->selectRaw('IFNULL(kv.placa, "") as placa, IFNULL(ko.fecha_fin_conversion, "") as entrega')
            ->orderBy('i.id')
            ->get();
    }

    private function baseSerializados()
    {
        $q = DB::table('items_serializados as i')
            ->join('productos as p', 'p.id', 'i.producto_id')
            ->join('categorias_almacen as c', 'c.id', 'p.categoria_id')
            ->leftJoin('service_orders as o', 'o.id', 'i.service_order_id')
            ->leftJoin('vehiculos as v', 'v.id', 'o.vehiculo_id')
            ->join('sedes as s', 's.id', 'i.sede_id')
            ->where('c.es_serializado', 1)
            ->select('i.id', 'i.serie', 'i.atributos', 'i.estado', 'i.kit_padre_id', 'i.created_at',
                'p.nombre as prod', 's.nombre as sede',
                'v.placa', 'o.fecha_fin_conversion');

        if ($this->filtroSede) {
            $q->where('i.sede_id', $this->filtroSede);
        }
        return $q;
    }

    /** Hijos serializados de kits (equipos) con datos del kit (placa, taller, fechas). */
    private function equiposDeKits(): array
    {
        $q = DB::table('items_serializados as i')
            ->join('productos as p', 'p.id', 'i.producto_id')
            ->join('categorias_almacen as c', 'c.id', 'p.categoria_id')
            ->join('items_serializados as k', 'k.id', 'i.kit_padre_id')
            ->join('sedes as s', 's.id', 'k.sede_id')
            ->leftJoin('service_orders as o', 'o.id', 'k.service_order_id')
            ->leftJoin('vehiculos as v', 'v.id', 'o.vehiculo_id')
            ->where('c.es_serializado', 1)
            ->whereNotNull('i.kit_padre_id')
            ->select('i.id', 'i.serie', 'i.atributos', 'p.nombre as prod',
                'k.id as kit_id', 'k.service_order_id', 's.nombre as taller',
                'k.created_at', 'v.placa', 'o.fecha_fin_conversion')
            ->orderBy('k.id')
            ->orderBy('i.id');

        if ($this->filtroSede) {
            $q->where('k.sede_id', $this->filtroSede);
        }

        return collect($q->get())->map(function ($e) {
            $a = $this->attrs($e);
            $e->recepcion = $a['recepcion_fecha'] ?? substr($e->created_at ?? '', 0, 10);
            $e->entrega   = $e->fecha_fin_conversion;
            return $e;
        })->toArray();
    }

    /** Componentes de kits agrupados (FECHA / PRODUCTO / CANTIDAD). */
    private function componentesDeKits(): array
    {
        $q = DB::table('items_serializados as i')
            ->join('productos as p', 'p.id', 'i.producto_id')
            ->join('categorias_almacen as c', 'c.id', 'p.categoria_id')
            ->join('items_serializados as k', 'k.id', 'i.kit_padre_id')
            ->where('c.es_serializado', 0)
            ->where('c.es_kit', 0)
            ->whereNotNull('i.kit_padre_id')
            ->select('i.atributos', 'p.nombre as producto', 'i.created_at', 'i.sede_id')
            ->orderBy('i.id');

        if ($this->filtroSede) {
            $q->where('i.sede_id', $this->filtroSede);
        }

        return $q->get()
            ->groupBy(function ($c) {
                $a = $this->attrs($c);
                return ($a['fecha'] ?? substr($c->created_at ?? '', 0, 10)) . '||' . $c->producto;
            })
            ->map(function ($grupo) {
                $primero = $grupo->first();
                $a = $this->attrs($primero);
                return (object) [
                    'fecha'    => $a['fecha'] ?? substr($primero->created_at ?? '', 0, 10),
                    'producto' => $primero->producto,
                    'cantidad' => $grupo->count(),
                ];
            })
            ->values()
            ->toArray();
    }

    /** Kits con orden (ya instalados en un vehículo) + cliente/vehículo/servicio. */
    private function kitsInstalados()
    {
        $q = DB::table('items_serializados as i')
            ->join('productos as p', 'p.id', 'i.producto_id')
            ->join('categorias_almacen as c', 'c.id', 'p.categoria_id')
            ->join('sedes as s', 's.id', 'i.sede_id')
            ->join('service_orders as o', 'o.id', 'i.service_order_id')
            ->leftJoin('clientes as cl', 'cl.id', 'o.cliente_id')
            ->leftJoin('vehiculos as v', 'v.id', 'o.vehiculo_id')
            ->leftJoin('services as sv', 'sv.id', 'o.service_id')
            ->where('c.es_kit', 1)
            ->whereNull('i.kit_padre_id')
            ->select(
                'i.atributos', 'i.created_at',
                'p.nombre as kit_nombre', 's.nombre as sede',
                'o.estado as estado_orden', 'o.fecha_inicio_conversion as fecha_inicio',
                'o.fecha_fin_conversion as fecha_fin', 'o.precio_final',
                'cl.nombre as cli_nombre', 'cl.apellido as cli_apellido', 'cl.documento',
                'v.placa', 'v.marca', 'v.modelo', 'v.color',
                'sv.nombre as servicio'
            )
            ->orderBy('i.id');

        if ($this->filtroSede) {
            $q->where('i.sede_id', $this->filtroSede);
        }

        return $q->get()->map(function ($k) {
            $a = $this->attrs($k);
            $k->recepcion = $a['recepcion_fecha'] ?? substr($k->created_at ?? '', 0, 10);
            return $k;
        });
    }

    // ────────────────────────────────────────────────────────────
    //  ESTILO / HELPERS
    // ────────────────────────────────────────────────────────────

    private function estiloHeader(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sh, string $rango): void
    {
        $sh->getStyle($rango)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => self::FILL_HEADER],
            ],
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['bottom' => ['borderStyle' => 'thin']],
        ]);
    }

    private function estiloDatos(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sh, string $rango): void
    {
        $sh->getStyle($rango)->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 11],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['bottom' => ['borderStyle' => 'thin']],
        ]);
    }

    /** Mergea celdas consecutivas iguales de una columna (lotes de FECHA DE LEGADA). */
    private function mergeLotes(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sh, string $col, int $maxFilas): void
    {
        $ini = 3;
        for ($r = 3; $r <= 2 + $maxFilas; $r++) {
            $actual = $sh->getCell($col . $r)->getValue();
            $siguiente = $r < 2 + $maxFilas ? $sh->getCell($col . ($r + 1))->getValue() : "\x00fin";
            if ($actual !== $siguiente) {
                if ($r > $ini && $actual !== '' && $actual !== null) {
                    $sh->mergeCells("{$col}{$ini}:{$col}{$r}");
                }
                $ini = $r + 1;
            }
        }
    }

    private function attrs($item): array
    {
        if (empty($item->atributos)) return [];
        $a = json_decode($item->atributos, true);
        return is_array($a) ? $a : [];
    }

    /** PRODUCE: valor del JSON o "sin registrar". */
    private function produce(array $a): string
    {
        return (isset($a['produce']) && $a['produce'] !== '') ? $a['produce'] : 'sin registrar';
    }

    /** Formato de fecha dd-mm-yy (texto, igual que la plantilla); vacío → "---". */
    private function fecha(?string $fecha): string
    {
        if (!$fecha) return '---';
        return date('d-m-y', strtotime($fecha));
    }
}
