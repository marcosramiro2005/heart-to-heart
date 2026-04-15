<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="error-wrapper">
        <div class="error-card">
            <div class="error-emoji">💔</div>
            <h1>404</h1>
            <h2>Página no encontrada</h2>
            <p>La página que buscas no existe o ha sido movida.</p>
            <div class="error-acciones">
                <Link href="/home" class="btn-home">🏠 Ir al inicio</Link>
                <Link href="/hearty" class="btn-hearty">💬 Hablar con Hearty</Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.error-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-bg);
    padding: 2rem;
}

.error-card {
    text-align: center;
    background: var(--color-bg-card);
    border-radius: 20px;
    padding: 3rem 2.5rem;
    border: 1px solid var(--color-border);
    max-width: 420px;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.error-emoji { font-size: 4rem; }

h1 {
    font-size: 4rem;
    font-weight: 900;
    color: var(--color-primary);
    margin: 0;
    line-height: 1;
}

h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--color-text);
    margin: 0;
}

p {
    color: var(--color-text-muted);
    font-size: 0.95rem;
    margin: 0;
}

.error-acciones {
    display: flex;
    gap: 0.75rem;
    margin-top: 0.5rem;
    flex-wrap: wrap;
    justify-content: center;
}

.btn-home {
    padding: 0.75rem 1.5rem;
    background: var(--color-primary);
    color: white;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 700;
    transition: background 0.2s;
}

.btn-home:hover { background: var(--color-primary-dark); }

.btn-hearty {
    padding: 0.75rem 1.5rem;
    background: var(--color-bg-secondary);
    color: var(--color-primary);
    border-radius: 25px;
    text-decoration: none;
    font-weight: 700;
    border: 2px solid var(--color-primary);
    transition: background 0.2s;
}

.btn-hearty:hover {
    background: var(--color-primary-light);
}
</style>