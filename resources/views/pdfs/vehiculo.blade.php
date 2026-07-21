<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CARTA DE GARANTIA</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            background-image: url("{{ public_path('images/hoja_membretada.png') }}");
            background-repeat: no-repeat;
            background-size: 210mm 295mm;
            /* A4 portrait */
        }

        .contenido {
            padding: 50mm 20mm 25mm 20mm;
            /* top, right, bottom, left */
        }

        .fecha {
            text-align: right;
            margin-bottom: 8mm;
            font-size: 11pt;
        }

         h3 {
            text-align: center;
            text-decoration: underline;
            margin: 0 0 8mm 0;
            font-size: 13pt;
        }

        h4 {
            margin: 0 0 4mm 0;
            font-size: 12pt;
        }

        p {
            text-align: justify;
            margin: 0 0 4mm 0;
        }

        .firma {
            margin-top: 30mm;
            text-align: center;
        }

        .firma .linea {
            margin-top: 18mm; /* también podemos agrandar un poco la separación de la línea */
            border-top: 1px solid #000;
            width: 40%;
            margin-left: auto;
            margin-right: auto;
        }

        /* ✅ Sobrescribe justificado SOLO para el texto de la firma */
        .firma p {
            text-align: center;
            margin-top: 4mm; /* espacio entre la línea y el texto */
        }
    </style>
</head>

<body>
    <main class="contenido">
        <div class="fecha">
            <p>Lima, {{ $fecha }}</p>
        </div>

        <h3>CARTA DE GARANTIA</h3>

        <h4>SEÑOR:</h4>

        <p>
            POR MEDIO DE LA PRESENTE ME DIRIJO A USTED PARA HACERLE LLEGAR LA CARTA DE
            GARANTÍA DE 02 AÑOS DE LA INSTALACIÓN DEL EQUIPO DE GNV A SU AUTO, PLACA <strong>{{ $vehiculo->placa }}</strong>,
            EQUIPO DE LA MARCA <strong>IGT MOTORS</strong>, DE <strong>5TA GENERACIÓN,</strong> EN EL CUAL EL AUTO HA SALIDO EN PERFECTAS
            CONDICIONES.
        </p>

        <p>
            SE LE MENCIONA QUE DOS REVISIONES ANUALES GNV PUEDE REALIZARLA EN EL TALLER <strong>LIFE GAS
                COMPANY</strong> SIN NINGÚN COSTO.
        </p>

        <p>
            LA GARANTÍA SE HARÁ EFECTIVA SI EL CLIENTE CUMPLE CON SU MANTENIMIENTO PREVENTIVO/CORRECTIVO, <strong>A LOS 7 MESES,
            TENIENDO COSTO DE S/.0.00. DOS MANTENIMIENTOS DE GAS PASANDO LOS 15,000 KM DE RECORRIDO SIN COSTO ADICIONAL.</strong>
            SI NO SE REALIZARA EL MANTENIMIENTO INDICADO EL EQUIPO SE DETERIORA DE UNA MANERA RÁPIDA Y LA EMPRESA NO SE
            HARÁ RESPONSABLE, POR ELLO SE LE RECOMIENDA CUMPLIR CON LO INDICADO PARA UN MEJOR CUIDADO DEL MOTOR DE LA
            UNIDAD.
        </p>

        <p>
            SIN OTRO PARTICULAR, ME DESPIDO.
        </p>

        <div class="firma">
            <div class="linea"></div>
            <p><strong>Firma y Sello</strong></p>
        </div>
    </main>
</body>

</html>
