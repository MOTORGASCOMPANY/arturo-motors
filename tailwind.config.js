import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // FASE 2: tipografía de los reportes
                inter: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            // FASE 2: paleta azul (acento) — los grises vienen de gray/slate
            colors: {
                brand: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#172554',
                },
            },
            // FASE 2: radios y sombras de tarjeta
            borderRadius: {
                card: '12px',
            },
            boxShadow: {
                card: '0 1px 3px rgba(29,78,216,.08)',
            },
        },
    },

    plugins: [forms, typography],
};
