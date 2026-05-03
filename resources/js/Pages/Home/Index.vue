<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

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
    { texto: 'Cuidarte no es egoísta, es necesario.',             emoji: '💚' },
    { texto: 'Un día a la vez. Solo el siguiente paso.',          emoji: '👣' },
    { texto: 'Mereces el mismo cuidado que das a los demás.',     emoji: '🌸' },
    { texto: 'Las emociones difíciles no duran para siempre.',    emoji: '🌊' },
    { texto: 'Tu bienestar emocional importa de verdad.',         emoji: '💙' },
    { texto: 'Eres más fuerte de lo que crees.',                  emoji: '💪' },
]
const fraseHoy = frases[ahora.getDay() % frases.length]

const horaFormateada = computed(() => {
    const h = hora.toString().padStart(2, '0')
    const m = minutos.toString().padStart(2, '0')
    return `${h}:${m}`
})

const ctxTitle = computed(() => {
    if (hora < 9)  return { icon: '🌅', texto: 'Para empezar bien el día' }
    if (hora < 14) return { icon: '☀️', texto: 'Para este momento del día' }
    if (hora < 20) return { icon: '🌤️', texto: 'Para esta tarde' }
    return { icon: '🌙', texto: 'Para terminar el día bien' }
})

const accesosPorHora = computed(() => {
    if (hora < 9) return [
        { nombre: 'Empezar el día',    desc: 'Respiración para activarte',      icon: '🫁', ruta: '/respiracion',   color: '#d4edda', accent: '#6BCF7F' },
        { nombre: 'Registrar emoción', desc: 'Cómo empiezas la mañana',         icon: '📊', ruta: '/mis-emociones', color: '#E8FAF9', accent: '#4ECDC4' },
        { nombre: 'Hablar con Hearty', desc: 'Tu primer check-in del día',      icon: '💬', ruta: '/hearty',        color: '#fce4ec', accent: '#f48fb1' },
    ]
    if (hora < 14) return [
        { nombre: 'Modo Focus',        desc: 'Pomodoro y concentración máxima', icon: '🎯', ruta: '/retos',         color: '#fff9c4', accent: '#FFD700' },
        { nombre: 'Hablar con Hearty', desc: 'Cómo vas a mitad del día',        icon: '💬', ruta: '/hearty',        color: '#E8FAF9', accent: '#4ECDC4' },
        { nombre: 'Gestionar estrés',  desc: 'Técnicas para aliviar tensión',   icon: '🌿', ruta: '/respiracion',  color: '#d4edda', accent: '#6BCF7F' },
    ]
    if (hora < 20) return [
        { nombre: 'Registrar emoción', desc: 'Cómo ha ido el día',              icon: '📊', ruta: '/mis-emociones', color: '#E8FAF9', accent: '#4ECDC4' },
        { nombre: 'Diario emocional',  desc: 'Refleja y procesa tu jornada',    icon: '📓', ruta: '/diario',        color: '#fff9c4', accent: '#FFD700' },
        { nombre: 'Ejercicio',         desc: 'Libera el estrés acumulado',      icon: '🏃', ruta: '/ejercicio',     color: '#ffd5d5', accent: '#E63946' },
    ]
    return [
        { nombre: 'Relajación nocturna', desc: 'Prepárate para descansar bien', icon: '🌙', ruta: '/meditacion',   color: '#e8eaf6', accent: '#9B8EC4' },
        { nombre: 'Infusión relajante',  desc: 'Ritual antes de dormir',        icon: '🍵', ruta: '/infusiones',   color: '#fff9c4', accent: '#FFD700' },
        { nombre: 'Sonidos para dormir', desc: 'Duerme mejor esta noche',       icon: '🎵', ruta: '/sonidos',      color: '#d0eaf8', accent: '#45B7D1' },
    ]
})

