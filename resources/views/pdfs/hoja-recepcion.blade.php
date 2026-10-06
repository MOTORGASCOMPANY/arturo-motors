<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Hoja de Recepción del Vehículo</title>
@include('pdfs._marca')
<style>
    /* Documento denso: calibrado para caber en UNA sola hoja */
    @page { margin: 20px 30px 34px 30px; }

    body { font-size: 8px; line-height: 1.3; }

    table.watermark img { width: 280px; }

    .logo-cell img { height: 36px; width: auto; }
    .brand-title { font-size: 15px; }
    .brand-subtitle { font-size: 6.5px; }
    .ruc-value { font-size: 9px; }
    .rd-value { font-size: 6.5px; }
    .header-divider { margin: 5px 0 7px 0; }

    table.doc-title-box { margin-bottom: 7px; }
    table.doc-title-box td { padding-bottom: 4px; }
    .doc-title-main { font-size: 11.5px; }
    .doc-title-sub { font-size: 6.5px; }
    .doc-title-tag { text-align: right; font-size: 8.5px; font-weight: bold; color: #1565c0; }

    table.fechas { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
    table.fechas td { padding: 3px 4px; font-size: 8px; color: #46586b; border-bottom: 0.75px solid #e2e6ea; }
    table.fechas td strong { color: #1565c0; }

    .section-title { margin: 8px 0 3px 0; padding-bottom: 2px; font-size: 8px; }

    table.info th { width: 16%; padding: 3px 5px; font-size: 6.5px; }
    table.info td { width: 34%; padding: 3px 5px; font-size: 8px; }

    table.recepcion { width: 100%; border-collapse: collapse; }
    table.recepcion td.esquema {
        width: 42%;
        border: 0.75px solid #d6dde5;
        padding: 0;
        background-color: #ffffff;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: contain;
    }
    table.recepcion td.accesorios { width: 58%; vertical-align: top; padding: 0 0 0 6px; }

    table.acc-table { width: 100%; border-collapse: collapse; }
    table.acc-table th {
        padding: 2px 3px;
        font-size: 6.5px;
        color: #46586b;
        text-transform: uppercase;
        border-bottom: 1px solid #1565c0;
    }
    table.acc-table td { padding: 2px 3px; font-size: 7.5px; color: #4a4a4a; border-bottom: 0.75px solid #e2e6ea; }
    table.acc-table th.chk, table.acc-table td.chk { text-align: center; width: 14px; }
    table.acc-table td.chk { color: #1565c0; font-weight: bold; }

    .obs-box { border: 0.75px solid #d6dde5; margin-top: 2px; padding: 5px; min-height: 30px; color: #4a4a4a; font-size: 8px; }
    .obs-box em { color: #46586b; }

    table.firmas { margin-top: 18px; }
    table.firmas td { padding-top: 34px; }
    table.firmas strong { font-size: 8px; }
    table.firmas span { font-size: 7px; }

    .footer-note { bottom: -28px; font-size: 6.5px; }
</style>
</head>
<body>

    <table class="watermark">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/LOGOFINAL.jpg') }}"></td></tr>
    </table>

    <!-- ENCABEZADO -->
    <table class="header">
        <tr>
            <td class="logo-cell"><img src="{{ public_path('images/LOGOFINAL.jpg') }}"></td>
            <td class="brand-cell">
                <p class="brand-title">ARTURO MOTORS</p>
                <p class="brand-subtitle">Tecnología Automotriz</p>
            </td>
            <td class="ruc-cell">
                <div class="ruc-label">R.U.C.</div>
                <div class="ruc-value">{{ $ruc ?? '20610295321' }}</div>
                <div class="rd-value">R.D. N° {{ $rd_numero ?? '0224-2025-MTC/17.03' }}</div>
            </td>
        </tr>
    </table>
    <hr class="header-divider">

    <!-- TÍTULO -->
    <table class="doc-title-box">
        <tr>
            <td style="width: 70%;">
                <p class="doc-title-main">Hoja de Recepción del Vehículo</p>
                <p class="doc-title-sub">Registro de ingreso, estado y accesorios</p>
            </td>
            <td class="doc-title-tag" style="width: 30%;">{!! $tipo_inspeccion ?? 'PRE INSPECCIÓN' !!}</td>
        </tr>
    </table>

    <table class="fechas">
        <tr>
            <td style="width:50%;"><strong>Fecha de ingreso al taller:</strong> {{ $fecha_ingreso }}</td>
            <td style="width:50%;"><strong>Fecha de salida del taller:</strong> {{ $fecha_salida }}</td>
        </tr>
    </table>

    <!-- DUEÑO -->
    <div class="section-title">Datos del Dueño</div>
    <table class="info">
        <tr><th>Nombre</th><td colspan="3">{{ $nombre_dueno }}</td></tr>
        <tr><th>DNI</th><td>{{ $dni }}</td><th>Teléfono</th><td>{{ $telefono }}</td></tr>
    </table>

    <!-- VEHÍCULO -->
    <div class="section-title">Datos y Características del Vehículo</div>
    <table class="info">
        <tr><th>Placa actual</th><td>{{ $placa_actual }}</td><th>Marca</th><td>{{ $marca }}</td></tr>
        <tr><th>Placa anterior</th><td>{{ $placa_anterior ?? 'NE' }}</td><th>Modelo</th><td>{{ $modelo }}</td></tr>
        <tr><th>N° Motor</th><td>{{ $motor_num }}</td><th>Color</th><td>{{ $color }}</td></tr>
        <tr><th>Año</th><td>{{ $anio }}</td><th>Combustible</th><td>{{ $combustible ?? 'BI-COMBUSTIBLE GNV' }}</td></tr>
        <tr><th>Kilometraje</th><td colspan="3">{{ $kilometraje ?? 'NE' }}</td></tr>
    </table>

    <!-- RECEPCIÓN -->
    <div class="section-title">Recepción del Vehículo</div>
    @php
        $fichaAbs = null;
        if (!empty($orden->ficha_dano)) {
            $fichaRel = ltrim($orden->ficha_dano, '/');
            if (str_starts_with($fichaRel, 'storage/')) {
                $fichaRel = substr($fichaRel, strlen('storage/'));
            }
            $candidato = \Illuminate\Support\Facades\Storage::disk('public')->path($fichaRel);
            if (file_exists($candidato)) {
                $fichaAbs = $candidato;
            }
        }
        $usarDiagrama = in_array($orden->estado, ['aprobado_conversion', 'en_conversion', 'conversion_completada', 'entregado']);

        $filasAcc  = max(count($accesorios_izq ?? []), count($accesorios_der ?? []), 1);
        $altoFila  = 14;
        $altoCabecera = 15;
        $altoCaja  = $altoCabecera + ($filasAcc * $altoFila);

        $imgPath = $fichaAbs ?? ($usarDiagrama ? public_path('images/Diagrama-vechiculos.png') : null);
        $imgFinal = null;
        if ($imgPath && file_exists($imgPath)) {
            $imgFinal = $imgPath;
            if (function_exists('imagecropauto')) {
                try {
                    $dirTmp = storage_path('app/tmp');
                    if (!is_dir($dirTmp)) { @mkdir($dirTmp, 0775, true); }
                    $destino = $dirTmp . '/esquema_' . md5($imgPath . filemtime($imgPath)) . '.png';
                    if (!file_exists($destino)) {
                        $src = @imagecreatefromstring(file_get_contents($imgPath));
                        if ($src) {
                            $w = imagesx($src); $h = imagesy($src);
                            $lienzo = imagecreatetruecolor($w, $h);
                            $blanco = imagecolorallocate($lienzo, 255, 255, 255);
                            imagefill($lienzo, 0, 0, $blanco);
                            imagecopy($lienzo, $src, 0, 0, 0, 0, $w, $h);
                            $recorte = imagecropauto($lienzo, IMG_CROP_THRESHOLD, 0.15, $blanco);
                            $base = $recorte !== false ? $recorte : $lienzo;
                            $cw = imagesx($base); $ch = imagesy($base);
                            $m = 6;
                            $final = imagecreatetruecolor($cw + $m * 2, $ch + $m * 2);
                            imagefill($final, 0, 0, imagecolorallocate($final, 255, 255, 255));
                            imagecopy($final, $base, $m, $m, 0, 0, $cw, $ch);
                            imagepng($final, $destino);
                        }
                    }
                    if (file_exists($destino)) { $imgFinal = $destino; }
                } catch (\Throwable $e) {
                    $imgFinal = $imgPath;
                }
            }
        }
    @endphp
    <table class="recepcion">
        <tr>
            <td class="esquema" style="{{ $imgFinal ? 'background-image: url(' . $imgFinal . ');' : '' }}">
                @if(!$imgFinal)
                    <div style="text-align: center; padding: 8px; color: #8a8a8a; font-style: italic; font-size: 9px;">
                        No disponible
                    </div>
                @else
                    &nbsp;
                @endif
            </td>
            <td class="accesorios">
                <table class="acc-table" style="height: {{ $altoCaja }}px;">
                    <tr style="height: {{ $altoCabecera }}px;">
                        <th style="width:44%; text-align:left;">Accesorios</th><th class="chk">SI</th><th class="chk">NO</th>
                        <th style="width:44%; text-align:left;">Accesorios</th><th class="chk">SI</th><th class="chk">NO</th>
                    </tr>
                    @php $izq = $accesorios_izq ?? []; $der = $accesorios_der ?? []; $maxRows = max(count($izq), count($der)); @endphp
                    @for ($i = 0; $i < $maxRows; $i++)
                    <tr style="height: {{ $altoFila }}px;">
                        @php $itemIzq = $izq[$i] ?? null; @endphp
                        <td>{{ $itemIzq['nombre'] ?? '' }}</td>
                        <td class="chk">{!! ($itemIzq && ($itemIzq['si'] ?? false)) ? '&#10003;' : '' !!}</td>
                        <td class="chk">{!! ($itemIzq && !($itemIzq['si'] ?? true)) ? '&#10003;' : '' !!}</td>
                        @php $itemDer = $der[$i] ?? null; @endphp
                        <td>{{ $itemDer['nombre'] ?? '' }}</td>
                        <td class="chk">{!! ($itemDer && ($itemDer['si'] ?? false)) ? '&#10003;' : '' !!}</td>
                        <td class="chk">{!! ($itemDer && !($itemDer['si'] ?? true)) ? '&#10003;' : '' !!}</td>
                    </tr>
                    @endfor
                </table>
            </td>
        </tr>
    </table>

    <!-- OBSERVACIONES -->
    <div class="section-title">Observaciones</div>
    <div class="obs-box">
        {!! nl2br(e($observaciones ?? '')) !!}
        <br><br><em>Con la presente yo y/o en representación autorizo el trabajo a realizarse en mi vehículo.</em>
    </div>

    <!-- FIRMAS -->
    <table class="firmas">
        <tr>
            <td>
                <div class="firma-linea"></div>
                <strong>Firma del Cliente</strong>
                <span>Conformidad de Recepción</span>
            </td>
            <td>
                <div class="firma-linea"></div>
                <strong>Firma Representante del Taller</strong>
                <span>ARTURO MOTORS</span>
            </td>
        </tr>
    </table>

    <p class="footer-note">DOCUMENTO GENERADO POR EL SISTEMA DE GESTIÓN DE ARTURO MOTORS</p>

</body>
</html>