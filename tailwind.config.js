/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './app/Views/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#E8A0BF',
                    dark: '#C77DA0',
                    light: '#F5D5E0',
                },
                secondary: {
                    DEFAULT: '#D4A574',
                    dark: '#B8895A',
                    light: '#F0D4B4',
                },
                cream: '#FFF8F0',
                success: '#7DB88F',
                warning: '#E8C47D',
                danger: '#D47D7D',
                info: '#7DA8D4',
            },
            fontFamily: {
                playfair: ['Playfair Display', 'serif'],
                poppins: ['Poppins', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
