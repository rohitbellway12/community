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
                reiac: {
                    navy:       '#0B132B',
                    slate:      '#1C2541',
                    gold:       '#F7B500',
                    'gold-hover': '#E0A400',
                    bg:         '#F4F6F9',
                },
            },
        },
    },

    plugins: [forms],
};
