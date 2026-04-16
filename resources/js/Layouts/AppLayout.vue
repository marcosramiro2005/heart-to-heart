<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import AchievementToast from '@/Components/AchievementToast.vue'
import AppFooter from '@/Components/AppFooter.vue'

const page = usePage()
const dropdownAbierto  = ref(false)
const menuMovilAbierto = ref(false)

const navLinks = [
    { name: 'Inicio',          href: '/home' },
    { name: 'Mis emociones',   href: '/mis-emociones' },
    { name: 'Biblioteca',      href: '/biblioteca' },
    { name: 'Recursos',        href: '/recursos' },
    { name: 'Comunidad',       href: '/comunidad' },
    { name: 'Mis logros',      href: '/logros' },
    { name: '¿Quiénes somos?', href: '/quienes-somos' },
]

const toggleDropdown = () => {
    dropdownAbierto.value  = !dropdownAbierto.value
    menuMovilAbierto.value = false
}

const cerrarDropdown = () => {
    dropdownAbierto.value = false
}

const toggleMovil = () => {
    menuMovilAbierto.value = !menuMovilAbierto.value
    dropdownAbierto.value  = false
}

const cerrarSesion = () => {
    cerrarDropdown()
    router.post('/logout')
}

const navegarA = (href) => {
    cerrarDropdown()
    router.visit(href)
}

const clickFuera = (e) => {
    const wrapper = document.getElementById('avatar-wrapper')
    if (wrapper && !wrapper.contains(e.target)) {
        dropdownAbierto.value = false
    }
}

onMounted(()  => document.addEventListener('click', clickFuera))
onUnmounted(() => document.removeEventListener('click', clickFuera))
</script>

<template>
    <div class="app-wrapper">

        <nav class="navbar">
            <div class="navbar-inner">

                <Link href="/home" class="nav-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </Link>

                <ul class="nav-links">
                    <li v-for="link in navLinks" :key="link.name">
                        <Link :href="link.href">{{ link.name }}</Link>
                    </li>
                </ul>

                <div class="nav-derecha">

                    <div id="avatar-wrapper" class="avatar-wrapper">
                        <button class="nav-avatar" @click.stop="toggleDropdown">
                            {{ page.props.auth?.user?.avatar || '👤' }}
                        </button>

                        <Transition name="dropdown">
                            <div v-if="dropdownAbierto" class="avatar-dropdown">
                                <div class="dd-header">
                                    <span class="dd-avatar">{{ page.props.auth?.user?.avatar || '👤' }}</span>
                                    <div class="dd-info">
                                        <span class="dd-nombre">{{ page.props.auth?.user?.name }}</span>
                                        <span class="dd-email">{{ page.props.auth?.user?.email }}</span>
                                    </div>
                                </div>
                                <div class="dd-divider"></div>
                                <button class="dd-item" @click="navegarA('/perfil')">
                                    👤 Mi perfil
                                </button>
                                <button class="dd-item" @click="navegarA('/mis-emociones')">
                                    📊 Mis emociones
                                </button>
                                <button class="dd-item" @click="navegarA('/logros')">
                                    🏆 Mis logros
                                </button>
                                <button class="dd-item" @click="navegarA('/hearty')">
                                    💬 Hablar con Hearty
                                </button>
                                <div class="dd-divider"></div>
                                <button class="dd-item dd-logout" @click="cerrarSesion">
                                    🚪 Cerrar sesión
                                </button>
                            </div>
                        </Transition>
                    </div>

                    <button class="hamburger" @click.stop="toggleMovil">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>

                </div>
            </div>

            <Transition name="slide">
                <div v-if="menuMovilAbierto" class="mobile-menu">
                    <Link
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="link.href"
                        @click="menuMovilAbierto = false"
                    >
                        {{ link.name }}
                    </Link>
                    <div class="mm-divider"></div>
                    <Link href="/perfil" @click="menuMovilAbierto = false">👤 Mi perfil</Link>
                    <button class="mm-logout" @click="cerrarSesion">🚪 Cerrar sesión</button>
                </div>
            </Transition>
        </nav>

        <main>
            <slot />
        </main>

        <AppFooter />
        <AchievementToast />

    </div>
</template>

<style scoped>
.app-wrapper {
    min-height: 100vh;
    background: #fff;
}

.navbar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
    box-shadow: 0 1px 8px rgba(0,0,0,0.06);
}

.navbar-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0.75rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.nav-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    flex-shrink: 0;
}

.nav-logo img  { width: 42px; height: auto; }
.nav-logo span {
    font-weight: 700;
    font-size: 0.85rem;
    color: #E63946;
    letter-spacing: 0.08em;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 1.5rem;
    margin: 0;
    padding: 0;
    flex: 1;
    justify-content: center;
}

.nav-links a {
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 600;
    color: #2D2D2D;
    transition: color 0.2s;
    white-space: nowrap;
}

.nav-links a:hover { color: #4ECDC4; }

.nav-derecha {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.avatar-wrapper {
    position: relative;
}

.nav-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #E8FAF9;
    border: 2px solid #4ECDC4;
    font-size: 1.2rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, box-shadow 0.2s;
    line-height: 1;
}

.nav-avatar:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 12px rgba(78,205,196,0.35);
}

.avatar-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 240px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.14);
    border: 1px solid #f0f0f0;
    z-index: 999;
    overflow: hidden;
}

.dd-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #E8FAF9;
}

.dd-avatar {
    font-size: 1.6rem;
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid #4ECDC4;
}

.dd-info {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    min-width: 0;
}

.dd-nombre {
    font-weight: 700;
    font-size: 0.88rem;
    color: #2D2D2D;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dd-email {
    font-size: 0.72rem;
    color: #888;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dd-divider { height: 1px; background: #f0f0f0; }

.dd-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.8rem 1rem;
    font-size: 0.88rem;
    font-weight: 600;
    color: #2D2D2D;
    cursor: pointer;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    font-family: inherit;
    transition: background 0.15s, color 0.15s;
}

.dd-item:hover { background: #f5f5f5; color: #4ECDC4; }
.dd-logout     { color: #E63946; }
.dd-logout:hover { background: #fff5f5; color: #E63946; }

.dropdown-enter-active, .dropdown-leave-active {
    transition: opacity 0.15s, transform 0.15s;
}
.dropdown-enter-from, .dropdown-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

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

.mobile-menu {
    display: flex;
    flex-direction: column;
    padding: 0.75rem 1.5rem 1rem;
    gap: 0.4rem;
    border-top: 1px solid #f0f0f0;
    background: white;
}

.mobile-menu a {
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 600;
    color: #2D2D2D;
    padding: 0.4rem 0;
    transition: color 0.2s;
}

.mobile-menu a:hover { color: #4ECDC4; }

.mm-divider { height: 1px; background: #f0f0f0; margin: 0.3rem 0; }

.mm-logout {
    background: none;
    border: none;
    text-align: left;
    font-size: 0.95rem;
    font-weight: 600;
    color: #E63946;
    cursor: pointer;
    padding: 0.4rem 0;
    font-family: inherit;
}

.slide-enter-active, .slide-leave-active {
    transition: opacity 0.2s, transform 0.2s;
}
.slide-enter-from, .slide-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

@media (max-width: 900px) {
    .nav-links  { display: none; }
    .hamburger  { display: flex; }
}
</style>