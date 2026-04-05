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
                // Biru–lavender (palet: #6367FF, #8494FF, #C9BEFF, #FFDBFD)
                egg: {
                    50: '#FFDBFD',
                    100: '#EDE8FF',
                    200: '#C9BEFF',
                    300: '#A8B4FF',
                    400: '#8494FF',
                    500: '#6367FF',
                    600: '#5558E6',
                    700: '#4547C4',
                    800: '#3839A0',
                    900: '#242560',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
