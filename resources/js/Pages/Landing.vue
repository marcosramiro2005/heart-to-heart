<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

const scrollY = ref(0)
const onScroll = () => { scrollY.value = window.scrollY }
const mobileMenuOpen = ref(false)
const toggleMobileMenu = () => { mobileMenuOpen.value = !mobileMenuOpen.value }
const closeMobileMenu = () => { mobileMenuOpen.value = false }

onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

const chatDemo = ref([
    { sender: 'hearty', texto: '¡Hola! Soy Hearty 💚 ¿Cómo te sientes hoy?', visible: false },
    { sender: 'user',   texto: 'Estoy muy ansioso por los exámenes...', visible: false },
    { sender: 'hearty', texto: 'Entiendo 💙 Los exámenes generan mucha presión. ¿Cuándo los tienes?', visible: false },
    { sender: 'user',   texto: 'La semana que viene y no he estudiado nada', visible: false },
    { sender: 'hearty', texto: 'Primero calmemos esa ansiedad para que puedas concentrarte. Prueba la respiración 4-7-8 ahora mismo 🫁', visible: false },
])

let chatInterval = null
onMounted(() => {
    let i = 0
    chatInterval = setInterval(() => {
        if (i < chatDemo.value.length) { chatDemo.value[i].visible = true; i++ }
        else {
            i = 0
            chatDemo.value.forEach(m => m.visible = false)
            setTimeout(() => { chatDemo.value[0].visible = true; i = 1 }, 1200)
        }
    }, 1900)
})
onUnmounted(() => clearInterval(chatInterval))

onMounted(async () => {
    await nextTick()
    const obs = new IntersectionObserver(
        entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible') }),
        { threshold: 0.1 }
    )
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el))
})

const statsAnimated = ref(false)
const counters = ref([
    { target: 15,  current: 0, suffix: '+',  label: 'Técnicas de bienestar' },
    { target: 100, current: 0, suffix: '%',  label: 'Gratuito para siempre' },
    { target: 24,  current: 0, suffix: '/7', label: 'Hearty disponible' },
    { target: 8,   current: 0, suffix: '',   label: 'Categorías emocionales' },
])
onMounted(async () => {
    await nextTick()
    const el = document.querySelector('.stats-row')
    if (!el) return
    const sObs = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting && !statsAnimated.value) {
            statsAnimated.value = true
            counters.value.forEach(c => {
                const steps = 60
                const inc = c.target / steps
                let count = 0
                const t = setInterval(() => {
                    count++
                    c.current = Math.min(Math.round(inc * count), c.target)
                    if (count >= steps) clearInterval(t)
                }, 22)
            })
        }
    }, { threshold: 0.6 })
    sObs.observe(el)
})

const pasos = [
    { num: '01', emoji: '✍️', titulo: 'Crea tu cuenta',      desc: 'Regístrate en 30 segundos. Gratis, sin tarjeta de crédito, sin trucos.' },
    { num: '02', emoji: '💬', titulo: 'Habla con Hearty',    desc: 'Cuéntale cómo te sientes. Te escucha, detecta tu estado y te guía.' },
    { num: '03', emoji: '🌱', titulo: 'Crece cada día',      desc: 'Descubre tus patrones, desbloquea logros y construye hábitos de bienestar.' },
]

const funciones = [
    { emoji: '💬', titulo: 'Hearty — Tu guía emocional',   desc: 'Un asistente que te escucha de verdad, sin juzgarte. Detecta cómo te sientes y sugiere técnicas personalizadas. Disponible 24/7.', accent: '#4ECDC4' },
    { emoji: '📊', titulo: 'Dashboard emocional',           desc: 'Registra tus emociones diariamente y descubre tus patrones. Calendario visual, estadísticas y racha de días.',                   accent: '#45B7D1' },
    { emoji: '🌱', titulo: 'Plan semanal personalizado',    desc: 'Genera automáticamente un plan de 7 días con técnicas adaptadas a tu objetivo: ansiedad, tristeza, estrés o sueño.',             accent: '#6BCF7F' },
    { emoji: '🎯', titulo: 'Modo Focus con Pomodoro',       desc: 'Técnica Pomodoro con música de fondo en tiempo real. Organiza tus tareas y mantén la concentración sin esfuerzo.',              accent: '#FFB347' },
    { emoji: '🏆', titulo: 'Logros y gamificación',         desc: '14 logros desbloqueables y 6 niveles de progreso: desde Semilla hasta Maestro del Bienestar.',                                   accent: '#FFD700' },
    { emoji: '🆘', titulo: 'Modo SOS de emergencia',        desc: 'Acceso inmediato a técnicas de emergencia: respiración 4-7-8, grounding y afirmaciones de calma cuando más lo necesitas.',      accent: '#E63946' },
    { emoji: '👥', titulo: 'Comunidad de apoyo',            desc: 'Foro de autoayuda con categorías, posts anónimos, sistema de likes y posts trending de la semana.',                              accent: '#9B8EC4' },
    { emoji: '🧠', titulo: 'Test PHQ-9 semanal',            desc: 'Evaluación semanal adaptada del cuestionario clínico PHQ-9 con recomendaciones personalizadas para ti.',                         accent: '#4ECDC4' },
]

const tecnicas = [
    { emoji: '🫁', nombre: 'Respiración 4-7-8',   desc: 'Para la ansiedad aguda',  color: '#d4edda' },
    { emoji: '🧘', nombre: 'Meditación guiada',    desc: 'Calma la mente',          color: '#e8d5f5' },
    { emoji: '🌍', nombre: 'Grounding 5-4-3-2-1', desc: 'Ancla al presente',       color: '#d0eaf8' },
    { emoji: '📓', nombre: 'Diario emocional',     desc: 'Procesa tus emociones',   color: '#fff9c4' },
    { emoji: '🏃', nombre: 'Ejercicio terapéutico','desc': 'Libera endorfinas',     color: '#ffd5d5' },
    { emoji: '🎵', nombre: 'Sonidos relajantes',   desc: 'Ambiente de calma',       color: '#ffecd2' },
    { emoji: '💗', nombre: 'Autocompasión',         desc: 'Trátate bien',           color: '#fce4ec' },
    { emoji: '🌈', nombre: 'Visualización guiada', desc: 'Viajes mentales',         color: '#ffd5e5' },
]