const secciones = [
    { href: '/mis-emociones',   emoji: '📊', titulo: 'Mis emociones',  desc: 'Registra y visualiza tus patrones emocionales. Calendario visual y estadísticas.', cta: 'Ver dashboard',  bg: 'linear-gradient(150deg,#fffde7,#fff3cd)', glow: 'rgba(255,213,79,0.3)'   },
    { href: '/retos',           emoji: '🎯', titulo: 'Retos',          desc: 'Construye hábitos de bienestar con retos de 7 y 30 días. Avanza día a día.',        cta: 'Ver retos',      bg: 'linear-gradient(150deg,#d4edda,#c3e6cb)', glow: 'rgba(107,207,127,0.25)' },
    { href: '/comunidad',       emoji: '👥', titulo: 'Comunidad',      desc: 'Espacio seguro para compartir experiencias y apoyarse de forma anónima.',           cta: 'Explorar',       bg: 'linear-gradient(150deg,#fce4ec,#f8bbd0)', glow: 'rgba(240,98,146,0.2)'   },
    { href: '/test-bienestar',  emoji: '🧠', titulo: 'Test semanal',   desc: 'Evaluación PHQ-9 adaptada con recomendaciones personalizadas para ti.',             cta: 'Hacer test',     bg: 'linear-gradient(150deg,#e8d5f5,#dcc4f0)', glow: 'rgba(155,142,196,0.25)' },
    { href: '/biblioteca',      emoji: '📚', titulo: 'Biblioteca',     desc: 'Artículos y guías sobre salud mental escritos para entender tu bienestar.',          cta: 'Leer',           bg: 'linear-gradient(150deg,#e8eaf6,#d9ddf0)', glow: 'rgba(83,109,254,0.15)'  },
    { href: '/mis-estadisticas',emoji: '📈', titulo: 'Estadísticas',   desc: 'Mira tu evolución, tus rachas y tu progreso a lo largo del tiempo.',                cta: 'Ver progreso',   bg: 'linear-gradient(150deg,#e0f2f1,#b2dfdb)', glow: 'rgba(0,150,136,0.2)'    },
]

const tecnicas = [
    { id: 'respiracion',   nombre: 'Respiración',    color: '#d4edda', icon: '🫁', ruta: '/respiracion'        },
    { id: 'meditacion',    nombre: 'Meditación',      color: '#e8d5f5', icon: '🧘', ruta: '/meditacion'         },
    { id: 'sonidos',       nombre: 'Sonidos',         color: '#d0eaf8', icon: '🎵', ruta: '/sonidos'            },
    { id: 'diario',        nombre: 'Diario',          color: '#fff9c4', icon: '📓', ruta: '/diario'             },
    { id: 'tapping',       nombre: 'Tapping',         color: '#ffecd2', icon: '👆', ruta: '/tapping'            },
    { id: 'visualizacion', nombre: 'Visualización',   color: '#ffd5e5', icon: '🌈', ruta: '/visualizacion'      },
    { id: 'yoga',          nombre: 'Yoga',            color: '#d4f5e9', icon: '🤸', ruta: '/yoga'               },
    { id: 'journaling',    nombre: 'Journaling',      color: '#e8f4f8', icon: '📝', ruta: '/journaling'         },
    { id: 'infusiones',    nombre: 'Infusiones',      color: '#e8d5f5', icon: '🍵', ruta: '/infusiones'         },
    { id: 'ejercicio',     nombre: 'Ejercicio',       color: '#ffd5d5', icon: '🏃', ruta: '/ejercicio'          },
    { id: 'grounding',     nombre: '5-4-3-2-1',       color: '#d0eaf8', icon: '🌍', ruta: '/tecnica-5-4-3-2-1'  },
    { id: 'autocompasion', nombre: 'Autocompasión',   color: '#fce4ec', icon: '💗', ruta: '/autocompasion'      },
    { id: 'musicoterapia', nombre: 'Musicoterapia',   color: '#e8eaf6', icon: '🎶', ruta: '/musicoterapia'      },
    { id: 'relajacion',    nombre: 'Relajación',      color: '#e0f2f1', icon: '💆', ruta: '/relajacion-muscular'},
    { id: 'gratitud',      nombre: 'Gratitud visual', color: '#fff8e1', icon: '✨', ruta: '/gratitud-visual'    },
]

const checkInHecho    = ref(false)
const emocionCheckin  = ref(null)
const enviandoCheckin = ref(false)

