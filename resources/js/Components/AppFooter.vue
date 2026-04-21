<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <footer class="footer">
        <div class="footer-inner">

            <div class="footer-brand">
                <div class="fb-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </div>
                <p>Tu espacio seguro de bienestar emocional. Gratuito, privado y siempre disponible.</p>
                <div class="fb-aviso">
                    ⚠️ No sustituye atención psicológica profesional.
                    En crisis llama al <strong>024</strong> o <strong>112</strong>.
                </div>
            </div>

            <div class="footer-links">
                <div class="fl-col">
                    <h4>Técnicas</h4>
                    <Link href="/respiracion">Respiración guiada</Link>
                    <Link href="/meditacion">Meditación</Link>
                    <Link href="/visualizacion">Visualización</Link>
                    <Link href="/ejercicio">Ejercicio</Link>
                    <Link href="/sonidos">Sonidos</Link>
                </div>
                <div class="fl-col">
                    <h4>App</h4>
                    <Link href="/home">Inicio</Link>
                    <Link href="/hearty">Hearty</Link>
                    <Link href="/mis-emociones">Mis emociones</Link>
                    <Link href="/comunidad">Comunidad</Link>
                    <Link href="/retos">Retos</Link>
                </div>
                <div class="fl-col">
                    <h4>Más</h4>
                    <Link href="/logros">Mis logros</Link>
                    <Link href="/biblioteca">Biblioteca</Link>
                    <Link href="/test-bienestar">Test bienestar</Link>
                    <Link href="/quienes-somos">¿Quiénes somos?</Link>
                    <Link href="/sos">Modo SOS</Link>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© {{ new Date().getFullYear() }} Heart to Heart · TFG DAW · Marcos Ramiro Roig · CPIFP Los Enlaces, Zaragoza</p>
            <p>Desarrollado con 💚 usando Laravel, Vue 3, Inertia.js y Python/Flask</p>
        </div>
    </footer>
</template>

<style scoped>
.footer {
    background: #141420;
    margin-top: 3rem;
}

.footer-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 3rem 2rem;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 2rem;
}

/* ── Brand ── */
.footer-brand {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.fb-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.fb-logo img  { width: 32px; filter: brightness(1.2); }
.fb-logo span { font-weight: 700; font-size: 0.82rem; color: #4ECDC4; letter-spacing: 0.1em; }

.footer-brand p {
    font-size: 0.82rem;
    color: #555;
    line-height: 1.6;
    margin: 0;
}

.fb-aviso {
    font-size: 0.75rem;
    color: #444;
    line-height: 1.5;
    background: rgba(230,57,70,0.08);
    border-radius: 8px;
    padding: 0.6rem 0.75rem;
    border-left: 2px solid #E63946;
}

.fb-aviso strong { color: #E63946; }

/* ── Links ── */
.footer-links {
    display: contents;
}

.fl-col {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.fl-col h4 {
    font-size: 0.78rem;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin: 0 0 0.25rem;
}

.fl-col a {
    font-size: 0.82rem;
    color: #444;
    text-decoration: none;
    transition: color 0.2s;
}

.fl-col a:hover { color: #4ECDC4; }

/* ── Bottom ── */
.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.05);
    padding: 1.25rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.footer-bottom p {
    font-size: 0.72rem;
    color: #333;
    margin: 0;
    text-align: center;
}

/* ── Responsive ── */
@media (max-width: 900px) {
    .footer-inner {
        grid-template-columns: 1fr 1fr;
    }

    .footer-brand { grid-column: 1 / -1; }
}

@media (max-width: 480px) {
    .footer-inner { grid-template-columns: 1fr; }
}
</style>