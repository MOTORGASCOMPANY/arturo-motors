// Landing pública (resources/views/landing-page.blade.php)
// Inicialización de Swiper (hero) y AOS. Lógica original del public/js/main.js
// eliminado en el commit 71ccf27; acoplada a la estructura Vite resources/js/.

document.addEventListener('DOMContentLoaded', () => {
    // Slider del hero (fade con autoplay). Swiper llega por CDN en la vista.
    const slides = document.querySelectorAll('.hero-slider .swiper-slide');
    if (slides.length && typeof Swiper !== 'undefined') {
        new Swiper('.hero-slider', {
            effect: 'fade',
            fadeEffect: { crossFade: true },
            loop: slides.length >= 3,
            autoplay: { delay: 4000, disableOnInteraction: false },
            speed: 1200,
        });
    }

    // AOS oculta por CSS todo elemento con data-aos hasta init: sin este
    // archivo la landing se renderiza pero invisible (opacity: 0).
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true,
            easing: 'ease-in-out',
        });
    }
});
