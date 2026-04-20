<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const faseActiva    = ref('inicio')
const tecnicaActiva = ref(null)
const cuenta        = ref(0)
const faseTemporizador = ref('')
let intervalo = null

// ── Respiración de emergencia 4-7-8 ──
const iniciarRespiracion = () => {
    tecnicaActiva.value = 'respiracion'
    faseActiva.value    = 'tecnica'
    let ciclo = 0
    const fases = [
        { nombre: 'INHALA',  duracion: 4, color: '#4ECDC4' },
        { nombre: 'MANTÉN',  duracion: 7, color: '#9B8EC4' },
        { nombre: 'EXHALA',  duracion: 8, color: '#6B9FD4' },
    ]
    let faseIdx = 0
    cuenta.value = fases[0].duracion
    faseTemporizador.value = fases[0].nombre

    clearInterval(intervalo)
    intervalo = setInterval(() => {
        cuenta.value--
        if (cuenta.value <= 0) {
            faseIdx = (faseIdx + 1) % fases.length
            if (faseIdx === 0) ciclo++
            if (ciclo >= 4) {
                clearInterval(intervalo)
                faseActiva.value = 'completado'
                return
            }
            cuenta.value           = fases[faseIdx].duracion
            faseTemporizador.value = fases[faseIdx].nombre
        }
    }, 1000)
}

const detener = () => {
    clearInterval(intervalo)
    faseActiva.value    = 'inicio'
    tecnicaActiva.value = null
}

const colorFase = () => ({
    'INHALA': '#4ECDC4',
    'MANTÉN': '#9B8EC4',
    'EXHALA': '#6B9FD4',
}[faseTemporizador.value] ?? '#4ECDC4')

// ── Grounding rápido ──
const groundingPasos = [
    { num: 5, sentido: 'cosas que VES',    emoji: '👁️' },
    { num: 4, sentido: 'cosas que TOCAS',  emoji: '✋' },
    { num: 3, sentido: 'cosas que OYES',   emoji: '👂' },
    { num: 2, sentido: 'cosas que HUELES', emoji: '👃' },
    { num: 1, sentido: 'cosa que SABORES', emoji: '👄' },
]

const pasoGrounding = ref(0)
const groundingActivo = ref(false)

const iniciarGrounding = () => {
    tecnicaActiva.value  = 'grounding'
    faseActiva.value     = 'tecnica'
    groundingActivo.value = true
    pasoGrounding.value  = 0
}

const siguientePasoGrounding = () => {
    if (pasoGrounding.value < groundingPasos.length - 1) {
        pasoGrounding.value++
    } else {
        faseActiva.value = 'completado'
    }
}

// ── Afirmaciones de crisis ──
const afirmaciones = [
    'Este momento es temporal. Pasará. 💙',
    'He sobrevivido momentos difíciles antes. Puedo con esto.',
    'No estoy solo/a. Hay personas que se preocupan por mí.',
    'Mis emociones son válidas pero no definen quién soy.',
    'Ahora mismo estoy a salvo. Solo necesito respirar.',
    'Un paso a la vez. Solo el siguiente paso.',
    'Soy más fuerte de lo que creo en este momento.',
    'Esto también pasará. Siempre pasa.',
]

const afirmacionIdx  = ref(0)
const afirmacionActual = ref(afirmaciones[0])

const siguienteAfirmacion = () => {
    afirmacionIdx.value = (afirmacionIdx.value + 1) % afirmaciones.length
    afirmacionActual.value = afirmaciones[afirmacionIdx.value]
}

const iniciarAfirmaciones = () => {
    tecnicaActiva.value    = 'afirmaciones'
    faseActiva.value       = 'tecnica'
    afirmacionIdx.value    = 0
    afirmacionActual.value = afirmaciones[0]
}

import { onUnmounted } from 'vue'
onUnmounted(() => clearInterval(intervalo))
</script>