const testimonios = [
    { texto: 'Hearty me ayudó a gestionar mi ansiedad antes de los exámenes. Ahora uso la respiración 4-7-8 y me cambia el día entero.', nombre: 'Lucía M.', edad: '22 años', emoji: '🌸' },
    { texto: 'El dashboard emocional me hizo darme cuenta de mis patrones. Antes no entendía por qué me ponía mal ciertos días. Ahora sí.', nombre: 'Carlos R.', edad: '19 años', emoji: '🌿' },
    { texto: 'El modo SOS me salvó en un momento de crisis real. Tener esas herramientas al instante hace toda la diferencia del mundo.', nombre: 'Andrea P.', edad: '25 años', emoji: '💙' },
]

const faqs = [
    { p: '¿Es completamente gratuito?',   r: 'Sí, todas las funciones de Heart to Heart son gratuitas. No hay versión premium ni pagos ocultos.' },
    { p: '¿Mis datos son privados?',       r: 'Absolutamente. Tus datos emocionales son completamente privados. Nunca los compartimos con nadie.' },
    { p: '¿Puede sustituir a un psicólogo?', r: 'No. Heart to Heart es una herramienta de apoyo al bienestar, no un servicio clínico. Si necesitas ayuda profesional, búscala.' },
    { p: '¿Qué es Hearty exactamente?',    r: 'Hearty es nuestro asistente emocional que detecta cómo te sientes y te guía hacia las técnicas más adecuadas para ti en ese momento.' },
    { p: '¿Necesito experiencia previa?',  r: 'Para nada. Todo está diseñado para ser accesible a cualquier persona, independientemente de su experiencia con el bienestar.' },
]

const faqAbierta = ref(null)
</script>

