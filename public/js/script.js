// Obtiene el año actual para el pie de página.
document.getElementById('current-year').textContent = new Date().getFullYear();

// Muestra/oculta el menú móvil al hacer clic.
const mobileMenuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');
mobileMenuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});

// Cambia el fondo del encabezado al desplazar la página.
const header = document.getElementById('main-header');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        header.classList.add('header-bg', 'scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Lógica para el carrusel de imágenes de fondo.
const backgrounds = document.querySelectorAll('.hero-bg-image');
const prevBtn = document.getElementById('prev-btn');
const nextBtn = document.getElementById('next-btn');
let currentBgIndex = 0;

function showBackground(index) {
    backgrounds.forEach((bg, i) => {
        bg.classList.remove('active');
        if (i === index) {
            bg.classList.add('active');
        }
    });
}

if (prevBtn && nextBtn && backgrounds.length > 0) {
    prevBtn.addEventListener('click', () => {
        currentBgIndex = (currentBgIndex - 1 + backgrounds.length) % backgrounds.length;
        showBackground(currentBgIndex);
    });

    nextBtn.addEventListener('click', () => {
        currentBgIndex = (currentBgIndex + 1) % backgrounds.length;
        showBackground(currentBgIndex);
    });
}

// Lógica para el contador animado.
const counterSection = document.getElementById('parallax-counter');
const counters = document.querySelectorAll('.counter');

// Usamos Intersection Observer para detectar cuando la sección entra en el viewport
const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        // Si la sección es visible
        if (entry.isIntersecting) {
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const increment = target / 200; // La velocidad de la animación

                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        setTimeout(updateCount, 1);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
            // Dejamos de observar la sección una vez que se activa el contador
            observer.unobserve(counterSection);
        }
    });
}, { threshold: 0.5 }); // El umbral 0.5 significa que se activará cuando el 50% de la sección sea visible

// Le decimos al observador que observe la sección del contador
if (counterSection) {
    observer.observe(counterSection);
}


// Seccion album flyers (carrusel desliza cinta)
const swiper = new Swiper('.flyers-carousel', {
    slidesPerView: 1,        // Móvil: 1 flyer por vista
    spaceBetween: 20,
    loop: true,              // Loop infinito
    autoplay: {
      delay: 2500,           // Avanza cada 2.5s
      disableOnInteraction: false,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    breakpoints: {
      640: { slidesPerView: 2 },   // Tablet
      1024: { slidesPerView: 3 },  // Laptop
      1280: { slidesPerView: 3 },  // Pantallas grandes
    },
  });