<template>
    <AppLayout>
        <div class="sos-wrapper">

            <!-- Cabecera de emergencia -->
            <div class="sos-header">
                <div class="sos-alerta">🆘</div>
                <div>
                    <h1>Modo de emergencia emocional</h1>
                    <p>Estás a salvo. Vamos a atravesar esto juntos, paso a paso.</p>
                </div>
            </div>

            <!-- Aviso 024 -->
            <div class="aviso-crisis">
                <span class="ac-icono">📞</span>
                <div>
                    <strong>Si tu vida está en peligro, llama al 024 o al 112 ahora mismo.</strong>
                    <p>El 024 es la línea de atención a la conducta suicida. Gratuita, confidencial y disponible 24h.</p>
                </div>
                <a href="tel:024" class="btn-llamar">Llamar al 024</a>
            </div>

            <!-- Pantalla de inicio -->
            <div v-if="faseActiva === 'inicio'" class="sos-inicio">

                <p class="sos-pregunta">
                    ¿Qué necesitas ahora mismo?
                </p>

                <div class="tecnicas-sos-grid">

                    <button class="sos-tecnica-btn respiracion" @click="iniciarRespiracion">
                        <span class="stb-emoji">🫁</span>
                        <h3>Respiración 4-7-8</h3>
                        <p>Para los ataques de pánico y ansiedad intensa. Actúa en 2 minutos.</p>
                        <span class="stb-tiempo">⏱ 2 minutos</span>
                    </button>

                    <button class="sos-tecnica-btn grounding" @click="iniciarGrounding">
                        <span class="stb-emoji">🌍</span>
                        <h3>Grounding 5-4-3-2-1</h3>
                        <p>Ancla tu mente al presente cuando sientes que pierdes el control.</p>
                        <span class="stb-tiempo">⏱ 3 minutos</span>
                    </button>

                    <button class="sos-tecnica-btn afirmaciones" @click="iniciarAfirmaciones">
                        <span class="stb-emoji">💙</span>
                        <h3>Afirmaciones de calma</h3>
                        <p>Frases que anclan la mente cuando los pensamientos se descontrolan.</p>
                        <span class="stb-tiempo">⏱ A tu ritmo</span>
                    </button>

                </div>

                <!-- Recursos adicionales -->
                <div class="sos-recursos">
                    <h3>También puedes</h3>
                    <div class="sr-grid">
                        <Link href="/hearty" class="sr-item">
                            <span>💬</span>
                            <span>Hablar con Hearty</span>
                        </Link>
                        <Link href="/respiracion" class="sr-item">
                            <span>🫁</span>
                            <span>Respiración guiada</span>
                        </Link>
                        <Link href="/sonidos" class="sr-item">
                            <span>🎵</span>
                            <span>Sonidos relajantes</span>
                        </Link>
                        <Link href="/tecnica-5-4-3-2-1" class="sr-item">
                            <span>🌍</span>
                            <span>Grounding completo</span>
                        </Link>
                    </div>
                </div>

            </div>

            <!-- Técnica activa: Respiración -->
            <div v-if="faseActiva === 'tecnica' && tecnicaActiva === 'respiracion'" class="tecnica-activa">

                <p class="ta-instruccion">
                    Sigue el ritmo. No pienses en nada más que en respirar.
                </p>

                <div class="respiracion-circulo" :style="{ borderColor: colorFase() }">
                    <div class="rc-inner" :style="{
                        backgroundColor: colorFase() + '20',
                        transform: faseTemporizador === 'INHALA' ? 'scale(1.15)' : faseTemporizador === 'EXHALA' ? 'scale(0.85)' : 'scale(1)',
                        transition: faseTemporizador === 'INHALA' ? 'transform 4s ease-in' : faseTemporizador === 'EXHALA' ? 'transform 8s ease-out' : 'transform 0.5s'
                    }">
                        <span class="rc-fase" :style="{ color: colorFase() }">{{ faseTemporizador }}</span>
                        <span class="rc-cuenta">{{ cuenta }}</span>
                    </div>
                </div>

                <div class="resp-info">
                    <span class="ri-ciclo">Respira naturalmente — 4 ciclos completos</span>
                </div>

                <button class="btn-detener-sos" @click="detener">✕ Salir</button>

            </div>

            <!-- Técnica activa: Grounding -->
            <div v-if="faseActiva === 'tecnica' && tecnicaActiva === 'grounding'" class="tecnica-activa">

                <div class="grounding-sos">
                    <div class="gs-progreso">
                        <div
                            v-for="(p, i) in groundingPasos"
                            :key="i"
                            class="gsp-punto"
                            :class="{ activo: i === pasoGrounding, completado: i < pasoGrounding }"
                        >
                            {{ p.num }}
                        </div>
                    </div>

                    <div class="gs-card">
                        <span class="gs-emoji">{{ groundingPasos[pasoGrounding].emoji }}</span>
                        <div class="gs-num">{{ groundingPasos[pasoGrounding].num }}</div>
                        <h2>{{ groundingPasos[pasoGrounding].sentido }}</h2>
                        <p>
                            Nombra {{ groundingPasos[pasoGrounding].num }}
                            {{ groundingPasos[pasoGrounding].sentido }} a tu alrededor,
                            en voz alta o mentalmente.
                            Tómate el tiempo que necesites.
                        </p>
                    </div>

                    <div class="gs-botones">
                        <button class="btn-detener-sos" @click="detener">✕ Salir</button>
                        <button class="btn-siguiente-sos" @click="siguientePasoGrounding">
                            {{ pasoGrounding < groundingPasos.length - 1 ? 'Siguiente →' : '✓ Terminar' }}
                        </button>
                    </div>
                </div>

            </div>

            <!-- Técnica activa: Afirmaciones -->
            <div v-if="faseActiva === 'tecnica' && tecnicaActiva === 'afirmaciones'" class="tecnica-activa">

                <p class="ta-instruccion">
                    Lee cada afirmación despacio. Respira entre cada una.
                </p>

                <div class="afirmacion-card">
                    <span class="af-comilla">"</span>
                    <p class="af-texto">{{ afirmacionActual }}</p>
                    <span class="af-num">{{ afirmacionIdx + 1 }} / {{ afirmaciones.length }}</span>
                </div>

                <div class="af-botones">
                    <button class="btn-detener-sos" @click="detener">✕ Salir</button>
                    <button class="btn-siguiente-sos" @click="siguienteAfirmacion">
                        Siguiente →
                    </button>
                </div>

            </div>

            <!-- Completado -->
            <div v-if="faseActiva === 'completado'" class="sos-completado">
                <span class="sc-emoji">💙</span>
                <h2>Lo has hecho muy bien</h2>
                <p>Has completado la técnica. ¿Cómo te sientes ahora?</p>

                <div class="sc-opciones">
                    <button class="sc-btn mejor" @click="faseActiva = 'inicio'">
                        😊 Me siento mejor
                    </button>
                    <button class="sc-btn repetir" @click="faseActiva = 'inicio'">
                        🔄 Repetir otra técnica
                    </button>
                </div>

                <div class="sc-recursos">
                    <p>Si sigues necesitando ayuda:</p>
                    <div class="scr-grid">
                        <a href="tel:024" class="scr-item llamar">📞 Llamar al 024</a>
                        <Link href="/hearty" class="scr-item hearty">💬 Hablar con Hearty</Link>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.sos-wrapper {
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ── Cabecera ── */
.sos-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 16px;
    padding: 1.5rem;
    color: white;
}