<template>
    <div class="landing">

        <!-- ── Navbar ── -->
        <nav class="land-nav" :class="{ scrolled: scrollY > 50 }">
            <div class="land-nav-inner">
                <div class="land-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </div>
                <div class="land-nav-links">
                    <a href="#como-funciona" @click="closeMobileMenu">Cómo funciona</a>
                    <a href="#funciones" @click="closeMobileMenu">Funciones</a>
                    <a href="#tecnicas" @click="closeMobileMenu">Técnicas</a>
                    <a href="#faq" @click="closeMobileMenu">FAQ</a>
                </div>
                <div class="land-nav-ctas">
                    <Link href="/login" class="btn-login">Iniciar sesión</Link>
                    <Link href="/register" class="btn-register">Empezar gratis</Link>
                </div>
                <!-- Hamburguesa móvil -->
                <button class="land-hamburger" @click="toggleMobileMenu" :class="{ open: mobileMenuOpen }" aria-label="Menú">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <!-- Menú móvil -->
            <Transition name="land-slide">
                <div v-if="mobileMenuOpen" class="land-mobile-menu">
                    <a href="#como-funciona" @click="closeMobileMenu">🚀 Cómo funciona</a>
                    <a href="#funciones" @click="closeMobileMenu">✨ Funciones</a>
                    <a href="#tecnicas" @click="closeMobileMenu">🌿 Técnicas</a>
                    <a href="#faq" @click="closeMobileMenu">❓ FAQ</a>
                    <div class="lmm-divider"></div>
                    <Link href="/login" class="lmm-btn-login" @click="closeMobileMenu">Iniciar sesión</Link>
                    <Link href="/register" class="lmm-btn-register" @click="closeMobileMenu">💚 Empezar gratis</Link>
                </div>
            </Transition>
        </nav>

        <!-- ── Hero ── -->
        <section class="hero">
            <div class="hero-bg">
                <div class="blob b1"></div>
                <div class="blob b2"></div>
                <div class="blob b3"></div>
                <div class="blob b4"></div>
            </div>

            <div class="hero-wrapper">
                <div class="hero-content">
                    <div class="hero-badge hero-anim-1">
                        <span class="badge-dot"></span>
                        100% gratuito · Sin anuncios · Sin datos vendidos
                    </div>
                    <h1 class="hero-anim-2">
                        Tu bienestar emocional<br>
                        <span class="gradient-text">empieza aquí</span>
                    </h1>
                    <p class="hero-sub hero-anim-3">
                        Heart to Heart es tu espacio seguro para cuidar tu salud mental cada día.
                        Habla con Hearty, practica técnicas y descubre tus patrones emocionales.
                    </p>
                    <div class="hero-ctas hero-anim-4">
                        <Link href="/register" class="btn-primary">
                            <span>💚 Empezar gratis ahora</span>
                            <div class="btn-shimmer"></div>
                        </Link>
                        <Link href="/login" class="btn-ghost">Ya tengo cuenta →</Link>
                    </div>
                    <div class="trust-row hero-anim-5">
                        <span>✓ Sin tarjeta de crédito</span>
                        <span>✓ Privacidad total</span>
                        <span>✓ Siempre gratis</span>
                    </div>
                </div>

                <div class="hero-demo hero-anim-6">
                    <div class="hd-glow"></div>
                    <div class="hd-card">
                        <div class="hdc-header">
                            <div class="hdc-avatar">💚</div>
                            <div>
                                <span class="hdc-name">Hearty</span>
                                <span class="hdc-status"><span class="status-dot"></span>En línea</span>
                            </div>
                        </div>
                        <div class="hdc-chat">
                            <TransitionGroup name="msg">
                                <div
                                    v-for="(msg, i) in chatDemo.filter(m => m.visible)"
                                    :key="i"
                                    class="hdc-msg"
                                    :class="msg.sender === 'user' ? 'hdc-user' : 'hdc-hearty'"
                                >{{ msg.texto }}</div>
                            </TransitionGroup>
                        </div>
                    </div>
                    <div class="hd-float hdf1"><span>🔥</span><div><b>Racha</b><small>7 días</small></div></div>
                    <div class="hd-float hdf2"><span>🏆</span><div><b>Logro</b><small>Desbloqueado</small></div></div>
                    <div class="hd-float hdf3"><span>😌</span><div><b>Hoy</b><small>Tranquilo/a</small></div></div>
                </div>
            </div>
        </section>

        <!-- ── Stats ── -->
        <section class="stats-section">
            <div class="stats-row">
                <div v-for="c in counters" :key="c.label" class="stat-item">
                    <span class="stat-val">{{ c.current }}{{ c.suffix }}</span>
                    <span class="stat-lab">{{ c.label }}</span>
                </div>
            </div>
        </section>

        <!-- ── Cómo funciona ── -->
        <section class="steps-section" id="como-funciona">
            <div class="section-inner">
                <div class="section-tag reveal">🚀 Empezar es fácil</div>
                <h2 class="reveal">En 3 pasos, empieza a sentirte mejor</h2>
                <p class="section-sub reveal">No hace falta preparación. Ni experiencia. Solo entrar.</p>
                <div class="steps-grid">
                    <div
                        v-for="(paso, i) in pasos"
                        :key="paso.num"
                        class="step-card reveal"
                        :style="{ '--d': i * 0.12 + 's' }"
                    >
                        <div class="step-num">{{ paso.num }}</div>
                        <div class="step-emoji">{{ paso.emoji }}</div>
                        <h3>{{ paso.titulo }}</h3>
                        <p>{{ paso.desc }}</p>
                    </div>
                </div>
                <Link href="/register" class="btn-primary reveal">
                    <span>💚 Crear mi cuenta gratis</span>
                    <div class="btn-shimmer"></div>
                </Link>
            </div>
        </section>

        <!-- ── Funciones ── -->
        <section class="funciones-section" id="funciones">
            <div class="section-inner">
                <div class="section-tag reveal">✨ Todo lo que necesitas</div>
                <h2 class="reveal">Herramientas reales para tu bienestar</h2>
                <p class="section-sub reveal">Heart to Heart no es solo una app — es un ecosistema completo de cuidado emocional</p>
                <div class="funciones-grid">
                    <div
                        v-for="(f, i) in funciones"
                        :key="f.titulo"
                        class="funcion-card reveal"
                        :style="{ '--d': (i % 4) * 0.1 + 's', '--accent': f.accent }"
                    >
                        <div class="fc-icon-wrap" :style="{ background: f.accent + '22' }">
                            <span class="fc-emoji">{{ f.emoji }}</span>
                        </div>
                        <h3>{{ f.titulo }}</h3>
                        <p>{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Hearty feature ── -->
        <section class="hearty-feature">
            <div class="section-inner hf-inner">
                <div class="hf-texto reveal">
                    <div class="section-tag">🤖 Tu guía emocional</div>
                    <h2>Conoce a Hearty</h2>
                    <p>Hearty es el corazón de Heart to Heart. Un asistente emocional que te escucha, detecta cómo te sientes y te guía hacia las técnicas más adecuadas para ti en cada momento.</p>
                    <ul class="hf-lista">
                        <li><span class="hfl-dot">💬</span> Conversación natural y empática</li>
                        <li><span class="hfl-dot">🧠</span> Detecta 8 categorías emocionales</li>
                        <li><span class="hfl-dot">🎯</span> Sugiere técnicas personalizadas</li>
                        <li><span class="hfl-dot">💙</span> Memoria de la conversación</li>
                        <li><span class="hfl-dot">🚨</span> Detecta situaciones de crisis</li>
                        <li><span class="hfl-dot">🔒</span> Todo es completamente privado</li>
                    </ul>
                    <Link href="/register" class="btn-primary" style="width:fit-content">
                        <span>Hablar con Hearty →</span>
                        <div class="btn-shimmer"></div>
                    </Link>
                </div>
                <div class="hf-visual reveal">
                    <div class="hfv-glow"></div>
                    <div class="hfv-chat">
                        <div class="hfv-header">
                            <div class="hfv-avatar">💚</div>
                            <div>
                                <span class="hfv-name">Hearty</span>
                                <span class="hfv-online"><span class="status-dot"></span>En línea</span>
                            </div>
                        </div>
                        <div class="hfv-msg hearty">¡Hola de nuevo! La última vez me contabas que tenías ansiedad. ¿Cómo estás hoy? 💙</div>
                        <div class="hfv-msg user">Hoy estoy un poco mejor, gracias</div>
                        <div class="hfv-msg hearty">¡Me alegra mucho! 😊 Noto que mejoras. ¿Quieres practicar algo para mantener ese bienestar?</div>
                        <div class="hfv-opciones">
                            <span>🧘 Meditar</span>
                            <span>🫁 Respirar</span>
                            <span>📓 Diario</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Técnicas ── -->
        <section class="tecnicas-section" id="tecnicas">
            <div class="section-inner">
                <div class="section-tag reveal">🌿 15+ técnicas</div>
                <h2 class="reveal">Para cada momento y cada emoción</h2>
                <p class="section-sub reveal">Desde técnicas de emergencia hasta prácticas para construir hábitos duraderos</p>
                <div class="tecnicas-grid">
                    <div
                        v-for="(tec, i) in tecnicas"
                        :key="tec.nombre"
                        class="tec-card reveal"
                        :style="{ background: tec.color, '--d': (i % 4) * 0.08 + 's' }"
                    >
                        <span class="tec-emoji">{{ tec.emoji }}</span>
                        <h3>{{ tec.nombre }}</h3>
                        <p>{{ tec.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Testimonios ── -->
        <section class="testimonios-section">
            <div class="section-inner">
                <div class="section-tag reveal">💬 Historias reales</div>
                <h2 class="reveal">Lo que dicen quienes ya lo usan</h2>
                <div class="testimonios-grid">
                    <div
                        v-for="(t, i) in testimonios"
                        :key="t.nombre"
                        class="testimonio-card reveal"
                        :style="{ '--d': i * 0.14 + 's' }"
                    >
                        <div class="tc-comillas">"</div>
                        <p class="tc-texto">{{ t.texto }}</p>
                        <div class="tc-author">
                            <div class="tc-avatar">{{ t.emoji }}</div>
                            <div>
                                <span class="tc-nombre">{{ t.nombre }}</span>
                                <span class="tc-edad">{{ t.edad }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── SOS ── -->
        <section class="sos-feature">
            <div class="section-inner">
                <div class="sf-card reveal">
                    <div class="sfc-left">
                        <div class="sfc-icon-wrap">
                            <span>🆘</span>
                        </div>
                        <div>
                            <h3>Modo SOS — Para cuando más lo necesitas</h3>
                            <p>Cuando la ansiedad o la tristeza se vuelven abrumadoras, el modo SOS te da acceso inmediato a técnicas de emergencia probadas: respiración 4-7-8, grounding y afirmaciones de calma.</p>
                        </div>
                    </div>
                    <div class="sfc-right">
                        <div class="sfc-tec"><span>🫁</span><span>Respiración de emergencia</span></div>
                        <div class="sfc-tec"><span>🌍</span><span>Grounding 5-4-3-2-1</span></div>
                        <div class="sfc-tec"><span>💙</span><span>Afirmaciones de calma</span></div>
                        <div class="sfc-aviso">📞 Con acceso directo al <strong>024</strong></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── FAQ ── -->
        <section class="faq-section" id="faq">
            <div class="section-inner faq-inner">
                <div class="section-tag reveal">❓ Preguntas frecuentes</div>
                <h2 class="reveal">Todo lo que necesitas saber</h2>
                <div class="faq-lista reveal">
                    <div
                        v-for="(faq, i) in faqs"
                        :key="i"
                        class="faq-item"
                        :class="{ abierta: faqAbierta === i }"
                        @click="faqAbierta = faqAbierta === i ? null : i"
                    >
                        <div class="faq-pregunta">
                            <span>{{ faq.p }}</span>
                            <span class="faq-icono">{{ faqAbierta === i ? '−' : '+' }}</span>
                        </div>
                        <Transition name="faq">
                            <p v-if="faqAbierta === i" class="faq-respuesta">{{ faq.r }}</p>
                        </Transition>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CTA Final ── -->
        <section class="cta-final">
            <div class="cta-blobs">
                <div class="cta-blob cb1"></div>
                <div class="cta-blob cb2"></div>
                <div class="cta-blob cb3"></div>
            </div>
            <div class="cta-content reveal">
                <div class="cta-badge">💚 Heart to Heart</div>
                <h2>Tu bienestar empieza hoy</h2>
                <p>Únete gratis. Sin tarjeta de crédito. Sin compromisos. Sin excusas.</p>
                <div class="cta-btns">
                    <Link href="/register" class="btn-primary btn-xl">
                        <span>💚 Crear mi cuenta gratis</span>
                        <div class="btn-shimmer"></div>
                    </Link>
                    <Link href="/login" class="btn-ghost-light">Ya tengo cuenta</Link>
                </div>
                <p class="cta-disclaimer">
                    ⚠️ Heart to Heart no sustituye la atención psicológica profesional.
                    Si estás en crisis llama al <strong>024</strong>.
                </p>
            </div>
        </section>

        <!-- ── Footer ── -->
        <footer class="land-footer">
            <div class="lf-inner">
                <div class="lf-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </div>
                <p>© {{ new Date().getFullYear() }} Heart to Heart · TFG DAW · Marcos Ramiro Roig · CPIFP Los Enlaces, Zaragoza</p>
                <div class="lf-links">
                    <Link href="/login">Iniciar sesión</Link>
                    <Link href="/register">Registrarse</Link>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }

.landing {
    font-family: 'Segoe UI', system-ui, sans-serif;
    color: #2D2D2D;
    overflow-x: hidden;
}

/* ─────────────────────────────────────
   SCROLL REVEAL
───────────────────────────────────── */
.reveal {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity 0.65s ease calc(var(--d, 0s)), transform 0.65s ease calc(var(--d, 0s));
}
.reveal.visible {
    opacity: 1;
    transform: translateY(0);
}

/* ─────────────────────────────────────
   SHARED TOKENS
───────────────────────────────────── */
.gradient-text {
    background: linear-gradient(135deg, #4ECDC4 0%, #45B7D1 50%, #6BCF7F 100%);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: grad-shift 4s ease infinite;
}
@keyframes grad-shift {
    0%,100% { background-position: 0% 50%; }
    50%      { background-position: 100% 50%; }
}

.section-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #E8FAF9;
    color: #3BAFA7;
    padding: 0.35rem 1rem;
    border-radius: 25px;
    font-size: 0.82rem;
    font-weight: 700;
    border: 1.5px solid #4ECDC4;
}

.section-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 5.5rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
}

.section-inner h2 {
    font-size: clamp(1.7rem, 3vw, 2.5rem);
    font-weight: 900;
    text-align: center;
    color: #1a1a1a;
}

.section-sub {
    font-size: 1rem;
    color: #666;
    text-align: center;
    max-width: 560px;
    line-height: 1.65;
}

/* ─────────────────────────────────────
   BUTTONS
───────────────────────────────────── */
.btn-primary {
    position: relative;
    overflow: hidden;
    padding: 0.9rem 2rem;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white;
    font-weight: 700;
    font-size: 1rem;
    border-radius: 30px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 20px rgba(78,205,196,0.35);
}
.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 32px rgba(78,205,196,0.5);
}
.btn-primary.btn-xl {
    padding: 1.1rem 2.75rem;
    font-size: 1.05rem;
}

.btn-shimmer {
    position: absolute;
    top: -50%; left: -75%;
    width: 50%; height: 200%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.28), transparent);
    transform: skewX(-20deg);
    animation: shimmer 2.8s infinite;
}
@keyframes shimmer {
    0%   { left: -75%; }
    100% { left: 130%; }
}

