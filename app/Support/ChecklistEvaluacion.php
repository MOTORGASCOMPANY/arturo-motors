<?php

namespace App\Support;

class ChecklistEvaluacion
{
    public static function grupos(): array
    {
        return [
            'Documentos' => [
                'tarjeta_propiedad' => 'Tarjeta de propiedad',
                'soat' => 'SOAT',
            ],
            'Exterior' => [
                'espejos' => 'Espejos',
                'antena' => 'Antena',
                'plumillas' => 'Plumillas',
                'emblemas' => 'Emblemas',
                'tapa_combustible' => 'Tapa de combustible',
                'tapa_aceite' => 'Tapa de aceite',
                'tapa_radiador' => 'Tapa de radiador',
                'barita_capot' => 'Barita de capot',
                'espejo_anterior' => 'Espejo interior',
            ],
            'Interior' => [
                'llave_contacto' => 'Llave de contacto',
                'vasos' => 'Vasos / portavasos',
                'tapasoles' => 'Tapasoles',
                'radio' => 'Radio',
                'reproductor_cd' => 'Reproductor CD',
                'parlantes' => 'Parlantes',
                'cenicero' => 'Cenicero',
                'encendedor' => 'Encendedor',
                'pisos' => 'Pisos',
                'fundas_forros' => 'Fundas / forros',
                'cinturones' => 'Cinturones de seguridad',
                'claxon' => 'Claxon',
                'bateria' => 'Batería',
            ],
            'Seguridad y herramientas' => [
                'llanta_repuesto' => 'Llanta de repuesto',
                'gata_palanca' => 'Gata y palanca',
                'llave_ruedas' => 'Llave de ruedas',
                'triangulo' => 'Triángulo de seguridad',
                'extintor' => 'Extintor',
                'linterna' => 'Linterna',
                'herramientas' => 'Kit de herramientas',
            ],
        ];
    }
}