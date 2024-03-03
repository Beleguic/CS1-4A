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
          400: "#ffffff",
          800: "#181717",
        },
        TextColor: {
          400: "#181717",
          800: "#ffffff",
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