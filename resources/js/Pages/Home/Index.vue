<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import axios from 'axios'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const ahora     = new Date()
const hora      = ahora.getHours()
const minutos   = ahora.getMinutes()
const diaSemana = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'][ahora.getDay()]
const diaNumero = ahora.getDate()
const mes       = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'][ahora.getMonth()]

const saludo = computed(() => {
    if (hora < 6)  return 'Buenas noches'
    if (hora < 12) return 'Buenos días'
    if (hora < 20) return 'Buenas tardes'
    return 'Buenas noches'
})
const saludoEmoji = computed(() => {
    if (hora < 6)  return '🌙'
    if (hora < 12) return '🌅'
    if (hora < 20) return '☀️'
    return '🌙'
})

const frases = [
    { texto: 'El progreso, no la perfección, es lo que importa.', emoji: '🌱' },
    { texto: 'Cuidarte no es egoísta, es necesario.', emoji: '💚' },
    { texto: 'Un día a la vez. Solo el siguiente paso.', emoji: '👣' },
    { texto: 'Mereces el mismo cuidado que das a los demás.', emoji: '🌸' },
    { texto: 'Las emociones difíciles no duran para siempre.', emoji: '🌊' },
    { texto: 'Tu bienestar emocional importa de verdad.', emoji: '💙' },
    { texto: 'Eres más fuerte de lo que crees.', emoji: '💪' },
]
const fraseHoy = frases[ahora.getDay() % frases.length]

const horaFormateada = computed(() => {
    const h = hora.toString().padStart(2, '0')
    const m = minutos.toString().padStart(2, '0')
    return `${h}:${m}`
})

const accesosPorHora = computed(() => {
    if (hora < 9) return [
        { nombre: 'Empezar el día',      desc: 'Respiración para activarte',      icon: '🫁', ruta: '/respiracion',   color: '#d4edda', accent: '#6BCF7F' },
        { nombre: 'Registrar emoción',   desc: 'Cómo empiezas la mañana',         icon: '📊', ruta: '/mis-emociones', color: '#E8FAF9', accent: '#4ECDC4' },
        { nombre: 'Hablar con Hearty',   desc: 'Tu primer check-in del día',      icon: '💬', ruta: '/hearty',         color: '#fce4ec', accent: '#f48fb1' },
    ]
    if (hora < 14) return [
        { nombre: 'Modo Focus',          desc: 'Pomodoro y concentración máxima', icon: '🎯', ruta: '/retos',          color: '#fff9c4', accent: '#FFD700' },
        { nombre: 'Hablar con Hearty',   desc: 'Cómo vas a mitad del día',        icon: '💬', ruta: '/hearty',         color: '#E8FAF9', accent: '#4ECDC4' },
        { nombre: 'Gestionar estrés',    desc: 'Técnicas para aliviar tensión',   icon: '🌿', ruta: '/respiracion',   color: '#d4edda', accent: '#6BCF7F' },
    ]
    if (hora < 20) return [
        { nombre: 'Registrar emoción',   desc: 'Cómo ha ido el día',              icon: '📊', ruta: '/mis-emociones', color: '#E8FAF9', accent: '#4ECDC4' },
        { nombre: 'Diario emocional',    desc: 'Refleja y procesa tu jornada',    icon: '📓', ruta: '/diario',         color: '#fff9c4', accent: '#FFD700' },
        { nombre: 'Ejercicio',           desc: 'Libera el estrés acumulado',      icon: '🏃', ruta: '/ejercicio',      color: '#ffd5d5', accent: '#E63946' },
    ]
    return [
        { nombre: 'Relajación nocturna', desc: 'Prepárate para descansar bien',   icon: '🌙', ruta: '/meditacion',    color: '#e8eaf6', accent: '#9B8EC4' },
        { nombre: 'Infusión relajante',  desc: 'Ritual antes de dormir',          icon: '🍵', ruta: '/infusiones',    color: '#fff9c4', accent: '#FFD700' },
        { nombre: 'Sonidos para dormir', desc: 'Duerme mejor esta noche',         icon: '🎵', ruta: '/sonidos',        color: '#d0eaf8', accent: '#45B7D1' },
    ]
})

