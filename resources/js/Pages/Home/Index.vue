<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    auth: Object,
})

const tecnicas = [
    { id: 'respiracion',       nombre: 'Respiración',          color: '#d4edda', icon: '🫁', ruta: '/respiracion' },
    { id: 'meditacion',        nombre: 'Meditación',            color: '#e8d5f5', icon: '🧘', ruta: '/meditacion' },
    { id: 'sonidos',           nombre: 'Sonidos',               color: '#d0eaf8', icon: '🎵', ruta: '/sonidos' },
    { id: 'diario',            nombre: 'Diario gratitud',       color: '#fff9c4', icon: '📓', ruta: '/diario' },
    { id: 'tapping',           nombre: 'EFT Tapping',           color: '#ffecd2', icon: '👆', ruta: '/tapping' },
    { id: 'visualizacion',     nombre: 'Visualización',         color: '#ffd5e5', icon: '🌈', ruta: '/visualizacion' },
    { id: 'yoga',              nombre: 'Yoga suave',            color: '#d4f5e9', icon: '🤸', ruta: '/yoga' },
    { id: 'journaling',        nombre: 'Journaling',            color: '#e8f4f8', icon: '📝', ruta: '/journaling' },
    { id: 'infusiones',        nombre: 'Infusiones',            color: '#e8d5f5', icon: '🍵', ruta: '/infusiones' },
    { id: 'ejercicio',         nombre: 'Ejercicio',             color: '#ffd5d5', icon: '🏃', ruta: '/ejercicio' },
    { id: 'grounding',         nombre: '5-4-3-2-1',             color: '#d0eaf8', icon: '🌍', ruta: '/tecnica-5-4-3-2-1' },
    { id: 'autocompasion',     nombre: 'Autocompasión',         color: '#fce4ec', icon: '💗', ruta: '/autocompasion' },
    { id: 'musicoterapia',     nombre: 'Musicoterapia',         color: '#e8eaf6', icon: '🎶', ruta: '/musicoterapia' },
    { id: 'relajacion',        nombre: 'Relajación muscular',   color: '#e0f2f1', icon: '💆', ruta: '/relajacion-muscular' },
    { id: 'gratitud_visual',   nombre: 'Gratitud visual',       color: '#fff8e1', icon: '✨', ruta: '/gratitud-visual' },
]

const accesosRapidos = [
    { nombre: 'Mis emociones',  icon: '📊', ruta: '/mis-emociones',  color: '#E8FAF9', border: '#4ECDC4' },
    { nombre: 'Hablar con Hearty', icon: '💬', ruta: '/hearty',      color: '#E8FAF9', border: '#4ECDC4' },
    { nombre: 'Comunidad',      icon: '👥', ruta: '/comunidad',       color: '#fce4ec', border: '#f48fb1' },
    { nombre: 'Mis logros',     icon: '🏆', ruta: '/logros',          color: '#fff9c4', border: '#FFD700' },
    { nombre: 'Biblioteca',     icon: '📚', ruta: '/biblioteca',      color: '#e8eaf6', border: '#9B8EC4' },
    { nombre: 'Retos',          icon: '🎯', ruta: '/retos',           color: '#d4edda', border: '#4ECDC4' },
]

const frases = [
    'Cuidarte no es egoísta, es necesario. 💚',
    'El progreso, no la perfección, es lo que importa. 🌱',
    'Cada día que eliges seguir adelante es una victoria. 🏆',
    'Mereces el mismo cuidado que das a los demás. 🌸',
    'Un paso a la vez. Solo el siguiente paso. 👣',
    'Tu bienestar emocional importa de verdad. 💙',
]

const fraseHoy = ref(frases[new Date().getDay() % frases.length])

const horaDelDia = new Date().getHours()
const saludo = horaDelDia < 12 ? 'Buenos días' : horaDelDia < 20 ? 'Buenas tardes' : 'Buenas noches'
</script>

