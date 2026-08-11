<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteService;
use App\Models\ProcessStep;
use App\Models\WhyCard;
use App\Models\ContactInfo;
use App\Models\SocialLink;

class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedServices();
        $this->seedSteps();
        $this->seedWhyCards();
        $this->seedContactInfo();
        $this->seedSocialLinks();
    }

    private function seedServices(): void
    {
        $services = [
            [
                'title' => 'Conversión a GNV / GLP',
                'description' => 'Instalación de sistemas de 5ta generación con ECU calibrada por computadora. Garantizamos un ahorro de hasta el 60% en combustible.',
                'icon' => 'fa-solid fa-gas-pump',
                'features' => ['Equipos de marcas italianas', 'Garantía escrita de 1 año', 'Incluye certificación inicial'],
                'cta_text' => 'Cotizar Conversión',
                'cta_link' => 'https://wa.me/51943694464?text=Hola,%20deseo%20cotizar%20una%20conversión%20a%20GNV/GLP',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Certificaciones Oficiales',
                'description' => 'Emisión e inspección técnica para certificación anual de GNV/GLP y prueba hidráulica quinquenal de cilindros de manera rápida.',
                'icon' => 'fa-solid fa-file-signature',
                'features' => ['Revisión de fuga y hermeticidad', 'Aprobación en sistema oficial', 'Trámite en 30 minutos'],
                'cta_text' => 'Agendar Inspección',
                'cta_link' => 'https://wa.me/51943694464?text=Hola,%20necesito%20información%20sobre%20certificación%20anual/quinquenal',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Mantenimiento de Sistema a Gas',
                'description' => 'Mantenimiento preventivo y correctivo para inyectores, cambio de filtros de gas, reductor y calibración fina de mapas de inyección.',
                'icon' => 'fa-solid fa-sliders',
                'features' => ['Limpieza ultrasónica de inyectores', 'Cambio de filtros de fase líquida/gas', 'Ajuste de presión de regulador'],
                'cta_text' => 'Consultar Servicio',
                'cta_link' => 'https://wa.me/51943694464?text=Hola,%20busco%20mantenimiento%20para%20mi%20equipo%20de%20gas',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Mecánica General',
                'description' => 'Servicios integrales para el motor de tu auto: afinamiento electrónico, cambio de aceite, frenos, embragues y suspensión.',
                'icon' => 'fa-solid fa-screwdriver-wrench',
                'features' => ['Afinamiento de motor', 'Sistema de frenos y suspensión', 'Mantenimiento por kilometraje'],
                'cta_text' => 'Solicitar Cotización',
                'cta_link' => 'https://wa.me/51943694464?text=Hola,%20quisiera%20cotizar%20mantenimiento%20mecánico',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Diagnóstico Computarizado',
                'description' => 'Lectura y borrado de códigos de error (Check Engine), análisis de sensores en tiempo real y diagnóstico multimarca.',
                'icon' => 'fa-solid fa-laptop-code',
                'features' => ['Escáner profesional multimarca', 'Reporte detallado de fallas', 'Prueba de sensores y actuadores'],
                'cta_text' => 'Reservar Escaneo',
                'cta_link' => 'https://wa.me/51943694464?text=Hola,%20deseo%20un%20diagnóstico%20por%20escáner',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Lavado de Inyectores',
                'description' => 'Prueba de estanqueidad, abanico y entrega de flujo en banco de pruebas con tina ultrasónica para inyectores de gasolina.',
                'icon' => 'fa-solid fa-filter-circle-xmark',
                'features' => ['Limpieza por Ultrasonido', 'Reemplazo de Microfiltros y O-rings', 'Prueba en banco computarizado'],
                'cta_text' => 'Consultar Precio',
                'cta_link' => 'https://wa.me/51943694464?text=Hola,%20quisiera%20información%20sobre%20lavado%20de%20inyectores',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $data) {
            SiteService::create($data);
        }
    }

    private function seedSteps(): void
    {
        $steps = [
            [
                'step_number' => '01',
                'title' => 'Diagnóstico Inicial',
                'description' => 'Evaluamos el estado del motor y del sistema eléctrico antes de intervenir.',
                'icon' => 'fa-solid fa-magnifying-glass',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'step_number' => '02',
                'title' => 'Instalación / Servicio',
                'description' => 'Montaje de equipos o mantenimiento técnico siguiendo normas de fábrica.',
                'icon' => 'fa-solid fa-wrench',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'step_number' => '03',
                'title' => 'Calibración & Pruebas',
                'description' => 'Ajuste fino de parámetros por computadora y prueba de fuga/seguridad.',
                'icon' => 'fa-solid fa-gear',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'step_number' => '04',
                'title' => 'Certificado y Entrega',
                'description' => 'Te entregamos tu vehículo verificado con su documentación oficial lista.',
                'icon' => 'fa-solid fa-certificate',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($steps as $data) {
            ProcessStep::create($data);
        }
    }

    private function seedWhyCards(): void
    {
        $cards = [
            [
                'title' => 'Escáner & Diagnóstico',
                'description' => 'Calibración por computadora para no perder potencia en el motor.',
                'icon' => 'fa-solid fa-microchip',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Certificación Legal',
                'description' => 'Aprobaciones oficiales al instante para circular sin multas.',
                'icon' => 'fa-solid fa-file-contract',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Máximo Ahorro',
                'description' => 'Optimización de consumo para reducir gastos hasta en un 60%.',
                'icon' => 'fa-solid fa-piggy-bank',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Entrega Puntual',
                'description' => 'Respetamos los tiempos de trabajo pactados desde el inicio.',
                'icon' => 'fa-solid fa-clock-rotate-left',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($cards as $data) {
            WhyCard::create($data);
        }
    }

    private function seedContactInfo(): void
    {
        $contacts = [
            [
                'type' => 'address',
                'label' => 'Dirección del Taller',
                'value' => 'Prolongación Av. Perú 5176, Callao, Peru',
                'icon' => 'fa-solid fa-map-location-dot',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'phone',
                'label' => 'Teléfonos de Atención',
                'value' => '+51 943 694 464 / 943 694 464',
                'icon' => 'fa-solid fa-phone',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'type' => 'whatsapp',
                'label' => 'WhatsApp General',
                'value' => '51943694464',
                'icon' => 'fa-brands fa-whatsapp',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'schedule',
                'label' => 'Horario de Atención',
                'value' => 'Lunes a Sábado: 8:00 AM - 6:00 PM',
                'icon' => 'fa-solid fa-clock',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'type' => 'map_iframe',
                'label' => 'Mapa de Ubicación',
                'value' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d975.5839857865125!2d-77.10123263037826!3d-12.020377599263401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105ce9f965ce71b%3A0x39248294a3841558!2sAv.%20Per%C3%BA%205176%2C%20Callao%2007036!5e0!3m2!1ses-419!2spe!4v1785440284486!5m2!1ses-419!2spe" width="100%" height="100%" style="border:0; min-height: 400px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                'icon' => 'fa-solid fa-map',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($contacts as $data) {
            ContactInfo::create($data);
        }
    }

    private function seedSocialLinks(): void
    {
        $links = [
            [
                'platform' => 'facebook',
                'url' => 'https://facebook.com/arturomotors',
                'icon' => 'fa-brands fa-facebook-f',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'platform' => 'instagram',
                'url' => 'https://instagram.com/arturomotors',
                'icon' => 'fa-brands fa-instagram',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'platform' => 'whatsapp',
                'url' => 'https://wa.me/51943694464',
                'icon' => 'fa-brands fa-whatsapp',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($links as $data) {
            SocialLink::create($data);
        }
    }
}