const emocionesCheckin = [
    { id: 'genial',  emoji: '🤩', label: 'Genial',    intensidad: 8 },
    { id: 'bien',    emoji: '😊', label: 'Bien',      intensidad: 6 },
    { id: 'normal',  emoji: '😐', label: 'Normal',    intensidad: 5 },
    { id: 'cansado', emoji: '😴', label: 'Cansado/a', intensidad: 4 },
    { id: 'ansioso', emoji: '😰', label: 'Ansioso/a', intensidad: 3 },
    { id: 'triste',  emoji: '😢', label: 'Triste',    intensidad: 3 },
]

const hacerCheckin = (emocion) => {
    if (enviandoCheckin.value) return
    emocionCheckin.value  = emocion
    enviandoCheckin.value = true
    router.post('/mis-emociones/registrar', {
        emotion:   emocion.id,
        intensity: emocion.intensidad,
        note:      'Check-in diario desde el inicio',
    }, {
        preserveScroll: true,
        onFinish: () => {
            enviandoCheckin.value = false
            checkInHecho.value    = true
        },
    })
}
</script>

<template>
    <AppLayout>
        <div class="home-wrap">

            <!-- ══════════════════════════════════════
                 HERO
            ══════════════════════════════════════ -->
            <section class="hero">
                <div class="hero-bg">
                    <div class="hblob b1"></div>
                    <div class="hblob b2"></div>
                    <div class="hblob b3"></div>
                </div>

                <div class="hero-inner">

                    <!-- Columna izquierda -->
                    <div class="hero-left">

                        <span class="fecha-pill">
                            {{ saludoEmoji }}
                            <span>{{ diaSemana }}, {{ diaNumero }} de {{ mes }}</span>
                            <span class="hora-chip">{{ horaFormateada }}</span>
                        </span>

                        <h1>
                            {{ saludo }},
                            <span class="nombre-grad">{{ user?.name?.split(' ')[0] }}</span>
                            <span class="avatar-inline">{{ user?.avatar || '👤' }}</span>
                        </h1>

                        <div class="frase-card">
                            <span class="frase-emoji">{{ fraseHoy.emoji }}</span>
                            <p>{{ fraseHoy.texto }}</p>
                        </div>

                        <div class="hero-actions">
                            <Link href="/hearty" class="btn-hearty">
                                <span class="bh-icon">💬</span>
                                <div class="bh-text">
                                    <b>Hablar con Hearty</b>
                                    <small>Tu guía emocional · 24/7</small>
                                </div>
                                <span class="bh-arrow">→</span>
                            </Link>
                        </div>

                        <div class="hero-stats-row">
                            <div class="hsr-item">
                                <span>🔥</span>
                                <div>
                                    <b>Racha activa</b>
                                    <small>Sigue así</small>
                                </div>
                            </div>
                            <div class="hsr-sep"></div>
                            <div class="hsr-item">
                                <span>🏅</span>
                                <div>
                                    <b>Semilla</b>
                                    <small>Nivel 1</small>
                                </div>
                            </div>
                            <div class="hsr-sep"></div>
                            <div class="hsr-item">
                                <span>💚</span>
                                <div>
                                    <b>15 técnicas</b>
                                    <small>Disponibles</small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Columna derecha: guía de introducción -->
                    <div class="hero-right">
                        <div class="guia-card">
                            <div class="guia-head">
                                <div class="guia-icon">🗺️</div>
                                <div>
                                    <h3>Tu guía de inicio</h3>
                                    <p>Descubre qué puedes hacer en Heart to Heart</p>
                                </div>
                            </div>
                            <ul class="guia-list">
                                <li><span class="guia-emoji">📊</span> Registra tus emociones diariamente</li>
                                <li><span class="guia-emoji">💬</span> Habla con Hearty, tu guía emocional 24/7</li>
                                <li><span class="guia-emoji">🌿</span> Explora técnicas de bienestar personalizadas</li>
                                <li><span class="guia-emoji">👥</span> Únete a la comunidad anónima</li>
                                <li><span class="guia-emoji">📚</span> Lee artículos y consejos expertos</li>
                                <li><span class="guia-emoji">🎯</span> Completa retos para construir hábitos</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </section>

            <!-- ══════════════════════════════════════
                 QUÉ HACER AHORA
            ══════════════════════════════════════ -->
            <section class="block">
                <div class="block-head">
                    <h2><span class="bh-icon-emoji">{{ ctxTitle.icon }}</span> Qué hacer ahora</h2>
                    <span class="block-badge">Personalizado · {{ horaFormateada }}</span>
                </div>
                <div class="ctx-grid">
                    <Link
                        v-for="(a, i) in accesosPorHora"
                        :key="a.nombre"
                        :href="a.ruta"
                        class="ctx-card"
                        :style="{ '--bg': a.color, '--acc': a.accent, animationDelay: i * 0.08 + 's' }"
                    >
                        <div class="ctx-icon" :style="{ background: a.accent + '25' }">
                            <span>{{ a.icon }}</span>
                        </div>
                        <div class="ctx-text">
                            <span class="ctx-nombre">{{ a.nombre }}</span>
                            <span class="ctx-desc">{{ a.desc }}</span>
                        </div>
                        <span class="ctx-arrow" :style="{ color: a.accent }">→</span>
                    </Link>
                </div>
            </section>

            <!-- ══════════════════════════════════════
                 HEARTY — TARJETA DESTACADA
            ══════════════════════════════════════ -->
            <section class="hearty-feature">
                <Link href="/hearty" class="hf-card">
                    <div class="hf-glow"></div>
                    <div class="hf-content">
                        <div class="hf-left">
                            <div class="hf-avatar">💚</div>
                            <div class="hf-text-group">
                                <span class="hf-tag">Tu guía emocional · 24/7</span>
                                <h2>Hearty te escucha</h2>
                                <p>Habla con Hearty sobre cómo te sientes. Detecta tus emociones, sugiere técnicas personalizadas y te acompaña sin juzgarte.</p>
                                <span class="hf-cta-txt">Iniciar conversación →</span>
                            </div>
                        </div>
                        <div class="hf-right">
                            <div class="hf-chat">
                                <div class="hfc-msg hearty">¡Hola! ¿Cómo estás hoy? 💙</div>
                                <div class="hfc-msg user">Un poco ansioso la verdad</div>
                                <div class="hfc-msg hearty">Entiendo 💙 ¿Probamos la respiración 4-7-8?</div>
                                <div class="hfc-chips">
                                    <span>🫁 Respirar</span>
                                    <span>🧘 Meditar</span>
                                    <span>📓 Diario</span>
                                </div>
                            </div>
                            <div class="hf-online">
                                <span class="hdot"></span> En línea ahora
                            </div>
                        </div>
                    </div>
                </Link>
            </section>

            <!-- ══════════════════════════════════════
                 EXPLORA LAS SECCIONES
            ══════════════════════════════════════ -->
            <section class="block">
                <div class="block-head">
                    <h2><span class="bh-icon-emoji">🗺️</span> Explora las secciones</h2>
                </div>
                <div class="secciones-grid">
                    <Link
                        v-for="(s, i) in secciones"
                        :key="s.href"
                        :href="s.href"
                        class="sec-card"
                        :style="{ '--bg': s.bg, '--glow': s.glow, animationDelay: i * 0.07 + 's' }"
                    >
                        <span class="sec-emoji">{{ s.emoji }}</span>
                        <div class="sec-body">
                            <h3>{{ s.titulo }}</h3>
                            <p>{{ s.desc }}</p>
                        </div>
                        <span class="sec-cta">{{ s.cta }} →</span>
                    </Link>
                </div>
            </section>

            <!-- ══════════════════════════════════════
                 TÉCNICAS DISPONIBLES
            ══════════════════════════════════════ -->
            <section class="block">
                <div class="block-head">
                    <h2><span class="bh-icon-emoji">🌿</span> Técnicas disponibles</h2>
                    <span class="block-count">{{ tecnicas.length }} disponibles</span>
                </div>
                <div class="tec-grid">
                    <Link
                        v-for="tec in tecnicas"
                        :key="tec.id"
                        :href="tec.ruta"
                        class="tec-chip"
                        :style="{ background: tec.color }"
                    >
                        <span class="tec-icon">{{ tec.icon }}</span>
                        <span class="tec-label">{{ tec.nombre }}</span>
                    </Link>
                </div>
            </section>

            <!-- ══════════════════════════════════════
                 SOS BANNER
            ══════════════════════════════════════ -->
            <section class="sos-banner">
                <div class="sos-left">
                    <div class="sos-icon">🆘</div>
                    <div>
                        <h3>¿Estás pasando un momento difícil?</h3>
                        <p>El modo SOS te da acceso inmediato a técnicas de emergencia. Si la situación es urgente, llama al 024.</p>
                    </div>
                </div>
                <div class="sos-actions">
                    <Link href="/sos" class="sos-btn-main">Ir al modo SOS →</Link>
                    <a href="tel:024" class="sos-btn-024">📞 024</a>
                </div>
            </section>

        </div>
    </AppLayout>