<template>
    <AppLayout>
        <div class="home-wrapper">

            <!-- ── Hero personalizado ── -->
            <section class="home-hero">
                <div class="hero-bg-circles">
                    <div class="hbc c1"></div>
                    <div class="hbc c2"></div>
                </div>
                <div class="hero-content">
                    <div class="hero-saludo">
                        {{ saludo }}, <strong>{{ $page.props.auth?.user?.name?.split(' ')[0] }}</strong> {{ $page.props.auth?.user?.avatar || '👤' }}
                    </div>
                    <h1>¿Cómo te sientes hoy?</h1>
                    <p class="hero-frase">{{ fraseHoy }}</p>
                    <div class="hero-ctas">
                        <Link href="/hearty" class="btn-hearty-hero">
                            💬 Hablar con Hearty
                        </Link>
                        <Link href="/mis-emociones" class="btn-emociones-hero">
                            📊 Registrar emoción
                        </Link>
                    </div>
                </div>
                <div class="hero-imagen">
                    <img src="/images/hero.png" alt="Bienestar" onerror="this.style.display='none'" />
                    <div class="hero-card-flotante">
                        <span>💙</span>
                        <span>Hearty está aquí para ti</span>
                    </div>
                </div>
            </section>

            <!-- ── Técnicas ── -->
            <section class="tecnicas-section">
                <div class="sec-header">
                    <h2 class="sec-titulo">🌿 Técnicas de bienestar</h2>
                    <p class="sec-sub">{{ tecnicas.length }} técnicas disponibles para cada momento</p>
                </div>
                <div class="tecnicas-grid">
                    <Link
                        v-for="tec in tecnicas"
                        :key="tec.id"
                        :href="tec.ruta"
                        class="tecnica-card"
                        :style="{ backgroundColor: tec.color }"
                    >
                        <span class="tc-icon">{{ tec.icon }}</span>
                        <span class="tc-nombre">{{ tec.nombre }}</span>
                    </Link>
                </div>
            </section>

            <!-- ── SOS Banner ── -->
            <section class="sos-banner">
                <div class="sos-banner-content">
                    <span class="sb-emoji">🆘</span>
                    <div>
                        <h3>¿Estás pasando un momento difícil?</h3>
                        <p>El modo SOS tiene técnicas de emergencia para ayudarte ahora mismo.</p>
                    </div>
                    <Link href="/sos" class="btn-sos-banner">Ir al modo SOS →</Link>
                </div>
            </section>

            <!-- ── Secciones extra ── -->
            <section class="extras-grid">
                <Link href="/comunidad" class="extra-card comunidad">
                    <span class="ec-icon">👥</span>
                    <h3>Foro comunitario</h3>
                    <p>Conecta con personas que entienden lo que sientes</p>
                    <span class="ec-link">Explorar →</span>
                </Link>
                <Link href="/biblioteca" class="extra-card biblioteca">
                    <span class="ec-icon">📚</span>
                    <h3>Biblioteca de recursos</h3>
                    <p>Artículos y guías sobre salud mental escritos para ti</p>
                    <span class="ec-link">Explorar →</span>
                </Link>
                <Link href="/retos" class="extra-card retos">
                    <span class="ec-icon">🎯</span>
                    <h3>Retos de bienestar</h3>
                    <p>Construye hábitos saludables con retos de 7 y 30 días</p>
                    <span class="ec-link">Explorar →</span>
                </Link>
            </section>

        </div>
    </AppLayout>
</template>

