import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand palette — Trickleup
                brand: {
                    DEFAULT: '#FCD82F',
                    dark: '#E8C620',
                    ink: '#0a0a0f',
                },
                // Remap the accent (indigo) Breeze uses to brand yellow, so focus
                // rings / active states match the brand across all default pages.
                indigo: {
                    50: '#FEFAE3',
                    100: '#FDF3BB',
                    400: '#FCD82F',
                    500: '#FCD82F',
                    600: '#E8C620',
                    700: '#C9A800',
                    800: '#A98C00',
                },
                // Make the dark buttons / headings true brand near-black.
                gray: {
                    700: '#1a1a22',
                    800: '#0a0a0f',
                    900: '#0a0a0f',
                },
            },
        },
    },

    plugins: [forms],
};
