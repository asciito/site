import defaultTheme from 'tailwindcss/defaultTheme';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/filament/filament/resources/views/components/user-menu.blade.php',
        './vendor/filament/support/resources/views/components/dropdown/**/*.blade.php',
        './vendor/filament/filament/resources/views/components/theme-switcher/**/*.blade.php',
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
                'dark-blue': {
                    DEFAULT: '#0000AA',
                    50: '#6363FF',
                    100: '#4E4EFF',
                    200: '#2525FF',
                    300: '#0000FC',
                    400: '#0000D3',
                    500: '#0000AA',
                    600: '#000072',
                    700: '#00003A',
                    800: '#000002',
                    900: '#000000',
                    950: '#000000'
                },
            },
            // that is animation class
            animation: {
                fade: 'fadeIn 0.2s ease-in-out',
            },

            // that is actual animation
            keyframes: theme => ({
                fadeIn: {
                    '0%': { opacity: 0.0 },
                    '100%': { opacity: 1 },
                },
            }),
        },
    },

    plugins: [typography],
};
