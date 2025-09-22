// Use o jQuery do NPM (resolve o erro do Vite)
import $ from 'jquery';

// expõe globalmente para plugins que esperam window.$ ou window.jQuery
window.$ = window.jQuery = $;

// plugins do template (dependem do jQuery global já setado)
import './bootstrap.min.js';
import './jquery.parallax.js';
import './owl.carousel.min.js';
import './smoothscroll.js';
import './wow.min.js';
import './custom.js';
