import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    tailwindcss(),
  ],

  build: {
    outDir: '../php-app/public/assets',
    emptyOutDir: true,

    rollupOptions: {
      input: 'src/integration.ts',

      output: {
        entryFileNames: 'vue-app.js',

        assetFileNames: (assetInfo) => {
          if (assetInfo.name?.endsWith('.css')) {
            return 'vue-app.css'
          }

          return 'assets/[name][extname]'
        },
      },
    },
  },
})