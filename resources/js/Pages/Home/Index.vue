<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const hora        = new Date().getHours()
const minutos     = new Date().getMinutes()
const diaSemana   = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'][new Date().getDay()]
const diaNumero   = new Date().getDate()
const mes         = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'][new Date().getMonth()]

const saludo = computed(() => {
    if (hora < 6)  return 'Buenas noches'
    if (hora < 12) return 'Buenos días'
    if (hora < 20) return 'Buenas tardes'
    return 'Buenas noches'
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

const fraseHoy = frases[new Date().getDay() % frases.length]

const accesosPorHora = computed(() => {
    if (hora < 9) return [
        { nombre: 'Buenos días 🌅', desc: 'Empieza con una técnica', icon: '🫁', ruta: '/respiracion', color: '#d4edda' },
        { nombre: 'Registrar emoción', desc: 'Cómo empiezas el día', icon: '📊', ruta: '/mis-emociones', color: '#E8FAF9' },
        { nombre: 'Hablar con Hearty', desc: 'Tu primer check-in', icon: '💬', ruta: '/hearty', color: '#fce4ec' },
    ]
    if (hora < 14) return [
        { nombre: 'Modo focus', desc: 'Pomodoro y concentración', icon: '🎯', ruta: '/retos', color: '#fff9c4' },
        { nombre: 'Hearty', desc: 'Cómo vas por el día', icon: '💬', ruta: '/hearty', color: '#E8FAF9' },
        { nombre: 'Técnicas', desc: 'Gestiona el estrés', icon: '🌿', ruta: '/respiracion', color: '#d4edda' },
    ]
    if (hora < 20) return [
        { nombre: 'Registrar emoción', desc: 'Cómo ha ido el día', icon: '📊', ruta: '/mis-emociones', color: '#E8FAF9' },
        { nombre: 'Diario', desc: 'Refleja tu día', icon: '📓', ruta: '/diario', color: '#fff9c4' },
        { nombre: 'Ejercicio', desc: 'Libera el estrés del día', icon: '🏃', ruta: '/ejercicio', color: '#ffd5d5' },
    ]
    return [
        { nombre: 'Relajación', desc: 'Prepárate para descansar', icon: '🌙', ruta: '/meditacion', color: '#e8eaf6' },
        { nombre: 'Infusión', desc: 'Ritual nocturno', icon: '🍵', ruta: '/infusiones', color: '#fff9c4' },
        { nombre: 'Sonidos', desc: 'Duerme mejor', icon: '🎵', ruta: '/sonidos', color: '#d0eaf8' },
    ]
})

const tecnicas = [
    { id: 'respiracion',     nombre: 'Respiración',       color: '#d4edda', icon: '🫁', ruta: '/respiracion' },
    { id: 'meditacion',      nombre: 'Meditación',        color: '#e8d5f5', icon: '🧘', ruta: '/meditacion' },
    { id: 'sonidos',         nombre: 'Sonidos',           color: '#d0eaf8', icon: '🎵', ruta: '/sonidos' },
    { id: 'diario',          nombre: 'Diario',            color: '#fff9c4', icon: '📓', ruta: '/diario' },
    { id: 'tapping',         nombre: 'Tapping',           color: '#ffecd2', icon: '👆', ruta: '/tapping' },
    { id: 'visualizacion',   nombre: 'Visualización',     color: '#ffd5e5', icon: '🌈', ruta: '/visualizacion' },
    { id: 'yoga',            nombre: 'Yoga',              color: '#d4f5e9', icon: '🤸', ruta: '/yoga' },
    { id: 'journaling',      nombre: 'Journaling',        color: '#e8f4f8', icon: '📝', ruta: '/journaling' },
    { id: 'infusiones',      nombre: 'Infusiones',        color: '#e8d5f5', icon: '🍵', ruta: '/infusiones' },
    { id: 'ejercicio',       nombre: 'Ejercicio',         color: '#ffd5d5', icon: '🏃', ruta: '/ejercicio' },
    { id: 'grounding',       nombre: '5-4-3-2-1',         color: '#d0eaf8', icon: '🌍', ruta: '/tecnica-5-4-3-2-1' },
    { id: 'autocompasion',   nombre: 'Autocompasión',     color: '#fce4ec', icon: '💗', ruta: '/autocompasion' },
    { id: 'musicoterapia',   nombre: 'Musicoterapia',     color: '#e8eaf6', icon: '🎶', ruta: '/musicoterapia' },
    { id: 'relajacion',      nombre: 'Relajación',        color: '#e0f2f1', icon: '💆', ruta: '/relajacion-muscular' },
    { id: 'gratitud',        nombre: 'Gratitud visual',   color: '#fff8e1', icon: '✨', ruta: '/gratitud-visual' },
]

const checkInHecho    = ref(false)
const emocionCheckin  = ref(null)
const enviandoCheckin = ref(false)

const emocionesCheckin = [
    { id: 'genial',    emoji: '🤩', label: 'Genial' },
    { id: 'bien',      emoji: '😊', label: 'Bien' },
    { id: 'normal',    emoji: '😐', label: 'Normal' },
    { id: 'cansado',   emoji: '😴', label: 'Cansado/a' },
    { id: 'ansioso',   emoji: '😰', label: 'Ansioso/a' },
    { id: 'triste',    emoji: '😢', label: 'Triste' },
]

const hacerCheckin = async (emocion) => {
    emocionCheckin.value  = emocion
    enviandoCheckin.value = true
    try {
        await axios.post('/mis-emociones/registrar', {
            emotion:   emocion.id,
            intensity: emocion.id === 'genial' ? 8 : emocion.id === 'bien' ? 6 : emocion.id === 'normal' ? 5 : 4,
            note:      'Check-in diario desde el inicio',
        })
        checkInHecho.value = true
    } catch (e) {}
    finally { enviandoCheckin.value = false }
}

const horaFormateada = computed(() => {
    const h = hora.toString().padStart(2, '0')
    const m = minutos.toString().padStart(2, '0')
    return `${h}:${m}`
})
</script>

<template>
    <AppLayout>
        <div class="home-wrapper">

            <!-- ── Hero personalizado con check-in ── -->
            <section class="home-hero">
                <div class="hero-bg-circles">
                    <div class="hbc c1"></div>
                    <div class="hbc c2"></div>
                </div>

                <div class="hero-left">
                    <div class="hero-fecha">
                        <span class="hf-dia">{{ diaSemana }}, {{ diaNumero }} de {{ mes }}</span>
                        <span class="hf-hora">{{ horaFormateada }}</span>
                    </div>
                    <h1>{{ saludo }},<br><strong>{{ user?.name?.split(' ')[0] }}</strong> {{ user?.avatar || '👤' }}</h1>
                    <div class="hero-frase">
                        <span class="hf-emoji">{{ fraseHoy.emoji }}</span>
                        <p>{{ fraseHoy.texto }}</p>
                    </div>
                    <div class="hero-ctas">
                        <Link href="/hearty" class="btn-hearty-hero">💬 Hablar con Hearty</Link>
                        <Link href="/sos" class="btn-sos-hero">🆘 SOS</Link>
                    </div>
                </div>

                <!-- Check-in emocional -->
                <div class="hero-checkin">
                    <div v-if="!checkInHecho" class="checkin-card">
                        <h3>¿Cómo te sientes ahora mismo?</h3>
                        <p>Check-in rápido de hoy</p>
                        <div class="checkin-emociones">
                            <button
                                v-for="em in emocionesCheckin"
                                :key="em.id"
                                class="ce-btn"
                                :class="{ seleccionada: emocionCheckin?.id === em.id }"
                                @click="hacerCheckin(em)"
                                :disabled="enviandoCheckin"
                            >
                                <span class="ce-emoji">{{ em.emoji }}</span>
                                <span class="ce-label">{{ em.label }}</span>
                            </button>
                        </div>
                    </div>
                    <div v-else class="checkin-completado">
                        <span class="cc-emoji">{{ emocionCheckin.emoji }}</span>
                        <h3>Check-in guardado 💚</h3>
                        <p>Te sientes <strong>{{ emocionCheckin.label }}</strong> hoy</p>
                        <Link href="/mis-emociones" class="cc-link">Ver mi dashboard →</Link>
                    </div>
                </div>
            </section>

            <!-- ── Accesos inteligentes según hora ── -->
            <section class="accesos-inteligentes">
                <div class="ai-header">
                    <h2>
                        {{ hora < 12 ? '🌅 Para empezar bien el día' : hora < 20 ? '☀️ Para este momento' : '🌙 Para terminar el día' }}
                    </h2>
                    <span class="ai-badge">Personalizado para ti</span>
                </div>
                <div class="ai-grid">
                    <Link
                        v-for="acceso in accesosPorHora"
                        :key="acceso.nombre"
                        :href="acceso.ruta"
                        class="ai-card"
                        :style="{ backgroundColor: acceso.color }"
                    >
                        <span class="aic-icon">{{ acceso.icon }}</span>
                        <div>
                            <span class="aic-nombre">{{ acceso.nombre }}</span>
                            <span class="aic-desc">{{ acceso.desc }}</span>
                        </div>
                        <span class="aic-arrow">→</span>
                    </Link>
                </div>
            </section>

            <!-- ── Técnicas ── -->
            <section class="tecnicas-section">
                <div class="sec-header">
                    <h2>🌿 Técnicas de bienestar</h2>
                    <span class="sec-count">{{ tecnicas.length }} disponibles</span>
                </div>
                <div class="tecnicas-grid">
                    <Link
                        v-for="(tec, i) in tecnicas"
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

            <!-- ── Secciones principales ── -->
            <section class="secciones-grid">
                <Link href="/hearty" class="seccion-card hearty-card">
                    <div class="sc-emoji">💬</div>
                    <h3>Hearty</h3>
                    <p>Tu asistente emocional disponible siempre. Escucha, aconseja y acompaña.</p>
                    <span class="sc-cta">Hablar ahora →</span>
                </Link>
                <Link href="/mis-emociones" class="seccion-card emociones-card">
                    <div class="sc-emoji">📊</div>
                    <h3>Mis emociones</h3>
                    <p>Registra y visualiza tus patrones emocionales con calendario y estadísticas.</p>
                    <span class="sc-cta">Ver dashboard →</span>
                </Link>
                <Link href="/retos" class="seccion-card retos-card">
                    <div class="sc-emoji">🎯</div>
                    <h3>Retos</h3>
                    <p>Construye hábitos saludables con retos de 7 y 30 días.</p>
                    <span class="sc-cta">Ver retos →</span>
                </Link>
                <Link href="/comunidad" class="seccion-card comunidad-card">
                    <div class="sc-emoji">👥</div>
                    <h3>Comunidad</h3>
                    <p>Un espacio seguro para compartir y apoyarse mutuamente.</p>
                    <span class="sc-cta">Explorar →</span>
                </Link>
                <Link href="/test-bienestar" class="seccion-card test-card">
                    <div class="sc-emoji">🧠</div>
                    <h3>Test semanal</h3>
                    <p>Evaluación PHQ-9 adaptada para conocer tu estado de bienestar.</p>
                    <span class="sc-cta">Hacer test →</span>
                </Link>
                <Link href="/biblioteca" class="seccion-card biblioteca-card">
                    <div class="sc-emoji">📚</div>
                    <h3>Biblioteca</h3>
                    <p>Artículos y guías sobre salud mental escritos para ti.</p>
                    <span class="sc-cta">Explorar →</span>
                </Link>
            </section>

            <!-- ── Banner SOS ── -->
            <section class="sos-banner">
                <div class="sos-banner-content">
                    <span>🆘</span>
                    <div>
                        <h3>¿Estás pasando un momento difícil?</h3>
                        <p>El modo SOS tiene técnicas de emergencia para ayudarte ahora mismo. Si es urgente, llama al 024.</p>
                    </div>
                    <div class="sb-bots">
                        <Link href="/sos" class="btn-sos-banner">Ir al modo SOS →</Link>
                        <a href="tel:024" class="btn-024">📞 024</a>
                    </div>
                </div>
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
    background: linear-gradient(135deg, #E8FAF9 0%, #f0fffe 50%, #ffeef0 100%);
    border-radius: 24px;
    padding: 2.5rem;
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 2rem;
    position: relative;
    overflow: hidden;
    margin-top: 1.5rem;
    align-items: center;
}

.hero-bg-circles { position: absolute; inset: 0; pointer-events: none; }
.hbc { position: absolute; border-radius: 50%; opacity: 0.1; }
.hbc.c1 { width: 350px; height: 350px; background: #4ECDC4; top: -80px; right: 300px; }
.hbc.c2 { width: 250px; height: 250px; background: #E63946; bottom: -60px; left: -40px; }

.hero-left {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.hero-fecha {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.hf-dia  { font-size: 0.85rem; color: #888; font-weight: 600; text-transform: capitalize; }
.hf-hora { font-size: 0.85rem; color: #4ECDC4; font-weight: 700; }

.hero-left h1 {
    font-size: clamp(1.6rem, 3vw, 2.4rem);
    font-weight: 900;
    color: #1a1a1a;
    line-height: 1.2;
    margin: 0;
}

.hero-left h1 strong { color: #4ECDC4; }

.hero-frase {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    background: rgba(255,255,255,0.7);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    border-left: 3px solid #4ECDC4;
}

.hf-emoji { font-size: 1.3rem; flex-shrink: 0; }
.hero-frase p { font-size: 0.92rem; color: #555; font-style: italic; margin: 0; line-height: 1.5; }

.hero-ctas { display: flex; gap: 0.75rem; flex-wrap: wrap; }

.btn-hearty-hero {
    padding: 0.8rem 1.75rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.95rem;
    transition: background 0.2s, transform 0.2s;
}

.btn-hearty-hero:hover { background: #3BAFA7; transform: translateY(-2px); }

.btn-sos-hero {
    padding: 0.8rem 1.25rem;
    background: white;
    color: #E63946;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.95rem;
    border: 2px solid #E63946;
    transition: all 0.2s;
}

.btn-sos-hero:hover { background: #E63946; color: white; }

/* ── Check-in ── */
.hero-checkin { position: relative; z-index: 1; }

.checkin-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.checkin-card h3 { font-size: 0.95rem; font-weight: 700; margin: 0; color: #2D2D2D; }
.checkin-card p  { font-size: 0.8rem; color: #888; margin: 0; }

.checkin-emociones {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.4rem;
}

.ce-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
    padding: 0.65rem 0.25rem;
    border: 2px solid #f0f0f0;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
}

.ce-btn:hover      { border-color: #4ECDC4; background: #E8FAF9; transform: translateY(-2px); }
.ce-btn.seleccionada { border-color: #4ECDC4; background: #E8FAF9; }
.ce-btn:disabled   { opacity: 0.6; cursor: not-allowed; }

.ce-emoji { font-size: 1.5rem; }
.ce-label { font-size: 0.7rem; font-weight: 600; color: #2D2D2D; }

.checkin-completado {
    background: white;
    border-radius: 20px;
    padding: 1.75rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.cc-emoji { font-size: 3rem; animation: bounce-in 0.4s ease; }

@keyframes bounce-in {
    0%   { transform: scale(0); }
    60%  { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.checkin-completado h3 { font-size: 1rem; font-weight: 700; margin: 0; }
.checkin-completado p  { font-size: 0.88rem; color: #666; margin: 0; }
.checkin-completado strong { color: #4ECDC4; }

.cc-link {
    font-size: 0.82rem;
    color: #4ECDC4;
    text-decoration: none;
    font-weight: 700;
}

/* ── Accesos inteligentes ── */
.accesos-inteligentes { display: flex; flex-direction: column; gap: 1rem; }

.ai-header { display: flex; align-items: center; justify-content: space-between; }
.ai-header h2 { font-size: 1.05rem; font-weight: 700; margin: 0; color: #2D2D2D; }

.ai-badge {
    background: #E8FAF9;
    color: #3BAFA7;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1.5px solid #4ECDC4;
}

.ai-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

.ai-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-radius: 14px;
    text-decoration: none;
    border: 2px solid transparent;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}

.ai-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); border-color: #4ECDC4; }

.aic-icon   { font-size: 1.6rem; flex-shrink: 0; }
.aic-nombre { display: block; font-size: 0.88rem; font-weight: 700; color: #2D2D2D; }
.aic-desc   { display: block; font-size: 0.75rem; color: #666; }
.aic-arrow  { margin-left: auto; font-size: 1rem; color: #4ECDC4; font-weight: 700; }

/* ── Técnicas ── */
.tecnicas-section { display: flex; flex-direction: column; gap: 1rem; }

.sec-header { display: flex; align-items: center; justify-content: space-between; }
.sec-header h2  { font-size: 1.05rem; font-weight: 700; margin: 0; }
.sec-count { font-size: 0.78rem; color: #888; font-weight: 600; }

.tecnicas-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.65rem;
}

.tecnica-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.45rem;
    padding: 1.1rem 0.5rem;
    border-radius: 14px;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.tecnica-card:hover { transform: translateY(-4px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }

.tc-icon   { font-size: 1.75rem; }
.tc-nombre { font-size: 0.78rem; font-weight: 700; color: #2D2D2D; text-align: center; }

/* ── Secciones grid ── */
.secciones-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.seccion-card {
    border-radius: 20px;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid rgba(0,0,0,0.05);
}

.seccion-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

.hearty-card    { background: linear-gradient(135deg, #E8FAF9, #d4f5f0); }
.emociones-card { background: linear-gradient(135deg, #fff9c4, #fff3aa); }
.retos-card     { background: linear-gradient(135deg, #d4edda, #c3e6cb); }
.comunidad-card { background: linear-gradient(135deg, #fce4ec, #f8bbd0); }
.test-card      { background: linear-gradient(135deg, #e8d5f5, #dcc4f0); }
.biblioteca-card{ background: linear-gradient(135deg, #e8eaf6, #d9ddf0); }

.sc-emoji { font-size: 2rem; }

.seccion-card h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
}

.seccion-card p {
    font-size: 0.82rem;
    color: #555;
    line-height: 1.5;
    margin: 0;
    flex: 1;
}

.sc-cta {
    font-size: 0.82rem;
    font-weight: 700;
    color: #4ECDC4;
    margin-top: auto;
}

/* ── SOS Banner ── */
.sos-banner {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 20px;
    padding: 1.75rem 2rem;
}

.sos-banner-content {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
}

.sos-banner-content > span { font-size: 2.5rem; flex-shrink: 0; }

.sos-banner-content h3 { font-size: 1rem; font-weight: 700; color: white; margin: 0 0 0.25rem; }
.sos-banner-content p  { font-size: 0.85rem; color: rgba(255,255,255,0.65); margin: 0; }
.sos-banner-content > div:nth-child(2) { flex: 1; }

.sb-bots { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }

.btn-sos-banner {
    padding: 0.75rem 1.5rem;
    background: #E63946;
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.2s;
    white-space: nowrap;
}

.btn-sos-banner:hover { background: #c0303b; }

.btn-024 {
    padding: 0.75rem 1.25rem;
    background: white;
    color: #E63946;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-024:hover { background: #E63946; color: white; }

/* ── Responsive ── */
@media (max-width: 1000px) {
    .home-hero      { grid-template-columns: 1fr; }
    .hero-checkin   { max-width: 440px; }
    .secciones-grid { grid-template-columns: repeat(2, 1fr); }
    .tecnicas-grid  { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 700px) {
    .ai-grid        { grid-template-columns: 1fr; }
    .secciones-grid { grid-template-columns: 1fr; }
    .tecnicas-grid  { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 480px) {
    .tecnicas-grid  { grid-template-columns: repeat(2, 1fr); }
    .home-hero      { padding: 1.5rem; }
}
</style>