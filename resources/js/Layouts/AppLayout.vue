<script setup>
// Layout principal que envuelve todas las páginas autenticadas de la app.
// Proporciona: barra de navegación, menú móvil, flash messages y pie de página.
// Todas las páginas de /Pages que usen <AppLayout> heredan esta estructura.

import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, watch } from 'vue'
import AchievementToast from '@/Components/AchievementToast.vue'
import AppFooter from '@/Components/AppFooter.vue'

// Controla si el mensaje flash de éxito está visible
const flashVisible = ref(false)

// Observa el prop flash.success compartido desde HandleInertiaRequests.
// Cuando cambia (se produce una redirección con ->with('success',...)), muestra
// el mensaje 3 segundos y lo oculta automáticamente.
watch(() => usePage().props.flash?.success, (val) => {
    if (val) {
        flashVisible.value = true
        setTimeout(() => { flashVisible.value = false }, 3000)
    }
}, { immediate: true }) // immediate: true para evaluar el valor inicial al montar el componente

const page = usePage() // acceso reactivo a los props compartidos de Inertia (auth, flash, etc.)

// Estados de apertura/cierre de los menús desplegables
const dropdownAbierto   = ref(false) // dropdown del avatar de usuario
const menuMovilAbierto  = ref(false) // menú hamburguesa en móvil
const scrolled          = ref(false) // true cuando se hace scroll, para aplicar sombra al navbar

// Links principales de la barra de navegación superior
const navLinks = [
    { name: 'Inicio',        href: '/home' },
    { name: 'Mis emociones', href: '/mis-emociones' },
    { name: 'Comunidad',     href: '/comunidad' },
    { name: 'Retos',         href: '/retos' },
    { name: 'Biblioteca',    href: '/biblioteca' },
]

// Cierra todos los menús y hace logout mediante POST (Inertia gestiona el token CSRF automáticamente)
const cerrarSesion = () => {
    cerrarTodo()
    router.post('/logout')
}

// Cierra todos los menús y navega a la ruta indicada usando Inertia (SPA, sin recarga completa)
const navegarA = (href) => {
    cerrarTodo()
    router.visit(href)
}

// Cierra todos los menús desplegables a la vez (usada antes de navegar o al hacer click fuera)
const cerrarTodo = () => {
    dropdownAbierto.value  = false
    menuMovilAbierto.value = false
}

// Cierra todos los menús cuando el usuario hace click fuera de la barra de navegación
const clickFuera = (e) => {
    if (!document.getElementById('navbar-inner')?.contains(e.target)) {
        cerrarTodo()
    }
}

// Detecta si el usuario ha hecho scroll más de 10px para aplicar sombra al navbar
const onScroll = () => { scrolled.value = window.scrollY > 10 }

// Registrar los listeners globales al montar el componente
onMounted(() => {
    document.addEventListener('click', clickFuera)
    window.addEventListener('scroll', onScroll)
})

// Limpiar los listeners al desmontar para evitar memory leaks
onUnmounted(() => {
    document.removeEventListener('click', clickFuera)
    window.removeEventListener('scroll', onScroll)
})
</script>

