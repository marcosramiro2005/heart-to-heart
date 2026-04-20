import './bootstrap'
import '../css/app.css'
import 'nprogress/nprogress.css'

import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import NProgress from 'nprogress'

NProgress.configure({ showSpinner: false, speed: 300 })

router.on('start',  () => NProgress.start())
router.on('finish', () => NProgress.done())

const appName = import.meta.env.VITE_APP_NAME || 'Heart to Heart'

createInertiaApp({
    title: (title) => title ? `${title} — ${appName}` : appName,
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
    ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el)
    },
    progress: false,
})