/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.ts",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'sans-serif'],
      },
      colors: {
        primary: {
          DEFAULT: '#2563EB',
          hover: '#1D4ED8',
          light: '#DBEAFE',
        },
      },
      borderRadius: {
        'modern': '8px',
        'modern-lg': '12px',
      },
      boxShadow: {
        'modern': '0 1px 3px 0 rgb(0 0 0 / 0.1)',
        'modern-md': '0 4px 6px -1px rgb(0 0 0 / 0.1)',
      },
      margin: {
        '65': '260px', // Custom margin for tablet sidebar width
      },
    },
  },
  plugins: [],
}