.btn-ghost {
    padding: 0.9rem 1.6rem;
    color: #4ECDC4;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    border: 2px solid #4ECDC4;
    border-radius: 30px;
    transition: all 0.2s;
}
.btn-ghost:hover { background: #4ECDC4; color: white; }

.btn-ghost-light {
    padding: 1rem 2rem;
    color: rgba(255,255,255,0.75);
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    border: 2px solid rgba(255,255,255,0.25);
    border-radius: 30px;
    transition: all 0.2s;
}
.btn-ghost-light:hover { border-color: white; color: white; }

/* ─────────────────────────────────────
   NAVBAR
───────────────────────────────────── */
.land-nav {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
    padding: 1rem 0;
    transition: background 0.3s, box-shadow 0.3s;
}
.land-nav.scrolled {
    background: rgba(255,255,255,0.95);
    box-shadow: 0 2px 24px rgba(0,0,0,0.08);
    backdrop-filter: blur(12px);
}
.land-nav-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}
.land-logo { display: flex; align-items: center; gap: 0.5rem; }
.land-logo img  { width: 38px; }
.land-logo span { font-weight: 800; font-size: 0.85rem; color: #E63946; letter-spacing: 0.08em; }
.land-nav-links { display: flex; gap: 2rem; }
.land-nav-links a {
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 600;
    color: #2D2D2D;
    transition: color 0.2s;
}
.land-nav-links a:hover { color: #4ECDC4; }
.land-nav-ctas { display: flex; gap: 0.75rem; align-items: center; }

.btn-login {
    padding: 0.55rem 1.25rem;
    font-size: 0.88rem;
    font-weight: 600;
    color: #4ECDC4;
    text-decoration: none;
    border: 2px solid #4ECDC4;
    border-radius: 25px;
    transition: all 0.2s;
}
.btn-login:hover { background: #4ECDC4; color: white; }

.btn-register {
    padding: 0.55rem 1.25rem;
    font-size: 0.88rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    border-radius: 25px;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 12px rgba(78,205,196,0.3);
}
.btn-register:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(78,205,196,0.45); }

/* ─────────────────────────────────────
   HERO
───────────────────────────────────── */
.hero {
    min-height: 100vh;
    position: relative;
    overflow: hidden;
    background: linear-gradient(160deg, #f0fffe 0%, #e8f8f5 45%, #fdf0ff 100%);
    display: flex;
    align-items: center;
}

.hero-bg { position: absolute; inset: 0; pointer-events: none; }

.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.45;
}
.b1 { width: 600px; height: 600px; background: radial-gradient(circle, #4ECDC4, transparent); top: -150px; right: -100px; animation: blob1 8s ease-in-out infinite; }
.b2 { width: 400px; height: 400px; background: radial-gradient(circle, #9B8EC4, transparent); bottom: -80px; left: -80px;  animation: blob2 10s ease-in-out infinite; }
.b3 { width: 300px; height: 300px; background: radial-gradient(circle, #6BCF7F, transparent); top: 40%; left: 20%;          animation: blob3 7s ease-in-out infinite; }
.b4 { width: 250px; height: 250px; background: radial-gradient(circle, #FFB347, transparent); bottom: 10%; right: 30%;      animation: blob1 9s ease-in-out infinite reverse; }

@keyframes blob1 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(30px,-30px) scale(1.08); } }
@keyframes blob2 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(-25px,25px) scale(1.12); } }
@keyframes blob3 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(20px,-20px) scale(0.92); } }

.hero-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 8rem 2rem 4rem;
    width: 100%;
    display: flex;
    align-items: center;
    gap: 4rem;
    position: relative;
    z-index: 1;
}

.hero-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1.6rem;
}

/* Hero entrance animations */
.hero-anim-1 { animation: hero-up 0.7s ease 0.1s both; }
.hero-anim-2 { animation: hero-up 0.7s ease 0.25s both; }
.hero-anim-3 { animation: hero-up 0.7s ease 0.4s both; }
.hero-anim-4 { animation: hero-up 0.7s ease 0.55s both; }
.hero-anim-5 { animation: hero-up 0.7s ease 0.7s both; }
.hero-anim-6 { animation: hero-up 0.7s ease 0.4s both; }
@keyframes hero-up { from { opacity: 0; transform: translateY(32px); } to { opacity: 1; transform: translateY(0); } }

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    background: white;
    border: 1.5px solid #4ECDC4;
    border-radius: 25px;
    padding: 0.45rem 1.1rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #3BAFA7;
    width: fit-content;
    box-shadow: 0 2px 12px rgba(78,205,196,0.2);
}
.badge-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #4ECDC4;
    animation: pulse-dot 2s ease infinite;
}
@keyframes pulse-dot {
    0%,100% { box-shadow: 0 0 0 0 rgba(78,205,196,0.5); }
    50%      { box-shadow: 0 0 0 6px rgba(78,205,196,0); }
}

