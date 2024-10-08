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
        './resources/js/**/*.vue',
        './resources/js/jsonforms/styles/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                dosis: ['Dosis', 'sans-serif'],
            },
            colors: {
                'sinai-beige': 'rgb(247, 242, 234)',
                'sinai-dark-beige': 'rgb(231, 227, 221)',
                'sinai-red': 'rgb(171, 47, 10)',
                'sinai-light-blue': 'rgba(149, 204, 209, 0.24)',
                'sinai-dark-blue': 'rgb(31, 105, 113)',
                'sinai-gray': 'rgba(227, 227, 227, 0.37)'
            },
            maxWidth: {
                '8xl': '96rem',
            },
        },
    },

    plugins: [forms, typography],

    safelist: [
        'gap-x-1',
        'gap-x-2',
        'gap-x-4',
        'gap-y-1',
        'gap-y-2',
        'gap-y-4',
        '!m-0',
        '!p-4',
        '!px-1',
        '!px-2',
        'max-w-4xl',
        'max-w-5xl',
        'max-w-6xl',
    ],
};
