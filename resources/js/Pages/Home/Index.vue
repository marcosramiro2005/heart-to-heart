<script setup>
// Página de inicio rediseñada.
// Estructura: Hero a pantalla completa → Hearty (protagonista) → 4 secciones → SOS.
// Se eliminaron: sección "Qué hacer ahora", grid de 15 técnicas y la "Guía de inicio"
// para reducir la sobrecarga visual y dar una primera impresión más impactante.

import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

// Datos del usuario autenticado compartidos por HandleInertiaRequests
const page = usePage()
const user = computed(() => page.props.auth?.user)

// Fecha y hora actuales para el saludo y la píldora de fecha
const ahora     = new Date()
const hora      = ahora.getHours()
const minutos   = ahora.getMinutes()
const diaSemana = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'][ahora.getDay()]
const diaNumero = ahora.getDate()
const mes       = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'][ahora.getMonth()]

// Saludo y emoji que cambian según el tramo horario del día
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

// 7 frases motivacionales — se elige una distinta cada día (índice = día de la semana)
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

// Hora formateada con cero a la izquierda: "09:05"
const horaFormateada = computed(() =>
    `${hora.toString().padStart(2,'0')}:${minutos.toString().padStart(2,'0')}`
)

// Las 15 técnicas de bienestar disponibles en la app.
// Cada chip navega a su ruta correspondiente.
const tecnicas = [
    { id: 'respiracion',   nombre: 'Respiración',    color: '#d4edda', icon: '🫁', ruta: '/respiracion'         },
    { id: 'meditacion',    nombre: 'Meditación',      color: '#e8d5f5', icon: '🧘', ruta: '/meditacion'          },
    { id: 'sonidos',       nombre: 'Sonidos',         color: '#d0eaf8', icon: '🎵', ruta: '/sonidos'             },
    { id: 'diario',        nombre: 'Diario',          color: '#fff9c4', icon: '📓', ruta: '/diario'              },
    { id: 'tapping',       nombre: 'Tapping',         color: '#ffecd2', icon: '👆', ruta: '/tapping'             },
    { id: 'visualizacion', nombre: 'Visualización',   color: '#ffd5e5', icon: '🌈', ruta: '/visualizacion'       },
    { id: 'yoga',          nombre: 'Yoga',            color: '#d4f5e9', icon: '🤸', ruta: '/yoga'                },
    { id: 'journaling',    nombre: 'Journaling',      color: '#e8f4f8', icon: '📝', ruta: '/journaling'          },
    { id: 'infusiones',    nombre: 'Infusiones',      color: '#e8d5f5', icon: '🍵', ruta: '/infusiones'          },
    { id: 'ejercicio',     nombre: 'Ejercicio',       color: '#ffd5d5', icon: '🏃', ruta: '/ejercicio'           },
    { id: 'grounding',     nombre: '5-4-3-2-1',       color: '#d0eaf8', icon: '🌍', ruta: '/tecnica-5-4-3-2-1'  },
    { id: 'autocompasion', nombre: 'Autocompasión',   color: '#fce4ec', icon: '💗', ruta: '/autocompasion'       },
    { id: 'musicoterapia', nombre: 'Musicoterapia',   color: '#e8eaf6', icon: '🎶', ruta: '/musicoterapia'       },
    { id: 'relajacion',    nombre: 'Relajación',      color: '#e0f2f1', icon: '💆', ruta: '/relajacion-muscular' },
    { id: 'gratitud',      nombre: 'Gratitud visual', color: '#fff8e1', icon: '✨', ruta: '/gratitud-visual'     },
]

