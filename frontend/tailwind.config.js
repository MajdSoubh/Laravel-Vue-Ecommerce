/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      boxShadow: {
        blue: "0 0 0 3px rgb(3 155 229 /.1)",
        gray: "0 0 6px 3px rgb(100 116 139 /.12)",
        indigo: "0 0 0 3px rgb(68 57 202 /.1)",
        red: "0 0 0 3px rgb(252 165 165 /.2)",
        green: "0 0 0 2px rgb(16 185 129 /.2)",
        purple: "0 0 0 2px rgb(147 51 234 /.2)",
      },
      colors: {
        "purple-secondary": "#a956cf",
      },
      keyframes: {
        "fade-in-down": {
          from: {
            transform: "translateY(-0.75rem)",
            opacity: "0",
          },
          to: {
            transform: "translateY(0rem)",
            opacity: "1",
          },
        },
        "fade-in-left": {
          from: {
            transform: "translateX(2.75rem)",
            opacity: "0",
          },
          to: {
            transform: "translateX(0rem)",
            opacity: "1",
          },
        },
      },
      animation: {
        "fade-in-down": "fade-in-down 0.2s ease-in-out both",
        "fade-in-left": "fade-in-left 0.3s ease-in-out both",
      },
      height: {
        screen: "var(--real-vh)",
      },
      width: {
        screen: "var(--real-vw)",
      },
    },
  },
  plugins: [require("@tailwindcss/forms")],
};
