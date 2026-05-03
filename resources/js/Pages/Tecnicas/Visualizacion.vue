<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onUnmounted } from 'vue'

const visualizaciones = [
    {
        id: 'playa',
        titulo: 'Playa tranquila',
        emoji: '🏖️',
        duracion: '8 min',
        color: '#d0eaf8',
        colorAcento: '#3B8BD4',
        descripcion: 'Deja que las olas te lleven a un estado de calma profunda.',
        audio: { url: '/sounds/olas.mp3' },
        guia: [
            { texto: 'Cierra los ojos. Voy a acompañarte en este viaje. Respira profundo tres veces, soltando la tensión en cada exhalación.', pausa: 5000 },
            { texto: 'Imagina que estás en una playa tranquila. El sol de la tarde calienta suavemente tu piel.', pausa: 4000 },
            { texto: 'Escuchas el ritmo constante de las olas. Un sonido que lleva existiendo miles de años, siempre igual, siempre calmante.', pausa: 4000 },
            { texto: 'Sientes la arena cálida bajo tus pies. Cada grano de arena está ahí para sostenerte.', pausa: 4000 },
            { texto: 'Caminas lentamente hacia la orilla. El agua fresca toca tus pies. Está perfectamente fría y refrescante.', pausa: 5000 },
            { texto: 'Cada ola que llega se lleva consigo una preocupación. Observa cómo se las lleva el mar.', pausa: 5000 },
            { texto: 'Te sientas en la arena. El horizonte es infinito, azul y sereno. No hay nada que hacer, solo estar.', pausa: 5000 },
            { texto: 'Respira el aire salado. Con cada respiración, tu cuerpo se relaja un poco más.', pausa: 5000 },
            { texto: 'Este lugar siempre estará aquí para ti. Puedes volver cuando lo necesites.', pausa: 4000 },
            { texto: 'Poco a poco, toma conciencia de tu cuerpo. Mueve los dedos de las manos. Abre los ojos lentamente. Llevas esta calma contigo.', pausa: 0 },
        ],
    },
    {
        id: 'bosque',
        titulo: 'Bosque mágico',
        emoji: '🌲',
        duracion: '10 min',
        color: '#d4edda',
        colorAcento: '#2d8a4e',
        descripcion: 'Conéctate con la energía tranquilizadora de la naturaleza.',
        audio: { url: '/sounds/bosque.mp3' },
        guia: [
            { texto: 'Cierra los ojos y deja que tu cuerpo se relaje completamente. Estoy aquí contigo.', pausa: 5000 },
            { texto: 'Imagina que estás al inicio de un sendero en un bosque antiguo. La luz del sol se filtra entre las hojas creando destellos dorados.', pausa: 5000 },
            { texto: 'Escuchas el canto de los pájaros a lo lejos. El viento susurra entre los árboles como si te contara secretos.', pausa: 5000 },
            { texto: 'Das tus primeros pasos por el sendero de tierra suave. Cada paso te conecta más con la tierra bajo tus pies.', pausa: 4000 },
            { texto: 'El aire huele a pino y a tierra mojada. Es un olor limpio, natural, que calma la mente.', pausa: 4000 },
            { texto: 'Llegas a un claro. En el centro hay un árbol enorme y antiguo. Sus raíces abrazan la tierra desde hace siglos.', pausa: 5000 },
            { texto: 'Te apoyas en su tronco. Sientes su energía, su paciencia, su fortaleza silenciosa. Absorbe tu cansancio.', pausa: 5000 },
            { texto: 'Los árboles a tu alrededor te forman un círculo protector. Aquí estás completamente a salvo.', pausa: 5000 },
            { texto: 'Respira el aire puro del bosque. Con cada respiración te llevas un poco de su calma.', pausa: 4000 },
            { texto: 'El bosque te despide con el susurro del viento. Abre los ojos cuando estés listo. Llevas el bosque contigo.', pausa: 0 },
        ],
    },
    {
        id: 'montana',
        titulo: 'Cima de montaña',
        emoji: '⛰️',
        duracion: '7 min',
        color: '#e8d5f5',
        colorAcento: '#7a4da8',
        descripcion: 'Gana perspectiva y claridad desde lo alto.',
        audio: { url: '/sounds/noche.mp3' },
        guia: [
            { texto: 'Cierra los ojos. Respira hondo y suéltalo todo. No hay nada que resolver ahora mismo.', pausa: 5000 },
            { texto: 'Imagina que estás en la cima de una montaña hermosa. El camino hasta aquí fue largo, pero lo lograste.', pausa: 5000 },
            { texto: 'A tus pies se extienden valles verdes, ríos brillantes y pueblos pequeños como miniaturas.', pausa: 4000 },
            { texto: 'Desde aquí arriba, los problemas del día a día parecen pequeños, manejables, pasajeros.', pausa: 5000 },
            { texto: 'El cielo es de un azul profundo. Una brisa fresca y limpia despeja tu mente por completo.', pausa: 4000 },
            { texto: 'Sientes la fortaleza de la montaña bajo tus pies. Millones de años de historia que te sostienen.', pausa: 5000 },
            { texto: 'Esa fortaleza también es tuya. La llevas dentro desde siempre.', pausa: 4000 },
            { texto: 'Eres libre aquí. Puedes ver con claridad lo que realmente importa en tu vida.', pausa: 5000 },
            { texto: 'Cuando estés listo, abre los ojos llevando esta perspectiva y esta claridad contigo al mundo.', pausa: 0 },
        ],
    },
]

