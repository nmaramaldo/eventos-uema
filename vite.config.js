// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',

        // entradas do template:
        'resources/template/new-event/css/new-event.css',
        'resources/template/new-event/js/new-event.js',
      ],
      refresh: true,
    }),
  ],
})
