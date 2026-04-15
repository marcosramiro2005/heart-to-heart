<script setup>
import { Link } from '@inertiajs/vue3'

const anioActual = new Date().getFullYear()

const secciones = [
    {
        titulo: 'Explorar',
        links: [
            { label: 'Inicio',        href: '/home' },
            { label: 'Mis emociones', href: '/mis-emociones' },
            { label: 'Biblioteca',    href: '/biblioteca' },
            { label: 'Recursos',      href: '/recursos' },
            { label: 'Comunidad',     href: '/comunidad' },
        ]
    },
    {
        titulo: 'Herramientas',
        links: [
            { label: 'Hablar con Hearty',  href: '/hearty' },
            { label: 'Respiración guiada', href: '/respiracion' },
            { label: 'Sonidos relajantes', href: '/sonidos' },
            { label: 'Diario de gratitud', href: '/diario' },
            { label: 'Mis logros',         href: '/logros' },
        ]
    },
    {
        titulo: 'Información',
        links: [
            { label: '¿Quiénes somos?', href: '/quienes-somos' },
            { label: 'Mi perfil',       href: '/perfil' },
            { label: 'Biblioteca',      href: '/biblioteca' },
        ]
    },
]
</script>

<template>
    <footer class="footer">
        <div class="footer-inner">

            <div class="footer-marca">
                <div class="footer-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </div>

                <p class="footer-tagline">
                    Un espacio seguro para cuidar tu bienestar emocional cada día.
                    Gratuito, accesible y hecho con amor.
                </p>

                <div class="footer-redes">
                    <a href="https://github.com" target="_blank" rel="noopener noreferrer" class="red-btn" title="GitHub">🐙</a>
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="red-btn" title="Instagram">📸</a>
                    <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="red-btn" title="Twitter">🐦</a>
                </div>

                <div class="footer-aviso">
                    <span class="aviso-icono">⚠️</span>
                    <p>
                        Heart to Heart es una herramienta de apoyo y
                        <strong>no sustituye a la atención psicológica profesional</strong>.
                        Si necesitas ayuda urgente, llama al
                        <strong>024</strong> (línea de atención a conducta suicida).
                    </p>
                </div>
            </div>

            <div v-for="seccion in secciones" :key="seccion.titulo" class="footer-col">
                <h4>{{ seccion.titulo }}</h4>
                <ul>
                    <li v-for="link in seccion.links" :key="link.label">
                        <Link :href="link.href">{{ link.label }}</Link>
                    </li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <p>© {{ anioActual }} Heart to Heart · Desarrollado por Marcos Ramiro Roig</p>
                <p>TFG — Ciclo Superior DAW · CPIFP Los Enlaces, Zaragoza</p>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.footer {
    background: #1a1a2e;
    color: #e0e0e0;
    margin-top: 4rem;
}

.footer-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 3rem 1.5rem 2rem;
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr;
    gap: 2.5rem;
}

.footer-marca {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.footer-logo img {
    width: 38px;
    height: auto;
    filter: brightness(1.1);
}

.footer-logo span {
    font-weight: 700;
    font-size: 0.88rem;
    color: #4ECDC4;
    letter-spacing: 0.08em;
}

.footer-tagline {
    font-size: 0.85rem;
    color: #aaa;
    line-height: 1.7;
    margin: 0;
}

.footer-redes {
    display: flex;
    gap: 0.5rem;
}

.red-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #2a2a3e;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    text-decoration: none;
    transition: background 0.2s, transform 0.2s;
    border: 1px solid #333;
}

.red-btn:hover {
    background: #4ECDC4;
    transform: translateY(-2px);
}

.footer-aviso {
    display: flex;
    gap: 0.6rem;
    align-items: flex-start;
    background: #2a1a1a;
    border: 1px solid #5a2020;
    border-radius: 10px;
    padding: 0.75rem;
}

.aviso-icono {
    font-size: 1rem;
    flex-shrink: 0;
    margin-top: 0.1rem;
}

.footer-aviso p {
    font-size: 0.78rem;
    color: #cc9999;
    line-height: 1.5;
    margin: 0;
}

.footer-aviso strong { color: #e0aaaa; }

.footer-col h4 {
    font-size: 0.82rem;
    font-weight: 700;
    color: #4ECDC4;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin: 0 0 1rem;
}

.footer-col ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.footer-col a {
    font-size: 0.85rem;
    color: #aaa;
    text-decoration: none;
    transition: color 0.2s;
    display: inline-block;
}

.footer-col a:hover { color: #4ECDC4; }

.footer-bottom {
    border-top: 1px solid #2a2a3e;
    background: #141420;
}

.footer-bottom-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.footer-bottom p {
    font-size: 0.78rem;
    color: #666;
    margin: 0;
}

@media (max-width: 900px) {
    .footer-inner {
        grid-template-columns: 1fr 1fr;
    }
    .footer-marca {
        grid-column: 1 / -1;
    }
}

@media (max-width: 560px) {
    .footer-inner {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    .footer-bottom-inner {
        flex-direction: column;
        text-align: center;
    }
}
</style>