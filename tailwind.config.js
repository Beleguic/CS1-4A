/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        bgBody: { 
          400: "#EEEBE7",
          800: "#1D1B1B",
        },
        TextColor: {
          400: "#1D1B1B",
          800: "#EEEBE7",
        },
        mainColor :{
          400: "#134F63",
        },
        secondaryColor :{
          400: "#0091C2",
        }
      },
      maxWidth:{
        '9xl': "9rem",
        '10xl': "100rem",
      }
    },
  },
  plugins: [],
}