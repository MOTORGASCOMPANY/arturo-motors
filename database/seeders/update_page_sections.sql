-- Poblar page_sections con el texto actual del landing
-- Ejecutar en phpMyAdmin, base de datos: laravel (o arturo_motors)

UPDATE page_sections SET
    title = 'Potencia tu Vehículo con',
    subtitle = 'GNV & GLP de 5ta Gen',
    description = 'Conversiones de alta precisión, certificaciones oficiales y mantenimiento especializado. Ahorra hasta un 60% en combustible con total seguridad.'
WHERE `key` = 'hero';

UPDATE page_sections SET
    title = 'Líderes en Conversiones y',
    subtitle = 'Soluciones Automotrices',
    description = 'En Arturo Motors nos dedicamos a transformar la movilidad de nuestros clientes ofreciendo soluciones de conversión a GNV y GLP con los más altos estándares de calidad y seguridad.'
WHERE `key` = 'about';

UPDATE page_sections SET
    title = 'Nuestros Servicios',
    subtitle = 'Especializados',
    description = 'Tecnología de última generación y personal altamente capacitado para garantizar el máximo rendimiento de tu vehículo.'
WHERE `key` = 'services';

UPDATE page_sections SET
    title = '¿Por qué confiar tu vehículo en',
    subtitle = 'Arturo Motors?',
    description = 'Nos enfocamos en brindar un servicio transparente, rápido y respaldado por normativas vigentes para que conduzcas con total tranquilidad y máximo ahorro.'
WHERE `key` = 'why';

UPDATE page_sections SET
    title = 'Nuestro Proceso de',
    subtitle = 'Trabajo',
    description = 'Un flujo transparente y seguro desde la recepción hasta la entrega de tu auto.'
WHERE `key` = 'process';

UPDATE page_sections SET
    title = 'Visítanos o',
    subtitle = 'Ponte en Contacto',
    description = 'Estamos listos para atender tus consultas, certificar tu auto o agendar tu conversión.'
WHERE `key` = 'contact';
