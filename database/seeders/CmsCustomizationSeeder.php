<?php

namespace Database\Seeders;

use App\Models\ContactInfo;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\ProcessStep;
use App\Models\SiteService;
use App\Models\SocialLink;
use App\Models\WhyCard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CmsCustomizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create storage directories
        Storage::disk('public')->makeDirectory('cms/logo');
        Storage::disk('public')->makeDirectory('cms/favicon');
        Storage::disk('public')->makeDirectory('cms/hero');
        Storage::disk('public')->makeDirectory('cms/about');

        // Get or create the home page
        $page = Page::firstOrCreate(['slug' => 'home'], [
            'title' => 'Arturo Motors - Página Principal',
            'meta_title' => 'Arturo Motors | Conversiones GNV/GLP y Mantenimiento Automotriz',
            'meta_description' => 'Especialistas en conversiones GNV y GLP, certificaciones oficiales y mantenimiento automotriz en Perú.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Set customization meta
        $page->update([
            'meta' => [
                'colors' => [
                    'primary' => '#f59e0b',      // Amber - principal
                    'primary_hover' => '#d97706', // Amber 700
                    'secondary' => '#ef4444',    // Red - secundario
                    'secondary_hover' => '#dc2626', // Red 700
                    'accent' => '#3b82f6',       // Blue - acento
                    'background' => '#0a0f1e',   // Dark slate - fondo
                    'surface' => '#1e293b',      // Slate 800 - superficie
                    'text_primary' => '#ffffff', // White - texto principal
                    'text_secondary' => '#cbd5e1', // Slate 300 - texto secundario
                    'text_muted' => '#94a3b8',   // Slate 400 - texto muted
                    'border' => '#334155',       // Slate 700 - bordes
                    'success' => '#22c55e',      // Green - éxito
                    'warning' => '#f59e0b',      // Amber - advertencia
                    'error' => '#ef4444',        // Red - error
                ],
                'seo' => [
                    'title' => 'Arturo Motors | Conversiones GNV/GLP y Mantenimiento Automotriz',
                    'description' => 'Especialistas en conversiones GNV y GLP, certificaciones oficiales y mantenimiento automotriz en Perú. Ahorra hasta 60% en combustible.',
                    'og_title' => 'Arturo Motors | Conversiones GNV/GLP',
                    'og_description' => 'Certificaciones, conversiones y mantenimiento automotriz.',
                    'og_image' => null, // Will use logo
                ],
                'custom_css' => null,
                'custom_js' => null,
            ],
        ]);

        $this->command->info('Page customization meta created.');

        // Create default page sections if they don't exist
        $defaultSections = [
            ['key' => 'hero', 'title' => 'Hero / Banner Principal', 'subtitle' => 'GNV & GLP de 5ta Gen', 'description' => 'Conversiones de alta precisión, certificaciones oficiales y mantenimiento especializado. Ahorra hasta un 60% en combustible con total seguridad.', 'sort_order' => 1],
            ['key' => 'about', 'title' => 'Nosotros', 'subtitle' => 'Soluciones Automotrices', 'description' => 'En Arturo Motors nos dedicamos a transformar la movilidad de nuestros clientes ofreciendo soluciones de conversión a GNV y GLP con los más altos estándares de calidad y seguridad.', 'sort_order' => 2],
            ['key' => 'services', 'title' => 'Nuestros Servicios', 'subtitle' => 'Especializados', 'description' => 'Tecnología de última generación y personal altamente capacitado para garantizar el máximo rendimiento de tu vehículo.', 'sort_order' => 3],
            ['key' => 'why', 'title' => 'Por Qué Elegirnos', 'subtitle' => 'Arturo Motors?', 'description' => 'Nos enfocamos en brindar un servicio transparente, rápido y respaldado por normativas vigentes para que conduzcas con total tranquilidad y máximo ahorro.', 'sort_order' => 4],
            ['key' => 'process', 'title' => 'Nuestro Proceso de', 'subtitle' => 'Trabajo', 'description' => 'Un flujo transparente y seguro desde la recepción hasta la entrega de tu auto.', 'sort_order' => 5],
            ['key' => 'contact', 'title' => 'Visítanos o', 'subtitle' => 'Ponte en Contacto', 'description' => 'Estamos listos para atender tus consultas, certificar tu auto o agendar tu conversión.', 'sort_order' => 6],
        ];

        foreach ($defaultSections as $sectionData) {
            PageSection::firstOrCreate(
                ['page_id' => $page->id, 'key' => $sectionData['key']],
                array_merge($sectionData, ['page_id' => $page->id, 'is_active' => true])
            );
        }

        $this->command->info('Default page sections created.');

        // Create sample services
        $services = [
            [
                'title' => 'Conversión a GNV',
                'description' => 'Conversión profesional a Gas Natural Vehicular con equipos de 5ta generación',
                'icon' => 'fa-solid fa-gas-pump',
                'features' => ['Equipos italianos Landi Renzo', 'Garantía 1 año / 20,000 km', 'Certificación inmediata', 'Instalación en 4 horas'],
                'cta_text' => 'Cotizar Conversión GNV',
                'cta_link' => 'https://wa.me/51943694464?text=Hola%20quiero%20cotizar%20conversi%C3%B3n%20GNV',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Conversión a GLP',
                'description' => 'Instalación de sistemas GLP de alta eficiencia para todo tipo de vehículos',
                'icon' => 'fa-solid fa-gas-pump',
                'features' => ['Tanques homologados ECE R67', 'Inyección electrónica secuencial', 'Ahorro hasta 50%', 'Mantenimiento incluido 6 meses'],
                'cta_text' => 'Cotizar Conversión GLP',
                'cta_link' => 'https://wa.me/51943694464?text=Hola%20quiero%20cotizar%20conversi%C3%B3n%20GLP',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Certificación Anual/Quinquenal',
                'description' => 'Trámite y certificación oficial de tus cilindros GNV/GLP ante el MTC',
                'icon' => 'fa-solid fa-file-signature',
                'features' => ['Inspección visual y hidrostática', 'Prueba de fugas certificada', 'Emisión de certificado MTC', 'Entrega en 24 horas'],
                'cta_text' => 'Agendar Certificación',
                'cta_link' => 'https://wa.me/51943694464?text=Hola%20necesito%20certificar%20mis%20cilindros',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Mantenimiento Preventivo',
                'description' => 'Servicio integral de mantenimiento para sistemas GNV/GLP y motor',
                'icon' => 'fa-solid fa-sliders',
                'features' => ['Cambio de filtros GNV/GLP', 'Revisión de inyectores', 'Escaneo computarizado', 'Ajuste de mezcla aire/combustible'],
                'cta_text' => 'Programar Mantenimiento',
                'cta_link' => 'https://wa.me/51943694464?text=Hola%20quiero%20agendar%20mantenimiento',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            SiteService::firstOrCreate(
                ['title' => $serviceData['title']],
                $serviceData
            );
        }

        $this->command->info('Sample services created.');

        // Create sample process steps
        $steps = [
            ['title' => 'Diagnóstico Inicial', 'description' => 'Inspección completa del vehículo y evaluación de factibilidad', 'step_number' => '01', 'icon' => 'fa-solid fa-search', 'sort_order' => 1, 'is_active' => true],
            ['title' => 'Instalación Profesional', 'description' => 'Montaje de kit certificado por técnicos especializados', 'step_number' => '02', 'icon' => 'fa-solid fa-tools', 'sort_order' => 2, 'is_active' => true],
            ['title' => 'Pruebas y Calibración', 'description' => 'Verificación de fugas, escaneo y ajuste de parámetros', 'step_number' => '03', 'icon' => 'fa-solid fa-gauge-high', 'sort_order' => 3, 'is_active' => true],
            ['title' => 'Certificación y Entrega', 'description' => 'Trámite MTC, certificado oficial y entrega con garantía', 'step_number' => '04', 'icon' => 'fa-solid fa-certificate', 'sort_order' => 4, 'is_active' => true],
        ];

        foreach ($steps as $stepData) {
            ProcessStep::firstOrCreate(
                ['title' => $stepData['title']],
                $stepData
            );
        }

        $this->command->info('Sample process steps created.');

        // Create sample why cards
        $whyCards = [
            ['title' => 'Garantía Total', 'description' => '1 año de garantía en conversión e instalación con respaldo escrito', 'icon' => 'fa-solid fa-shield-halved', 'sort_order' => 1, 'is_active' => true],
            ['title' => 'Ahorro Real 60%', 'description' => 'Reducción comprobada del gasto en combustible desde el primer kilómetro', 'icon' => 'fa-solid fa-percent', 'sort_order' => 2, 'is_active' => true],
            ['title' => 'Tecnología 5ta Gen', 'description' => 'Equipos de inyección secuencial más eficientes y seguros del mercado', 'icon' => 'fa-solid fa-microchip', 'sort_order' => 3, 'is_active' => true],
            ['title' => 'Certificación MTC', 'description' => 'Trámites oficiales ante el Ministerio de Transportes y Comunicaciones', 'icon' => 'fa-solid fa-landmark', 'sort_order' => 4, 'is_active' => true],
            ['title' => 'Atención 24/7', 'description' => 'Soporte técnico y emergencias disponible todos los días del año', 'icon' => 'fa-solid fa-headset', 'sort_order' => 5, 'is_active' => true],
            ['title' => 'Financiamiento', 'description' => 'Planes de pago flexibles sin intereses para tu conversión', 'icon' => 'fa-solid fa-credit-card', 'sort_order' => 6, 'is_active' => true],
        ];

        foreach ($whyCards as $cardData) {
            WhyCard::firstOrCreate(
                ['title' => $cardData['title']],
                $cardData
            );
        }

        $this->command->info('Sample why cards created.');

        // Create sample contact info
        $contacts = [
            ['type' => 'address', 'label' => 'Dirección', 'value' => 'Av. Perú 5176, Callao 07036', 'icon' => 'fa-solid fa-map-location-dot', 'sort_order' => 1, 'is_active' => true],
            ['type' => 'phone', 'label' => 'Teléfono', 'value' => '+51 1 234 5678', 'icon' => 'fa-solid fa-phone', 'sort_order' => 2, 'is_active' => true],
            ['type' => 'schedule', 'label' => 'Horario', 'value' => 'Lun - Vie: 8:00 - 18:00 / Sáb: 8:00 - 13:00', 'icon' => 'fa-solid fa-clock', 'sort_order' => 3, 'is_active' => true],
            ['type' => 'whatsapp', 'label' => 'WhatsApp', 'value' => '51943694464', 'icon' => 'fa-brands fa-whatsapp', 'sort_order' => 4, 'is_active' => true],
            ['type' => 'email', 'label' => 'Correo', 'value' => 'ventas@arturomotors.com', 'icon' => 'fa-solid fa-envelope', 'sort_order' => 5, 'is_active' => true],
        ];

        foreach ($contacts as $contactData) {
            ContactInfo::firstOrCreate(
                ['type' => $contactData['type'], 'label' => $contactData['label']],
                $contactData
            );
        }

        $this->command->info('Sample contact info created.');

        // Create sample social links
        $socialLinks = [
            ['platform' => 'facebook', 'url' => 'https://facebook.com/arturomotorsperu', 'icon' => 'fa-brands fa-facebook-f', 'sort_order' => 1, 'is_active' => true],
            ['platform' => 'instagram', 'url' => 'https://instagram.com/arturomotorsperu', 'icon' => 'fa-brands fa-instagram', 'sort_order' => 2, 'is_active' => true],
            ['platform' => 'whatsapp', 'url' => 'https://wa.me/51943694464', 'icon' => 'fa-brands fa-whatsapp', 'sort_order' => 3, 'is_active' => true],
            ['platform' => 'youtube', 'url' => 'https://youtube.com/@arturomotorsperu', 'icon' => 'fa-brands fa-youtube', 'sort_order' => 4, 'is_active' => true],
            ['platform' => 'tiktok', 'url' => 'https://tiktok.com/@arturomotorsperu', 'icon' => 'fa-brands fa-tiktok', 'sort_order' => 5, 'is_active' => true],
        ];

        foreach ($socialLinks as $linkData) {
            SocialLink::firstOrCreate(
                ['platform' => $linkData['platform']],
                $linkData
            );
        }

        $this->command->info('Sample social links created.');

        $this->command->info('CMS Customization seeding completed successfully!');
    }
}
