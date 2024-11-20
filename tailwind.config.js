/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./resources/**/*.blade.php", "./resources/**/*.js"],
    theme: {
        extend: {
            colors: {
                'text': 'var(--text)',
                'background': 'var(--background)',
                'background-low': 'var(--background-low)',
                'primary': 'var(--primary)',
                'secondary': 'var(--secondary)',
                'secondary-low':'var(--secondary-low)' ,
                'accent': 'var(--accent)',
                'accent-low': 'var(--accent-low)',
               },
        },
    },
    plugins: [],
};
