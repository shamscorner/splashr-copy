const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    purge: [
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Barlow', ...defaultTheme.fontFamily.sans]
            },
            fontSize: {
                xxs: '0.625rem'
            },
            colors: {
                blue: {
                    '600': '#604efd',
                    '700': '#5646e4'
                },
                'splashr-violet-deep': '#21005C',
                'splashr-violet-light': '#604EFD',
                'splashr-violet-extra-light': '#c0bcd9',
                'splashr-violet-soft-glow': '#f5f5ff',
                'splashr-blue-light': '#37DCFF',
                'splashr-danger': '#d9534f',
                'splashr-danger-deep': '#B71C1C',
                'splashr-indigo-deep': '#120539',
                'splashr-bg-doc': '#5491f2',
                'splashr-bg-slide': '#f2b737',
                'splashr-gray': '#d1d0d0',
                'splashr-gray-light': '#ededed',
                'splashr-cyan-deep': '#015b6f',
                'splashr-cyan-light': '#90e3f4'
            },
            spacing: {
                '22': '88px',
                '29': '116px',
                '38': '152px',
                '45': '180px',
                '1/7': '14.3%',
                '1/9': '11.11%',
                '2/9': '22.22%',
                '6/9': '65.9%',
                '550': '550px',
                '600': '600px',
                '700': '700px',
                '800': '800px'
            }
        }
    },

    variants: {
        extend: {
            opacity: ['responsive', 'hover', 'focus', 'disabled'],
            ringWidth: ['hover', 'active', 'group-hover'],
            cursor: ['hover', 'focus'],
            backgroundColor: ['active'],
            display: ['hover', 'active', 'group-hover']
        }
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/aspect-ratio')
    ]
}
