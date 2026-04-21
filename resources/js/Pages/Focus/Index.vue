<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onUnmounted } from 'vue'

// ── Configuración Pomodoro ──
const configs = [
    { id: 'clasico',    nombre: 'Clásico',       trabajo: 25, descanso: 5,  descansoLargo: 15, color: '#ffd5d5', emoji: '🍅' },
    { id: 'intenso',    nombre: 'Intenso',        trabajo: 50, descanso: 10, descansoLargo: 20, color: '#e8d5f5', emoji: '⚡' },
    { id: 'suave',      nombre: 'Suave',          trabajo: 15, descanso: 5,  descansoLargo: 10, color: '#d4edda', emoji: '🌿' },
    { id: 'estudiante', nombre: 'Estudiante',     trabajo: 45, descanso: 10, descansoLargo: 20, color: '#d0eaf8', emoji: '📚' },
]

const configActiva   = ref(configs[0])
const fase           = ref('idle') // idle, trabajo, descanso, descanso-largo
const segundos       = ref(configs[0].trabajo * 60)
const ciclos         = ref(0)
const ciclosTotal    = ref(0)
const corriendo      = ref(false)
const tarea          = ref('')
const tareasHechas   = ref([])
const musicaActiva   = ref(null)
let intervalo        = null
let audioCtx         = null
let nodoMusica       = null

// ── Audio ambient para focus ──
const musicaOptions = [
    { id: 'lluvia',  nombre: 'Lluvia suave',   emoji: '🌧️' },
    { id: 'bosque',  nombre: 'Bosque',          emoji: '🌲' },
    { id: 'cafe',    nombre: 'Cafetería',        emoji: '☕' },
    { id: 'blanco',  nombre: 'Ruido blanco',     emoji: '〰️' },
    { id: 'ninguno', nombre: 'Sin música',       emoji: '🔇' },
]

const crearAudio = (tipo) => {
    if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)()
    if (audioCtx.state === 'suspended') audioCtx.resume()
    detenerAudio()

    if (tipo === 'ninguno') return

    const bufSize   = 4096
    const processor = audioCtx.createScriptProcessor(bufSize, 1, 1)
    const filter    = audioCtx.createBiquadFilter()
    const gain      = audioCtx.createGain()

    const cfgs = {
        lluvia: { freq: 200,  Q: 0.3, tipo: 'lowpass',  lfoF: 0.1,  lfoG: 0.07, vol: 0.07 },
        bosque: { freq: 800,  Q: 1.5, tipo: 'bandpass', lfoF: 0.6,  lfoG: 0.04, vol: 0.06 },
        cafe:   { freq: 1800, Q: 0.7, tipo: 'bandpass', lfoF: 0.3,  lfoG: 0.03, vol: 0.05 },
        blanco: { freq: 2000, Q: 0.1, tipo: 'highpass', lfoF: 0.05, lfoG: 0.02, vol: 0.04 },
    }

    const cfg = cfgs[tipo] ?? cfgs.lluvia
    filter.type            = cfg.tipo
    filter.frequency.value = cfg.freq
    filter.Q.value         = cfg.Q
    gain.gain.value        = cfg.vol

    processor.onaudioprocess = (e) => {
        const out = e.outputBuffer.getChannelData(0)
        for (let i = 0; i < bufSize; i++) out[i] = Math.random() * 2 - 1
    }

    const lfo = audioCtx.createOscillator()
    const lfoGain = audioCtx.createGain()
    lfo.frequency.value = cfg.lfoF
    lfoGain.gain.value  = cfg.lfoG
    lfo.connect(lfoGain)
    lfoGain.connect(gain.gain)
    lfo.start()

    processor.connect(filter)
    filter.connect(gain)
    gain.connect(audioCtx.destination)

    nodoMusica = { processor, filter, gain, lfo, lfoGain }
}

const detenerAudio = () => {
    if (!nodoMusica) return
    try {
        nodoMusica.processor.disconnect()
        nodoMusica.filter.disconnect()
        nodoMusica.gain.disconnect()
        nodoMusica.lfo.stop()
        nodoMusica.lfo.disconnect()
        nodoMusica.lfoGain.disconnect()
    } catch (e) {}
    nodoMusica = null
}