const secciones = [
    { href: '/hearty',        emoji: '💬', titulo: 'Hearty',            desc: 'Tu asistente emocional disponible siempre. Escucha, aconseja y acompaña sin juzgarte.',            cta: 'Hablar ahora',   bg: 'linear-gradient(135deg,#E8FAF9,#c8f5ef)',   glow: 'rgba(78,205,196,0.25)'   },
    { href: '/mis-emociones', emoji: '📊', titulo: 'Mis emociones',     desc: 'Registra y visualiza tus patrones emocionales. Calendario visual y estadísticas detalladas.',       cta: 'Ver dashboard',  bg: 'linear-gradient(135deg,#fffde7,#fff9c4)',   glow: 'rgba(255,213,79,0.3)'    },
    { href: '/retos',         emoji: '🎯', titulo: 'Retos',             desc: 'Construye hábitos de bienestar con retos de 7 y 30 días. Avanza día a día.',                        cta: 'Ver retos',      bg: 'linear-gradient(135deg,#d4edda,#c3e6cb)',   glow: 'rgba(107,207,127,0.25)'  },
    { href: '/comunidad',     emoji: '👥', titulo: 'Comunidad',         desc: 'Un espacio seguro para compartir experiencias y apoyarse mutuamente de forma anónima.',             cta: 'Explorar',       bg: 'linear-gradient(135deg,#fce4ec,#f8bbd0)',   glow: 'rgba(240,98,146,0.2)'    },
    { href: '/test-bienestar',emoji: '🧠', titulo: 'Test semanal',      desc: 'Evaluación PHQ-9 adaptada para conocer tu estado de bienestar con recomendaciones personalizadas.', cta: 'Hacer test',     bg: 'linear-gradient(135deg,#e8d5f5,#dcc4f0)',   glow: 'rgba(155,142,196,0.25)'  },
    { href: '/biblioteca',    emoji: '📚', titulo: 'Biblioteca',        desc: 'Artículos y guías sobre salud mental escritos para entender y mejorar tu bienestar.',               cta: 'Explorar',       bg: 'linear-gradient(135deg,#e8eaf6,#d9ddf0)',   glow: 'rgba(83,109,254,0.15)'   },
]

const tecnicas = [
    { id: 'respiracion',   nombre: 'Respiración',     color: '#d4edda', icon: '🫁', ruta: '/respiracion' },
    { id: 'meditacion',    nombre: 'Meditación',       color: '#e8d5f5', icon: '🧘', ruta: '/meditacion' },
    { id: 'sonidos',       nombre: 'Sonidos',          color: '#d0eaf8', icon: '🎵', ruta: '/sonidos' },
    { id: 'diario',        nombre: 'Diario',           color: '#fff9c4', icon: '📓', ruta: '/diario' },
    { id: 'tapping',       nombre: 'Tapping',          color: '#ffecd2', icon: '👆', ruta: '/tapping' },
    { id: 'visualizacion', nombre: 'Visualización',    color: '#ffd5e5', icon: '🌈', ruta: '/visualizacion' },
    { id: 'yoga',          nombre: 'Yoga',             color: '#d4f5e9', icon: '🤸', ruta: '/yoga' },
    { id: 'journaling',    nombre: 'Journaling',       color: '#e8f4f8', icon: '📝', ruta: '/journaling' },
    { id: 'infusiones',    nombre: 'Infusiones',       color: '#e8d5f5', icon: '🍵', ruta: '/infusiones' },
    { id: 'ejercicio',     nombre: 'Ejercicio',        color: '#ffd5d5', icon: '🏃', ruta: '/ejercicio' },
    { id: 'grounding',     nombre: '5-4-3-2-1',        color: '#d0eaf8', icon: '🌍', ruta: '/tecnica-5-4-3-2-1' },
    { id: 'autocompasion', nombre: 'Autocompasión',    color: '#fce4ec', icon: '💗', ruta: '/autocompasion' },
    { id: 'musicoterapia', nombre: 'Musicoterapia',    color: '#e8eaf6', icon: '🎶', ruta: '/musicoterapia' },
    { id: 'relajacion',    nombre: 'Relajación',       color: '#e0f2f1', icon: '💆', ruta: '/relajacion-muscular' },
    { id: 'gratitud',      nombre: 'Gratitud visual',  color: '#fff8e1', icon: '✨', ruta: '/gratitud-visual' },
]

