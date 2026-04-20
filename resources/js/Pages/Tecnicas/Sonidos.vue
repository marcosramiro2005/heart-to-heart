<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onUnmounted } from 'vue'

const sonidos = [
    {
        id: 'lluvia',
        nombre: 'Lluvia suave',
        emoji: '🌧️',
        color: '#d0eaf8',
        desc: 'Lluvia tranquila reconfortante',
        url: '/sounds/lluvia.mp3',
    },
    {
        id: 'olas',
        nombre: 'Olas del mar',
        emoji: '🌊',
        color: '#cce5ff',
        desc: 'Olas rompiendo en la orilla',
        url: '/sounds/olas.mp3',
    },
    {
        id: 'bosque',
        nombre: 'Bosque',
        emoji: '🌲',
        color: '#d4edda',
        desc: 'Pájaros y brisa entre árboles',
        url: '/sounds/bosque.mp3',
    },
    {
        id: 'fuego',
        nombre: 'Chimenea',
        emoji: '🔥',
        color: '#ffd5d5',
        desc: 'Crepitar cálido del fuego',
        url: '/sounds/fuego.mp3',
    },
    {
        id: 'rio',
        nombre: 'Río',
        emoji: '💧',
        color: '#e3f2fd',
        desc: 'Agua fluyendo entre piedras',
        url: '/sounds/rio.mp3',
    },
    {
        id: 'noche',
        nombre: 'Noche de verano',
        emoji: '🌙',
        color: '#e8eaf6',
        desc: 'Grillos y silencio nocturno',
        url: '/sounds/noche.mp3',
    },
    {
        id: 'viento',
        nombre: 'Viento suave',
        emoji: '🍃',
        color: '#e8f5e9',
        desc: 'Brisa tranquila entre hojas',
        url: '/sounds/viento.mp3',
    },
    {
        id: 'cafe',
        nombre: 'Cafetería',
        emoji: '☕',
        color: '#fff9c4',
        desc: 'Ambiente tranquilo de cafetería',
        url: '/sounds/cafe.mp3',
    },
]

const sonidoActivo = ref(null)
const volumen      = ref(70)
const cargando     = ref(false)
const error        = ref(false)
let audioActual    = null

const toggleSonido = async (sonido) => {
    error.value = false

    if (sonidoActivo.value === sonido.id) {
        audioActual?.pause()
        audioActual = null
        sonidoActivo.value = null
        return
    }

    if (audioActual) {
        audioActual.pause()
        audioActual = null
    }

    sonidoActivo.value = sonido.id
    cargando.value     = true

    try {
        const audio  = new Audio(sonido.url)
        audio.loop   = true
        audio.volume = volumen.value / 100

        audio.addEventListener('canplaythrough', () => {
            cargando.value = false
        }, { once: true })

        audio.addEventListener('error', () => {
            cargando.value     = false
            error.value        = true
            sonidoActivo.value = null
            audioActual        = null
        }, { once: true })

        await audio.play()
        audioActual = audio

    } catch (e) {
        cargando.value     = false
        error.value        = true
        sonidoActivo.value = null
        audioActual        = null
    }
}

const cambiarVolumen = () => {
    if (audioActual) {
        audioActual.volume = volumen.value / 100
    }
}

onUnmounted(() => {
    audioActual?.pause()
    audioActual = null
})
</script>

