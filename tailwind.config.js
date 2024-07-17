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
                ...defaultTheme.colors,
                'harlequin': {
                    DEFAULT: '#33FF33',
                    50: '#EBFFEB',
                    100: '#D6FFD6',
                    200: '#ADFFAD',
                    300: '#85FF85',
                    400: '#5CFF5C',
                    500: '#33FF33',
                    600: '#00FA00',
                    700: '#00C200',
                    800: '#008A00',
                    900: '#005200',
                    950: '#003600'
                },
            }
        },
    },

    plugins: [],
};
