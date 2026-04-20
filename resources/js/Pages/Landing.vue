<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'

const scrollY = ref(0)
const onScroll = () => { scrollY.value = window.scrollY }
onMounted(()  => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

const tecnicas = [
    { emoji: '🫁', nombre: 'Respiración guiada',    desc: 'Técnicas de respiración para calmar la ansiedad en minutos' },
    { emoji: '🧘', nombre: 'Meditación',              desc: 'Sesiones guiadas para encontrar la paz interior' },
    { emoji: '🎵', nombre: 'Sonidos relajantes',      desc: 'Ambientes sonoros para el descanso y la concentración' },
    { emoji: '📓', nombre: 'Diario de gratitud',      desc: 'Registra lo positivo y entrena tu mente hacia el bienestar' },
    { emoji: '👆', nombre: 'EFT Tapping',             desc: 'Acupresión emocional para liberar el estrés' },
    { emoji: '🌍', nombre: 'Grounding 5-4-3-2-1',     desc: 'Ancla tu mente al presente durante momentos de ansiedad' },
    { emoji: '🤸', nombre: 'Yoga suave',              desc: 'Posturas accesibles para liberar la tensión corporal' },
    { emoji: '💗', nombre: 'Autocompasión',           desc: 'Aprende a tratarte con la misma amabilidad que a los demás' },
    { emoji: '🎶', nombre: 'Musicoterapia',           desc: 'Usa la música para regular tus emociones' },
    { emoji: '💆', nombre: 'Relajación muscular',     desc: 'Técnica de Jacobson para liberar tensión acumulada' },
    { emoji: '🌈', nombre: 'Visualización guiada',    desc: 'Viajes mentales para encontrar calma y perspectiva' },
    { emoji: '✨', nombre: 'Gratitud visual',         desc: 'Construye tu tablero de gratitud y cambia tu perspectiva' },
]

const pasos = [
    { num: '01', titulo: 'Crea tu cuenta',          desc: 'Registro gratuito en menos de un minuto. Sin tarjeta de crédito.',         emoji: '✍️' },
    { num: '02', titulo: 'Cuéntale a Hearty',        desc: 'Hearty, nuestro asistente emocional, te escucha y guía sin juzgarte.',     emoji: '💬' },
    { num: '03', titulo: 'Practica técnicas',        desc: 'Más de 15 técnicas de bienestar adaptadas a lo que sientes hoy.',          emoji: '🧘' },
    { num: '04', titulo: 'Registra tu progreso',     desc: 'Visualiza tus patrones emocionales y celebra cada logro conseguido.',     emoji: '📊' },
]

const stats = [
    { valor: '15+',  label: 'Técnicas de bienestar' },
    { valor: '100%', label: 'Gratuito para siempre' },
    { valor: '24/7', label: 'Hearty disponible siempre' },
    { valor: '14',   label: 'Logros por desbloquear' },
]

const testimonios = [
    { texto: 'Hearty me ayudó a gestionar mi ansiedad en momentos difíciles. Lo recomiendo a cualquiera que lo esté pasando mal.', autor: 'Usuario anónimo', emoji: '💙' },
    { texto: 'El dashboard emocional me ayudó a darme cuenta de patrones que yo ni sabía que tenía. Es una herramienta increíble.', autor: 'Usuario anónimo', emoji: '📊' },
    { texto: 'La técnica de respiración guiada es lo primero que hago cuando siento que el estrés me supera. Funciona de verdad.', autor: 'Usuario anónimo', emoji: '🫁' },
]

const faqs = [
    { p: '¿Es completamente gratuito?', r: 'Sí, todas las funcionalidades de Heart to Heart son gratuitas. No hay versión premium ni pagos ocultos.' },
    { p: '¿Mis datos están seguros?', r: 'Absolutamente. Tus datos emocionales son completamente privados. Nunca los compartimos ni los vendemos.' },
    { p: '¿Puede sustituir a un psicólogo?', r: 'No. Heart to Heart es una herramienta de apoyo al bienestar, no un servicio clínico. Si necesitas ayuda profesional, te animamos a buscarla.' },
    { p: '¿Necesito experiencia previa?', r: 'Para nada. Todo está diseñado para ser accesible a cualquier persona, independientemente de su experiencia con el bienestar emocional.' },
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
                    <a href="#como-funciona">Cómo funciona</a>
                    <a href="#tecnicas">Técnicas</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="land-nav-ctas">
                    <Link href="/login" class="btn-login">Iniciar sesión</Link>
                    <Link href="/register" class="btn-register">Empezar gratis</Link>
                </div>
            </div>
        </nav>

        <!-- ── Hero ── -->
        <section class="hero">
            <div class="hero-bg">
                <div class="hero-circle c1"></div>
                <div class="hero-circle c2"></div>
                <div class="hero-circle c3"></div>
            </div>
            <div class="hero-content">
                <div class="hero-badge">💚 100% gratuito · Sin anuncios · Sin datos vendidos</div>
                <h1>Tu bienestar emocional<br><span class="hero-highlight">importa de verdad</span></h1>
                <p class="hero-sub">Heart to Heart es tu espacio seguro para cuidar tu salud mental cada día. Habla con Hearty, practica técnicas de bienestar y registra tu progreso emocional.</p>
                <div class="hero-ctas">
                    <Link href="/register" class="btn-hero-primary">
                        💚 Empezar gratis ahora
                    </Link>
                    <Link href="/login" class="btn-hero-secondary">
                        Ya tengo cuenta →
                    </Link>
                </div>
                <div class="hero-stats">
                    <div v-for="stat in stats" :key="stat.label" class="hero-stat">
                        <span class="hs-valor">{{ stat.valor }}</span>
                        <span class="hs-label">{{ stat.label }}</span>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hv-card hv-main">
                    <div class="hvc-header">
                        <span class="hvc-avatar">💚</span>
                        <div>
                            <span class="hvc-name">Hearty</span>
                            <span class="hvc-estado">● En línea</span>
                        </div>
                    </div>
                    <div class="hvc-msg hearty">
                        ¡Hola! Soy Hearty 💚 ¿Cómo te sientes hoy?
                    </div>
                    <div class="hvc-opciones">
                        <span>😊 Bien</span>
                        <span>😰 Ansioso/a</span>
                        <span>😢 Triste</span>
                    </div>
                    <div class="hvc-msg user">Me siento un poco ansioso hoy</div>
                    <div class="hvc-msg hearty">
                        Entiendo 💙 Vamos a trabajar juntos para que te sientas mejor...
                    </div>
                </div>
                <div class="hv-card hv-mini hv-m1">
                    <span class="hvm-emoji">📊</span>
                    <div>
                        <span class="hvm-titulo">Tu racha</span>
                        <span class="hvm-valor">7 días 🔥</span>
                    </div>
                </div>
                <div class="hv-card hv-mini hv-m2">
                    <span class="hvm-emoji">🏆</span>
                    <div>
                        <span class="hvm-titulo">Logro</span>
                        <span class="hvm-valor">Desbloqueado</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Cómo funciona ── -->
        <section class="como-funciona" id="como-funciona">
            <div class="section-inner">
                <div class="section-badge">✨ Simple y efectivo</div>
                <h2>Cómo funciona</h2>
                <p class="section-sub">Cuatro pasos para empezar a cuidarte hoy</p>
                <div class="pasos-grid">
                    <div v-for="paso in pasos" :key="paso.num" class="paso-card">
                        <div class="pc-num">{{ paso.num }}</div>
                        <span class="pc-emoji">{{ paso.emoji }}</span>
                        <h3>{{ paso.titulo }}</h3>
                        <p>{{ paso.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Hearty feature ── -->
        <section class="hearty-section">
            <div class="section-inner hs-inner">
                <div class="hs-texto">
                    <div class="section-badge">🤖 Inteligencia emocional</div>
                    <h2>Conoce a Hearty</h2>
                    <p>Hearty es tu asistente emocional inteligente, disponible las 24 horas. Te escucha sin juzgarte, detecta cómo te sientes y te sugiere técnicas personalizadas.</p>
                    <ul class="hs-features">
                        <li>💬 Conversación natural y empática</li>
                        <li>🧠 Detección emocional avanzada</li>
                        <li>🎯 Técnicas personalizadas según tu estado</li>
                        <li>💙 Memoria entre sesiones</li>
                        <li>🚨 Detección de situaciones de crisis</li>
                        <li>📈 Registro automático de tu bienestar</li>
                    </ul>
                    <Link href="/register" class="btn-hero-primary" style="width: fit-content">
                        Hablar con Hearty →
                    </Link>
                </div>
                <div class="hs-visual">
                    <div class="hsv-chat">
                        <div class="hsv-msg hearty">¡Hola de nuevo! La última vez te sentías un poco ansioso/a. ¿Cómo estás hoy? 💙</div>
                        <div class="hsv-msg user">Hoy estoy mejor, gracias</div>
                        <div class="hsv-msg hearty">¡Me alegra mucho! 😊 Noto que estás mejor que la última vez. ¿Quieres aprovechar este buen momento para practicar algo?</div>
                        <div class="hsv-opciones">
                            <span>🧘 Meditar</span>
                            <span>📓 Diario</span>
                            <span>🫁 Respirar</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Técnicas ── -->
        <section class="tecnicas-section" id="tecnicas">
            <div class="section-inner">
                <div class="section-badge">🌿 15+ técnicas</div>
                <h2>Herramientas para cada momento</h2>
                <p class="section-sub">Desde técnicas rápidas para emergencias hasta prácticas para construir hábitos duraderos</p>
                <div class="tecnicas-landing-grid">
                    <div v-for="tec in tecnicas" :key="tec.nombre" class="tec-card">
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
                <div class="section-badge">💙 Comunidad real</div>
                <h2>Lo que dice nuestra comunidad</h2>
                <div class="test-grid">
                    <div v-for="test in testimonios" :key="test.autor" class="test-card">
                        <span class="test-emoji">{{ test.emoji }}</span>
                        <p>"{{ test.texto }}"</p>
                        <span class="test-autor">— {{ test.autor }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── FAQ ── -->
        <section class="faq-section" id="faq">
            <div class="section-inner faq-inner">
                <div class="section-badge">❓ Preguntas frecuentes</div>
                <h2>Todo lo que necesitas saber</h2>
                <div class="faq-lista">
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

        <!-- ── CTA final ── -->
        <section class="cta-final">
            <div class="ctaf-inner">
                <div class="ctaf-circles">
                    <div class="ctafc c1"></div>
                    <div class="ctafc c2"></div>
                </div>
                <h2>Tu bienestar empieza hoy</h2>
                <p>Únete a Heart to Heart. Es gratis, es seguro y está aquí para ti.</p>
                <div class="ctaf-ctas">
                    <Link href="/register" class="btn-ctaf-primary">💚 Crear mi cuenta gratis</Link>
                    <Link href="/login" class="btn-ctaf-secondary">Ya tengo cuenta</Link>
                </div>
                <p class="ctaf-aviso">⚠️ Heart to Heart no sustituye la atención psicológica profesional. Si estás en crisis, llama al <strong>024</strong>.</p>
            </div>
        </section>

        <!-- ── Footer landing ── -->
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

/* ── Navbar ── */
.land-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    transition: background 0.3s, box-shadow 0.3s;
    padding: 1rem 0;
}

.land-nav.scrolled {
    background: rgba(255,255,255,0.96);
    box-shadow: 0 2px 16px rgba(0,0,0,0.08);
    backdrop-filter: blur(8px);
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

.land-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.land-logo img  { width: 38px; height: auto; }
.land-logo span { font-weight: 700; font-size: 0.85rem; color: #E63946; letter-spacing: 0.08em; }

.land-nav-links {
    display: flex;
    gap: 2rem;
}

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
    padding: 0.55rem 1.2rem;
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
    padding: 0.55rem 1.2rem;
    font-size: 0.88rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
    background: #4ECDC4;
    border-radius: 25px;
    transition: background 0.2s, transform 0.2s;
}

.btn-register:hover { background: #3BAFA7; transform: translateY(-1px); }

/* ── Hero ── */
.hero {
    min-height: 100vh;
    background: linear-gradient(135deg, #f0fffe 0%, #e8f8f5 50%, #ffeef0 100%);
    display: flex;
    align-items: center;
    padding: 7rem 2rem 4rem;
    position: relative;
    overflow: hidden;
    gap: 4rem;
    max-width: 1200px;
    margin: 0 auto;
}

.hero-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }

.hero-circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.15;
}

.hero-circle.c1 { width: 500px; height: 500px; background: #4ECDC4; top: -100px; right: -100px; }
.hero-circle.c2 { width: 300px; height: 300px; background: #E63946; bottom: -50px; left: -50px; }
.hero-circle.c3 { width: 200px; height: 200px; background: #4ECDC4; bottom: 100px; right: 200px; }

.hero-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    position: relative;
    z-index: 1;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    border: 1.5px solid #4ECDC4;
    border-radius: 25px;
    padding: 0.4rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #3BAFA7;
    width: fit-content;
}

.hero h1 {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 900;
    line-height: 1.2;
    color: #1a1a1a;
}

.hero-highlight {
    color: #4ECDC4;
    position: relative;
}

.hero-sub {
    font-size: 1.05rem;
    color: #555;
    line-height: 1.7;
    max-width: 520px;
}

.hero-ctas { display: flex; gap: 1rem; flex-wrap: wrap; }

.btn-hero-primary {
    padding: 0.9rem 2rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    border-radius: 30px;
    text-decoration: none;
    transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-hero-primary:hover {
    background: #3BAFA7;
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(78,205,196,0.4);
}

.btn-hero-secondary {
    padding: 0.9rem 1.5rem;
    color: #4ECDC4;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    border: 2px solid #4ECDC4;
    border-radius: 30px;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
}

.btn-hero-secondary:hover { background: #4ECDC4; color: white; }

.hero-stats {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(78,205,196,0.2);
}

.hero-stat { display: flex; flex-direction: column; gap: 0.1rem; }
.hs-valor  { font-size: 1.5rem; font-weight: 900; color: #4ECDC4; }
.hs-label  { font-size: 0.75rem; color: #888; font-weight: 600; }

/* ── Hero visual ── */
.hero-visual {
    flex-shrink: 0;
    position: relative;
    width: 380px;
    height: 420px;
    z-index: 1;
}

.hv-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    position: absolute;
}

.hv-main {
    width: 320px;
    padding: 1.25rem;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.hvc-header { display: flex; align-items: center; gap: 0.6rem; }

.hvc-avatar {
    width: 36px;
    height: 36px;
    background: #E8FAF9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    border: 2px solid #4ECDC4;
    flex-shrink: 0;
}

.hvc-name  { display: block; font-weight: 700; font-size: 0.88rem; }
.hvc-estado { display: block; font-size: 0.72rem; color: #4ECDC4; }

.hvc-msg {
    padding: 0.65rem 0.9rem;
    border-radius: 14px;
    font-size: 0.85rem;
    line-height: 1.4;
    max-width: 85%;
}

.hvc-msg.hearty { background: #4ECDC4; color: white; align-self: flex-start; }
.hvc-msg.user   { background: #f5f5f5; color: #2D2D2D; align-self: flex-end; margin-left: auto; }

.hvc-opciones {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}

.hvc-opciones span {
    padding: 0.3rem 0.7rem;
    background: #E8FAF9;
    border: 1.5px solid #4ECDC4;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #3BAFA7;
}

.hv-mini {
    width: 150px;
    padding: 0.9rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.hv-m1 { bottom: 60px; right: 0; animation: float 3s ease-in-out infinite; }
.hv-m2 { bottom: 0; left: 60px; animation: float 3s ease-in-out infinite 1s; }

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-8px); }
}

.hvm-emoji { font-size: 1.5rem; }
.hvm-titulo { display: block; font-size: 0.72rem; color: #888; }
.hvm-valor  { display: block; font-size: 0.9rem; font-weight: 700; color: #2D2D2D; }

/* ── Secciones ── */
.section-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 5rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.section-badge {
    background: #E8FAF9;
    color: #3BAFA7;
    padding: 0.4rem 1rem;
    border-radius: 25px;
    font-size: 0.82rem;
    font-weight: 700;
    border: 1.5px solid #4ECDC4;
}

.section-inner h2 {
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    font-weight: 900;
    text-align: center;
    color: #1a1a1a;
}

.section-sub {
    font-size: 1rem;
    color: #666;
    text-align: center;
    max-width: 540px;
    line-height: 1.6;
}

/* ── Cómo funciona ── */
.como-funciona { background: #fafffe; }

.pasos-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    width: 100%;
    margin-top: 1rem;
}

.paso-card {
    background: white;
    border-radius: 18px;
    padding: 1.75rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    border: 1px solid #f0f0f0;
    transition: transform 0.2s, box-shadow 0.2s;
    text-align: center;
    align-items: center;
}

.paso-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(78,205,196,0.12); }

.pc-num {
    font-size: 2rem;
    font-weight: 900;
    color: #4ECDC4;
    opacity: 0.4;
    line-height: 1;
}

.pc-emoji { font-size: 2rem; }

.paso-card h3 { font-size: 1rem; font-weight: 700; color: #1a1a1a; }
.paso-card p  { font-size: 0.85rem; color: #666; line-height: 1.5; }

/* ── Hearty section ── */
.hearty-section { background: white; }

.hs-inner {
    flex-direction: row !important;
    align-items: flex-start !important;
    gap: 4rem !important;
}

.hs-texto {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
}

.hs-texto h2    { text-align: left; }
.hs-texto p     { color: #555; line-height: 1.7; font-size: 1rem; }

.hs-features {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.hs-features li { font-size: 0.92rem; color: #444; font-weight: 500; }

.hs-visual { flex-shrink: 0; width: 340px; }

.hsv-chat {
    background: white;
    border-radius: 20px;
    padding: 1.25rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.hsv-msg {
    padding: 0.75rem 1rem;
    border-radius: 16px;
    font-size: 0.88rem;
    line-height: 1.5;
    max-width: 88%;
}

.hsv-msg.hearty { background: #4ECDC4; color: white; }
.hsv-msg.user   { background: #f5f5f5; align-self: flex-end; margin-left: auto; }

.hsv-opciones {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}

.hsv-opciones span {
    padding: 0.35rem 0.75rem;
    background: #E8FAF9;
    border: 1.5px solid #4ECDC4;
    border-radius: 15px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #3BAFA7;
}

/* ── Técnicas ── */
.tecnicas-section { background: #fafffe; }

.tecnicas-landing-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    width: 100%;
    margin-top: 1rem;
}

.tec-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    border: 1px solid #f0f0f0;
    transition: transform 0.2s, box-shadow 0.2s;
}

.tec-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(78,205,196,0.1); }

.tec-emoji { font-size: 1.6rem; }
.tec-card h3 { font-size: 0.9rem; font-weight: 700; color: #1a1a1a; }
.tec-card p  { font-size: 0.8rem; color: #666; line-height: 1.4; }

/* ── Testimonios ── */
.testimonios-section { background: #4ECDC4; }
.testimonios-section .section-inner h2 { color: white; }
.testimonios-section .section-badge { background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.4); }

.test-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    width: 100%;
    margin-top: 1rem;
}

.test-card {
    background: rgba(255,255,255,0.15);
    border-radius: 18px;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    border: 1px solid rgba(255,255,255,0.3);
}

.test-emoji { font-size: 2rem; }
.test-card p { font-size: 0.92rem; color: white; line-height: 1.7; font-style: italic; flex: 1; }
.test-autor  { font-size: 0.78rem; color: rgba(255,255,255,0.7); font-weight: 600; }

/* ── FAQ ── */
.faq-section { background: white; }

.faq-inner { max-width: 720px !important; }

.faq-lista {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 1rem;
}

.faq-item {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    cursor: pointer;
    border: 1.5px solid #f0f0f0;
    transition: border-color 0.2s;
}

.faq-item.abierta { border-color: #4ECDC4; background: #E8FAF9; }

.faq-pregunta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    font-weight: 700;
    font-size: 0.95rem;
    color: #2D2D2D;
}

.faq-icono { font-size: 1.3rem; color: #4ECDC4; font-weight: 400; flex-shrink: 0; }

.faq-respuesta {
    margin-top: 0.75rem;
    font-size: 0.9rem;
    color: #555;
    line-height: 1.6;
}

.faq-enter-active, .faq-leave-active { transition: opacity 0.2s; }
.faq-enter-from, .faq-leave-to { opacity: 0; }

/* ── CTA Final ── */
.cta-final {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    position: relative;
    overflow: hidden;
}

.ctaf-inner {
    max-width: 800px;
    margin: 0 auto;
    padding: 6rem 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    position: relative;
    z-index: 1;
}

.ctaf-circles { position: absolute; inset: 0; pointer-events: none; }
.ctafc { position: absolute; border-radius: 50%; opacity: 0.1; }
.ctafc.c1 { width: 400px; height: 400px; background: #4ECDC4; top: -100px; right: -100px; }
.ctafc.c2 { width: 300px; height: 300px; background: #E63946; bottom: -100px; left: -100px; }

.ctaf-inner h2 { font-size: 2.2rem; font-weight: 900; color: white; }
.ctaf-inner p  { font-size: 1.05rem; color: rgba(255,255,255,0.75); }

.ctaf-ctas { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; }

.btn-ctaf-primary {
    padding: 1rem 2.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    border-radius: 30px;
    text-decoration: none;
    transition: background 0.2s, transform 0.2s;
}

.btn-ctaf-primary:hover { background: #3BAFA7; transform: translateY(-2px); }

.btn-ctaf-secondary {
    padding: 1rem 2rem;
    color: rgba(255,255,255,0.8);
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 30px;
    transition: all 0.2s;
}

.btn-ctaf-secondary:hover { border-color: white; color: white; }

.ctaf-aviso {
    font-size: 0.78rem !important;
    color: rgba(255,255,255,0.45) !important;
    max-width: 500px;
    line-height: 1.5;
}

.ctaf-aviso strong { color: rgba(255,255,255,0.65); }

/* ── Footer ── */
.land-footer {
    background: #141420;
    padding: 1.5rem 2rem;
}

.lf-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.lf-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.lf-logo img  { width: 28px; filter: brightness(1.2); }
.lf-logo span { font-weight: 700; font-size: 0.78rem; color: #4ECDC4; }

.lf-inner p { font-size: 0.75rem; color: #555; }

.lf-links { display: flex; gap: 1rem; }
.lf-links a { font-size: 0.78rem; color: #4ECDC4; text-decoration: none; font-weight: 600; }
.lf-links a:hover { text-decoration: underline; }

/* ── Responsive ── */
@media (max-width: 1024px) {
    .hero           { flex-direction: column; text-align: center; align-items: center; padding-top: 6rem; }
    .hero-sub       { max-width: 100%; }
    .hero-ctas      { justify-content: center; }
    .hero-stats     { justify-content: center; }
    .hero-visual    { width: 100%; max-width: 380px; height: 400px; }
    .hs-inner       { flex-direction: column !important; }
    .hs-texto       { align-items: center; }
    .hs-texto h2    { text-align: center; }
    .hs-visual      { width: 100%; max-width: 400px; }
    .pasos-grid     { grid-template-columns: repeat(2, 1fr); }
    .tecnicas-landing-grid { grid-template-columns: repeat(3, 1fr); }
    .test-grid      { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .land-nav-links { display: none; }
    .tecnicas-landing-grid { grid-template-columns: repeat(2, 1fr); }
    .pasos-grid     { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
    .tecnicas-landing-grid { grid-template-columns: 1fr; }
    .hero-visual { display: none; }
}
</style>