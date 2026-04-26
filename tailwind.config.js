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
                primary: {
                    DEFAULT: '#10B981', // emerald-500
                    light: '#34D399',   // emerald-400
                    dark: '#059669',    // emerald-600
                    darker: '#047857',  // emerald-700
                    darkest: '#065F46', // emerald-800
                },
            },
        },
    },

    plugins: [forms],
};