// Solo 4 secciones (antes eran 6) para no saturar al usuario.
// Cada una tiene su gradiente de fondo (bg) y color de sombra al hover (glow).
const secciones = [
    { href: '/mis-emociones',  emoji: '📊', titulo: 'Mis emociones',  desc: 'Registra y visualiza tus patrones emocionales día a día.', cta: 'Ver dashboard',  bg: 'linear-gradient(150deg,#fffde7,#fff3cd)', glow: 'rgba(255,213,79,0.3)'   },
    { href: '/retos',          emoji: '🎯', titulo: 'Retos',           desc: 'Construye hábitos de bienestar con retos de 7 y 30 días.', cta: 'Ver retos',      bg: 'linear-gradient(150deg,#d4edda,#c3e6cb)', glow: 'rgba(107,207,127,0.25)' },
    { href: '/comunidad',      emoji: '👥', titulo: 'Comunidad',       desc: 'Comparte y apóyate en otros de forma anónima y segura.',   cta: 'Explorar',       bg: 'linear-gradient(150deg,#fce4ec,#f8bbd0)', glow: 'rgba(240,98,146,0.2)'   },
    { href: '/biblioteca',     emoji: '📚', titulo: 'Biblioteca',      desc: 'Artículos y guías sobre salud mental para tu bienestar.',  cta: 'Leer',           bg: 'linear-gradient(150deg,#e8eaf6,#d9ddf0)', glow: 'rgba(83,109,254,0.15)'  },
]
</script>

<template>
    <AppLayout>
        <!-- Contenedor principal: columna centrada con gap generoso entre secciones -->
        <div class="home-wrap">

            <!-- ══════════════════════════════════════
                 HERO — ocupa toda la primera pantalla
                 El usuario ve SOLO esto al entrar.
                 min-height = 100vh - navbar(70px) - margen superior.
            ══════════════════════════════════════ -->
            <section class="hero">

                <!-- Fondo animado: 3 blobs de color con blur que se mueven suavemente -->
                <div class="hero-bg">
                    <div class="hblob b1"></div>
                    <div class="hblob b2"></div>
                    <div class="hblob b3"></div>
                </div>

                <!-- Contenido del hero: centrado horizontal y verticalmente -->
                <div class="hero-content">

                    <!-- Píldora con el día, la fecha y la hora actual -->
                    <span class="fecha-pill">
                        {{ saludoEmoji }}
                        {{ diaSemana }}, {{ diaNumero }} de {{ mes }}
                        <span class="hora-chip">{{ horaFormateada }}</span>
                    </span>

                    <!-- Título principal: saludo + nombre del usuario con degradado animado -->
                    <h1>
                        {{ saludo }},
                        <span class="nombre-grad">{{ user?.name?.split(' ')[0] }}</span>
                        <span class="avatar-inline">{{ user?.avatar || '👤' }}</span>
                    </h1>

                    <!-- Frase motivacional del día (cambia cada día de la semana) -->
                    <div class="frase-card">
                        <span>{{ fraseHoy.emoji }}</span>
                        <p>{{ fraseHoy.texto }}</p>
                    </div>

                    <!-- Solo 2 CTAs: Hearty (principal) y Registrar emoción (secundario).
                         Antes había más opciones que saturaban al usuario. -->
                    <div class="hero-ctas">
                        <!-- CTA principal: lleva a Hearty, el chatbot emocional -->
                        <Link href="/hearty" class="cta-primary">
                            <span>💬</span>
                            <div>
                                <b>Hablar con Hearty</b>
                                <small>Tu guía emocional · 24/7</small>
                            </div>
                            <span class="cta-arrow">→</span>
                        </Link>
                        <!-- CTA secundario: acceso rápido al registro de emociones -->
                        <Link href="/mis-emociones" class="cta-secondary">
                            <span>📊</span>
                            <span>Registrar emoción</span>
                        </Link>
                    </div>

                    <!-- Barra de stats compacta: racha, nivel y técnicas disponibles -->
                    <div class="hero-stats">
                        <div class="stat-item"><span>🔥</span><span>Racha activa</span></div>
                        <div class="stat-sep"></div>
                        <div class="stat-item"><span>🏅</span><span>Semilla · Nivel 1</span></div>
                        <div class="stat-sep"></div>
                        <div class="stat-item"><span>💚</span><span>15 técnicas</span></div>
                    </div>
                </div>

                <!-- Indicador de scroll: anima hacia abajo para invitar a seguir bajando -->
                <div class="scroll-cue">
                    <span>↓</span>
                </div>
            </section>

            <!-- ══════════════════════════════════════
                 HEARTY — sección protagonista
                 Es lo primero que ve el usuario al hacer scroll.
                 Diseño oscuro para contrastar con el hero claro.
            ══════════════════════════════════════ -->
            <section class="hearty-feature">
                <!-- Toda la tarjeta es un enlace a /hearty -->
                <Link href="/hearty" class="hf-card">
                    <!-- Brillo decorativo en la esquina superior derecha -->
                    <div class="hf-glow"></div>

                    <div class="hf-content">
                        <!-- Columna izquierda: avatar + textos descriptivos de Hearty -->
                        <div class="hf-left">
                            <div class="hf-avatar">💚</div>
                            <div class="hf-text-group">
                                <span class="hf-tag">Tu guía emocional · 24/7</span>
                                <h2>Hearty te escucha</h2>
                                <p>Habla con Hearty sobre cómo te sientes. Detecta tus emociones, sugiere técnicas personalizadas y te acompaña sin juzgarte.</p>
                                <span class="hf-cta-txt">Iniciar conversación →</span>
                            </div>
                        </div>

                        <!-- Columna derecha: simulación visual de una conversación con Hearty -->
                        <div class="hf-right">
                            <div class="hf-chat">
                                <!-- Mensajes alternos: Hearty (izquierda) / usuario (derecha) -->
                                <div class="hfc-msg hearty">¡Hola! ¿Cómo estás hoy? 💙</div>
                                <div class="hfc-msg user">Un poco ansioso la verdad</div>
                                <div class="hfc-msg hearty">Entiendo 💙 ¿Probamos la respiración 4-7-8?</div>
                                <!-- Chips de sugerencias que Hearty podría ofrecer -->
                                <div class="hfc-chips">
                                    <span>🫁 Respirar</span>
                                    <span>🧘 Meditar</span>
                                    <span>📓 Diario</span>
                                </div>
                            </div>
                            <!-- Indicador de estado online con punto verde animado (pulse) -->
                            <div class="hf-online"><span class="hdot"></span> En línea ahora</div>
                        </div>
                    </div>
                </Link>
            </section>

            <!-- ══════════════════════════════════════
                 SECCIONES — cuadrícula 2×2
                 Solo 4 secciones (antes 6) para no sobrecargar.
                 Cada card usa variables CSS (--bg, --glow) para
                 aplicar su gradiente y color de sombra al hover.
            ══════════════════════════════════════ -->
            <section class="block">
                <h2 class="block-title">🗺️ Explora la app</h2>
                <div class="secciones-grid">
                    <!-- v-for sobre el array secciones; animationDelay escalonado para entrada suave -->
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
                 TÉCNICAS — grid de chips
                 Las 15 técnicas en chips clicables.
                 Cada chip tiene su color de fondo propio.
            ══════════════════════════════════════ -->
            <section class="block">
                <h2 class="block-title">🌿 Técnicas disponibles</h2>
                <div class="tec-grid">
                    <Link
                        v-for="t in tecnicas"
                        :key="t.id"
                        :href="t.ruta"
                        class="tec-chip"
                        :style="{ '--tec-bg': t.color }"
                    >
                        <span class="tmi-emoji">{{ t.icon }}</span>
                        <span class="tmi-nombre">{{ t.nombre }}</span>
                    </Link>
                </div>
            </section>

            <!-- ══════════════════════════════════════
                 SOS BANNER — siempre al final
                 Fondo oscuro para destacar la urgencia.
                 El botón "024" usa href="tel:024" — en móvil
                 abre la app de llamadas directamente.
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
* { box-sizing: border-box; }