</template>

<style scoped>
/* ═══════════════════════════════════════
   BASE
═══════════════════════════════════════ */
* { box-sizing: border-box; }

.home-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1.5rem 5rem;
    display: flex;
    flex-direction: column;
    gap: 4rem;
}

/* ═══════════════════════════════════════
   HERO
═══════════════════════════════════════ */
.hero {
    position: relative;
    background: linear-gradient(140deg, #e8fffb 0%, #edf8ff 45%, #f5eeff 100%);
    border-radius: 28px;
    overflow: hidden;
    margin-top: 1.5rem;
    padding: 2.75rem;
    box-shadow: 0 2px 24px rgba(78,205,196,0.08);
}

.hero-bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.hblob {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.35;
}
.b1 { width: 440px; height: 440px; background: radial-gradient(circle, #4ECDC4, transparent); top: -120px; right: -80px;  animation: bm1 9s ease-in-out infinite; }
.b2 { width: 300px; height: 300px; background: radial-gradient(circle, #9B8EC4, transparent); bottom: -80px; left: -60px; animation: bm2 12s ease-in-out infinite; }
.b3 { width: 200px; height: 200px; background: radial-gradient(circle, #6BCF7F, transparent); top: 40%; left: 38%;        animation: bm3 7s ease-in-out infinite; }
@keyframes bm1 { 0%,100%{transform:translate(0,0)scale(1)} 50%{transform:translate(24px,-20px)scale(1.07)} }
@keyframes bm2 { 0%,100%{transform:translate(0,0)scale(1)} 50%{transform:translate(-18px,18px)scale(1.1)} }
@keyframes bm3 { 0%,100%{transform:translate(0,0)scale(1)} 50%{transform:translate(14px,-14px)scale(0.92)} }

.hero-inner {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 3rem;
    align-items: start;
}

/* ── Hero: columna izquierda ── */
.hero-left {
    display: flex;
    flex-direction: column;
    gap: 1.4rem;
    animation: su 0.6s ease 0.05s both;
}

.fecha-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.82);
    border: 1px solid rgba(78,205,196,0.25);
    border-radius: 20px;
    padding: 0.45rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #555;
    width: fit-content;
    backdrop-filter: blur(8px);
}
.hora-chip {
    background: #4ECDC4;
    color: white;
    border-radius: 8px;
    padding: 0.1rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 700;
}

.hero-left h1 {
    font-size: clamp(2rem, 3.2vw, 2.8rem);
    font-weight: 900;
    color: #1a1a1a;
    line-height: 1.12;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.35rem;
}
.nombre-grad {
    background: linear-gradient(135deg, #4ECDC4 0%, #45B7D1 50%, #6BCF7F 100%);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: gc 5s ease infinite;
}
@keyframes gc { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
.avatar-inline { font-size: 2.1rem; -webkit-text-fill-color: initial; }

.frase-card {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    background: rgba(255,255,255,0.8);
    border-left: 3px solid #4ECDC4;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    backdrop-filter: blur(8px);
    max-width: 490px;
}
.frase-emoji { font-size: 1.4rem; flex-shrink: 0; line-height: 1.4; }
.frase-card p {
    margin: 0;
    font-size: 0.92rem;
    color: #444;
    font-style: italic;
    line-height: 1.6;
}

.hero-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }

.btn-hearty {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.85rem 1.5rem;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white;
    border-radius: 16px;
    text-decoration: none;
    box-shadow: 0 4px 20px rgba(78,205,196,0.35);
    transition: transform 0.2s, box-shadow 0.2s;
    flex-shrink: 0;
}
.btn-hearty:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(78,205,196,0.48); }
.bh-icon  { font-size: 1.4rem; }
.bh-text  { line-height: 1.2; }
.bh-text b     { display: block; font-size: 0.9rem; font-weight: 800; }
.bh-text small { display: block; font-size: 0.72rem; opacity: 0.82; font-weight: 500; }
.bh-arrow { font-size: 1.1rem; opacity: 0.7; margin-left: 0.25rem; transition: transform 0.2s; }
.btn-hearty:hover .bh-arrow { transform: translateX(3px); opacity: 1; }

.btn-sos {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.15rem;
    padding: 0.85rem 1.1rem;
    background: white;
    color: #E63946;
    border: 2px solid #E63946;
    border-radius: 16px;
    text-decoration: none;
    font-size: 1.5rem;
    transition: all 0.2s;
}
.btn-sos small { font-weight: 800; font-size: 0.68rem; letter-spacing: 0.05em; }
.btn-sos:hover { background: #E63946; color: white; }

.hero-stats-row {
    display: flex;
    align-items: center;
    gap: 0;
    background: rgba(255,255,255,0.7);
    border: 1px solid rgba(78,205,196,0.18);
    border-radius: 14px;
    padding: 0.9rem 1.25rem;
    backdrop-filter: blur(8px);
    width: fit-content;
    max-width: 100%;
}
.hsr-item {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    font-size: 1.25rem;
    padding: 0 1rem;
}
.hsr-item:first-child { padding-left: 0; }
.hsr-item:last-child  { padding-right: 0; }
.hsr-item b     { display: block; font-size: 0.8rem; font-weight: 800; color: #1a1a1a; white-space: nowrap; }
.hsr-item small { display: block; font-size: 0.68rem; color: #888; white-space: nowrap; }
.hsr-sep { width: 1px; height: 32px; background: rgba(78,205,196,0.2); flex-shrink: 0; }

/* ── Hero: columna derecha ── */
.hero-right { animation: su 0.6s ease 0.18s both; }

.guia-card {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(18px);
    border-radius: 22px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1), 0 0 0 1px rgba(78,205,196,0.14);
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}

.guia-head {
    display: flex;
    align-items: center;
    gap: 0.9rem;
}
.guia-icon {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, #E8FAF9, #c8f5ef);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.guia-head h3 { font-size: 0.97rem; font-weight: 800; margin: 0; color: #1a1a1a; }
.guia-head p  { font-size: 0.78rem; color: #999; margin: 0; }

.guia-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}
.guia-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    font-size: 0.82rem;
    color: #555;
    line-height: 1.4;
}
.guia-emoji { font-size: 1.1rem; flex-shrink: 0; }

/* ═══════════════════════════════════════
   BLOQUES DE SECCIÓN
═══════════════════════════════════════ */
.block { display: flex; flex-direction: column; gap: 1.1rem; }

.block-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.block-head h2 {
    font-size: 1.05rem;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.bh-icon-emoji { font-size: 1.1rem; }

.block-badge {
    background: #E8FAF9;
    color: #3BAFA7;
    padding: 0.28rem 0.85rem;
    border-radius: 20px;
    font-size: 0.74rem;
    font-weight: 700;
    border: 1.5px solid #4ECDC4;
    white-space: nowrap;
}
.block-count {
    font-size: 0.78rem;
    color: #999;
    font-weight: 600;
}

/* ═══════════════════════════════════════
   ACCESOS CONTEXTUALES
═══════════════════════════════════════ */
.ctx-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.ctx-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.35rem 1.3rem;
    border-radius: 18px;
    background: var(--bg);
    text-decoration: none;
    border: 1.5px solid transparent;
    transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s;
    animation: su 0.5s ease both;
}
.ctx-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.1);
    border-color: var(--acc);
}
.ctx-icon {
    width: 50px; height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.ctx-text { flex: 1; min-width: 0; }
.ctx-nombre { display: block; font-size: 0.9rem; font-weight: 800; color: #1a1a1a; }
.ctx-desc   { display: block; font-size: 0.76rem; color: #666; margin-top: 0.15rem; }
.ctx-arrow  { font-size: 1rem; font-weight: 700; opacity: 0; transition: opacity 0.2s, transform 0.2s; flex-shrink: 0; }
.ctx-card:hover .ctx-arrow { opacity: 1; transform: translateX(3px); }

/* ═══════════════════════════════════════
   HEARTY FEATURED
═══════════════════════════════════════ */
.hf-card {
    display: block;
    position: relative;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    border-radius: 24px;
    overflow: hidden;
    text-decoration: none;
    transition: transform 0.25s, box-shadow 0.25s;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}
.hf-card:hover { transform: translateY(-4px); box-shadow: 0 20px 50px rgba(0,0,0,0.28); }

.hf-glow {
    position: absolute;
    top: -60px; right: -60px;
    width: 350px; height: 350px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(78,205,196,0.25), transparent 70%);
    pointer-events: none;
}

.hf-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 3rem;
    padding: 2.5rem 2.75rem;
}

.hf-left {
    flex: 1;
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
}
.hf-avatar {
    width: 64px; height: 64px;
    background: rgba(78,205,196,0.2);
    border: 2px solid rgba(78,205,196,0.4);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
}
.hf-text-group { display: flex; flex-direction: column; gap: 0.5rem; }
.hf-tag {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 700;
    color: #4ECDC4;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
.hf-text-group h2 {
    font-size: clamp(1.4rem, 2.5vw, 1.9rem);
    font-weight: 900;
    color: white;
    margin: 0;
    line-height: 1.15;
}
.hf-text-group p {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.6);
    line-height: 1.65;
    margin: 0;
    max-width: 360px;
}
.hf-cta-txt {
    display: inline-block;
    font-size: 0.9rem;
    font-weight: 700;
    color: #4ECDC4;
    transition: transform 0.2s;
}
.hf-card:hover .hf-cta-txt { transform: translateX(4px); }

.hf-right { flex-shrink: 0; min-width: 240px; }
.hf-chat {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 18px;
    padding: 1.1rem 1.2rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.hfc-msg {
    padding: 0.6rem 0.85rem;
    border-radius: 12px;
    font-size: 0.82rem;
    line-height: 1.45;
    max-width: 90%;
}
.hfc-msg.hearty {
    background: rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.9);
    border-radius: 4px 12px 12px 12px;
    align-self: flex-start;
}
.hfc-msg.user {
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white;
    border-radius: 12px 4px 12px 12px;
    align-self: flex-end;
    margin-left: auto;
}
.hfc-chips {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    padding-top: 0.2rem;
}
.hfc-chips span {
    padding: 0.28rem 0.7rem;
    background: rgba(78,205,196,0.18);
    border: 1.5px solid rgba(78,205,196,0.4);
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #4ECDC4;
}
.hf-online {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    color: rgba(255,255,255,0.45);
    font-weight: 600;
    padding-top: 0.75rem;
    padding-left: 0.25rem;
}
.hdot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #7fffad;
    display: inline-block;
    animation: pulse 2s ease infinite;
}
@keyframes pulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(127,255,173,0.5); }
    50%      { box-shadow: 0 0 0 5px rgba(127,255,173,0); }
}

/* ═══════════════════════════════════════
   SECCIONES PRINCIPALES
═══════════════════════════════════════ */
.secciones-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.sec-card {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    padding: 1.6rem;
    border-radius: 20px;
    background: var(--bg);
    text-decoration: none;
    border: 1.5px solid rgba(0,0,0,0.04);
    transition: transform 0.22s, box-shadow 0.22s;
    animation: su 0.5s ease both;
}
.sec-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 36px var(--glow, rgba(0,0,0,0.12));
}
.sec-emoji { font-size: 2.2rem; }
.sec-body  { flex: 1; }
.sec-body h3 { font-size: 0.95rem; font-weight: 800; color: #1a1a1a; margin: 0 0 0.35rem; }
.sec-body p  { font-size: 0.8rem; color: #555; line-height: 1.55; margin: 0; }
.sec-cta     { font-size: 0.8rem; font-weight: 700; color: #4ECDC4; }

/* ═══════════════════════════════════════
   TÉCNICAS
═══════════════════════════════════════ */
.tec-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
}

.tec-chip {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    padding: 1.4rem 1rem;
    border-radius: 18px;
    text-decoration: none;
    border: 1.5px solid transparent;
    transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s;
    animation: su 0.5s ease both;
}
.tec-chip:hover {
    transform: translateY(-5px) scale(1.08);
    box-shadow: 0 10px 28px rgba(0,0,0,0.12);
    border-color: rgba(78,205,196,0.4);
}
.tec-icon  { font-size: 2.2rem; }
.tec-label { font-size: 0.82rem; font-weight: 700; color: #2D2D2D; text-align: center; line-height: 1.3; }

/* ═══════════════════════════════════════
   SOS BANNER
═══════════════════════════════════════ */
.sos-banner {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 22px;
    padding: 2rem 2.25rem;
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
    width: 56px; height: 56px;
    background: rgba(230,57,70,0.18);
    border: 1.5px solid rgba(230,57,70,0.3);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    flex-shrink: 0;
}
.sos-left h3 { font-size: 1rem; font-weight: 800; color: white; margin: 0 0 0.3rem; }
.sos-left p  { font-size: 0.84rem; color: rgba(255,255,255,0.55); margin: 0; line-height: 1.55; }
.sos-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
.sos-btn-main {
    padding: 0.85rem 1.75rem;
    background: #E63946;
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.2s, transform 0.2s;
    white-space: nowrap;
}
.sos-btn-main:hover { background: #c0303b; transform: translateY(-1px); }
.sos-btn-024 {
    padding: 0.85rem 1.35rem;
    background: rgba(255,255,255,0.08);
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    border: 1.5px solid rgba(255,255,255,0.2);
    transition: all 0.2s;
    white-space: nowrap;
}
.sos-btn-024:hover { background: rgba(255,255,255,0.16); border-color: white; }

/* ═══════════════════════════════════════
   ANIMACIÓN ENTRADA
═══════════════════════════════════════ */
@keyframes su { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:translateY(0)} }

/* ═══════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════ */
@media (max-width: 1000px) {
    .hero-inner       { grid-template-columns: 1fr; gap: 2rem; }
    .hero-right       { max-width: 440px; }
    .ctx-grid         { grid-template-columns: 1fr; gap: 0.75rem; }
    .secciones-grid   { grid-template-columns: repeat(2, 1fr); }
    .tec-grid         { grid-template-columns: repeat(4, 1fr); }
    .hf-content       { flex-direction: column; gap: 1.75rem; padding: 2rem; }
    .hf-right         { width: 100%; }
}

@media (max-width: 700px) {
    .home-wrap        { padding: 0 1rem 4rem; gap: 2rem; }
    .hero             { padding: 1.75rem; margin-top: 1rem; }
    .hero-stats-row   { width: 100%; justify-content: space-around; }
    .secciones-grid   { grid-template-columns: 1fr; }
    .tec-grid         { grid-template-columns: repeat(3, 1fr); gap: 0.8rem; }
    .tec-chip         { padding: 1.1rem 0.8rem; }
    .tec-icon         { font-size: 1.8rem; }
    .sos-banner       { flex-direction: column; }
    .hf-content       { padding: 1.5rem; }
    .hf-left          { flex-direction: column; gap: 1rem; }
}

@media (max-width: 480px) {
    .hero             { padding: 1.4rem; }
    .hero-left h1     { font-size: clamp(1.7rem, 7vw, 2.2rem); }
    .hsr-item         { padding: 0 0.6rem; }
    .ctx-grid         { gap: 0.6rem; }
    .tec-grid         { grid-template-columns: repeat(2, 1fr); gap: 0.65rem; }
    .tec-chip         { padding: 0.9rem 0.6rem; gap: 0.4rem; }
    .tec-icon         { font-size: 1.5rem; }
    .tec-label        { font-size: 0.7rem; }
    .hero-stats-row   { flex-direction: column; gap: 0.6rem; padding: 0.8rem 1rem; }
    .hsr-sep          { width: 60px; height: 1px; }
}
</style>
