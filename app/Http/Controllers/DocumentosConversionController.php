<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ServiceOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentosConversionController extends Controller
{
    /**
     * Carta de Garantía con header de ARTURO MOTORS
     */
    public function cartaGarantia(int $ordenId)
    {
        $orden = ServiceOrder::with(['cliente', 'vehiculo', 'service'])
            ->findOrFail($ordenId);

        $vehiculo = $orden->vehiculo;
        $cliente = $orden->cliente;

        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                   'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $now = Carbon::now();
        $ciudad_fecha = 'Lima, ' . $now->day . ' de ' . $meses[$now->month - 1] . ' del ' . $now->year;

        $pdf = Pdf::loadView('pdfs.carta-garantia', [
            'ciudad_fecha'   => $ciudad_fecha,
            'anios_garantia' => '02',
            'placa'          => $vehiculo->placa ?? '',
            'marca_equipo'   => 'IGT MOTORS',
            'generacion'     => '5TA GENERACIÓN',
            'meses_mant'     => '7',
            'km_mant'        => '15,000',
        ]);

        return $pdf->stream('carta-garantia-orden-' . $orden->id . '.pdf');
    }

    /**
     * Hoja de Recepción (checklist de ingreso)
     */
    public function hojaRecepcion(int $ordenId)
    {
        $orden = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'cita'])
            ->findOrFail($ordenId);

        $vehiculo = $orden->vehiculo;
        $cliente = $orden->cliente;

        $checklist = $orden->checklist_evaluacion ?? [];

        // Mapear checklist del JSON a arrays de accesorios para la vista
        // Solo usar claves que existen en ChecklistEvaluacion::grupos()
        $accesorios_izq = [
            ['nombre' => 'Tarjeta Propiedad', 'si' => $checklist['tarjeta_propiedad'] ?? false],
            ['nombre' => 'SOAT',              'si' => $checklist['soat'] ?? false],
            ['nombre' => 'Llave Contacto',    'si' => $checklist['llave_contacto'] ?? false],
            ['nombre' => 'Espejos',           'si' => $checklist['espejos'] ?? false],
            ['nombre' => 'Antena',            'si' => $checklist['antena'] ?? false],
            ['nombre' => 'Plumillas',         'si' => $checklist['plumillas'] ?? false],
            ['nombre' => 'Vasos',             'si' => $checklist['vasos'] ?? false],
            ['nombre' => 'Emblemas',          'si' => $checklist['emblemas'] ?? false],
            ['nombre' => 'Tapa Combustible',  'si' => $checklist['tapa_combustible'] ?? false],
            ['nombre' => 'Batería',           'si' => $checklist['bateria'] ?? false],
            ['nombre' => 'Claxon',            'si' => $checklist['claxon'] ?? false],
            ['nombre' => 'Tapa Aceite',       'si' => $checklist['tapa_aceite'] ?? false],
            ['nombre' => 'Tapa Radiador',     'si' => $checklist['tapa_radiador'] ?? false],
            ['nombre' => 'Barita Capot',      'si' => $checklist['barita_capot'] ?? false],
            ['nombre' => 'Espejo Anterior',   'si' => $checklist['espejo_anterior'] ?? false],
            ['nombre' => 'Tapasoles',         'si' => $checklist['tapasoles'] ?? false],
        ];

        $accesorios_der = [
            ['nombre' => 'Radio',             'si' => $checklist['radio'] ?? false],
            ['nombre' => 'Reproductor CD',    'si' => $checklist['reproductor_cd'] ?? false],
            ['nombre' => 'Parlantes',         'si' => $checklist['parlantes'] ?? false],
            ['nombre' => 'Cenicero',          'si' => $checklist['cenicero'] ?? false],
            ['nombre' => 'Encendedor',        'si' => $checklist['encendedor'] ?? false],
            ['nombre' => 'Pisos',             'si' => $checklist['pisos'] ?? false],
            ['nombre' => 'Fundas Forros',     'si' => $checklist['fundas_forros'] ?? false],
            ['nombre' => 'Cinturones',        'si' => $checklist['cinturones'] ?? false],
            ['nombre' => 'Llanta Repuesto',   'si' => $checklist['llanta_repuesto'] ?? false],
            ['nombre' => 'Gata Palanca',      'si' => $checklist['gata_palanca'] ?? false],
            ['nombre' => 'Llave Ruedas',      'si' => $checklist['llave_ruedas'] ?? false],
            ['nombre' => 'Triángulo',         'si' => $checklist['triangulo'] ?? false],
            ['nombre' => 'Extintor',          'si' => $checklist['extintor'] ?? false],
            ['nombre' => 'Linterna',          'si' => $checklist['linterna'] ?? false],
            ['nombre' => 'Herramientas',      'si' => $checklist['herramientas'] ?? false],
        ];

        $pdf = Pdf::loadView('pdfs.hoja-recepcion', [
            'orden'           => $orden,
            'fecha_ingreso'   => $orden->created_at ? $orden->created_at->format('d/m/Y') : '---',
            'fecha_salida'    => $orden->fecha_fin_conversion ? $orden->fecha_fin_conversion->format('d/m/Y') : 'Pendiente',
            'nombre_dueno'    => $cliente->nombre_completo ?? ($cliente->nombre . ' ' . $cliente->apellido),
            'dni'             => $cliente->documento ?? '',
            'telefono'        => $cliente->telefono ?? '',
            'placa_actual'    => $vehiculo->placa ?? '',
            'placa_anterior'  => 'NE',
            'marca'           => $vehiculo->marca ?? '',
            'modelo'          => $vehiculo->modelo ?? '',
            'motor_num'       => $vehiculo->serie ?? '',
            'color'           => $vehiculo->color ?? '',
            'anio'            => $vehiculo->anio ?? '',
            'combustible'     => $vehiculo->combustible ?? 'BI-COMBUSTIBLE GNV',
            'kilometraje'     => 'NE',
            'accesorios_izq'  => $accesorios_izq,
            'accesorios_der'  => $accesorios_der,
            'observaciones'   => $orden->evaluacion_observaciones ?? '',
        ]);

        return $pdf->stream('hoja-recepcion-orden-' . $orden->id . '.pdf');
    }

    /**
     * Constancia de Entrega
     */
    public function constanciaEntrega(int $ordenId)
    {
        $orden = ServiceOrder::with(['cliente', 'vehiculo', 'service'])
            ->findOrFail($ordenId);

        $vehiculo = $orden->vehiculo;
        $cliente = $orden->cliente;

        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                   'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $now = Carbon::now();
        $ciudad_fecha = 'Lima, ' . $now->day . ' de ' . $meses[$now->month - 1] . ' del ' . $now->year . '.';

        $pdf = Pdf::loadView('pdfs.constancia-entrega', [
            'ciudad_fecha'    => $ciudad_fecha,
            'nombre_cliente'  => $cliente->nombre_completo ?? ($cliente->nombre . ' ' . $cliente->apellido),
            'dni_cliente'     => $cliente->documento ?? '',
            'marca_vehiculo'  => $vehiculo->marca ?? '',
            'modelo_vehiculo' => $vehiculo->modelo ?? '',
            'placa'           => $vehiculo->placa ?? '',
        ]);

        return $pdf->stream('constancia-entrega-orden-' . $orden->id . '.pdf');
    }
}