const toggleMusica = (opcion) => {
    musicaActiva.value = opcion.id === musicaActiva.value ? null : opcion.id
    if (musicaActiva.value && musicaActiva.value !== 'ninguno') {
        crearAudio(musicaActiva.value)
    } else {
        detenerAudio()
    }
}

// ── Pomodoro ──
const iniciar = () => {
    if (fase.value === 'idle') {
        fase.value    = 'trabajo'
        segundos.value = configActiva.value.trabajo * 60
    }
    corriendo.value = true
    clearInterval(intervalo)
    intervalo = setInterval(tick, 1000)
}

const pausar = () => {
    corriendo.value = false
    clearInterval(intervalo)
}

const tick = () => {
    segundos.value--
    if (segundos.value <= 0) {
        clearInterval(intervalo)
        reproducirSonidoFin()
        if (fase.value === 'trabajo') {
            ciclos.value++
            ciclosTotal.value++
            if (ciclos.value % 4 === 0) {
                fase.value     = 'descanso-largo'
                segundos.value = configActiva.value.descansoLargo * 60
            } else {
                fase.value     = 'descanso'
                segundos.value = configActiva.value.descanso * 60
            }
        } else {
            fase.value     = 'trabajo'
            segundos.value = configActiva.value.trabajo * 60
        }
        corriendo.value = false
    }
}

const reproducirSonidoFin = () => {
    try {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)()
        const osc  = audioCtx.createOscillator()
        const gain = audioCtx.createGain()
        osc.connect(gain)
        gain.connect(audioCtx.destination)
        osc.frequency.value = fase.value === 'trabajo' ? 440 : 528
        gain.gain.value     = 0.3
        osc.start()
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.5)
        osc.stop(audioCtx.currentTime + 1.5)
    } catch (e) {}
}

const reiniciar = () => {
    clearInterval(intervalo)
    corriendo.value  = false
    fase.value       = 'idle'
    ciclos.value     = 0
    segundos.value   = configActiva.value.trabajo * 60
}

const cambiarConfig = (cfg) => {
    if (corriendo.value) return
    configActiva.value = cfg
    segundos.value     = cfg.trabajo * 60
    fase.value         = 'idle'
}

const agregarTarea = () => {
    if (!tarea.value.trim()) return
    tareasHechas.value.unshift({ texto: tarea.value, hecha: false, tiempo: new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) })
    tarea.value = ''
}

const toggleTarea = (i) => {
    tareasHechas.value[i].hecha = !tareasHechas.value[i].hecha
}

const eliminarTarea = (i) => {
    tareasHechas.value.splice(i, 1)
}

// ── Computed ──
const minutos = computed(() => Math.floor(segundos.value / 60).toString().padStart(2, '0'))
const segs    = computed(() => (segundos.value % 60).toString().padStart(2, '0'))

const colorFase = computed(() => ({
    idle:           '#4ECDC4',
    trabajo:        '#E63946',
    descanso:       '#4ECDC4',
    'descanso-largo': '#9B8EC4',
}[fase.value] ?? '#4ECDC4'))

const labelFase = computed(() => ({
    idle:           '¡Listo para empezar!',
    trabajo:        '🔴 Tiempo de trabajo',
    descanso:       '💚 Descanso corto',
    'descanso-largo': '💜 Descanso largo',
}[fase.value] ?? ''))

const progresoPct = computed(() => {
    const total = fase.value === 'trabajo'
        ? configActiva.value.trabajo * 60
        : fase.value === 'descanso-largo'
            ? configActiva.value.descansoLargo * 60
            : configActiva.value.descanso * 60
    return Math.round(((total - segundos.value) / total) * 100)
})

const tareasPendientes = computed(() => tareasHechas.value.filter(t => !t.hecha).length)
const tareasCompletadas = computed(() => tareasHechas.value.filter(t => t.hecha).length)

onUnmounted(() => {
    clearInterval(intervalo)
    detenerAudio()
    audioCtx?.close()
})
</script>

