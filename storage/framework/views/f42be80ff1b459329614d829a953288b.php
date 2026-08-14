<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arturo Motors | Conversiones GNV/GLP y Mantenimiento Automotriz</title>
    <meta name="description" content="Especialistas en conversiones GNV y GLP, certificaciones y mantenimiento automotriz en Perú.">
    <meta property="og:title" content="Arturo Motors | Conversiones GNV/GLP">
    <meta property="og:description" content="Certificaciones, conversiones y mantenimiento automotriz.">
    <meta property="og:image" content="https://arturomotorsperu.com/images/icon.png">
    <link rel="icon" type="image/png" href="images/icon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg fixed-top custom-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <div class="brand-icon">
                    <img src="images/icon.png" alt="ARTURO MOTORS" style="width: 35px; height: 35px;">
                </div>
                <div class="brand-text-container">
                    <span class="brand-title">ARTURO <span class="text-blue">MOTORS</span></span>
                    <span class="brand-subtitle">TECNOLOGÍA AUTOMOTRIZ</span>
                </div>
            </a>
            <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link custom-link active" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link custom-link" href="#nosotros">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link custom-link" href="#servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link custom-link" href="#contacto">Contacto</a></li>
                </ul>
                <div class="d-flex flex-column flex-lg-row align-items-center gap-2 ms-lg-3 mt-3 mt-lg-0">
                    <a href="<?php echo e($whatsappLink); ?>" target="_blank" class="btn btn-glow rounded-pill px-3 py-2 fw-bold text-nowrap w-100 w-lg-auto">
                        <i class="fa-brands fa-whatsapp me-2"></i>Cotización Rápida
                    </a>
                    <a href="/login" class="btn btn-glass rounded-pill px-3 py-2 fw-bold text-nowrap w-100 w-lg-auto">
                        <i class="fa-solid fa-user-lock me-2"></i>Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero-wrapper">
        <div class="swiper hero-slider">
            <div class="swiper-wrapper">
                <?php if(isset($sections['hero']) && count($sections['hero']->mediaItems) > 0): ?>
                    <?php $__currentLoopData = $sections['hero']->mediaItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="swiper-slide" style="background-image: url('<?php echo e($pm->media->bestUrl()); ?>'); background-size: cover; background-position: center;"></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="swiper-slide slide-1"></div>
                    <div class="swiper-slide slide-2"></div>
                    <div class="swiper-slide slide-3"></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-overlay d-flex align-items-center">
            <div class="container">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-7 text-lg-start text-center" data-aos="fade-right" data-aos-duration="1000">
                        <div class="badge-tech mb-3">
                            <span class="pulse-dot"></span> Taller Autorizado & Certificado
                        </div>
                        <h1 class="hero-heading">
                            <?php echo e($sections['hero']->title ?? 'Potencia tu Vehículo con'); ?> <br>
                            <span class="gradient-text"><?php echo e($sections['hero']->subtitle ?? 'GNV & GLP de 5ta Gen'); ?></span>
                        </h1>
                        <p class="hero-lead">
                            <?php echo e($sections['hero']->description ?? 'Conversiones de alta precisión, certificaciones oficiales y mantenimiento especializado. Ahorra hasta un 60% en combustible con total seguridad.'); ?>

                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-lg-start justify-content-center mt-4">
                            <a href="<?php echo e($whatsappLink); ?>" target="_blank" class="btn btn-main btn-lg rounded-pill">
                                <i class="fa-solid fa-calendar-check me-2"></i>Agendar Cita
                            </a>
                            <a href="#servicios" class="btn btn-glass btn-lg rounded-pill">
                                <i class="fa-solid fa-sliders me-2"></i>Nuestros Servicios
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                        <div class="hero-glass-card shadow-2xl">
                            <div class="card-header-badge">
                                <i class="fa-solid fa-certificate"></i> Garantía & Calidad
                            </div>
                            <div class="metrics-grid">
                                <div class="metric-item">
                                    <h3 class="metric-number">+100%</h3>
                                    <p class="metric-label">Garantía en Conversión</p>
                                </div>
                                <div class="metric-item">
                                    <h3 class="metric-number">60%</h3>
                                    <p class="metric-label">Ahorro Promedio</p>
                                </div>
                            </div>
                            <hr class="divider">
                            <div class="features-list">
                                <div class="feature-line">
                                    <i class="fa-solid fa-circle-check text-blue"></i>
                                    <span>Certificación Anual y Quinquenal al instante.</span>
                                </div>
                                <div class="feature-line">
                                    <i class="fa-solid fa-circle-check text-blue"></i>
                                    <span>Equipos e Inyectores de última generación.</span>
                                </div>
                                <div class="feature-line">
                                    <i class="fa-solid fa-circle-check text-blue"></i>
                                    <span>Diagnóstico Computarizado por Escáner.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Nosotros -->
    <section id="nosotros" class="about-section py-6">
        <div class="container py-lg-5">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <div class="about-image-wrapper">
                        <div class="main-img-box">
                            <?php if(isset($sections['about']) && count($sections['about']->mediaItems) > 0): ?>
                                <img src="<?php echo e($sections['about']->mediaItems->first()->media->bestUrl()); ?>" alt="Taller Mecánico Arturo Motors" class="img-fluid rounded-4 shadow-lg">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?q=80&w=800" alt="Taller Mecánico Arturo Motors" class="img-fluid rounded-4 shadow-lg">
                            <?php endif; ?>
                        </div>
                        <div class="secondary-img-box d-none d-sm-block">
                            <?php if(isset($sections['about']) && count($sections['about']->mediaItems) > 1): ?>
                                <img src="<?php echo e($sections['about']->mediaItems->skip(1)->first()->media->bestUrl()); ?>" alt="Diagnóstico Computarizado" class="img-fluid rounded-4 shadow-2xl">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1625047509168-a7026f36de04?q=80&w=500" alt="Diagnóstico Computarizado" class="img-fluid rounded-4 shadow-2xl">
                            <?php endif; ?>
                        </div>
                        <div class="experience-badge shadow-lg">
                            <h3 class="badge-number">+10</h3>
                            <p class="badge-text">Años de <br>Experiencia</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                    <div class="ps-lg-4">
                        <div class="badge-tech mb-3">
                            <i class="fa-solid fa-users me-1"></i> Sobre Arturo Motors
                        </div>
                        <h2 class="section-title fw-bold mb-4">
                            <?php echo e($sections['about']->title ?? 'Líderes en Conversiones y'); ?> <br>
                            <span class="gradient-text"><?php echo e($sections['about']->subtitle ?? 'Soluciones Automotrices'); ?></span>
                        </h2>
                        <p class="text-sub mb-4">
                            <?php echo e($sections['about']->description ?? 'En Arturo Motors nos dedicamos a transformar la movilidad de nuestros clientes ofreciendo soluciones de conversión a GNV y GLP con los más altos estándares de calidad y seguridad.'); ?>

                        </p>
                        <p class="text-sub mb-4">
                            Contamos con equipos de diagnóstico de última generación y un equipo de técnicos certificados enfocados en maximizar el rinde de tu motor y garantizar tu ahorro diario.
                        </p>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="about-feature-card">
                                    <div class="feature-icon">
                                        <i class="fa-solid fa-microchip"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-white mb-1">Tecnología de Punta</h5>
                                        <p class="small text-sub mb-0">Escáneres y software de calibración precisos.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="about-feature-card">
                                    <div class="feature-icon">
                                        <i class="fa-solid fa-award"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-white mb-1">Certificación Oficial</h5>
                                        <p class="small text-sub mb-0">Trámites y revisiones 100% legales.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2 mb-4">
                            <div class="feature-line">
                                <i class="fa-solid fa-square-check text-blue"></i>
                                <span>Garantía escrita en cada conversión e instalación.</span>
                            </div>
                            <div class="feature-line">
                                <i class="fa-solid fa-square-check text-blue"></i>
                                <span>Atención personalizada y asesoría técnica directa.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Servicios -->
    <section id="servicios" class="services-section py-6">
        <div class="container py-lg-5">
            <div class="text-center mb-5" data-aos="fade-up" data-aos-duration="1000">
                <div class="badge-tech mb-3 mx-auto">
                    <i class="fa-solid fa-wrench me-1"></i> Lo Que Ofrecemos
                </div>
                <h2 class="section-title fw-bold">
                    <?php echo e($sections['services']->title ?? 'Nuestros Servicios'); ?> <span class="gradient-text"><?php echo e($sections['services']->subtitle ?? 'Especializados'); ?></span>
                </h2>
                <p class="text-sub mx-auto" style="max-width: 600px;">
                    <?php echo e($sections['services']->description ?? 'Tecnología de última generación y personal altamente capacitado para garantizar el máximo rendimiento de tu vehículo.'); ?>

                </p>
            </div>

            <div class="row g-4">
                <?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo e(($index % 3 + 1) * 100); ?>">
                        <div class="service-card h-100">
                            <div class="service-icon-wrapper">
                                <i class="<?php echo e($service->icon); ?>"></i>
                            </div>
                            <h4 class="service-title"><?php echo e($service->title); ?></h4>
                            <p class="service-desc"><?php echo e($service->description); ?></p>
                            <?php if($service->features): ?>
                                <ul class="service-list list-unstyled">
                                    <?php $__currentLoopData = $service->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><i class="fa-solid fa-check text-blue me-2"></i><?php echo e($feature); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                            <a href="<?php echo e($service->cta_link); ?>" target="_blank" class="btn btn-service w-100 mt-auto">
                                <?php echo e($service->cta_text); ?> <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Cargando servicios...</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Sección Por Qué Elegirnos & Proceso -->
    <section id="proceso" class="process-section py-6">
        <div class="container py-lg-5">
            <!-- Por Qué Elegirnos -->
            <div class="row align-items-center mb-6 gy-5">
                <div class="col-lg-5" data-aos="fade-right" data-aos-duration="1000">
                    <div class="badge-tech mb-3">
                        <i class="fa-solid fa-shield-halved me-1"></i> Ventajas Competitivas
                    </div>
                    <h2 class="section-title fw-bold mb-4">
                        <?php echo e($sections['why']->title ?? '¿Por qué confiar tu vehículo en'); ?> <br>
                        <span class="gradient-text"><?php echo e($sections['why']->subtitle ?? 'Arturo Motors?'); ?></span>
                    </h2>
                    <p class="text-sub mb-4">
                        <?php echo e($sections['why']->description ?? 'Nos enfocamos en brindar un servicio transparente, rápido y respaldado por normativas vigentes para que conduzcas con total tranquilidad y máximo ahorro.'); ?>

                    </p>
                    <a href="<?php echo e($whatsappLink); ?>" target="_blank" class="btn btn-main rounded-pill px-4 py-3 fw-semibold">
                        <i class="fa-solid fa-comments me-2"></i>Hablar con un Asesor
                    </a>
                </div>
                <div class="col-lg-7" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="row g-3">
                        <?php $__empty_1 = true; $__currentLoopData = $whyCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="col-sm-6">
                                <div class="why-card">
                                    <div class="why-icon"><i class="<?php echo e($card->icon); ?>"></i></div>
                                    <h5 class="fw-bold text-white mb-2"><?php echo e($card->title); ?></h5>
                                    <p class="small text-sub mb-0"><?php echo e($card->description); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-12 text-center py-3">
                                <p class="text-muted">Cargando ventajas...</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Proceso de Trabajo -->
            <div class="mt-4 pt-5 border-top border-secondary-subtle">
                <div class="text-center mb-5" data-aos="fade-up">
                    <div class="badge-tech mb-3 mx-auto">
                        <i class="fa-solid fa-route me-1"></i> Paso a Paso
                    </div>
                    <h2 class="section-title fw-bold">
                        <?php echo e($sections['process']->title ?? 'Nuestro Proceso de'); ?> <span class="gradient-text"><?php echo e($sections['process']->subtitle ?? 'Trabajo'); ?></span>
                    </h2>
                    <p class="text-sub mx-auto" style="max-width: 550px;">
                        <?php echo e($sections['process']->description ?? 'Un flujo transparente y seguro desde la recepción hasta la entrega de tu auto.'); ?>

                    </p>
                </div>
                <div class="row g-4 position-relative">
                    <?php $__empty_1 = true; $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo e(($index + 1) * 100); ?>">
                            <div class="step-card">
                                <div class="step-number"><?php echo e($step->step_number); ?></div>
                                <h5 class="fw-bold text-white mb-2"><?php echo e($step->title); ?></h5>
                                <p class="small text-sub mb-0"><?php echo e($step->description); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12 text-center py-3">
                            <p class="text-muted">Cargando pasos...</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Contacto -->
    <section id="contacto" class="contact-section py-6">
        <div class="container py-lg-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="badge-tech mb-3 mx-auto">
                    <i class="fa-solid fa-location-dot me-1"></i> Encuéntranos Fácilmente
                </div>
                <h2 class="section-title fw-bold">
                    <?php echo e($sections['contact']->title ?? 'Visítanos o'); ?> <span class="gradient-text"><?php echo e($sections['contact']->subtitle ?? 'Ponte en Contacto'); ?></span>
                </h2>
                <p class="text-sub mx-auto" style="max-width: 550px;">
                    <?php echo e($sections['contact']->description ?? 'Estamos listos para atender tus consultas, certificar tu auto o agendar tu conversión.'); ?>

                </p>
            </div>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-5" data-aos="fade-right" data-aos-duration="1000">
                    <div class="d-flex flex-column gap-3 h-100 justify-content-between">
                        <?php if(isset($contacts['address'])): ?>
                            <div class="contact-info-card">
                                <div class="contact-icon"><i class="<?php echo e($contacts['address']['icon']); ?>"></i></div>
                                <div>
                                    <h6 class="fw-bold text-white mb-1"><?php echo e($contacts['address']['label']); ?></h6>
                                    <p class="small text-sub mb-0"><?php echo e($contacts['address']['value']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($contacts['phone'])): ?>
                            <div class="contact-info-card">
                                <div class="contact-icon"><i class="<?php echo e($contacts['phone']['icon']); ?>"></i></div>
                                <div>
                                    <h6 class="fw-bold text-white mb-1"><?php echo e($contacts['phone']['label']); ?></h6>
                                    <p class="small text-sub mb-0"><?php echo e($contacts['phone']['value']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($contacts['schedule'])): ?>
                            <div class="contact-info-card">
                                <div class="contact-icon"><i class="<?php echo e($contacts['schedule']['icon']); ?>"></i></div>
                                <div>
                                    <h6 class="fw-bold text-white mb-1"><?php echo e($contacts['schedule']['label']); ?></h6>
                                    <p class="small text-sub mb-0"><?php echo e($contacts['schedule']['value']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="whatsapp-cta-box text-center p-4 rounded-4 mt-2">
                            <i class="fa-brands fa-whatsapp text-success display-5 mb-2"></i>
                            <h5 class="fw-bold text-white mb-2">¿Atención Inmediata?</h5>
                            <p class="small text-sub mb-3">Escríbenos directamente para consultar disponibilidad de citas en tiempo real.</p>
                            <a href="<?php echo e($whatsappLink); ?>" target="_blank" class="btn btn-success rounded-pill w-100 fw-bold py-2">
                                Chatear con un Asesor
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7" data-aos="fade-left" data-aos-duration="1000">
                    <div class="map-wrapper h-100 rounded-4 overflow-hidden border border-secondary-subtle">
                        <?php if(isset($contacts['map_iframe'])): ?>
                            <?php echo $contacts['map_iframe']['value']; ?>

                        <?php else: ?>
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d975.5839857865125!2d-77.10123263037826!3d-12.020377599263401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105ce9f965ce71b%3A0x39248294a3841558!2sAv.%20Per%C3%BA%205176%2C%20Callao%2007036!5e0!3m2!1ses-419!2spe!4v1785440284486!5m2!1ses-419!2spe"
                                width="100%" 
                                height="100%" 
                                style="border:0; min-height: 400px;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer py-5">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-md-4 text-center text-md-start">
                    <a class="d-inline-flex align-items-center gap-2 text-decoration-none mb-2" href="#">
                        <div class="brand-icon">
                            <img src="images/icon.png" alt="ARTURO MOTORS" style="width: 35px; height: 35px;">
                        </div>
                        <span class="brand-title">ARTURO <span class="text-blue">MOTORS</span></span>
                    </a>
                    <p class="small text-sub mb-0 mt-2">Especialistas en conversiones GNV/GLP, certificaciones oficiales y mecánica integral.</p>
                </div>
                <div class="col-md-4 text-center">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item mx-2"><a href="#inicio" class="text-sub text-decoration-none small hover-blue">Inicio</a></li>
                        <li class="list-inline-item mx-2"><a href="#nosotros" class="text-sub text-decoration-none small hover-blue">Nosotros</a></li>
                        <li class="list-inline-item mx-2"><a href="#servicios" class="text-sub text-decoration-none small hover-blue">Servicios</a></li>
                        <li class="list-inline-item mx-2"><a href="#proceso" class="text-sub text-decoration-none small hover-blue">Proceso</a></li>
                    </ul>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3 mb-2">
                        <?php $__empty_1 = true; $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e($link->url); ?>" target="_blank" class="social-link"><i class="<?php echo e($link->icon); ?>"></i></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <a href="#" class="social-link"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                        <?php endif; ?>
                    </div>
                    <p class="small text-sub mb-0">&copy; <?php echo e(date('Y')); ?> Arturo Motors. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Botón Flotante WhatsApp -->
    <a href="<?php echo e($whatsappLink); ?>" target="_blank" class="whatsapp-btn-pulse" aria-label="Contacto WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="js/main.js"></script>
    <script>
        (function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('highlight') === '1' && window.location.hash) {
                const sectionId = window.location.hash.substring(1);
                const section = document.getElementById(sectionId);
                if (section) {
                    // Add highlight styles
                    section.style.transition = 'box-shadow 0.3s ease, outline 0.3s ease';
                    section.style.outline = '3px solid #3b82f6';
                    section.style.outlineOffset = '-3px';
                    section.style.boxShadow = '0 0 20px rgba(59, 130, 246, 0.5)';
                    section.style.borderRadius = '12px';
                    section.style.position = 'relative';

                    // Add label
                    const label = document.createElement('div');
                    label.textContent = 'Sección modificada';
                    label.style.cssText = 'position: absolute; top: -28px; left: 0; background: #3b82f6; color: white; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; z-index: 10; white-space: nowrap;';
                    section.style.position = 'relative';
                    section.prepend(label);

                    // Scroll to section
                    setTimeout(() => {
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);

                    // Remove highlight after 3 seconds
                    setTimeout(() => {
                        section.style.outline = '';
                        section.style.outlineOffset = '';
                        section.style.boxShadow = '';
                        section.style.borderRadius = '';
                        if (label.parentNode) label.remove();
                    }, 3000);
                }
            }
        })();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/landing-page.blade.php ENDPATH**/ ?>