.home-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1.5rem 6rem;
    display: flex;
    flex-direction: column;
    gap: 5rem;
}

/* ═══════════════════════════════════════
   HERO
═══════════════════════════════════════ */
.hero {
    position: relative;
    background: linear-gradient(140deg, #e8fffb 0%, #edf8ff 50%, #f5eeff 100%);
    border-radius: 32px;
    overflow: hidden;
    margin-top: 1.5rem;
    /* Ocupa toda la primera pantalla */
    min-height: calc(100dvh - 70px - 1.5rem - 3rem);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem 4rem;
}

.hero-bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.hblob { position: absolute; border-radius: 50%; filter: blur(70px); opacity: 0.28; }
.b1 { width: 500px; height: 500px; background: radial-gradient(circle, #4ECDC4, transparent); top: -160px; right: -120px; animation: bm1 10s ease-in-out infinite; }
.b2 { width: 380px; height: 380px; background: radial-gradient(circle, #9B8EC4, transparent); bottom: -120px; left: -100px; animation: bm2 13s ease-in-out infinite; }
.b3 { width: 260px; height: 260px; background: radial-gradient(circle, #6BCF7F, transparent); top: 35%; left: 35%; animation: bm3 8s ease-in-out infinite; }
@keyframes bm1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,-22px)} }
@keyframes bm2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,20px)} }
@keyframes bm3 { 0%,100%{transform:translate(0,0)scale(1)} 50%{transform:translate(16px,-16px)scale(0.9)} }

.hero-content {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.75rem;
    text-align: center;
    max-width: 640px;
    animation: su 0.7s ease both;
}

.fecha-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.82);
    border: 1px solid rgba(78,205,196,0.25);
    border-radius: 20px;
    padding: 0.45rem 1.1rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #555;
    backdrop-filter: blur(8px);
}
.hora-chip {
    background: #4ECDC4;
    color: white;
    border-radius: 8px;
    padding: 0.1rem 0.55rem;
    font-size: 0.74rem;
    font-weight: 700;
}