<template>
    <AppLayout>
        <div class="sonidos-wrapper">

            <div class="sonidos-header">
                <span>🎵</span>
                <h1>SONIDOS RELAJANTES</h1>
            </div>

            <p class="subtitulo">Elige un ambiente sonoro para acompañar tu momento de calma</p>

            <!-- Reproductor activo -->
            <div v-if="sonidoActivo" class="reproductor-activo">
                <div class="ra-info">
                    <span class="ra-emoji">{{ sonidos.find(s => s.id === sonidoActivo)?.emoji }}</span>
                    <div>
                        <span class="ra-nombre">{{ sonidos.find(s => s.id === sonidoActivo)?.nombre }}</span>
                        <span class="ra-estado">{{ cargando ? '⏳ Cargando...' : '▶ Reproduciendo' }}</span>
                    </div>
                </div>
                <div v-if="!cargando" class="ondas-anim">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
            </div>

            <!-- Error -->
            <div v-if="error" class="error-msg">
                ⚠️ No se pudo cargar el sonido. Comprueba tu conexión a internet.
            </div>

            <!-- Grid de sonidos -->
            <div class="sonidos-grid">
                <button
                    v-for="sonido in sonidos"
                    :key="sonido.id"
                    class="sonido-card"
                    :class="{ activo: sonidoActivo === sonido.id, cargando: cargando && sonidoActivo === sonido.id }"
                    :style="{ backgroundColor: sonido.color }"
                    @click="toggleSonido(sonido)"
                >
                    <span class="sonido-emoji">{{ sonido.emoji }}</span>
                    <span class="sonido-nombre">{{ sonido.nombre }}</span>
                    <span class="sonido-desc">{{ sonido.desc }}</span>
                    <span v-if="cargando && sonidoActivo === sonido.id" class="sonido-estado">⏳ Cargando...</span>
                    <span v-else-if="sonidoActivo === sonido.id" class="sonido-playing">⏸ Pausar</span>
                    <span v-else class="sonido-play">▶ Escuchar</span>
                </button>
            </div>

            <!-- Volumen -->
            <div class="volumen-control">
                <span>🔈</span>
                <input
                    type="range"
                    v-model="volumen"
                    min="0"
                    max="100"
                    class="volumen-slider"
                    @input="cambiarVolumen"
                />
                <span>🔊</span>
                <span class="vol-valor">{{ volumen }}%</span>
            </div>

            <div class="sonidos-nota">
                💡 Los sonidos se reproducen en bucle. Necesitas conexión a internet para cargarlos.
                Combínalos con la respiración guiada para una experiencia más completa.
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.sonidos-wrapper {
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.sonidos-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #d0eaf8;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.sonidos-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; letter-spacing: 0.1em; }
.subtitulo { color: #666; font-size: 0.95rem; text-align: center; }

.reproductor-activo {
    width: 100%;
    background: #E8FAF9;
    border-radius: 14px;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 2px solid #4ECDC4;
}

.ra-info   { display: flex; align-items: center; gap: 0.75rem; }
.ra-emoji  { font-size: 1.8rem; }
.ra-nombre { display: block; font-weight: 700; font-size: 0.95rem; color: #2D2D2D; }
.ra-estado { display: block; font-size: 0.78rem; color: #3BAFA7; font-weight: 600; }

.ondas-anim { display: flex; align-items: center; gap: 3px; height: 30px; }
.ondas-anim span {
    width: 4px;
    background: #4ECDC4;
    border-radius: 2px;
    animation: onda 1s ease-in-out infinite;
}
.ondas-anim span:nth-child(1) { height: 10px; animation-delay: 0s; }
.ondas-anim span:nth-child(2) { height: 20px; animation-delay: 0.1s; }
.ondas-anim span:nth-child(3) { height: 28px; animation-delay: 0.2s; }
.ondas-anim span:nth-child(4) { height: 20px; animation-delay: 0.3s; }
.ondas-anim span:nth-child(5) { height: 10px; animation-delay: 0.4s; }

@keyframes onda {
    0%, 100% { transform: scaleY(0.4); }
    50%       { transform: scaleY(1); }
}

.error-msg {
    width: 100%;
    background: #fff5f5;
    border: 1.5px solid #E63946;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.88rem;
    color: #E63946;
    font-weight: 600;
    text-align: center;
}

.sonidos-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    width: 100%;
}

.sonido-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    padding: 1.25rem 0.75rem;
    border: 2px solid transparent;
    border-radius: 14px;
    cursor: pointer;
    transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
    text-align: center;
}

.sonido-card:hover   { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }
.sonido-card.activo  { border-color: #4ECDC4; box-shadow: 0 4px 16px rgba(78,205,196,0.3); }
.sonido-card.cargando { opacity: 0.7; cursor: wait; }

.sonido-emoji   { font-size: 2rem; }
.sonido-nombre  { font-size: 0.85rem; font-weight: 700; color: #2D2D2D; }
.sonido-desc    { font-size: 0.72rem; color: #666; line-height: 1.3; }
.sonido-playing { font-size: 0.75rem; color: #3BAFA7; font-weight: 700; }
.sonido-play    { font-size: 0.75rem; color: #aaa; }
.sonido-estado  { font-size: 0.75rem; color: #888; }

.volumen-control {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    padding: 1rem 1.5rem;
    background: #fafafa;
    border-radius: 12px;
}

.volumen-slider { flex: 1; accent-color: #4ECDC4; cursor: pointer; height: 6px; }
.vol-valor { font-weight: 700; font-size: 0.9rem; color: #2D2D2D; min-width: 40px; text-align: right; }

.sonidos-nota {
    font-size: 0.85rem;
    color: #666;
    text-align: center;
    background: #fafafa;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    line-height: 1.5;
    width: 100%;
}

@media (max-width: 600px) {
    .sonidos-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>