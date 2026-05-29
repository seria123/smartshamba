import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                green: {
                    50: '#E8F5E9',
                    100: '#C8E6C9',
                    200: '#A5D6A7',
                    300: '#81C784',
                    400: '#66BB6A',
                    500: '#4CAF50',
                    600: '#43A047',
                    700: '#388E3C',
                    800: '#2E7D32',
                    900: '#1B5E20',
                },
                brown: {
                    50: '#EFEBE9',
                    100: '#D7CCC8',
                    200: '#BCAAA4',
                    300: '#A1887F',
                    400: '#8D6E63',
                    500: '#795548',
                    600: '#6D4C41',
                    700: '#5D4037',
                    800: '#4E342E',
                    900: '#3E2723',
                },
                yellow: {
                    50: '#FFFDE7',
                    100: '#FFF9C4',
                    200: '#FFF59D',
                    300: '#FFF176',
                    400: '#FFEE58',
                    500: '#FDD835',
                    600: '#FBC02D',
                    700: '#F9A825',
                    800: '#F57F17',
                    900: '#F9A825',
                },
                blue: {
                    50: '#E1F5FE',
                    100: '#B3E5FC',
                    200: '#81D4FA',
                    300: '#4FC3F7',
                    400: '#29B6F6',
                    500: '#039BE5',
                    600: '#0288D1',
                    700: '#0277BD',
                    800: '#01579B',
                    900: '#01579B',
                },
                red: {
                    50: '#FFEBEE',
                    100: '#FFCDD2',
                    200: '#EF9A9A',
                    300: '#E57373',
                    400: '#EF5350',
                    500: '#F44336',
                    600: '#E53935',
                    700: '#D32F2F',
                    800: '#C62828',
                    900: '#B71C1C',
                },
                amber: {
                    50: '#FFF8E1',
                    100: '#FFECB3',
                    200: '#FFE082',
                    300: '#FFD54F',
                    400: '#FFCA28',
                    500: '#FFC107',
                    600: '#FFB300',
                    700: '#FFA000',
                    800: '#FF8F00',
                    900: '#FF6F00',
                },
            },
        },
    },

    plugins: [forms],
};