<template>
    <div class="app-wrapper">

        <nav class="navbar" :class="{ scrolled }">
            <div class="navbar-inner" id="navbar-inner">

                <!-- Logo -->
                <Link href="/home" class="nav-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <div class="logo-text">
                        <span class="logo-title">Heart to Heart</span>
                        <span class="logo-tagline">Bienestar emocional</span>
                    </div>
                </Link>

                <!-- Links principales -->
                <ul class="nav-links">
                    <li v-for="link in navLinks" :key="link.name">
                        <Link
                            :href="link.href"
                            :class="{ activa: $page.url.startsWith(link.href) }"
                        >
                            {{ link.name }}
                        </Link>
                    </li>

                </ul>

                <!-- Controles derecha -->
                <div class="nav-derecha">

                    <!-- Botón SOS -->
                    <Link href="/sos" class="nav-sos" title="Modo emergencia">
                        SOS
                    </Link>

                    <!-- Avatar dropdown -->
                    <div class="avatar-wrapper">
                        <button class="nav-avatar" @click.stop="dropdownAbierto = !dropdownAbierto; tecnicasAbierto = false">
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
                                <button class="dd-item" @click="navegarA('/perfil')">👤 Mi perfil</button>
                                <button class="dd-item" @click="navegarA('/mis-emociones')">📊 Mis emociones</button>
                                <button class="dd-item" @click="navegarA('/logros')">🏆 Mis logros</button>
                                <button class="dd-item" @click="navegarA('/retos')">🎯 Mis retos</button>
                                <button class="dd-item" @click="navegarA('/quienes-somos')">💚 ¿Quiénes somos?</button>
                                <button class="dd-item" @click="navegarA('/test-bienestar')">🧠 Test de bienestar</button>
                                <button class="dd-item" @click="navegarA('/mi-plan')">🌱 Mi plan semanal</button>
                                <button class="dd-item" @click="navegarA('/focus')">🎯 Modo Focus</button>
                                <div class="dd-divider"></div>
                                <button class="dd-item dd-logout" @click="cerrarSesion">🚪 Cerrar sesión</button>
                            </div>
                        </Transition>
                    </div>

                    <!-- Hamburguesa móvil -->
                    <button class="hamburger" @click.stop="menuMovilAbierto = !menuMovilAbierto; dropdownAbierto = false">
                        <span :class="{ open: menuMovilAbierto }"></span>
                        <span :class="{ open: menuMovilAbierto }"></span>
                        <span :class="{ open: menuMovilAbierto }"></span>
                    </button>
                </div>
            </div>

            <!-- Menú móvil -->
            <Transition name="slide">
                <div v-if="menuMovilAbierto" class="mobile-menu">
                    <Link v-for="link in navLinks" :key="link.name"
                        :href="link.href" @click="cerrarTodo">
                        {{ link.name }}
                    </Link>
                    <div class="mm-divider"></div>
                    <Link href="/perfil" @click="cerrarTodo">👤 Mi perfil</Link>
                    <Link href="/sos" @click="cerrarTodo">🆘 Modo SOS</Link>
                    <button class="mm-logout" @click="cerrarSesion">🚪 Cerrar sesión</button>
                </div>
            </Transition>
        </nav>

        <main>
            <slot />
        </main>

        <Transition name="flash">
    <div v-if="flashVisible && $page.props.flash?.success" class="flash-msg flash-success">
        ✅ {{ $page.props.flash.success }}
    </div>
</Transition>

<Transition name="flash">
    <div v-if="$page.props.flash?.error" class="flash-msg flash-error">
        ⚠️ {{ $page.props.flash.error }}
    </div>
</Transition>

        <AppFooter />
        <AchievementToast />

    </div>
</template>

<style scoped>
.app-wrapper { min-height: 100vh; background: #fff; }

/* ── Navbar ── */
.navbar {
    position: sticky;
    top: 0;
    z-index: 500;
    background: rgba(255,255,255,0.97);
    border-bottom: 1px solid transparent;
    transition: border-color 0.3s, box-shadow 0.3s;
    backdrop-filter: blur(12px);
}

.navbar.scrolled {
    border-bottom-color: #ebebeb;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
}

.navbar-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1.5rem;
    height: 70px;
    display: flex;
    align-items: center;
    gap: 0;
}

/* ── Logo ── */
.nav-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    flex-shrink: 0;
    padding-right: 1.75rem;
    margin-right: 1rem;
    border-right: 1.5px solid #eee;
}

.nav-logo img {
    width: 56px;
    height: 56px;
    object-fit: contain;
    flex-shrink: 0;
}

.logo-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
    gap: 1px;
}

.logo-title {
    font-weight: 800;
    font-size: 1.05rem;
    color: #E63946;
    letter-spacing: 0.01em;
    white-space: nowrap;
}

.logo-tagline {
    font-size: 0.6rem;
    font-weight: 700;
    color: #4ECDC4;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    white-space: nowrap;
}

/* ── Links ── */
.nav-links {
    display: flex;
    list-style: none;
    gap: 0.1rem;
    margin: 0;
    padding: 0;
    flex: 1;
    align-items: center;
    height: 100%;
}

.nav-links a {
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    color: #555;
    padding: 0.5rem 0.85rem;
    border-radius: 8px;
    transition: background 0.15s, color 0.15s;
    white-space: nowrap;
    position: relative;
}

