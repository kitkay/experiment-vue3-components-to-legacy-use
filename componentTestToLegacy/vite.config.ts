import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig(({ command, mode }) => ({
  plugins: [
    vue(),
    tailwindcss(),

    ...(command === 'serve'
      ? [vueDevTools()]
      : []),
  ],

  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },

  build: {
    outDir: '../php-app/public/assets',
    emptyOutDir: true,

    ...(mode === 'integration'
      ? {
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
        }
      : {}
    ),
  },
}))