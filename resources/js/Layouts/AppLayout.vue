<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const menuOpen = ref(false)

const navLinks = [
    { name: 'Inicio', href: '/home' },
    { name: 'Recursos', href: '/recursos' },
    { name: 'Comunidad', href: '/comunidad' },
    { name: '¿Quiénes somos?', href: '/quienes-somos' },
]
</script>

<template>
    <div class="app-wrapper">

        <!-- Navbar -->
        <nav class="navbar">
            <div class="navbar-inner">
                <!-- Logo -->
                <Link href="/home" class="nav-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </Link>

                <!-- Links escritorio -->
                <ul class="nav-links">
                    <li v-for="link in navLinks" :key="link.name">
                        <Link :href="link.href">{{ link.name }}</Link>
                    </li>
                </ul>

                <!-- Menú hamburguesa móvil -->
                <button class="hamburger" @click="menuOpen = !menuOpen">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <!-- Menú móvil desplegable -->
            <div v-if="menuOpen" class="mobile-menu">
                <Link
                    v-for="link in navLinks"
                    :key="link.name"
                    :href="link.href"
                    @click="menuOpen = false"
                >
                    {{ link.name }}
                </Link>
            </div>
        </nav>

        <!-- Contenido de la página -->
        <main>
            <slot />
        </main>

    </div>
</template>

<style scoped>
.app-wrapper {
    min-height: 100vh;
    background: #fff;
}

/* ── Navbar ── */
.navbar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
    box-shadow: 0 1px 8px rgba(0,0,0,0.06);
}

.navbar-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0.75rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.nav-logo img {
    width: 42px;
    height: auto;
}

.nav-logo span {
    font-weight: 700;
    font-size: 0.85rem;
    color: #E63946;
    letter-spacing: 0.08em;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 2rem;
    margin: 0;
    padding: 0;
}

.nav-links a {
    text-decoration: none;
    font-size: 0.92rem;
    font-weight: 600;
    color: #2D2D2D;
    transition: color 0.2s;
}

.nav-links a:hover {
    color: #4ECDC4;
}

/* ── Hamburguesa ── */
.hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
}

.hamburger span {
    display: block;
    width: 24px;
    height: 2px;
    background: #2D2D2D;
    border-radius: 2px;
}

/* ── Menú móvil ── */
.mobile-menu {
    display: flex;
    flex-direction: column;
    padding: 0.5rem 1.5rem 1rem;
    gap: 0.75rem;
    border-top: 1px solid #f0f0f0;
}

.mobile-menu a {
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 600;
    color: #2D2D2D;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .nav-links { display: none; }
    .hamburger { display: flex; }
}
</style>