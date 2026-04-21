<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, watch } from 'vue'
import AchievementToast from '@/Components/AchievementToast.vue'
import AppFooter from '@/Components/AppFooter.vue'

const flashVisible = ref(false)

watch(() => usePage().props.flash?.success, (val) => {
    if (val) {
        flashVisible.value = true
        setTimeout(() => { flashVisible.value = false }, 3000)
    }
}, { immediate: true })

const page              = usePage()
const dropdownAbierto   = ref(false)
const menuMovilAbierto  = ref(false)
const tecnicasAbierto   = ref(false)
const scrolled          = ref(false)

const navLinks = [
    { name: 'Inicio',        href: '/home' },
    { name: 'Mis emociones', href: '/mis-emociones' },
    { name: 'Comunidad',     href: '/comunidad' },
    { name: 'Retos',         href: '/retos' },
    { name: 'Biblioteca',    href: '/biblioteca' },
]

const tecnicas = [
    { nombre: 'Respiración',       emoji: '🫁', href: '/respiracion' },
    { nombre: 'Meditación',        emoji: '🧘', href: '/meditacion' },
    { nombre: 'Sonidos',           emoji: '🎵', href: '/sonidos' },
    { nombre: 'Diario gratitud',   emoji: '📓', href: '/diario' },
    { nombre: 'EFT Tapping',       emoji: '👆', href: '/tapping' },
    { nombre: 'Visualización',     emoji: '🌈', href: '/visualizacion' },
    { nombre: 'Yoga suave',        emoji: '🤸', href: '/yoga' },
    { nombre: 'Journaling',        emoji: '📝', href: '/journaling' },
    { nombre: 'Infusiones',        emoji: '🍵', href: '/infusiones' },
    { nombre: 'Ejercicio',         emoji: '🏃', href: '/ejercicio' },
    { nombre: '5-4-3-2-1',        emoji: '🌍', href: '/tecnica-5-4-3-2-1' },
    { nombre: 'Autocompasión',     emoji: '💗', href: '/autocompasion' },
    { nombre: 'Musicoterapia',     emoji: '🎶', href: '/musicoterapia' },
    { nombre: 'Relajación muscular', emoji: '💆', href: '/relajacion-muscular' },
    { nombre: 'Gratitud visual',   emoji: '✨', href: '/gratitud-visual' },
]

const cerrarSesion = () => {
    cerrarTodo()
    router.post('/logout')
}

const navegarA = (href) => {
    cerrarTodo()
    router.visit(href)
}

const cerrarTodo = () => {
    dropdownAbierto.value  = false
    tecnicasAbierto.value  = false
    menuMovilAbierto.value = false
}

const clickFuera = (e) => {
    if (!document.getElementById('navbar-inner')?.contains(e.target)) {
        cerrarTodo()
    }
}

const onScroll = () => { scrolled.value = window.scrollY > 10 }

onMounted(() => {
    document.addEventListener('click', clickFuera)
    window.addEventListener('scroll', onScroll)
})

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
                    <span>HEART TO HEART</span>
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

                    <!-- Técnicas dropdown -->
                    <li class="tecnicas-li">
                        <button
                            class="nav-tecnicas-btn"
                            :class="{ activa: tecnicasAbierto }"
                            @click.stop="tecnicasAbierto = !tecnicasAbierto; dropdownAbierto = false"
                        >
                            🌿 Técnicas
                            <span class="nav-chevron" :class="{ rotado: tecnicasAbierto }">▾</span>
                        </button>

                        <Transition name="dropdown">
                            <div v-if="tecnicasAbierto" class="tecnicas-mega">
                                <div class="tecnicas-mega-grid">
                                    <Link
                                        v-for="tec in tecnicas"
                                        :key="tec.nombre"
                                        :href="tec.href"
                                        class="tec-mega-item"
                                        @click="cerrarTodo"
                                    >
                                        <span class="tmi-emoji">{{ tec.emoji }}</span>
                                        <span class="tmi-nombre">{{ tec.nombre }}</span>
                                    </Link>
                                </div>
                            </div>
                        </Transition>
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
                    <button class="hamburger" @click.stop="menuMovilAbierto = !menuMovilAbierto; cerrarTodo()">
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
                    <div class="mm-seccion">🌿 Técnicas</div>
                    <div class="mm-tecnicas">
                        <Link v-for="tec in tecnicas" :key="tec.nombre"
                            :href="tec.href" @click="cerrarTodo" class="mm-tec">
                            {{ tec.emoji }} {{ tec.nombre }}
                        </Link>
                    </div>
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

        <!-- Botón SOS flotante -->
        <Link href="/sos" class="btn-sos-flotante" title="Modo emergencia emocional">
            SOS
        </Link>

    </div>