.nav-links a:hover { background: #f7f7f7; color: #2D2D2D; }
.nav-links a.activa {
    background: #edfaf9;
    color: #3ab8b0;
    box-shadow: inset 0 0 0 1px rgba(78,205,196,0.18);
}

/* ── Controles derecha ── */
.nav-derecha {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
    margin-left: auto;
}

/* ── Botón SOS navbar ── */
.nav-sos {
    padding: 0.4rem 0.9rem;
    background: #E63946;
    color: white;
    font-weight: 800;
    font-size: 0.78rem;
    border-radius: 20px;
    text-decoration: none;
    letter-spacing: 0.05em;
    transition: background 0.2s, transform 0.2s;
}

.nav-sos:hover { background: #c0303b; transform: scale(1.05); }

/* ── Avatar ── */
.avatar-wrapper { position: relative; }

.nav-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #E8FAF9;
    border: 2px solid #4ECDC4;
    font-size: 1.1rem;
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

/* ── Dropdown avatar ── */
.avatar-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 230px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.14);
    border: 1px solid #f0f0f0;
    z-index: 600;
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
    font-size: 1.5rem;
    width: 38px;
    height: 38px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid #4ECDC4;
}

.dd-info { display: flex; flex-direction: column; min-width: 0; }
.dd-nombre { font-weight: 700; font-size: 0.85rem; color: #2D2D2D; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dd-email  { font-size: 0.7rem; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.dd-divider { height: 1px; background: #f0f0f0; }

.dd-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.7rem 1rem;
    font-size: 0.85rem;
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

.dd-item:hover  { background: #f5f5f5; color: #4ECDC4; }
.dd-logout      { color: #E63946; }
.dd-logout:hover { background: #fff5f5; color: #E63946; }

/* ── Animaciones ── */
.dropdown-enter-active, .dropdown-leave-active { transition: opacity 0.15s, transform 0.15s; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-6px); }

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
    transition: all 0.3s;
}

/* ── Menú móvil ── */
.mobile-menu {
    display: flex;
    flex-direction: column;
    padding: 0.75rem 1.5rem 1.25rem;
    gap: 0.4rem;
    border-top: 1px solid #f0f0f0;
    background: white;
    max-height: 70vh;
    overflow-y: auto;
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

.mm-divider { height: 1px; background: #f0f0f0; margin: 0.25rem 0; }

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

.slide-enter-active, .slide-leave-active { transition: opacity 0.2s, transform 0.2s; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-8px); }

/* ── Botón SOS flotante ── */
.btn-sos-flotante {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    padding: 0.6rem 1.1rem;
    background: #E63946;
    color: white;
    font-weight: 800;
    font-size: 0.85rem;
    border-radius: 25px;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(230,57,70,0.4);
    z-index: 400;
    letter-spacing: 0.05em;
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-sos-flotante:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 6px 24px rgba(230,57,70,0.5);
}

/* ── Hamburguesa animación X ── */
.hamburger span:nth-child(1).open { transform: rotate(45deg) translate(5px, 5px); }
.hamburger span:nth-child(2).open { opacity: 0; transform: scaleX(0); }
.hamburger span:nth-child(3).open { transform: rotate(-45deg) translate(5px, -5px); }

/* ── Avatar dropdown: no salir de pantalla en móvil ── */
@media (max-width: 420px) {
    .avatar-dropdown {
        right: -1rem;
        width: calc(100vw - 2rem);
        max-width: 280px;
    }
}

/* ── Responsive ── */
@media (max-width: 960px) {
    .nav-links  { display: none; }
    .hamburger  { display: flex; }
    .nav-sos    { display: none; }
    .nav-logo   { padding-right: 0; border-right: none; margin-right: 0; }
}

/* ── Botón SOS flotante: más pequeño en móvil ── */
@media (max-width: 480px) {
    .btn-sos-flotante {
        bottom: 1.25rem;
        right: 1.25rem;
        padding: 0.5rem 0.9rem;
        font-size: 0.78rem;
    }
    .navbar-inner {
        padding: 0 1rem;
        height: 62px;
    }
    .nav-logo { padding-right: 0; border-right: none; margin-right: 0; }
    .logo-tagline { display: none; }
    .nav-logo img { width: 46px; height: 46px; }
}

main { outline: none; min-height: calc(100vh - 70px); }

/* ── Flash messages ── */
.flash-msg {
    position: fixed;
    bottom: 5.5rem;
    left: 50%;
    transform: translateX(-50%);
    padding: 0.85rem 1.75rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 0.92rem;
    z-index: 800;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    white-space: normal;
    max-width: min(90vw, 420px);
    text-align: center;
    word-break: break-word;
}

.flash-success {
    background: #4ECDC4;
    color: white;
}

.flash-error {
    background: #E63946;
    color: white;
}

.flash-enter-active, .flash-leave-active {
    transition: opacity 0.3s, transform 0.3s;
}

.flash-enter-from, .flash-leave-to {
    opacity: 0;
    transform: translateX(-50%) translateY(20px);
}
</style>