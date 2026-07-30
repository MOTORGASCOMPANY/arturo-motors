document.addEventListener('DOMContentLoaded', function () {
    
    // Inicializar Swiper (Slider de fondo automático con fade)
    const heroSwiper = new Swiper('.hero-slider', {
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        speed: 1200
    });

    // Inicializar AOS (Animate On Scroll)
    AOS.init({
        duration: 1000,
        once: true,
        easing: 'ease-in-out'
    });
});