const visualActiva  = ref(null)
const faseIdx       = ref(0)
const leyendo       = ref(false)
const completado    = ref(false)
const textoActual   = ref('')
const vozDisponible = ref(!!window.speechSynthesis)
let audioCtx        = null
let nodoActual      = null
let audioElement    = null
let cancelado       = false

const detenerAudio = () => {
    if (audioElement) {
        audioElement.pause()
        audioElement = null
    }
    if (!nodoActual) return
    try {
        nodoActual.processor.disconnect()
        nodoActual.filter.disconnect()
        nodoActual.gain.disconnect()
        nodoActual.lfo.stop()
        nodoActual.lfo.disconnect()
        nodoActual.lfoGain.disconnect()
    } catch (e) {}
    nodoActual = null
}

// ── Cargar voces disponibles ──
const cargarMejorVoz = () => {
    const voces = window.speechSynthesis.getVoices()
    
    // Prioridad de voces en español — las más naturales primero
    const prioridad = [
        'Microsoft Pablo',       // Windows — voz masculina natural
        'Microsoft Sabina',      // Windows — voz femenina natural  
        'Microsoft Helena',      // Windows — otra opción
        'Google español',
        'Jorge',
        'Mónica',
        'Paulina',
        'Diego',
    ]

    for (const nombre of prioridad) {
        const voz = voces.find(v => v.name.includes(nombre))
        if (voz) return voz
    }

    // Fallback — cualquier voz en español
    return voces.find(v => v.lang.startsWith('es')) || null
}

const hablar = (texto, onFin) => {
    if (!window.speechSynthesis || cancelado) { onFin?.(); return }

    window.speechSynthesis.cancel()

    const utt    = new SpeechSynthesisUtterance(texto)
    utt.lang     = 'es-ES'
    utt.rate     = 0.78   // más lento y pausado
    utt.pitch    = 0.85   // más grave
    utt.volume   = 0.55   // más bajo para no tapar el audio

    const voz = cargarMejorVoz()
    if (voz) utt.voice = voz

    utt.onend  = () => { if (!cancelado) onFin?.() }
    utt.onerror = () => { if (!cancelado) onFin?.() }

    // Workaround para Chrome que a veces corta la voz en textos largos
    const keepAlive = setInterval(() => {
        if (!window.speechSynthesis.speaking) clearInterval(keepAlive)
        else window.speechSynthesis.pause(), window.speechSynthesis.resume()
    }, 10000)

    window.speechSynthesis.speak(utt)
}

// ── Web Audio API (ambiente) ──
const crearAudio = (cfg) => {
    detenerAudio()

    if (cfg?.url) {
        audioElement = new Audio(cfg.url)
        audioElement.loop = true
        audioElement.volume = 0.06
        audioElement.play().catch(() => {})
        return
    }

    if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)()
    if (audioCtx.state === 'suspended') audioCtx.resume()

    const bufSize   = 4096
    const processor = audioCtx.createScriptProcessor(bufSize, 1, 1)
    const filter    = audioCtx.createBiquadFilter()
    const gain      = audioCtx.createGain()

    filter.type            = cfg.tipo
    filter.frequency.value = cfg.frecuencia
    filter.Q.value         = cfg.Q
    gain.gain.value        = 0.06

    processor.onaudioprocess = (e) => {
        const out = e.outputBuffer.getChannelData(0)
        for (let i = 0; i < bufSize; i++) out[i] = Math.random() * 2 - 1
    }

    const lfo     = audioCtx.createOscillator()
    const lfoGain = audioCtx.createGain()
    lfo.frequency.value = cfg.lfoFreq
    lfoGain.gain.value  = cfg.lfoGain
    lfo.connect(lfoGain)
    lfoGain.connect(gain.gain)
    lfo.start()

    processor.connect(filter)
    filter.connect(gain)
    gain.connect(audioCtx.destination)

    nodoActual = { processor, filter, gain, lfo, lfoGain }
}

