<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="error-page">
        <div class="error-bg">
            <div class="eb-circle c1"></div>
            <div class="eb-circle c2"></div>
        </div>
        <div class="error-card">
            <div class="ec-logo">
                <img src="/images/logo.png" alt="Heart to Heart" />
                <span>HEART TO HEART</span>
            </div>
            <div class="ec-num">404</div>
            <h1>Página no encontrada</h1>
            <p>La página que buscas no existe o ha sido movida. No te preocupes, volvemos al inicio.</p>
            <Link href="/home" class="btn-volver">
                💚 Volver al inicio
            </Link>
        </div>
    </div>
</template>

<style scoped>
.error-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f0fffe, #e8f8f5, #ffeef0);
    position: relative;
    overflow: hidden;
}

.error-bg { position: absolute; inset: 0; pointer-events: none; }
.eb-circle { position: absolute; border-radius: 50%; opacity: 0.1; }
.eb-circle.c1 { width: 400px; height: 400px; background: #4ECDC4; top: -100px; right: -80px; }
.eb-circle.c2 { width: 300px; height: 300px; background: #E63946; bottom: -80px; left: -60px; }

.error-card {
    background: white;
    border-radius: 24px;
    padding: 3rem 2.5rem;
    max-width: 440px;
    width: 100%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.ec-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ec-logo img  { width: 32px; }
.ec-logo span { font-weight: 700; font-size: 0.8rem; color: #E63946; letter-spacing: 0.08em; }

.ec-num {
    font-size: 5rem;
    font-weight: 900;
    color: #4ECDC4;
    opacity: 0.3;
    line-height: 1;
}

.error-card h1 { font-size: 1.5rem; font-weight: 800; color: #2D2D2D; margin: 0; }
.error-card p  { font-size: 0.95rem; color: #666; line-height: 1.6; margin: 0; max-width: 340px; }

.btn-volver {
    padding: 0.9rem 2rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    transition: background 0.2s, transform 0.2s;
    margin-top: 0.5rem;
}

.btn-volver:hover { background: #3BAFA7; transform: translateY(-2px); }
</style>