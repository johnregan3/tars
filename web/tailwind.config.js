/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme')
module.exports = {
  content: ['./src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      animation: {
        'border-highlight-fade': 'border-highlight-fade 1.5s ease-in-out'
      },
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans]
      },
      maxHeight: {
        128: '32rem'
      },
      rotate: {
        30: '30deg'
      },
      keyframes: {
        'border-highlight-fade': {
          '0%, 100%': { borderColor: 'rgba(55,65,81, 1)' },
          '20%, 50%': { borderColor: '#f59e0b' }
        }
      }
    }
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')]
}