// ── Flujo principal ──
const iniciar = (viz) => {
    cancelado          = false
    visualActiva.value = viz
    faseIdx.value      = 0
    leyendo.value      = true
    completado.value   = false
    crearAudio(viz.audio)
    ejecutarFase(viz, 0)
}

const ejecutarFase = (viz, idx) => {
    if (cancelado) return
    faseIdx.value    = idx
    textoActual.value = viz.guia[idx].texto
    leyendo.value    = true

    hablar(viz.guia[idx].texto, () => {
        if (cancelado) return
        leyendo.value = false

        const pausa = viz.guia[idx].pausa
        if (idx < viz.guia.length - 1) {
            setTimeout(() => {
                if (!cancelado) ejecutarFase(viz, idx + 1)
            }, pausa)
        } else {
            setTimeout(() => {
                if (!cancelado) {
                    detenerAudio()
                    completado.value = true
                }
            }, 2000)
        }
    })
}

const detener = () => {
    cancelado = true
    window.speechSynthesis?.cancel()
    detenerAudio()
    leyendo.value      = false
    visualActiva.value = null
    completado.value   = false
}

const progresoActual = () => {
    if (!visualActiva.value) return 0
    return Math.round((faseIdx.value / (visualActiva.value.guia.length - 1)) * 100)
}

onUnmounted(() => {
    cancelado = true
    window.speechSynthesis?.cancel()
    detenerAudio()
    audioCtx?.close()
})
</script>