<template>
    <AppLayout>
        <div class="focus-wrapper">

            <!-- Cabecera -->
            <div class="focus-header">
                <div>
                    <h1>🎯 Modo Focus</h1>
                    <p>Técnica Pomodoro para concentrarte y ser más productivo/a</p>
                </div>
                <div class="fh-stats">
                    <div class="fhs-item">
                        <span class="fhs-val">{{ ciclosTotal }}</span>
                        <span class="fhs-lab">Pomodoros hoy</span>
                    </div>
                    <div class="fhs-item">
                        <span class="fhs-val">{{ tareasCompletadas }}</span>
                        <span class="fhs-lab">Tareas hechas</span>
                    </div>
                </div>
            </div>

            <div class="focus-grid">

                <!-- Columna principal — Timer ── -->
                <div class="focus-main">

                    <!-- Config selector -->
                    <div class="config-selector">
                        <button
                            v-for="cfg in configs"
                            :key="cfg.id"
                            class="cs-btn"
                            :class="{ activa: configActiva.id === cfg.id }"
                            :style="configActiva.id === cfg.id ? { backgroundColor: cfg.color } : {}"
                            @click="cambiarConfig(cfg)"
                            :disabled="corriendo"
                        >
                            {{ cfg.emoji }} {{ cfg.nombre }}
                        </button>
                    </div>

                    <!-- Timer principal -->
                    <div class="timer-card" :style="{ borderColor: colorFase }">

                        <!-- SVG círculo de progreso -->
                        <div class="timer-circulo">
                            <svg viewBox="0 0 200 200" width="220" height="220">
                                <circle
                                    cx="100" cy="100" r="88"
                                    fill="none"
                                    stroke="#f0f0f0"
                                    stroke-width="8"
                                />
                                <circle
                                    cx="100" cy="100" r="88"
                                    fill="none"
                                    :stroke="colorFase"
                                    stroke-width="8"
                                    stroke-linecap="round"
                                    :stroke-dasharray="553"
                                    :stroke-dashoffset="553 * (1 - progresoPct / 100)"
                                    transform="rotate(-90 100 100)"
                                    style="transition: stroke-dashoffset 1s linear, stroke 0.5s ease"
                                />
                                <text x="100" y="90" text-anchor="middle"
                                    dominant-baseline="central"
                                    fill="#2D2D2D"
                                    font-size="36"
                                    font-weight="900"
                                    font-family="Arial">
                                    {{ minutos }}:{{ segs }}
                                </text>
                                <text x="100" y="125" text-anchor="middle"
                                    dominant-baseline="central"
                                    :fill="colorFase"
                                    font-size="11"
                                    font-weight="700"
                                    font-family="Arial">
                                    {{ labelFase }}
                                </text>
                            </svg>
                        </div>

                        <!-- Ciclos -->
                        <div class="ciclos-visual">
                            <div
                                v-for="i in 4"
                                :key="i"
                                class="cv-punto"
                                :class="{ completado: (ciclos % 4) >= i }"
                                :style="(ciclos % 4) >= i ? { backgroundColor: colorFase } : {}"
                            ></div>
                        </div>
                        <p class="ciclos-label">{{ ciclos % 4 }}/4 → descanso largo</p>

                        <!-- Controles -->
                        <div class="timer-controles">
                            <button
                                v-if="!corriendo"
                                class="btn-iniciar-focus"
                                :style="{ backgroundColor: colorFase }"
                                @click="iniciar"
                            >
                                {{ fase === 'idle' ? '▶ Comenzar' : '▶ Continuar' }}
                            </button>
                            <button
                                v-else
                                class="btn-pausar-focus"
                                @click="pausar"
                            >
                                ⏸ Pausar
                            </button>
                            <button class="btn-reiniciar-focus" @click="reiniciar">
                                ↺ Reiniciar
                            </button>
                        </div>

                        <!-- Info de la config -->
                        <div class="config-info">
                            <span>⏱ {{ configActiva.trabajo }}min trabajo</span>
                            <span>☕ {{ configActiva.descanso }}min descanso</span>
                            <span>💤 {{ configActiva.descansoLargo }}min descanso largo</span>
                        </div>
                    </div>

                    <!-- Música de fondo -->
                    <div class="musica-card">
                        <h3>🎵 Música de fondo</h3>
                        <div class="musica-grid">
                            <button
                                v-for="op in musicaOptions"
                                :key="op.id"
                                class="mg-btn"
                                :class="{ activa: musicaActiva === op.id }"
                                @click="toggleMusica(op)"
                            >
                                <span>{{ op.emoji }}</span>
                                <span>{{ op.nombre }}</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Columna lateral — Tareas ── -->
                <div class="focus-sidebar">

                    <div class="tareas-card">
                        <div class="tc-header">
                            <h3>📋 Tareas de esta sesión</h3>
                            <span class="tc-badge">{{ tareasPendientes }} pendientes</span>
                        </div>

                        <!-- Input nueva tarea -->
                        <div class="nueva-tarea">
                            <input
                                v-model="tarea"
                                type="text"
                                placeholder="¿Qué vas a hacer?"
                                class="nt-input"
                                @keyup.enter="agregarTarea"
                                maxlength="100"
                            />
                            <button class="nt-btn" @click="agregarTarea">+</button>
                        </div>

                        <!-- Lista de tareas -->
                        <div v-if="tareasHechas.length === 0" class="tc-empty">
                            <span>📝</span>
                            <p>Añade las tareas que quieres completar en esta sesión</p>
                        </div>

                        <div v-else class="tareas-lista">
                            <div
                                v-for="(t, i) in tareasHechas"
                                :key="i"
                                class="tarea-item"
                                :class="{ hecha: t.hecha }"
                            >
                                <button class="ti-check" @click="toggleTarea(i)">
                                    {{ t.hecha ? '✅' : '⬜' }}
                                </button>
                                <div class="ti-info">
                                    <span class="ti-texto" :class="{ tachado: t.hecha }">{{ t.texto }}</span>
                                    <span class="ti-hora">{{ t.tiempo }}</span>
                                </div>
                                <button class="ti-del" @click="eliminarTarea(i)">✕</button>
                            </div>
                        </div>

                        <div v-if="tareasCompletadas > 0" class="tc-completadas">
                            🎉 {{ tareasCompletadas }} tarea{{ tareasCompletadas > 1 ? 's' : '' }} completada{{ tareasCompletadas > 1 ? 's' : '' }}
                        </div>
                    </div>

                    <!-- Tips de focus -->
                    <div class="tips-card">
                        <h3>💡 Tips de productividad</h3>
                        <div class="tips-lista">
                            <div class="tip-item">
                                <span>📵</span>
                                <p>Silencia las notificaciones del móvil durante el Pomodoro</p>
                            </div>
                            <div class="tip-item">
                                <span>💧</span>
                                <p>Aprovecha los descansos para hidratarte y estirar</p>
                            </div>
                            <div class="tip-item">
                                <span>🎯</span>
                                <p>Escribe una sola tarea prioritaria antes de empezar</p>
                            </div>
                            <div class="tip-item">
                                <span>🧘</span>
                                <p>En el descanso largo, haz 5 minutos de meditación</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.focus-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.focus-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.focus-header h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
