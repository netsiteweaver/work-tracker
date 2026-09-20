import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

// Top menu buttons are coloured from the database, so their class names never
// appear in a file Tailwind scans. Mirror of App\Support\NavColors::COLORS —
// keep the two lists in step.
const navColors = [
    'slate', 'gray', 'zinc', 'neutral', 'stone', 'red',
    'orange', 'amber', 'yellow', 'lime', 'green', 'emerald',
    'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet',
    'purple', 'fuchsia', 'pink', 'rose',
];

const navColorClasses = navColors.flatMap((color) => [
    `bg-${color}-600`,
    `hover:bg-${color}-700`,
    `border-${color}-600`,
    `text-${color}-700`,
    `hover:bg-${color}-600`,
    `dark:border-${color}-400`,
    `dark:text-${color}-300`,
    `dark:hover:bg-${color}-500`,
]);

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // Enable class-based dark mode
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './config/navigation.php',
    ],

    // ...plus the states the colour picker toggles from JavaScript.
    safelist: navColorClasses.concat(['ring-2', 'ring-gray-900', 'dark:ring-white', 'opacity-40']),

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