.sos-alerta { font-size: 2.5rem; flex-shrink: 0; }

.sos-header h1 {
    font-size: 1.2rem;
    font-weight: 800;
    margin: 0 0 0.25rem;
    color: white;
}

.sos-header p {
    font-size: 0.88rem;
    color: rgba(255,255,255,0.75);
    margin: 0;
}

/* ── Aviso 024 ── */
.aviso-crisis {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #fff5f5;
    border: 2px solid #E63946;
    border-radius: 14px;
    padding: 1.25rem;
    flex-wrap: wrap;
}

.ac-icono { font-size: 2rem; flex-shrink: 0; }

.aviso-crisis strong {
    display: block;
    font-size: 0.95rem;
    color: #2D2D2D;
    margin-bottom: 0.25rem;
}

.aviso-crisis p {
    font-size: 0.82rem;
    color: #666;
    margin: 0;
    line-height: 1.4;
}

.btn-llamar {
    margin-left: auto;
    padding: 0.75rem 1.5rem;
    background: #E63946;
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    white-space: nowrap;
    transition: background 0.2s;
}

.btn-llamar:hover { background: #c0303b; }

/* ── Inicio ── */
.sos-inicio {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.sos-pregunta {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2D2D2D;
    text-align: center;
    margin: 0;
}

.tecnicas-sos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.sos-tecnica-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.6rem;
    padding: 1.5rem 1rem;
    border: 2px solid transparent;
    border-radius: 18px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    text-align: center;
}

.sos-tecnica-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.sos-tecnica-btn.respiracion {
    background: #E8FAF9;
    border-color: #4ECDC4;
}

.sos-tecnica-btn.grounding {
    background: #E8F4F8;
    border-color: #6B9FD4;
}

.sos-tecnica-btn.afirmaciones {
    background: #EEF2FF;
    border-color: #9B8EC4;
}

.stb-emoji { font-size: 2.5rem; }

.sos-tecnica-btn h3 {
    font-size: 0.92rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
}

.sos-tecnica-btn p {
    font-size: 0.78rem;
    color: #666;
    line-height: 1.4;
    margin: 0;
}

.stb-tiempo {
    font-size: 0.72rem;
    color: #888;
    font-weight: 600;
}

/* ── Recursos ── */
.sos-recursos {
    background: #fafafa;
    border-radius: 14px;
    padding: 1.25rem;
}

.sos-recursos h3 {
    font-size: 0.88rem;
    font-weight: 700;
    color: #888;
    margin: 0 0 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.sr-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
}

.sr-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 0.75rem;
    background: white;
    border-radius: 10px;
    border: 1px solid #f0f0f0;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    color: #2D2D2D;
    transition: all 0.2s;
}