const checkInHecho    = ref(false)
const emocionCheckin  = ref(null)
const enviandoCheckin = ref(false)

const emocionesCheckin = [
    { id: 'genial',   emoji: '🤩', label: 'Genial',    intensidad: 8 },
    { id: 'bien',     emoji: '😊', label: 'Bien',      intensidad: 6 },
    { id: 'normal',   emoji: '😐', label: 'Normal',    intensidad: 5 },
    { id: 'cansado',  emoji: '😴', label: 'Cansado/a', intensidad: 4 },
    { id: 'ansioso',  emoji: '😰', label: 'Ansioso/a', intensidad: 3 },
    { id: 'triste',   emoji: '😢', label: 'Triste',    intensidad: 3 },
]

const hacerCheckin = async (emocion) => {
    if (enviandoCheckin.value) return
    emocionCheckin.value  = emocion
    enviandoCheckin.value = true
    try {
        await axios.post('/mis-emociones/registrar', {
            emotion:   emocion.id,
            intensity: emocion.intensidad,
            note:      'Check-in diario desde el inicio',
        })
    } catch (e) {}
    finally {
        enviandoCheckin.value = false
        checkInHecho.value    = true
    }
}
</script>

<template>
    <AppLayout>
        <div class="home-wrap">

            <!-- ═══════════════════════════════
                 HERO
            ═══════════════════════════════ -->
            <section class="hero">
                <div class="hero-bg">
                    <div class="hblob b1"></div>
                    <div class="hblob b2"></div>
                    <div class="hblob b3"></div>
                </div>

                <div class="hero-grid">

                    <!-- Izquierda: saludo + frase + CTAs + mini-stats -->
                    <div class="hero-left">
                        <div class="fecha-pill">
                            <span>{{ saludoEmoji }}</span>
                            <span>{{ diaSemana }}, {{ diaNumero }} de {{ mes }}</span>
                            <span class="hora-chip">{{ horaFormateada }}</span>
                        </div>

                        <h1>
                            {{ saludo }},<br>
                            <span class="nombre-grad">{{ user?.name?.split(' ')[0] }}</span>
                            <span class="user-avatar">{{ user?.avatar || '👤' }}</span>
                        </h1>

                        <div class="frase-card">
                            <span class="fc-em">{{ fraseHoy.emoji }}</span>
                            <p>{{ fraseHoy.texto }}</p>
                        </div>

                        <div class="hero-btns">
                            <Link href="/hearty" class="btn-hearty">
                                <span class="bh-icon">💬</span>
                                <div>
                                    <b>Hablar con Hearty</b>
                                    <small>Tu guía emocional</small>
                                </div>
                            </Link>
                            <Link href="/sos" class="btn-sos-mini">
                                <span>🆘</span>
                                <small>SOS</small>
                            </Link>
                        </div>

                        <div class="mini-stats">
                            <div class="ms-item">
                                <span>🔥</span>
                                <div><b>Racha activa</b><small>Sigue así</small></div>
                            </div>
                            <div class="ms-item">
                                <span>🏅</span>
                                <div><b>Nivel 1</b><small>Semilla</small></div>
                            </div>
                            <div class="ms-item">
                                <span>💚</span>
                                <div><b>15 técnicas</b><small>Disponibles</small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Derecha: check-in emocional -->
                    <div class="hero-right">
                        <div class="checkin-wrap">
                            <Transition name="ci-flip" mode="out-in">

                                <div v-if="!checkInHecho" class="checkin-card" key="checkin">
                                    <div class="cc-header">
                                        <div class="cc-icon-wrap">💆</div>
                                        <div>
                                            <h3>¿Cómo estás ahora?</h3>
                                            <p>Check-in emocional de hoy</p>
                                        </div>
                                    </div>
                                    <div class="cc-emociones">
                                        <button
                                            v-for="em in emocionesCheckin"
                                            :key="em.id"
                                            class="ce-btn"
                                            :class="{ activa: emocionCheckin?.id === em.id, cargando: enviandoCheckin }"
                                            :disabled="enviandoCheckin"
                                            @click="hacerCheckin(em)"
                                        >
                                            <span class="ce-em">{{ em.emoji }}</span>
                                            <span class="ce-lb">{{ em.label }}</span>
                                        </button>
                                    </div>
                                    <p class="cc-hint">Toca para registrar tu emoción</p>
                                </div>

                                <div v-else class="checkin-done" key="done">
                                    <div class="cd-emoji">{{ emocionCheckin.emoji }}</div>
                                    <h3>Registrado 💚</h3>
                                    <p>Hoy te sientes <strong>{{ emocionCheckin.label }}</strong></p>
                                    <Link href="/mis-emociones" class="cd-link">
                                        Ver mi dashboard emocional →
                                    </Link>
                                </div>

                            </Transition>
                        </div>
                    </div>

                </div>
            </section>

            <!-- ═══════════════════════════════
                 ACCESOS CONTEXTUALES
            ═══════════════════════════════ -->
            <section class="section-block">
                <div class="sb-head">
                    <h2>{{ hora < 12 ? '🌅 Para empezar bien el día' : hora < 20 ? '☀️ Para este momento' : '🌙 Para terminar el día' }}</h2>
                    <span class="sb-badge">Personalizado · {{ horaFormateada }}</span>
                </div>
                <div class="accesos-grid">
                    <Link
                        v-for="(a, i) in accesosPorHora"
                        :key="a.nombre"
                        :href="a.ruta"
                        class="acceso-card"
                        :style="{ '--bg': a.color, '--accent': a.accent, '--d': (i * 0.1) + 's' }"
                    >
                        <div class="ac-icon" :style="{ background: a.accent + '22' }">
                            <span>{{ a.icon }}</span>
                        </div>
                        <div class="ac-text">
                            <span class="ac-nombre">{{ a.nombre }}</span>
                            <span class="ac-desc">{{ a.desc }}</span>
                        </div>
                        <span class="ac-arrow">→</span>
                    </Link>
                </div>
            </section>

            <!-- ═══════════════════════════════
                 SECCIONES PRINCIPALES
            ═══════════════════════════════ -->
            <section class="section-block">
                <div class="sb-head">
                    <h2>🗺️ Tu espacio de bienestar</h2>
                </div>
                <div class="secciones-grid">
                    <Link
                        v-for="(s, i) in secciones"
                        :key="s.href"
                        :href="s.href"
                        class="sec-card"
                        :style="{ '--bg': s.bg, '--glow': s.glow, '--d': (i * 0.08) + 's' }"
                    >
                        <div class="sc-emoji">{{ s.emoji }}</div>
                        <div class="sc-body">
                            <h3>{{ s.titulo }}</h3>
                            <p>{{ s.desc }}</p>
                        </div>
                        <span class="sc-cta">{{ s.cta }} →</span>
                    </Link>
                </div>
            </section>

            <!-- ═══════════════════════════════
                 TÉCNICAS
            ═══════════════════════════════ -->
            <section class="section-block">
                <div class="sb-head">
                    <h2>🌿 Técnicas de bienestar</h2>
                    <span class="sb-count">{{ tecnicas.length }} disponibles</span>
                </div>
                <div class="tecnicas-grid">
                    <Link
                        v-for="tec in tecnicas"
                        :key="tec.id"
                        :href="tec.ruta"
                        class="tec-card"
                        :style="{ '--bg': tec.color }"
                    >
                        <span class="tec-icon">{{ tec.icon }}</span>
                        <span class="tec-nombre">{{ tec.nombre }}</span>
                    </Link>
                </div>
            </section>

            <!-- ═══════════════════════════════
                 SOS BANNER
            ═══════════════════════════════ -->
            <section class="sos-banner">
                <div class="sos-left">
                    <div class="sos-icon">🆘</div>
                    <div>
                        <h3>¿Estás pasando un momento difícil?</h3>
                        <p>El modo SOS tiene técnicas de emergencia inmediatas. Si la situación es urgente, llama al 024.</p>
                    </div>
                </div>
                <div class="sos-btns">
                    <Link href="/sos" class="btn-sos-main">Ir al modo SOS →</Link>
                    <a href="tel:024" class="btn-024">📞 024</a>
                </div>
            </section>

        </div>
    </AppLayout>