h1 {
    font-size: clamp(2.4rem, 5vw, 3.6rem);
    font-weight: 900;
    color: #1a1a1a;
    line-height: 1.1;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
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
.avatar-inline { font-size: 2.6rem; -webkit-text-fill-color: initial; }

.frase-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255,255,255,0.78);
    border-left: 3px solid #4ECDC4;
    border-radius: 14px;
    padding: 1rem 1.4rem;
    backdrop-filter: blur(8px);
    max-width: 500px;
    text-align: left;
}
.frase-card span { font-size: 1.5rem; flex-shrink: 0; }
.frase-card p { margin: 0; font-size: 0.93rem; color: #444; font-style: italic; line-height: 1.6; }

/* CTAs */
.hero-ctas {
    display: flex;
    gap: 0.9rem;
    flex-wrap: wrap;
    justify-content: center;
}

.cta-primary {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1rem 1.75rem;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white;
    border-radius: 18px;
    text-decoration: none;
    box-shadow: 0 6px 24px rgba(78,205,196,0.38);
    transition: transform 0.2s, box-shadow 0.2s;
    font-size: 0.95rem;
}
.cta-primary:hover { transform: translateY(-3px); box-shadow: 0 12px 36px rgba(78,205,196,0.5); }
.cta-primary span:first-child { font-size: 1.4rem; }
.cta-primary b     { display: block; font-size: 0.92rem; font-weight: 800; }
.cta-primary small { display: block; font-size: 0.72rem; opacity: 0.85; }
.cta-arrow { opacity: 0.7; transition: transform 0.2s; margin-left: 0.2rem; }
.cta-primary:hover .cta-arrow { transform: translateX(3px); opacity: 1; }

.cta-secondary {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 1rem 1.5rem;
    background: rgba(255,255,255,0.85);
    color: #2D2D2D;
    border-radius: 18px;
    text-decoration: none;
    border: 1.5px solid rgba(78,205,196,0.3);
    font-weight: 700;
    font-size: 0.92rem;
    backdrop-filter: blur(8px);
    transition: background 0.2s, transform 0.2s, border-color 0.2s;
}
.cta-secondary:hover { background: white; border-color: #4ECDC4; transform: translateY(-3px); }
.cta-secondary span:first-child { font-size: 1.3rem; }

/* Stats */
.hero-stats {
    display: flex;
    align-items: center;
    gap: 0;
    background: rgba(255,255,255,0.7);
    border: 1px solid rgba(78,205,196,0.18);
    border-radius: 16px;
    padding: 0.85rem 1.5rem;
    backdrop-filter: blur(8px);
}
.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0 1.25rem;
    font-size: 0.84rem;
    font-weight: 700;
    color: #444;
}
.stat-item:first-child { padding-left: 0; }
.stat-item:last-child  { padding-right: 0; }
.stat-item span:first-child { font-size: 1.2rem; }
.stat-sep { width: 1px; height: 28px; background: rgba(78,205,196,0.2); flex-shrink: 0; }

/* Scroll cue */
.scroll-cue {
    position: absolute;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    font-size: 1.1rem;
    color: rgba(78,205,196,0.6);
    animation: bounce 2s ease-in-out infinite;
    z-index: 1;
}
@keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(6px)} }

