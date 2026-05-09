// Configura Axios como cliente HTTP global para todas las peticiones AJAX de la app.
// Al hacer window.axios = axios, Axios queda disponible en cualquier parte del código JS.
import axios from 'axios';
window.axios = axios;

// Este header le indica a Laravel que la petición viene de AJAX (no de un formulario normal).
// Laravel lo usa internamente para detectar si debe devolver JSON o redirigir.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
