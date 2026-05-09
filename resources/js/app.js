// Punto de entrada principal de la aplicación Vue 3 + Inertia.js
// Este archivo es el primero que ejecuta el navegador al cargar la app

import './bootstrap'       // configura Axios con el token CSRF para las peticiones HTTP
import '../css/app.css'    // estilos globales (Tailwind CSS + personalizados)
import 'nprogress/nprogress.css' // estilos de la barra de progreso de navegación

import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy' // plugin de rutas Laravel en JS
import NProgress from 'nprogress' // barra de progreso fina en la parte superior de la página

// Configurar NProgress: sin spinner giratorio, animación rápida de 300ms
NProgress.configure({ showSpinner: false, speed: 300 })

// Mostrar la barra de progreso cuando Inertia inicia una navegación
router.on('start',  () => NProgress.start())
// Ocultar la barra de progreso cuando la navegación termina
router.on('finish', () => NProgress.done())

// Nombre de la app desde las variables de entorno de Vite (definido en .env como VITE_APP_NAME)
const appName = import.meta.env.VITE_APP_NAME || 'Heart to Heart'

// Inicializar la aplicación Inertia.js
createInertiaApp({
    // Formato del título del documento: "Página — Heart to Heart" o solo "Heart to Heart"
    title: (title) => title ? `${title} — ${appName}` : appName,

    // Resuelve el nombre de componente de página (ej: 'Home/Index') a su archivo Vue correspondiente.
    // import.meta.glob carga los archivos de forma lazy (solo cuando se navega a esa página)
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue', { eager: false })
    ),

    // Monta la aplicación Vue en el elemento #app del HTML y registra los plugins necesarios:
    // - plugin: el plugin de Inertia.js para manejar la navegación SPA
    // - ZiggyVue: permite usar route('nombre') en componentes Vue como en Laravel
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el)
    },

    // progress: false porque gestionamos NProgress manualmente arriba para más control
    progress: false,
})