/* ═══════════════════════════════════════
   HEARTY
═══════════════════════════════════════ */
.hf-card {
    display: block;
    position: relative;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    border-radius: 28px;
    overflow: hidden;
    text-decoration: none;
    transition: transform 0.25s, box-shadow 0.25s;
    box-shadow: 0 12px 48px rgba(0,0,0,0.22);
}
.hf-card:hover { transform: translateY(-5px); box-shadow: 0 24px 64px rgba(0,0,0,0.3); }

.hf-glow {
    position: absolute;
    top: -80px; right: -80px;
    width: 450px; height: 450px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(78,205,196,0.22), transparent 70%);
    pointer-events: none;
}

.hf-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 3rem;
    padding: 3rem 3.5rem;
}

.hf-left {
    flex: 1;
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
}
.hf-avatar {
    width: 72px; height: 72px;
    background: rgba(78,205,196,0.18);
    border: 2px solid rgba(78,205,196,0.35);
    border-radius: 22px;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.2rem;
    flex-shrink: 0;
}
.hf-text-group { display: flex; flex-direction: column; gap: 0.65rem; }
.hf-tag {
    font-size: 0.74rem; font-weight: 700; color: #4ECDC4;
    letter-spacing: 0.07em; text-transform: uppercase;
}
.hf-text-group h2 {
    font-size: clamp(1.6rem, 2.8vw, 2.2rem);
    font-weight: 900; color: white; margin: 0; line-height: 1.15;
}
.hf-text-group p {
    font-size: 0.92rem; color: rgba(255,255,255,0.6);
    line-height: 1.7; margin: 0; max-width: 380px;
}
.hf-cta-txt {
    font-size: 0.92rem; font-weight: 700; color: #4ECDC4;
    transition: transform 0.2s; display: inline-block;
}
.hf-card:hover .hf-cta-txt { transform: translateX(4px); }

.hf-right { flex-shrink: 0; min-width: 260px; }
.hf-chat {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 1.25rem 1.4rem;
    display: flex; flex-direction: column; gap: 0.7rem;
}
.hfc-msg {
    padding: 0.65rem 0.9rem; border-radius: 12px;
    font-size: 0.83rem; line-height: 1.5; max-width: 92%;
}
.hfc-msg.hearty {
    background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.9);
    border-radius: 4px 12px 12px 12px; align-self: flex-start;
}
.hfc-msg.user {
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white; border-radius: 12px 4px 12px 12px;
    align-self: flex-end; margin-left: auto;
}
.hfc-chips { display: flex; gap: 0.4rem; flex-wrap: wrap; padding-top: 0.2rem; }
.hfc-chips span {
    padding: 0.3rem 0.75rem;
    background: rgba(78,205,196,0.16); border: 1.5px solid rgba(78,205,196,0.38);
    border-radius: 15px; font-size: 0.76rem; font-weight: 600; color: #4ECDC4;
}
.hf-online {
    display: flex; align-items: center; gap: 0.4rem;
    font-size: 0.76rem; color: rgba(255,255,255,0.4);
    font-weight: 600; padding-top: 0.8rem; padding-left: 0.25rem;
}
.hdot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #7fffad; display: inline-block;
    animation: pulse 2s ease infinite;
}
@keyframes pulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(127,255,173,0.5); }
    50%      { box-shadow: 0 0 0 5px rgba(127,255,173,0); }
}

/* ═══════════════════════════════════════
   SECCIONES
═══════════════════════════════════════ */
.block { display: flex; flex-direction: column; gap: 1.5rem; }
.block-title {
    font-size: 1.05rem; font-weight: 800; color: #1a1a1a; margin: 0;
}

.secciones-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.1rem;
}