.focus-header p  { color: #666; margin: 0.25rem 0 0; }

.fh-stats { display: flex; gap: 1rem; }

.fhs-item {
    background: white;
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    border: 1px solid #f0f0f0;
    text-align: center;
}

.fhs-val { display: block; font-size: 1.5rem; font-weight: 900; color: #4ECDC4; }
.fhs-lab { display: block; font-size: 0.72rem; color: #888; font-weight: 600; }

/* Grid */
.focus-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1.5rem;
    align-items: start;
}

.focus-main    { display: flex; flex-direction: column; gap: 1.25rem; }
.focus-sidebar { display: flex; flex-direction: column; gap: 1.25rem; }

/* Config selector */
.config-selector { display: flex; gap: 0.5rem; flex-wrap: wrap; }

.cs-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #f0f0f0;
    border-radius: 20px;
    background: white;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
    color: #555;
}

.cs-btn:hover:not(:disabled) { border-color: #4ECDC4; }
.cs-btn.activa { border-color: transparent; color: #2D2D2D; }
.cs-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* Timer card */
.timer-card {
    background: white;
    border-radius: 24px;
    padding: 2rem;
    border: 3px solid;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    transition: border-color 0.5s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.timer-circulo { display: flex; justify-content: center; }

/* Ciclos */
.ciclos-visual { display: flex; gap: 0.6rem; }

.cv-punto {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #f0f0f0;
    transition: all 0.3s;
}

.cv-punto.completado { transform: scale(1.1); }

.ciclos-label { font-size: 0.78rem; color: #aaa; margin: 0; }

/* Controles */
.timer-controles { display: flex; gap: 0.75rem; }

.btn-iniciar-focus {
    padding: 0.9rem 2.5rem;
    color: white;
    font-weight: 800;
    font-size: 1rem;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-family: inherit;
    transition: opacity 0.2s, transform 0.2s;
}

.btn-iniciar-focus:hover { opacity: 0.88; transform: translateY(-2px); }

.btn-pausar-focus {
    padding: 0.9rem 2.5rem;
    background: #f5f5f5;
    color: #2D2D2D;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
}

.btn-pausar-focus:hover { background: #e0e0e0; }

.btn-reiniciar-focus {
    padding: 0.9rem 1.25rem;
    background: white;
    border: 2px solid #f0f0f0;
    border-radius: 30px;
    color: #888;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.btn-reiniciar-focus:hover { border-color: #E63946; color: #E63946; }

/* Config info */
.config-info {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.config-info span { font-size: 0.78rem; color: #888; font-weight: 600; }

/* Música */
.musica-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.musica-card h3 { font-size: 0.95rem; font-weight: 700; margin: 0; }

.musica-grid {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.mg-btn {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 0.9rem;
    border: 2px solid #f0f0f0;
    border-radius: 20px;
    background: white;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    color: #555;
    transition: all 0.2s;
}

.mg-btn:hover { border-color: #4ECDC4; }
.mg-btn.activa { background: #E8FAF9; border-color: #4ECDC4; color: #3BAFA7; }

/* Tareas */
.tareas-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.tc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tc-header h3 { font-size: 0.95rem; font-weight: 700; margin: 0; }

.tc-badge {
    background: #fff9c4;
    color: #856404;
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 700;
}

.nueva-tarea { display: flex; gap: 0.4rem; }

.nt-input {
    flex: 1;
    padding: 0.65rem 0.85rem;
    border: 1.5px solid #e8e8e8;
    border-radius: 10px;
    font-size: 0.88rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
}

.nt-input:focus { border-color: #4ECDC4; }

.nt-btn {
    width: 38px;
    height: 38px;
    background: #4ECDC4;
    color: white;
    font-size: 1.3rem;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.2s;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nt-btn:hover { background: #3BAFA7; }

.tc-empty {
    text-align: center;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.tc-empty span { font-size: 1.5rem; }
.tc-empty p    { font-size: 0.82rem; color: #aaa; margin: 0; }

.tareas-lista { display: flex; flex-direction: column; gap: 0.4rem; }

.tarea-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.6rem 0.5rem;
    border-radius: 8px;
    background: #fafafa;
    transition: opacity 0.2s;
}

.tarea-item.hecha { opacity: 0.6; }

.ti-check {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
    padding: 0;
    flex-shrink: 0;
}

.ti-info   { flex: 1; }
.ti-texto  { display: block; font-size: 0.85rem; color: #2D2D2D; font-weight: 500; }
.ti-tachado { text-decoration: line-through; color: #aaa; }
.ti-hora   { display: block; font-size: 0.7rem; color: #aaa; }

.ti-del {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.75rem;
    color: #ccc;
    padding: 0;
    transition: color 0.2s;
    flex-shrink: 0;
}

.ti-del:hover { color: #E63946; }

.tc-completadas {
    text-align: center;
    font-size: 0.82rem;
    color: #3BAFA7;
    font-weight: 700;
    background: #E8FAF9;
    border-radius: 8px;
    padding: 0.5rem;
}

/* Tips */
.tips-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.tips-card h3 { font-size: 0.95rem; font-weight: 700; margin: 0; }

.tips-lista { display: flex; flex-direction: column; gap: 0.6rem; }

.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    font-size: 0.82rem;
    color: #555;
    line-height: 1.4;
}

.tip-item span:first-child { font-size: 1rem; flex-shrink: 0; }
.tip-item p { margin: 0; }

/* Responsive */
@media (max-width: 800px) {
    .focus-grid { grid-template-columns: 1fr; }
}
</style>