.sr-item:hover { background: #E8FAF9; color: #4ECDC4; border-color: #4ECDC4; }

/* ── Técnica activa ── */
.tecnica-activa {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.ta-instruccion {
    font-size: 1rem;
    color: #555;
    text-align: center;
    font-style: italic;
    margin: 0;
}

/* ── Respiración ── */
.respiracion-circulo {
    width: 240px;
    height: 240px;
    border-radius: 50%;
    border: 4px solid #4ECDC4;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rc-inner {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
}

.rc-fase {
    font-size: 0.9rem;
    font-weight: 800;
    letter-spacing: 0.1em;
}

.rc-cuenta {
    font-size: 3rem;
    font-weight: 900;
    color: #2D2D2D;
}

.resp-info { text-align: center; }
.ri-ciclo  { font-size: 0.82rem; color: #888; }

/* ── Grounding SOS ── */
.grounding-sos {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
}

.gs-progreso {
    display: flex;
    gap: 0.6rem;
}

.gsp-punto {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    color: #aaa;
    transition: all 0.3s;
}

.gsp-punto.activo    { background: #4ECDC4; color: white; transform: scale(1.1); }
.gsp-punto.completado { background: #3BAFA7; color: white; }

.gs-card {
    width: 100%;
    background: #E8FAF9;
    border-radius: 18px;
    padding: 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    border: 2px solid #4ECDC4;
}

.gs-emoji { font-size: 3rem; }
.gs-num   { font-size: 3rem; font-weight: 900; color: rgba(78,205,196,0.3); line-height: 1; margin-top: -1rem; }
.gs-card h2 { font-size: 1.1rem; font-weight: 800; color: #2D2D2D; margin: 0; }
.gs-card p  { font-size: 0.92rem; color: #555; line-height: 1.6; margin: 0; }

.gs-botones { display: flex; gap: 0.75rem; }

/* ── Afirmaciones ── */
.afirmacion-card {
    width: 100%;
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    position: relative;
}

.af-comilla {
    font-size: 4rem;
    color: #4ECDC4;
    opacity: 0.4;
    line-height: 1;
    font-family: Georgia, serif;
}

.af-texto {
    font-size: 1.2rem;
    color: white;
    line-height: 1.7;
    font-style: italic;
    margin: 0;
}

.af-num {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.4);
}

.af-botones { display: flex; gap: 0.75rem; }

/* ── Botones ── */
.btn-detener-sos {
    padding: 0.75rem 1.5rem;
    background: #f5f5f5;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: background 0.2s;
    font-size: 0.9rem;
}

.btn-detener-sos:hover { background: #e0e0e0; }

.btn-siguiente-sos {
    padding: 0.75rem 2rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.btn-siguiente-sos:hover { background: #3BAFA7; }

/* ── Completado ── */
.sos-completado {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    padding: 1rem;
}

.sc-emoji { font-size: 4rem; }

.sos-completado h2 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #2D2D2D;
    margin: 0;
}

.sos-completado > p {
    font-size: 0.95rem;
    color: #666;
    margin: 0;
}

.sc-opciones {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    justify-content: center;
}

.sc-btn {
    padding: 0.85rem 1.75rem;
    border: none;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.sc-btn.mejor   { background: #4ECDC4; color: white; }
.sc-btn.repetir { background: #f5f5f5; color: #2D2D2D; }
.sc-btn:hover   { transform: translateY(-2px); }

.sc-recursos {
    width: 100%;
    background: #fafafa;
    border-radius: 14px;
    padding: 1.25rem;
}

.sc-recursos p {
    font-size: 0.85rem;
    color: #888;
    margin: 0 0 0.75rem;
    font-weight: 600;
}

.scr-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.scr-item {
    padding: 0.85rem;
    border-radius: 12px;
    text-align: center;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.2s;
}

.scr-item.llamar { background: #fff5f5; color: #E63946; border: 2px solid #E63946; }
.scr-item.hearty { background: #E8FAF9; color: #3BAFA7; border: 2px solid #4ECDC4; }
.scr-item:hover  { transform: translateY(-2px); }

/* ── Responsive ── */
@media (max-width: 580px) {
    .tecnicas-sos-grid { grid-template-columns: 1fr; }
    .sr-grid           { grid-template-columns: 1fr; }
    .scr-grid          { grid-template-columns: 1fr; }
}
</style>