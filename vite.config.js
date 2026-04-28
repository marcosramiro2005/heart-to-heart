import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: { host: 'localhost', port: 5173 },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Dependencias principales de Vue e Inertia — siempre necesarias
                    if (id.includes('vue') || id.includes('@inertiajs')) return 'vendor'
                    // Resto de node_modules en un chunk separado
                    if (id.includes('node_modules')) return 'deps'
                    // Páginas de autenticación — pequeñas, se cargan solo en login/register
                    if (id.includes('/Pages/Auth/')) return 'auth'
                    // Técnicas de bienestar — muchas páginas similares, se agrupan
                    if (id.includes('/Pages/Tecnicas/')) return 'tecnicas'
                    // Foro — páginas pesadas con mucho JS
                    if (id.includes('/Pages/Forum/')) return 'forum'
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },
})
