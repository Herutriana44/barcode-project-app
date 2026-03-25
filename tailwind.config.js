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
            colors: {
                // Hijau telur asin / green gauze: sage–jade lembut, krim kehijauan
                egg: {
                    50: '#f2f7f4',
                    100: '#e2ebe5',
                    200: '#c5d6cc',
                    300: '#9bb8a7',
                    400: '#6f957f',
                    500: '#4e7a62',
                    600: '#3d614f',
                    700: '#334f42',
                    800: '#2b4137',
                    900: '#24362e',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