</template>

<style scoped>
/* ─────────────────────────────────────
   WRAPPER
───────────────────────────────────── */
.home-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1.5rem 4rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* ─────────────────────────────────────
   HERO
───────────────────────────────────── */
.hero {
    position: relative;
    background: linear-gradient(135deg, #e8fffe 0%, #f0f9ff 45%, #fff5ee 100%);
    border-radius: 28px;
    overflow: hidden;
    margin-top: 1.5rem;
    padding: 2.5rem;
}

.hero-bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.hblob   { position: absolute; border-radius: 50%; filter: blur(55px); opacity: 0.38; }
.b1 { width: 420px; height: 420px; background: radial-gradient(circle, #4ECDC4, transparent); top: -110px; right: -60px; animation: bm1 8s ease-in-out infinite; }
.b2 { width: 320px; height: 320px; background: radial-gradient(circle, #9B8EC4, transparent); bottom: -80px; left: -60px; animation: bm2 11s ease-in-out infinite; }
.b3 { width: 220px; height: 220px; background: radial-gradient(circle, #6BCF7F, transparent); top: 35%; left: 38%; animation: bm3 7s ease-in-out infinite; }
@keyframes bm1 { 0%,100%{transform:translate(0,0)scale(1)}50%{transform:translate(22px,-18px)scale(1.07)} }
@keyframes bm2 { 0%,100%{transform:translate(0,0)scale(1)}50%{transform:translate(-18px,16px)scale(1.1)} }
@keyframes bm3 { 0%,100%{transform:translate(0,0)scale(1)}50%{transform:translate(14px,-14px)scale(0.93)} }

.hero-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr 370px;
    gap: 2.5rem;
    align-items: start;
}

/* Left */
.hero-left {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    animation: su 0.65s ease 0.05s both;
}

.fecha-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.82);
    border-radius: 20px;
    padding: 0.4rem 0.9rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #555;
    width: fit-content;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(78,205,196,0.22);
}
.hora-chip {
    background: #4ECDC4;
    color: white;
    border-radius: 8px;
    padding: 0.12rem 0.5rem;
    font-size: 0.76rem;
    font-weight: 700;
}

.hero-left h1 {
    font-size: clamp(1.9rem, 3.2vw, 2.7rem);
    font-weight: 900;
    color: #1a1a1a;
    line-height: 1.13;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.3rem;
}

.nombre-grad {
    background: linear-gradient(135deg, #4ECDC4 0%, #45B7D1 50%, #6BCF7F 100%);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: gc 4s ease infinite;
}
@keyframes gc { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }

.user-avatar { font-size: 2rem; -webkit-text-fill-color: initial; }

.frase-card {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    background: rgba(255,255,255,0.78);
    border-radius: 14px;
    padding: 0.9rem 1.1rem;
    border-left: 3px solid #4ECDC4;
    backdrop-filter: blur(8px);
    max-width: 500px;
}
.fc-em { font-size: 1.4rem; flex-shrink: 0; }
.frase-card p { font-size: 0.91rem; color: #444; font-style: italic; line-height: 1.55; margin: 0; }

.hero-btns { display: flex; gap: 0.75rem; flex-wrap: wrap; }

.btn-hearty {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white;
    border-radius: 14px;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 18px rgba(78,205,196,0.32);
}
.bh-icon { font-size: 1.3rem; }
.btn-hearty b     { display: block; font-size: 0.9rem; font-weight: 800; }
.btn-hearty small { display: block; font-size: 0.72rem; opacity: 0.82; }
.btn-hearty:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(78,205,196,0.45); }

.btn-sos-mini {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.15rem;
    padding: 0.75rem 1rem;
    background: white;
    color: #E63946;
    border-radius: 14px;
    text-decoration: none;
    border: 2px solid #E63946;
    transition: all 0.2s;
    font-size: 1.4rem;
}
.btn-sos-mini small { font-weight: 700; font-size: 0.7rem; }
.btn-sos-mini:hover { background: #E63946; color: white; }

.mini-stats {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.ms-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.72);
    border-radius: 10px;
    padding: 0.5rem 0.75rem;
    font-size: 1.2rem;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(78,205,196,0.12);
}
.ms-item b     { display: block; font-size: 0.78rem; font-weight: 700; color: #1a1a1a; }
.ms-item small { display: block; font-size: 0.68rem; color: #888; }

/* Right: check-in */
.hero-right { animation: su 0.65s ease 0.2s both; }

.checkin-wrap {
    background: rgba(255,255,255,0.88);
    backdrop-filter: blur(18px);
    border-radius: 22px;
    box-shadow: 0 16px 48px rgba(0,0,0,0.1), 0 0 0 1px rgba(78,205,196,0.14);
    overflow: hidden;
}

.checkin-card {
    padding: 1.6rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cc-header {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}
.cc-icon-wrap {
    width: 46px; height: 46px;
    background: linear-gradient(135deg, #E8FAF9, #c8f5ef);
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.cc-header h3 { font-size: 0.97rem; font-weight: 800; margin: 0; color: #1a1a1a; }
.cc-header p  { font-size: 0.78rem; color: #999; margin: 0; }

.cc-emociones {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}
.ce-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
    padding: 0.75rem 0.25rem;
    border: 2px solid #f0f0f0;
    border-radius: 14px;
    background: white;
    cursor: pointer;
    font-family: inherit;
    transition: border-color 0.18s, background 0.18s, transform 0.18s, box-shadow 0.18s;
}
.ce-btn:hover {
    border-color: #4ECDC4;
    background: #f0fefc;
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 6px 16px rgba(78,205,196,0.2);
}
.ce-btn.activa {
    border-color: #4ECDC4;
    background: #E8FAF9;
    transform: scale(1.06);
    box-shadow: 0 4px 14px rgba(78,205,196,0.25);
}
.ce-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.ce-em { font-size: 1.75rem; }
.ce-lb { font-size: 0.7rem; font-weight: 700; color: #444; }

.cc-hint { font-size: 0.72rem; color: #ccc; text-align: center; }

.checkin-done {
    padding: 2.5rem 1.5rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.65rem;
}
.cd-emoji { font-size: 3.6rem; animation: pop 0.45s cubic-bezier(.34,1.56,.64,1) both; }
@keyframes pop { 0%{transform:scale(0);opacity:0} 100%{transform:scale(1);opacity:1} }
.checkin-done h3 { font-size: 1.1rem; font-weight: 800; color: #1a1a1a; margin: 0; }
.checkin-done p  { font-size: 0.88rem; color: #666; margin: 0; }
.checkin-done strong { color: #4ECDC4; }

.cd-link {
    margin-top: 0.5rem;
    padding: 0.65rem 1.4rem;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 14px rgba(78,205,196,0.3);
}
.cd-link:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(78,205,196,0.4); }

/* Check-in flip transition */
.ci-flip-enter-active { transition: all 0.45s cubic-bezier(.34,1.56,.64,1); }
.ci-flip-leave-active { transition: all 0.2s ease; }
.ci-flip-enter-from   { opacity: 0; transform: scale(0.9) translateY(12px); }
.ci-flip-leave-to     { opacity: 0; transform: scale(1.04); }

/* Slide-up entrance */
@keyframes su { from{opacity:0;transform:translateY(26px)} to{opacity:1;transform:translateY(0)} }

/* ─────────────────────────────────────
   SECTION BLOCKS
───────────────────────────────────── */
.section-block { display: flex; flex-direction: column; gap: 0.9rem; }

.sb-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.sb-head h2 { font-size: 1.05rem; font-weight: 800; margin: 0; color: #1a1a1a; }

.sb-badge {
    background: #E8FAF9;
    color: #3BAFA7;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.74rem;
    font-weight: 700;
    border: 1.5px solid #4ECDC4;
    white-space: nowrap;
}
.sb-count { font-size: 0.78rem; color: #888; font-weight: 600; }

/* ─────────────────────────────────────
   ACCESOS CONTEXTUALES
───────────────────────────────────── */
.accesos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.85rem;
}

.acceso-card {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1.1rem 1.2rem;
    border-radius: 16px;
    background: var(--bg);
    text-decoration: none;
    border: 1.5px solid transparent;
    transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s;
    animation: su 0.5s ease var(--d, 0s) both;
}
.acceso-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    border-color: var(--accent);
}
.ac-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.ac-text    { flex: 1; }
.ac-nombre  { display: block; font-size: 0.88rem; font-weight: 800; color: #1a1a1a; }
.ac-desc    { display: block; font-size: 0.75rem; color: #666; margin-top: 0.1rem; }
.ac-arrow   { font-size: 1rem; color: var(--accent); font-weight: 700; opacity: 0; transition: opacity 0.2s, transform 0.2s; }
.acceso-card:hover .ac-arrow { opacity: 1; transform: translateX(3px); }

/* ─────────────────────────────────────
   SECCIONES PRINCIPALES
───────────────────────────────────── */
.secciones-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.85rem;
}

.sec-card {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    padding: 1.5rem;
    border-radius: 20px;
    background: var(--bg);
    text-decoration: none;
    border: 1.5px solid rgba(0,0,0,0.04);
    transition: transform 0.22s, box-shadow 0.22s;
    animation: su 0.5s ease var(--d, 0s) both;
}
.sec-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 36px var(--glow, rgba(0,0,0,0.12));
}
.sc-emoji { font-size: 2.2rem; }
.sc-body  { flex: 1; }
.sc-body h3 { font-size: 0.96rem; font-weight: 800; color: #1a1a1a; margin: 0 0 0.35rem; }
.sc-body p  { font-size: 0.8rem; color: #555; line-height: 1.55; margin: 0; }
.sc-cta { font-size: 0.8rem; font-weight: 700; color: #4ECDC4; margin-top: 0.25rem; }

/* ─────────────────────────────────────
   TÉCNICAS
───────────────────────────────────── */
.tecnicas-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.65rem;
}

.tec-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    padding: 1.1rem 0.5rem;
    border-radius: 14px;
    background: var(--bg);
    text-decoration: none;
    border: 1.5px solid transparent;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}
.tec-card:hover {
    transform: translateY(-4px) scale(1.04);
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    border-color: rgba(78,205,196,0.4);
}
.tec-icon   { font-size: 1.75rem; }
.tec-nombre { font-size: 0.75rem; font-weight: 700; color: #2D2D2D; text-align: center; }

/* ─────────────────────────────────────
   SOS BANNER
───────────────────────────────────── */
.sos-banner {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 20px;
    padding: 1.75rem 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.sos-left {
    flex: 1;
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
    min-width: 260px;
}
.sos-icon {
    width: 52px; height: 52px;
    background: rgba(230,57,70,0.18);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
    border: 1.5px solid rgba(230,57,70,0.28);
}
.sos-left h3 { font-size: 1rem; font-weight: 800; color: white; margin: 0 0 0.3rem; }
.sos-left p  { font-size: 0.84rem; color: rgba(255,255,255,0.6); margin: 0; line-height: 1.5; }

.sos-btns { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }

.btn-sos-main {
    padding: 0.8rem 1.6rem;
    background: #E63946;
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.2s, transform 0.2s;
    white-space: nowrap;
}
.btn-sos-main:hover { background: #c0303b; transform: translateY(-1px); }

.btn-024 {
    padding: 0.8rem 1.25rem;
    background: rgba(255,255,255,0.1);
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    border: 1.5px solid rgba(255,255,255,0.2);
    transition: all 0.2s;
    white-space: nowrap;
}
.btn-024:hover { background: rgba(255,255,255,0.18); border-color: white; }

/* ─────────────────────────────────────
   RESPONSIVE
───────────────────────────────────── */
@media (max-width: 1000px) {
    .hero-grid      { grid-template-columns: 1fr; }
    .checkin-wrap   { max-width: 440px; }
    .accesos-grid   { grid-template-columns: 1fr; }
    .secciones-grid { grid-template-columns: repeat(2, 1fr); }
    .tecnicas-grid  { grid-template-columns: repeat(4, 1fr); }
}
@media (max-width: 700px) {
    .secciones-grid { grid-template-columns: 1fr; }
    .tecnicas-grid  { grid-template-columns: repeat(3, 1fr); }
    .sos-banner     { flex-direction: column; }
}
@media (max-width: 480px) {
    .hero           { padding: 1.5rem; }
    .tecnicas-grid  { grid-template-columns: repeat(2, 1fr); }
    .mini-stats     { gap: 0.5rem; }
}
</style>
