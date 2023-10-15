/** @type {import('tailwindcss').Config} */
export default {
  content: ['./resources/**/*.{vue,js,blade.php}'],
  theme: {
    extend: {
      colors: {
        customBlue: '#60B6D0',
        customRed: '#F05B5B',
        customGray: '#C4C4C4',
        customGreen: '#C3FFBE',
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
}
