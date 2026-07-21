<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Contrato de Trabajo - {{ $contrato->user->name }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            text-align: justify;
        }

        .header {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .clause {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            display: block;
        }

        .sub-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            display: block;
        }

        .content {
            margin-bottom: 8px;
        }

        .signatures {
            margin-top: 35px;
            width: 100%;
            border-collapse: collapse;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto 5px auto;
        }

        .footer-data {
            font-size: 9px;
            margin-top: 5px;
        }

        .bold {
            font-weight: bold;
        }

        ul {
            margin-top: 5px;
            padding-left: 20px;
        }

        li {
            margin-bottom: 5px;
        }

        .signatures-wrapper {
            margin-top: 35px;
            /* Aumentamos este valor para dar más espacio */
            page-break-inside: avoid;
        }

        .signature-container {
            position: relative;
            width: 100%;
            height: 120px;
            /* Aumentamos la altura para acomodar el sello circular */
            text-align: center;
        }

        .firma-img {
            position: absolute;
            /* Ajustamos para que el sello circular quede apenas rozando la línea */
            top: -85px;
            left: 50%;
            transform: translateX(-50%);
            width: 180px;
            /* Bajamos un poco el ancho para que no sature el espacio */
            z-index: 1;
        }

        .line {
            border-top: 1px solid #000;
            width: 80%;
            /* El margen superior de la línea define dónde termina la firma */
            margin: 80px auto 5px auto;
            position: relative;
            z-index: 2;
        }
    </style>
</head>

<body>
    <p class="header">
        <img src="{{ public_path('/images/arturo3.png') }}" width="300" height="100" />
    </p>

    <div class="header">
        CONTRATO DE TRABAJO SUJETO A MODALIDAD POR NECESIDADES DEL MERCADO
    </div>

    <div class="content">
        Conste por el presente documento que celebramos de una parte la empresa <span class="bold">MOTOR GAS COMPANY S.
            A</span>, con RUC
        N° 20472634501 con domicilio fiscal en Jr. San Pedro De Carabayllo N° 180 Urbanización Santa Isabel Distrito
        de Carabayllo, Provincia y Departamento de Lima, representado por Don <span class="bold">Lopez Henriquez
            Spasoje Bratzo</span> identificado con DNI 09551431, a quien en adelante se denominará EL
        CONTRATANTE <span class="bold">{{ $contrato->user->name }}</span>
        y por el otro parte identificado con DNI <span class="bold">{{ $contrato->user->dni }}</span> , domiciliada en
        {{ $contrato->user->direccion }},
        quien en adelante se le denominará EL CONTRATADO bajo los términos siguientes:
    </div>

    <div class="content">
        <span class="bold">PRIMERO:</span> EL CONTRATANTE de conformidad con el Articulo 57 de la Ley de Productividad
        y Competitividad
        laboral se celebra el contrato por Naturaleza Temporal con el CONTRATADO por un periodo comprendido
        entre los días <span
            class="bold">{{ $contrato->fecha_inicio_contrato->translatedFormat('d \d\e F \d\e\l Y') }}</span> al
        <span class="bold">{{ $contrato->fecha_vencimiento->translatedFormat('d \d\e F \d\e\l Y') }}</span>.
    </div>

    <div class="content">
        <span class="bold">SEGUNDO:</span> EL CONTRATADO cumplirá las funciones de <span
            class="bold">{{ $contrato->cargo }}</span> en el proceso de Certificaciones
        Vehiculares de Conversiones a Gas Natural Vehicular – GNV, encargadas por el ministerio de Transportes y
        Comunicaciones con puntualidad, responsabilidad y cumplimiento con el horario de trabajo y todas las
        metas establecidas por la empresa y otras funciones que le designe el contratante.
    </div>

    <div class="content">
        <span class="bold">TERCERO:</span> EL CONTRATANTE se compromete a abonar por sus <span class="bold">HONORARIOS DE 
        INSPECTOR</span> al CONTRATADO la suma de <span class="bold">S/
        {{ number_format($contrato->sueldo_bruto, 2) }} ({{ $sueldo_letras }})</span>, en forma mensual por la integridad de
        los trabajos realizados previa presentación del informe de labores.
    </div>

    <div class="content">
        <span class="bold">CUARTO: EL CONTRATADO</span> asume las siguientes obligaciones que están vinculadas a la naturaleza propia
        del servicio que presentará.
        <ul>
            <li>No realizar actividad alguna que pueda perjudicar a <span class="bold">MOTOR GAS COMPANY S.A.</span></li>
            <li> Por la naturaleza propia de las labores que desempeña el contratado, deberá tener disposición para
            efectuar viajes por todas las distintas ciudades del País.
            </li>
        </ul>
    </div>

    <div class="content">
        <span class="bold">QUINTO: EL CONTRATADO</span> en caso de una renuncia voluntaria deberá avisar a la Entidad Certificadora con
        plazo mínimo de 15 días y a su vez deberá guardar confidencialidad, no brindar información de la Entidad
        Certificadora a otras personas que no pertenecen a la empresa, en caso de no cumplir la cláusula habrá
        una penalidad por parte de la Entidad Certificadora.
    </div>

    <div class="content" style="margin-top: 20px;">
        En caso de que el taller asignado dejará de laboral con nosotros o solicita el cambio de su persona, por
        falta de taller; se terminará el contrato. Ambas partes se ratifican en todos los extremos y firman en
        aceptación del presente contrato a los
        <span class="bold">{{ $contrato->fecha_inicio_contrato->format('d') }}</span> días de
        <span class="bold">{{ $contrato->fecha_inicio_contrato->translatedFormat('F') }}</span>
        del año <span class="bold">{{ $contrato->fecha_inicio_contrato->format('Y') }}</span>.
    </div>

    <!-- Firmas -->
    <div class="signatures-wrapper">
        <table class="signatures" style="width: 100%;">
            <tr>
                <td>
                    <div class="signature-container">
                        <img src="{{ public_path('images/firmaing.jpeg') }}" class="firma-img">

                        <div class="line"></div>
                        <span class="bold">Lopez Henriquez Spasoje Bratzo</span><br>
                        <span style="font-size: 10px;">DNI: 09551431</span><br>
                        <span style="font-size: 10px;">Gerente General</span>
                    </div>
                </td>
                <td>
                    <div class="signature-container">
                        <div class="line"></div>
                        <span class="bold">{{ $contrato->user->name }}</span><br>
                        <span style="font-size: 10px;">DNI: {{ $contrato->user->dni }}</span><br>
                        <span style="font-size: 10px;">EL TRABAJADOR</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