<style scoped>
.home-wrapper {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1.5rem 3rem;
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

/* ── Hero ── */
.home-hero {
    background: linear-gradient(135deg, #E8FAF9 0%, #f0fffe 60%, #ffeef0 100%);
    border-radius: 24px;
    padding: 3rem 2.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    position: relative;
    margin-top: 1.5rem;
    /* SIN overflow: hidden */
}

.hero-bg-circles { position: absolute; inset: 0; pointer-events: none; }
.hbc { position: absolute; border-radius: 50%; opacity: 0.12; }
.hbc.c1 { width: 300px; height: 300px; background: #4ECDC4; top: -80px; right: -60px; }
.hbc.c2 { width: 200px; height: 200px; background: #E63946; bottom: -60px; left: -40px; }

.hero-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    position: relative;
    z-index: 1;
}

.hero-saludo {
    font-size: 1rem;
    color: #555;
}

.hero-saludo strong { color: #2D2D2D; }

.hero-content h1 {
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    font-weight: 900;
    color: #1a1a1a;
    margin: 0;
    line-height: 1.2;
}

.hero-frase {
    font-size: 0.95rem;
    color: #555;
    font-style: italic;
    margin: 0;
    line-height: 1.5;
}

.hero-ctas { display: flex; gap: 0.75rem; flex-wrap: wrap; }

.btn-hearty-hero {
    padding: 0.85rem 1.75rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.95rem;
    transition: background 0.2s, transform 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.btn-hearty-hero:hover { background: #3BAFA7; transform: translateY(-2px); }

.btn-emociones-hero {
    padding: 0.85rem 1.75rem;
    background: white;
    color: #4ECDC4;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.95rem;
    border: 2px solid #4ECDC4;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.btn-emociones-hero:hover { background: #4ECDC4; color: white; }

.hero-imagen {
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.hero-imagen img {
    width: 220px;
    height: auto;
    border-radius: 16px;
}

.hero-card-flotante {
    position: absolute;
    bottom: -15px;
    right: 10px;
    background: white;
    border-radius: 12px;
    padding: 0.6rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #2D2D2D;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 0.4rem;
    animation: float 3s ease-in-out infinite;
    white-space: nowrap;
    z-index: 10;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-6px); }
}

/* ── Sección título ── */
.sec-titulo { font-size: 1.1rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.sec-sub    { font-size: 0.85rem; color: #888; margin: 0.25rem 0 0; }
.sec-header { display: flex; flex-direction: column; gap: 0.25rem; }

/* ── Accesos rápidos ── */
.accesos-section { display: flex; flex-direction: column; gap: 1rem; }

.accesos-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0.75rem;
}

.acceso-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    padding: 1rem 0.5rem;
    border-radius: 14px;
    border: 2px solid transparent;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    text-align: center;
}

.acceso-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }

.ac-icon   { font-size: 1.6rem; }
.ac-nombre { font-size: 0.78rem; font-weight: 700; color: #2D2D2D; }

/* ── Técnicas ── */
.tecnicas-section { display: flex; flex-direction: column; gap: 1rem; }

.tecnicas-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.75rem;
}

.tecnica-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.25rem 0.75rem;
    border-radius: 14px;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    text-align: center;
}

.tecnica-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }

.tc-icon   { font-size: 1.8rem; }
.tc-nombre { font-size: 0.82rem; font-weight: 700; color: #2D2D2D; }

/* ── SOS Banner ── */
.sos-banner {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 16px;
    padding: 1.5rem 2rem;
}

.sos-banner-content {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
}

.sb-emoji { font-size: 2.5rem; flex-shrink: 0; }

.sos-banner-content h3 {
    font-size: 1rem;
    font-weight: 700;
    color: white;
    margin: 0 0 0.2rem;
}

.sos-banner-content p {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.65);
    margin: 0;
}

.sos-banner-content div { flex: 1; }

.btn-sos-banner {
    padding: 0.75rem 1.5rem;
    background: #E63946;
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    white-space: nowrap;
    transition: background 0.2s, transform 0.2s;
    flex-shrink: 0;
}

.btn-sos-banner:hover { background: #c0303b; transform: translateY(-2px); }

/* ── Extras ── */
.extras-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.extra-card {
    border-radius: 18px;
    padding: 1.75rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.extra-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

.extra-card.comunidad  { background: #fce4ec; }
.extra-card.biblioteca { background: #e8eaf6; }
.extra-card.retos      { background: #E8FAF9; }

.ec-icon { font-size: 2rem; }

.extra-card h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
}

.extra-card p {
    font-size: 0.85rem;
    color: #555;
    line-height: 1.5;
    margin: 0;
    flex: 1;
}

.ec-link {
    font-size: 0.85rem;
    font-weight: 700;
    color: #4ECDC4;
    margin-top: auto;
}

/* ── Responsive ── */
@media (max-width: 1000px) {
    .accesos-grid  { grid-template-columns: repeat(3, 1fr); }
    .tecnicas-grid { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 768px) {
    .home-hero     { flex-direction: column; text-align: center; }
    .hero-ctas     { justify-content: center; }
    .hero-imagen   { display: none; }
    .accesos-grid  { grid-template-columns: repeat(3, 1fr); }
    .tecnicas-grid { grid-template-columns: repeat(3, 1fr); }
    .extras-grid   { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
    .accesos-grid  { grid-template-columns: repeat(2, 1fr); }
    .tecnicas-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>