.sec-card {
    display: flex; flex-direction: column; gap: 0.75rem;
    padding: 1.75rem; border-radius: 22px;
    background: var(--bg); text-decoration: none;
    border: 1.5px solid rgba(0,0,0,0.04);
    transition: transform 0.22s, box-shadow 0.22s;
    animation: su 0.5s ease both;
}
.sec-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px var(--glow, rgba(0,0,0,0.1)); }
.sec-emoji { font-size: 2.4rem; }
.sec-body h3 { font-size: 1rem; font-weight: 800; color: #1a1a1a; margin: 0 0 0.35rem; }
.sec-body p  { font-size: 0.84rem; color: #555; line-height: 1.6; margin: 0; }
.sec-cta     { font-size: 0.84rem; font-weight: 700; color: #4ECDC4; margin-top: auto; }

/* ═══════════════════════════════════════
   SOS BANNER
═══════════════════════════════════════ */
.sos-banner {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 24px;
    padding: 2.25rem 2.75rem;
    display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;
}
.sos-left { flex: 1; display: flex; align-items: flex-start; gap: 1.25rem; min-width: 260px; }
.sos-icon {
    width: 60px; height: 60px;
    background: rgba(230,57,70,0.18); border: 1.5px solid rgba(230,57,70,0.3);
    border-radius: 18px; display: flex; align-items: center; justify-content: center;
    font-size: 1.9rem; flex-shrink: 0;
}
.sos-left h3 { font-size: 1rem; font-weight: 800; color: white; margin: 0 0 0.3rem; }
.sos-left p  { font-size: 0.86rem; color: rgba(255,255,255,0.55); margin: 0; line-height: 1.6; }
.sos-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
.sos-btn-main {
    padding: 0.9rem 1.85rem; background: #E63946; color: white;
    font-weight: 700; border-radius: 25px; text-decoration: none;
    font-size: 0.92rem; transition: background 0.2s, transform 0.2s; white-space: nowrap;
}
.sos-btn-main:hover { background: #c0303b; transform: translateY(-1px); }
.sos-btn-024 {
    padding: 0.9rem 1.4rem; background: rgba(255,255,255,0.08); color: white;
    font-weight: 700; border-radius: 25px; text-decoration: none;
    font-size: 0.92rem; border: 1.5px solid rgba(255,255,255,0.2);
    transition: all 0.2s; white-space: nowrap;
}
.sos-btn-024:hover { background: rgba(255,255,255,0.16); border-color: white; }

/* ═══════════════════════════════════════
   TÉCNICAS
═══════════════════════════════════════ */
.tec-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.75rem;
}

.tec-chip {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.85rem 1.1rem;
    background: var(--tec-bg, #f0f0f0);
    border-radius: 16px;
    text-decoration: none;
    border: 1.5px solid rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
}
.tec-chip:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    filter: brightness(0.96);
}

.tmi-emoji { font-size: 1.35rem; flex-shrink: 0; }
.tmi-nombre { font-size: 0.84rem; font-weight: 700; color: #2d2d2d; line-height: 1.2; }

/* ═══════════════════════════════════════
   ANIMACIÓN
═══════════════════════════════════════ */
@keyframes su { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }

/* ═══════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════ */
@media (max-width: 900px) {
    .hf-content  { flex-direction: column; gap: 2rem; padding: 2.25rem; }
    .hf-right    { width: 100%; }
}

@media (max-width: 900px) {
    .tec-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 700px) {
    .home-wrap      { padding: 0 1rem 5rem; gap: 3.5rem; }
    .hero           { padding: 2rem 1.5rem 3.5rem; min-height: auto; border-radius: 24px; }
    .hero-stats     { flex-wrap: wrap; justify-content: center; gap: 0.5rem; padding: 0.85rem 1rem; }
    .stat-item      { padding: 0 0.75rem; }
    .secciones-grid { grid-template-columns: 1fr; }
    .tec-grid       { grid-template-columns: repeat(2, 1fr); }
    .sos-banner     { flex-direction: column; padding: 1.75rem; }
    .hf-left        { flex-direction: column; gap: 1rem; }
}

@media (max-width: 480px) {
    h1              { font-size: clamp(2rem, 8vw, 2.6rem); }
    .hero-ctas      { flex-direction: column; width: 100%; }
    .cta-primary,
    .cta-secondary  { justify-content: center; }
    .hero-stats     { flex-direction: column; gap: 0.4rem; }
    .stat-sep       { width: 50px; height: 1px; }
    .stat-item      { padding: 0; }
}
</style>