.hero h1 {
    font-size: clamp(2.2rem, 4.5vw, 3.6rem);
    font-weight: 900;
    line-height: 1.12;
    color: #1a1a1a;
}

.hero-sub {
    font-size: 1.05rem;
    color: #555;
    line-height: 1.75;
    max-width: 520px;
}

.hero-ctas { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }

.trust-row {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.trust-row span {
    font-size: 0.82rem;
    color: #888;
    font-weight: 600;
}

/* Hero Demo */
.hero-demo {
    flex-shrink: 0;
    width: 360px;
    position: relative;
}
.hd-glow {
    position: absolute;
    inset: -20px;
    border-radius: 28px;
    background: linear-gradient(135deg, rgba(78,205,196,0.2), rgba(107,207,127,0.1));
    filter: blur(20px);
    pointer-events: none;
}
.hd-card {
    position: relative;
    background: white;
    border-radius: 22px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.13), 0 4px 16px rgba(78,205,196,0.15);
    overflow: hidden;
}
.hdc-header {
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.hdc-avatar {
    width: 40px; height: 40px;
    background: rgba(255,255,255,0.22);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
.hdc-name   { display: block; font-weight: 700; font-size: 0.92rem; color: white; }
.hdc-status { display: flex; align-items: center; gap: 0.3rem; font-size: 0.72rem; color: rgba(255,255,255,0.85); }
.status-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #7fffad;
    animation: pulse-dot 2s ease infinite;
}
.hdc-chat {
    padding: 1rem;
    min-height: 240px;
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    background: #f8fffe;
}
.hdc-msg {
    padding: 0.65rem 0.9rem;
    border-radius: 14px;
    font-size: 0.84rem;
    line-height: 1.5;
    max-width: 85%;
}
.hdc-hearty {
    background: white;
    color: #2D2D2D;
    border-radius: 4px 14px 14px 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    align-self: flex-start;
}
.hdc-user {
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white;
    border-radius: 14px 4px 14px 14px;
    align-self: flex-end;
    margin-left: auto;
}

.msg-enter-active { transition: opacity 0.45s ease, transform 0.45s ease; }
.msg-enter-from   { opacity: 0; transform: translateY(10px); }

/* Floating cards */
.hd-float {
    position: absolute;
    background: white;
    border-radius: 14px;
    padding: 0.65rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    font-size: 1.3rem;
    animation: float-card 3.5s ease-in-out infinite;
}
.hdf1 { bottom: -18px; right: -22px; animation-delay: 0s; }
.hdf2 { top: -14px;   right: -28px; animation-delay: 1.2s; }
.hdf3 { top: 50%;     left: -28px;  animation-delay: 2.4s; }
@keyframes float-card { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-9px); } }
.hd-float b     { display: block; font-size: 0.8rem; color: #2D2D2D; font-weight: 700; }
.hd-float small { display: block; font-size: 0.65rem; color: #aaa; font-weight: 600; }

/* ─────────────────────────────────────
   STATS
───────────────────────────────────── */
.stats-section {
    background: white;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
}
.stats-row {
    max-width: 900px;
    margin: 0 auto;
    padding: 2.5rem 2rem;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    text-align: center;
}
.stat-item { display: flex; flex-direction: column; gap: 0.2rem; }
.stat-val  { font-size: 2.2rem; font-weight: 900; color: #4ECDC4; line-height: 1; }
.stat-lab  { font-size: 0.78rem; color: #888; font-weight: 600; }

/* ─────────────────────────────────────
   STEPS
───────────────────────────────────── */
.steps-section { background: #fafffe; }

.steps-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    width: 100%;
    margin: 1rem 0;
    position: relative;
}
.steps-grid::before {
    content: '';
    position: absolute;
    top: 2.2rem;
    left: calc(100% / 6);
    right: calc(100% / 6);
    height: 2px;
    background: linear-gradient(90deg, #4ECDC4, #6BCF7F);
    opacity: 0.3;
    pointer-events: none;
}

.step-card {
    background: white;
    border-radius: 20px;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
    border: 1.5px solid #f0f0f0;
    transition: transform 0.25s, box-shadow 0.25s;
    position: relative;
}
.step-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 36px rgba(78,205,196,0.15);
    border-color: #4ECDC4;
}
.step-num {
    font-size: 0.72rem;
    font-weight: 900;
    color: #4ECDC4;
    letter-spacing: 0.1em;
    background: #E8FAF9;
    border-radius: 8px;
    padding: 0.2rem 0.55rem;
}
.step-emoji { font-size: 2.2rem; }
.step-card h3 { font-size: 1.05rem; font-weight: 800; color: #1a1a1a; }
.step-card p  { font-size: 0.88rem; color: #666; line-height: 1.6; }

/* ─────────────────────────────────────
   FUNCIONES
───────────────────────────────────── */
.funciones-section { background: white; }

.funciones-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    width: 100%;
    margin-top: 0.5rem;
}
.funcion-card {
    background: white;
    border-radius: 18px;
    padding: 1.5rem;
    border: 1.5px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
    cursor: default;
}
.funcion-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.09);
    border-color: var(--accent);
}
.fc-icon-wrap {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.fc-emoji    { font-size: 1.6rem; }
.funcion-card h3 { font-size: 0.9rem; font-weight: 800; color: #1a1a1a; }
.funcion-card p  { font-size: 0.8rem; color: #666; line-height: 1.55; }

/* ─────────────────────────────────────
   HEARTY FEATURE
───────────────────────────────────── */
.hearty-feature { background: linear-gradient(160deg, #f8fffd 0%, #f0f8ff 100%); }

.hf-inner {
    flex-direction: row !important;
    align-items: center !important;
    gap: 4rem !important;
}
.hf-texto {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
}
.hf-texto h2  { text-align: left; font-size: clamp(1.6rem, 3vw, 2.4rem); font-weight: 900; }
.hf-texto > p { font-size: 1rem; color: #555; line-height: 1.75; }

.hf-lista {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}
.hf-lista li {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.92rem;
    color: #444;
    font-weight: 500;
}

.hf-visual { flex-shrink: 0; width: 340px; position: relative; }
.hfv-glow {
    position: absolute;
    inset: -15px;
    border-radius: 30px;
    background: linear-gradient(135deg, rgba(78,205,196,0.15), rgba(107,207,127,0.1));
    filter: blur(16px);
    pointer-events: none;
}
.hfv-chat {
    position: relative;
    background: white;
    border-radius: 22px;
    padding: 1.25rem;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}
.hfv-header {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f0f0f0;
}
.hfv-avatar {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}
.hfv-name   { display: block; font-size: 0.88rem; font-weight: 700; color: #2D2D2D; }
.hfv-online { display: flex; align-items: center; gap: 0.3rem; font-size: 0.72rem; color: #4ECDC4; font-weight: 600; }

.hfv-msg {
    padding: 0.7rem 0.95rem;
    border-radius: 14px;
    font-size: 0.85rem;
    line-height: 1.5;
    max-width: 90%;
}
.hfv-msg.hearty {
    background: #f8fffe;
    color: #2D2D2D;
    border-radius: 4px 14px 14px 14px;
    border: 1px solid #eef9f8;
}
.hfv-msg.user {
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white;
    border-radius: 14px 4px 14px 14px;
    align-self: flex-end;
    margin-left: auto;
}
.hfv-opciones { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.hfv-opciones span {
    padding: 0.3rem 0.75rem;
    background: #E8FAF9;
    border: 1.5px solid #4ECDC4;
    border-radius: 15px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #3BAFA7;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
.hfv-opciones span:hover { background: #4ECDC4; color: white; }

/* ─────────────────────────────────────
   TÉCNICAS
───────────────────────────────────── */
.tecnicas-section { background: #fafffe; }

.tecnicas-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.85rem;
    width: 100%;
}
.tec-card {
    border-radius: 16px;
    padding: 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    transition: transform 0.25s, box-shadow 0.25s;
}
.tec-card:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
.tec-emoji { font-size: 1.8rem; }
.tec-card h3 { font-size: 0.88rem; font-weight: 800; color: #2D2D2D; }
.tec-card p  { font-size: 0.78rem; color: #666; }

/* ─────────────────────────────────────
   TESTIMONIOS
───────────────────────────────────── */
.testimonios-section { background: white; }

.testimonios-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    width: 100%;
    margin-top: 0.5rem;
}
.testimonio-card {
    background: linear-gradient(160deg, #f8fffe, #f0f9ff);
    border-radius: 20px;
    padding: 2rem;
    border: 1.5px solid #e8f8f5;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    position: relative;
    transition: transform 0.25s, box-shadow 0.25s;
}
.testimonio-card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(78,205,196,0.12); }

.tc-comillas {
    font-size: 4rem;
    line-height: 1;
    color: #4ECDC4;
    opacity: 0.35;
    font-family: Georgia, serif;
    position: absolute;
    top: 0.75rem;
    left: 1.25rem;
}
.tc-texto {
    font-size: 0.92rem;
    color: #444;
    line-height: 1.7;
    margin-top: 1.5rem;
    font-style: italic;
}
.tc-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-top: 1px solid #e8f8f5;
    padding-top: 1rem;
}
.tc-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #E8FAF9, #d0eaf8);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.tc-nombre { display: block; font-size: 0.88rem; font-weight: 700; color: #2D2D2D; }
.tc-edad   { display: block; font-size: 0.75rem; color: #999; font-weight: 600; }

/* ─────────────────────────────────────
   SOS
───────────────────────────────────── */
.sos-feature { background: linear-gradient(135deg, #1a1a2e, #16213e); }

.sf-card {
    display: flex;
    gap: 3rem;
    align-items: center;
    flex-wrap: wrap;
    width: 100%;
}
.sfc-left {
    flex: 1;
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
}
.sfc-icon-wrap {
    width: 64px; height: 64px;
    background: rgba(230,57,70,0.15);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
    border: 1.5px solid rgba(230,57,70,0.25);
}
.sfc-left h3 { font-size: 1.25rem; font-weight: 800; color: white; margin: 0 0 0.5rem; }
.sfc-left p  { font-size: 0.92rem; color: rgba(255,255,255,0.65); line-height: 1.65; }

.sfc-right { display: flex; flex-direction: column; gap: 0.65rem; min-width: 240px; }
.sfc-tec {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    background: rgba(255,255,255,0.07);
    border-radius: 12px;
    font-size: 0.9rem;
    color: white;
    font-weight: 600;
    border: 1px solid rgba(255,255,255,0.08);
    transition: background 0.2s;
}
.sfc-tec:hover { background: rgba(255,255,255,0.12); }
.sfc-tec span:first-child { font-size: 1.2rem; }
.sfc-aviso { font-size: 0.8rem; color: rgba(255,255,255,0.45); text-align: center; padding-top: 0.25rem; }
.sfc-aviso strong { color: #E63946; }

/* ─────────────────────────────────────
   FAQ
───────────────────────────────────── */
.faq-section { background: #fafffe; }
.faq-inner   { max-width: 740px !important; }

.faq-lista { width: 100%; display: flex; flex-direction: column; gap: 0.65rem; }

.faq-item {
    background: white;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    cursor: pointer;
    border: 1.5px solid #f0f0f0;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.faq-item:hover         { border-color: #4ECDC4; box-shadow: 0 4px 16px rgba(78,205,196,0.1); }
.faq-item.abierta       { border-color: #4ECDC4; background: #f0fefc; }

.faq-pregunta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    font-weight: 700;
    font-size: 0.95rem;
    color: #1a1a1a;
}
.faq-icono { font-size: 1.4rem; color: #4ECDC4; flex-shrink: 0; font-weight: 300; }

.faq-respuesta {
    margin-top: 0.75rem;
    font-size: 0.9rem;
    color: #555;
    line-height: 1.65;
    border-top: 1px solid #e8f8f5;
    padding-top: 0.75rem;
}
.faq-enter-active, .faq-leave-active { transition: opacity 0.2s; }
.faq-enter-from, .faq-leave-to       { opacity: 0; }

/* ─────────────────────────────────────
   CTA FINAL
───────────────────────────────────── */
.cta-final {
    background: linear-gradient(160deg, #0f1724, #1a1a2e, #16213e);
    position: relative;
    overflow: hidden;
    padding: 6rem 2rem;
}
.cta-blobs { position: absolute; inset: 0; pointer-events: none; }
.cta-blob  { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.15; }
.cb1 { width: 500px; height: 500px; background: #4ECDC4; top: -150px; right: -100px; animation: blob1 10s ease-in-out infinite; }
.cb2 { width: 350px; height: 350px; background: #9B8EC4; bottom: -100px; left: -80px; animation: blob2 12s ease-in-out infinite; }
.cb3 { width: 250px; height: 250px; background: #6BCF7F; top: 40%; left: 40%; animation: blob3 8s ease-in-out infinite; }

.cta-content {
    max-width: 680px;
    margin: 0 auto;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
    position: relative;
    z-index: 1;
}
.cta-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(78,205,196,0.15);
    color: #4ECDC4;
    padding: 0.4rem 1.1rem;
    border-radius: 25px;
    font-size: 0.82rem;
    font-weight: 700;
    border: 1.5px solid rgba(78,205,196,0.35);
}
.cta-content h2 {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 900;
    color: white;
    line-height: 1.15;
}
.cta-content > p {
    font-size: 1.05rem;
    color: rgba(255,255,255,0.6);
    max-width: 450px;
    line-height: 1.65;
}
.cta-btns { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; }

.cta-disclaimer {
    font-size: 0.76rem;
    color: rgba(255,255,255,0.28);
    max-width: 480px;
    line-height: 1.5;
}
.cta-disclaimer strong { color: rgba(255,255,255,0.5); }

/* ─────────────────────────────────────
   FOOTER
───────────────────────────────────── */
.land-footer { background: #0e1420; padding: 1.5rem 2rem; border-top: 1px solid rgba(255,255,255,0.05); }
.lf-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}
.lf-logo { display: flex; align-items: center; gap: 0.5rem; }
.lf-logo img  { width: 26px; opacity: 0.8; }
.lf-logo span { font-weight: 700; font-size: 0.78rem; color: #4ECDC4; }
.lf-inner p   { font-size: 0.74rem; color: #555; }
.lf-links     { display: flex; gap: 1rem; }
.lf-links a   { font-size: 0.78rem; color: #4ECDC4; text-decoration: none; font-weight: 600; }
.lf-links a:hover { text-decoration: underline; }

/* ─────────────────────────────────────
   HAMBURGUESA LANDING
───────────────────────────────────── */
.land-hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    flex-shrink: 0;
}

.land-hamburger span {
    display: block;
    width: 24px;
    height: 2px;
    background: #2D2D2D;
    border-radius: 2px;
    transition: all 0.3s;
}

.land-hamburger.open span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
.land-hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
.land-hamburger.open span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }

/* Menú móvil landing */
.land-mobile-menu {
    display: flex;
    flex-direction: column;
    background: rgba(255,255,255,0.98);
    backdrop-filter: blur(12px);
    border-top: 1px solid #f0f0f0;
    padding: 1rem 1.5rem 1.5rem;
    gap: 0.25rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.land-mobile-menu a {
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
    color: #2D2D2D;
    padding: 0.65rem 0;
    border-bottom: 1px solid #f5f5f5;
    transition: color 0.2s;
}

.land-mobile-menu a:hover { color: #4ECDC4; }

.lmm-divider { height: 1px; background: #eee; margin: 0.5rem 0; }

.lmm-btn-login {
    display: block;
    text-align: center;
    padding: 0.75rem;
    border: 2px solid #4ECDC4;
    border-radius: 25px;
    color: #4ECDC4;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s;
    margin-top: 0.25rem;
}
.lmm-btn-login:hover { background: #4ECDC4; color: white; }

.lmm-btn-register {
    display: block;
    text-align: center;
    padding: 0.75rem;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    border-radius: 25px;
    color: white;
    font-weight: 700;
    text-decoration: none;
    margin-top: 0.25rem;
    box-shadow: 0 4px 14px rgba(78,205,196,0.3);
    transition: transform 0.2s, box-shadow 0.2s;
}
.lmm-btn-register:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(78,205,196,0.45); }

.land-slide-enter-active, .land-slide-leave-active { transition: opacity 0.2s, transform 0.2s; }
.land-slide-enter-from, .land-slide-leave-to { opacity: 0; transform: translateY(-8px); }

/* ─────────────────────────────────────
   RESPONSIVE
───────────────────────────────────── */
@media (max-width: 1024px) {
    .hero-wrapper       { flex-direction: column; text-align: center; padding-top: 7rem; }
    .hero-sub           { max-width: 100%; }
    .hero-ctas          { justify-content: center; }
    .trust-row          { justify-content: center; }
    .hero-demo          { width: 100%; max-width: 380px; }
    .hf-inner           { flex-direction: column !important; }
    .hf-texto           { align-items: center; }
    .hf-texto h2        { text-align: center; }
    .hf-visual          { width: 100%; max-width: 400px; }
    .funciones-grid     { grid-template-columns: repeat(2, 1fr); }
    .testimonios-grid   { grid-template-columns: 1fr; }
    .steps-grid         { grid-template-columns: 1fr; gap: 1rem; }
    .steps-grid::before { display: none; }
    .sf-card            { flex-direction: column; }
    .stats-row          { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .land-nav-links     { display: none; }
    .land-nav-ctas      { display: none; }
    .land-hamburger     { display: flex; }
    .tecnicas-grid      { grid-template-columns: repeat(2, 1fr); }
    .funciones-grid     { grid-template-columns: 1fr; }
    .section-inner      { padding: 3.5rem 1.25rem; }
    .testimonios-grid   { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
    .tecnicas-grid      { grid-template-columns: repeat(2, 1fr); }
    .hero-demo          { display: none; }
    .stats-row          { grid-template-columns: repeat(2, 1fr); }
    .section-inner      { padding: 2.5rem 1rem; }
    .hero-wrapper       { padding: 6rem 1rem 2rem; }
    .hero h1            { font-size: clamp(1.8rem, 8vw, 2.8rem); }
    .cta-final          { padding: 4rem 1rem; }
    .btn-primary        { padding: 0.8rem 1.5rem; font-size: 0.95rem; }
    .btn-ghost          { padding: 0.8rem 1.2rem; font-size: 0.95rem; }
    .hero-badge         { font-size: 0.75rem; padding: 0.4rem 0.9rem; }
    .trust-row          { gap: 0.75rem; }
    .trust-row span     { font-size: 0.76rem; }
    .lf-inner           { flex-direction: column; text-align: center; }
    .lf-links           { justify-content: center; }
    .sf-card            { gap: 1.5rem; }
    .sfc-left           { flex-direction: column; align-items: center; text-align: center; }
}
</style>