</template>

<style scoped>
.app-wrapper { min-height: 100vh; background: #fff; }

/* ── Navbar ── */
.navbar {
    position: sticky;
    top: 0;
    z-index: 500;
    background: rgba(255,255,255,0.95);
    border-bottom: 1px solid transparent;
    transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
    backdrop-filter: blur(8px);
}

.navbar.scrolled {
    border-bottom-color: #f0f0f0;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
}

.navbar-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0.7rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* ── Logo ── */
.nav-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    flex-shrink: 0;
}

.nav-logo img  { width: 38px; height: auto; }
.nav-logo span { font-weight: 700; font-size: 0.82rem; color: #E63946; letter-spacing: 0.08em; }

/* ── Links ── */
.nav-links {
    display: flex;
    list-style: none;
    gap: 0.25rem;
    margin: 0;
    padding: 0;
    flex: 1;
    align-items: center;
}

.nav-links a {
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    color: #555;
    padding: 0.45rem 0.75rem;
    border-radius: 8px;
    transition: background 0.15s, color 0.15s;
    white-space: nowrap;
}

.nav-links a:hover { background: #f5f5f5; color: #2D2D2D; }
.nav-links a.activa { background: #E8FAF9; color: #4ECDC4; }

/* ── Técnicas dropdown ── */
.tecnicas-li { position: relative; }

.nav-tecnicas-btn {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #555;
    padding: 0.45rem 0.75rem;
    border-radius: 8px;
    border: none;
    background: none;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    white-space: nowrap;
    font-family: inherit;
}

.nav-tecnicas-btn:hover,
.nav-tecnicas-btn.activa { background: #E8FAF9; color: #4ECDC4; }

.nav-chevron {
    font-size: 0.7rem;
    transition: transform 0.2s;
    display: inline-block;
}

.nav-chevron.rotado { transform: rotate(180deg); }

/* ── Mega menu técnicas ── */
.tecnicas-mega {
    position: absolute;
    top: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    width: 480px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.12);
    border: 1px solid #f0f0f0;
    padding: 1rem;
    z-index: 600;
}

.tecnicas-mega-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.25rem;
}

.tec-mega-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 0.6rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 600;
    color: #2D2D2D;
    transition: background 0.15s, color 0.15s;
}

.tec-mega-item:hover { background: #E8FAF9; color: #4ECDC4; }
.tmi-emoji  { font-size: 1rem; flex-shrink: 0; }
.tmi-nombre { font-size: 0.8rem; }

/* ── Controles derecha ── */
.nav-derecha {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
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

.mm-seccion {
    font-size: 0.75rem;
    font-weight: 700;
    color: #aaa;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 0.5rem 0 0.25rem;
    margin-top: 0.25rem;
}

.mm-tecnicas {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.25rem;
}

.mm-tec {
    font-size: 0.82rem !important;
    padding: 0.3rem 0.5rem !important;
    background: #fafafa;
    border-radius: 8px;
}

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

/* ── Responsive ── */
@media (max-width: 960px) {
    .nav-links  { display: none; }
    .hamburger  { display: flex; }
    .nav-sos    { display: none; }
}

main { outline: none; min-height: calc(100vh - 60px); }

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
    white-space: nowrap;
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