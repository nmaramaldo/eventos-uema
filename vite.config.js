// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.scss',            // << trocado para SCSS
        'resources/js/app.js',

        // entradas do seu template (mantidas):
        'resources/template/new-event/css/new-event.css',
        'resources/template/new-event/js/new-event.js',
      ],
      refresh: true,
    }),
  ],
})
