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
                heading: ['Space Grotesk', 'sans-serif'],
            },
            colors: {
                'portal-sidebar': 'rgb(var(--portal-sidebar-rgb) / <alpha-value>)',
                'portal-bg': 'rgb(var(--portal-bg-rgb) / <alpha-value>)',
                'portal-orange': 'rgb(var(--portal-brand-rgb) / <alpha-value>)',
                'portal-card': 'rgb(var(--portal-card-rgb) / <alpha-value>)',
                brand: 'rgb(var(--portal-brand-rgb) / <alpha-value>)',
                'brand-hover': 'rgb(var(--portal-brand-hover-rgb) / <alpha-value>)',
                dark: 'rgb(var(--portal-ink-rgb) / <alpha-value>)',
                'dark-card': 'rgb(var(--portal-dark-card-rgb) / <alpha-value>)',
                bglight: 'rgb(var(--portal-bglight-rgb) / <alpha-value>)',
            },
        },
    },

    plugins: [forms],
};
