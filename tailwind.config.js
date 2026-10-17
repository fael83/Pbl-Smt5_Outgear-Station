import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Warna kustom yang sebelumnya hanya didefinisikan lewat
                // <script> tailwind.config di dalam view (Tailwind CDN).
                // Sekarang masuk ke build Vite supaya CDN tidak diperlukan.
                'portal-sidebar': '#18181b',
                'portal-bg': '#f4f2ef',
                'portal-orange': '#e05a3f',
                'portal-card': '#ffffff',

                brand: '#D86B43',
                'brand-hover': '#c25a34',
                dark: '#1A2024',
                'dark-card': '#151A1D',
                bglight: '#FBF9F6',
            },
        },
    },

    plugins: [forms],
};