<template>
    <AppLayout>
        <div class="viz-wrapper">

            <div class="viz-header">
                <span>🌈</span>
                <h1>VISUALIZACIÓN GUIADA</h1>
            </div>

            <p class="subtitulo">Una voz te guiará con los ojos cerrados mientras suena el audio ambiente</p>

            <!-- Aviso voz -->
            <div v-if="!vozDisponible" class="viz-aviso">
                ⚠️ Tu navegador no soporta síntesis de voz. Prueba con Chrome o Edge.
            </div>

            <!-- Selector -->
            <div v-if="!visualActiva" class="viz-selector">

                <div class="viz-instruccion">
                    <span>🎧</span>
                    <div>
                        <strong>Cómo funciona</strong>
                        <p>Elige un escenario, pon los auriculares, cierra los ojos y una voz te guiará. El audio ambiente sonará de fondo durante toda la sesión.</p>
                    </div>
                </div>

                <div
                    v-for="viz in visualizaciones"
                    :key="viz.id"
                    class="viz-card"
                    :style="{ backgroundColor: viz.color }"
                    @click="iniciar(viz)"
                >
                    <div class="vc-top">
                        <span class="vc-emoji">{{ viz.emoji }}</span>
                        <div class="vc-meta">
                            <span class="vc-dur">⏱ {{ viz.duracion }}</span>
                            <span class="vc-pasos">{{ viz.guia.length }} etapas con voz</span>
                        </div>
                    </div>
                    <h3>{{ viz.titulo }}</h3>
                    <p>{{ viz.descripcion }}</p>
                    <div class="vc-tags">
                        <span>🎙️ Voz guiada</span>
                        <span>🎵 Audio ambiente</span>
                        <span>👁️ Ojos cerrados</span>
                    </div>
                    <button class="btn-viz" :style="{ backgroundColor: viz.colorAcento }">
                        ▶ Comenzar
                    </button>
                </div>
            </div>

            <!-- Sesión activa -->
            <div v-if="visualActiva && !completado" class="viz-sesion">

                <div class="vs-cabecera" :style="{ backgroundColor: visualActiva.color }">
                    <span class="vs-emoji">{{ visualActiva.emoji }}</span>
                    <div class="vs-info">
                        <h3>{{ visualActiva.titulo }}</h3>
                        <span>Etapa {{ faseIdx + 1 }} de {{ visualActiva.guia.length }}</span>
                    </div>
                    <div class="vs-indicadores">
                        <!-- Audio -->
                        <div class="vs-audio-ind">
                            <div class="audio-ondas">
                                <span></span><span></span><span></span><span></span><span></span>
                            </div>
                            <span>Audio</span>
                        </div>
                        <!-- Voz -->
                        <div class="vs-voz-ind" :class="{ activa: leyendo }">
                            <span class="voz-mic">🎙️</span>
                            <span>{{ leyendo ? 'Leyendo' : 'Pausa' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Barra progreso -->
                <div class="vs-progreso">
                    <div class="vsp-barra">
                        <div
                            class="vsp-relleno"
                            :style="{ width: progresoActual() + '%', backgroundColor: visualActiva.colorAcento }"
                        ></div>
                    </div>
                    <span>{{ progresoActual() }}%</span>
                </div>

                <!-- Texto actual -->
                <div class="vs-texto-card">
                    <div class="vtc-ojo">👁️‍🗨️</div>
                    <p class="vtc-hint">Cierra los ojos y escucha la voz</p>
                    <div class="vtc-divider"></div>
                    <p :key="faseIdx" class="vtc-texto">{{ textoActual }}</p>
                    <div v-if="leyendo" class="vtc-voz-anim">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <div v-else class="vtc-pausa">
                        <span>⏳ Continuando en unos segundos...</span>
                    </div>
                </div>

                <button class="btn-detener-viz" @click="detener">⏹ Detener</button>
            </div>

            <!-- Completado -->
            <div v-if="completado" class="viz-completado">
                <span class="vc-big">{{ visualActiva.emoji }}</span>
                <h2>Sesión completada</h2>
                <p>Has completado <strong>{{ visualActiva.titulo }}</strong>.<br>Tómate un momento antes de continuar con tu día.</p>
                <div class="vizc-bots">
                    <button class="btn-rep" @click="iniciar(visualActiva)">🔄 Repetir</button>
                    <button class="btn-otra" @click="visualActiva = null; completado = false">Ver otros</button>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.viz-wrapper {
    max-width: 650px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.viz-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #ffd5e5;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    justify-content: center;
    font-size: 1.8rem;
}

.viz-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }
.subtitulo { color: #666; font-size: 0.95rem; text-align: center; }

.viz-aviso {
    background: #fff9c4;
    border: 1.5px solid #FFD700;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.88rem;
    color: #856404;
    font-weight: 600;
    text-align: center;
}

/* ── Selector ── */
.viz-selector { display: flex; flex-direction: column; gap: 1rem; }

.viz-instruccion {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    background: #fafafa;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    font-size: 0.88rem;
    color: #555;
    line-height: 1.5;
    border-left: 4px solid #4ECDC4;
}

.viz-instruccion span:first-child { font-size: 1.5rem; flex-shrink: 0; }
.viz-instruccion strong { display: block; color: #2D2D2D; margin-bottom: 0.25rem; }
.viz-instruccion p { margin: 0; }

.viz-card {
    border-radius: 18px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}

.viz-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

.vc-top { display: flex; justify-content: space-between; align-items: flex-start; }
.vc-emoji { font-size: 2rem; }
.vc-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 0.2rem; }
.vc-dur   { font-size: 0.78rem; color: #888; font-weight: 600; }
.vc-pasos { font-size: 0.75rem; color: #aaa; }

.viz-card h3 { font-size: 1rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.viz-card p  { font-size: 0.88rem; color: #555; line-height: 1.5; margin: 0; }

.vc-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}

.vc-tags span {
    padding: 0.2rem 0.6rem;
    background: rgba(255,255,255,0.6);
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #444;
}

.btn-viz {
    padding: 0.7rem 1.5rem;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.88rem;
    align-self: flex-start;
    transition: opacity 0.2s;
    font-family: inherit;
}

.btn-viz:hover { opacity: 0.88; }

/* ── Sesión ── */
.viz-sesion {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
}

.vs-cabecera {
    width: 100%;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.vs-emoji { font-size: 2rem; flex-shrink: 0; }
.vs-info  { flex: 1; }
.vs-info h3 { font-size: 0.95rem; font-weight: 700; margin: 0; }
.vs-info span { font-size: 0.75rem; color: #666; }

.vs-indicadores { display: flex; gap: 0.75rem; }

.vs-audio-ind, .vs-voz-ind {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
    font-size: 0.65rem;
    color: #666;
}

/* Ondas audio */
.audio-ondas { display: flex; align-items: center; gap: 2px; height: 16px; }
.audio-ondas span {
    width: 3px;
    background: rgba(0,0,0,0.3);
    border-radius: 2px;
    animation: onda-a 1s ease-in-out infinite;
}
.audio-ondas span:nth-child(1) { height: 6px; animation-delay: 0s; }
.audio-ondas span:nth-child(2) { height: 11px; animation-delay: 0.1s; }
.audio-ondas span:nth-child(3) { height: 15px; animation-delay: 0.2s; }
.audio-ondas span:nth-child(4) { height: 11px; animation-delay: 0.3s; }
.audio-ondas span:nth-child(5) { height: 6px; animation-delay: 0.4s; }
@keyframes onda-a { 0%,100%{transform:scaleY(0.4)} 50%{transform:scaleY(1)} }

.vs-voz-ind .voz-mic { font-size: 1rem; }
.vs-voz-ind.activa { color: #4ECDC4; }
.vs-voz-ind.activa .voz-mic { animation: pulso-mic 0.6s ease-in-out infinite; }
@keyframes pulso-mic { 0%,100%{transform:scale(1)} 50%{transform:scale(1.2)} }

/* Progreso */
.vs-progreso { width: 100%; display: flex; align-items: center; gap: 0.75rem; }
.vsp-barra { flex: 1; height: 6px; background: #f0f0f0; border-radius: 3px; overflow: hidden; }
.vsp-relleno { height: 100%; border-radius: 3px; transition: width 0.8s ease; }
.vs-progreso span { font-size: 0.78rem; color: #aaa; font-weight: 600; }

/* Texto */
.vs-texto-card {
    width: 100%;
    background: #1a1a2e;
    border-radius: 20px;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    text-align: center;
    min-height: 220px;
    justify-content: center;
}

.vtc-ojo { font-size: 2rem; opacity: 0.5; }

.vtc-hint {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.4);
    margin: 0;
    font-style: italic;
}

.vtc-divider {
    width: 40px;
    height: 1px;
    background: rgba(255,255,255,0.15);
}

.vtc-texto {
    font-size: 1rem;
    color: rgba(255,255,255,0.9);
    line-height: 1.8;
    font-style: italic;
    margin: 0;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }

/* Animación de voz activa */
.vtc-voz-anim {
    display: flex;
    gap: 4px;
    align-items: center;
}

.vtc-voz-anim span {
    width: 4px;
    background: #4ECDC4;
    border-radius: 2px;
    animation: voz-bar 1s ease-in-out infinite;
}

.vtc-voz-anim span:nth-child(1) { height: 10px; animation-delay: 0s; }
.vtc-voz-anim span:nth-child(2) { height: 18px; animation-delay: 0.1s; }
.vtc-voz-anim span:nth-child(3) { height: 24px; animation-delay: 0.2s; }
.vtc-voz-anim span:nth-child(4) { height: 18px; animation-delay: 0.3s; }
.vtc-voz-anim span:nth-child(5) { height: 10px; animation-delay: 0.4s; }

@keyframes voz-bar { 0%,100%{transform:scaleY(0.4)} 50%{transform:scaleY(1)} }

.vtc-pausa {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.3);
}

.btn-detener-viz {
    padding: 0.7rem 1.5rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    color: #aaa;
    transition: all 0.2s;
    font-family: inherit;
}

.btn-detener-viz:hover { background: #E63946; color: white; border-color: #E63946; }

/* Completado */
.viz-completado {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
}

.vc-big { font-size: 4rem; }
.viz-completado h2 { font-size: 1.4rem; font-weight: 800; color: #2D2D2D; margin: 0; }
.viz-completado p  { font-size: 0.95rem; color: #555; line-height: 1.7; margin: 0; }

.vizc-bots { display: flex; gap: 0.75rem; }

.btn-rep {
    padding: 0.85rem 1.75rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s;
    font-family: inherit;
}

.btn-rep:hover { background: #3BAFA7; }

.btn-otra {
    padding: 0.85rem 1.75rem;
    background: white;
    border: 2px solid #4ECDC4;
    border-radius: 25px;
    color: #3BAFA7;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}

.btn-otra:hover { background: #4ECDC4; color: white; }
</style>