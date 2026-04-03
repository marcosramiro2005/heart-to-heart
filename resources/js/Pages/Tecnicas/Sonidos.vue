<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const sonidos = [
    { id: 'lluvia', nombre: 'Lluvia suave', emoji: '🌧️', color: '#d0eaf8' },
    { id: 'bosque', nombre: 'Bosque', emoji: '🌲', color: '#d4edda' },
    { id: 'olas', nombre: 'Olas del mar', emoji: '🌊', color: '#cce5ff' },
    { id: 'fuego', nombre: 'Fuego', emoji: '🔥', color: '#ffd5d5' },
    { id: 'viento', nombre: 'Viento', emoji: '🍃', color: '#e8f5e9' },
    { id: 'pajaros', nombre: 'Pájaros', emoji: '🐦', color: '#fff9c4' },
]

const sonidoActivo = ref(null)
const volumen = ref(70)

const toggleSonido = (id) => {
    sonidoActivo.value = sonidoActivo.value === id ? null : id
}
</script>

<template>
    <AppLayout>
        <div class="sonidos-wrapper">

            <div class="sonidos-header">
                <span>🎵</span>
                <h1>SONIDOS RELAJANTES</h1>
            </div>

            <p class="subtitulo">Elige un sonido para acompañar tu momento de calma</p>

            <div class="sonidos-grid">
                <button
                    v-for="sonido in sonidos"
                    :key="sonido.id"
                    class="sonido-card"
                    :class="{ activo: sonidoActivo === sonido.id }"
                    :style="{ backgroundColor: sonido.color }"
                    @click="toggleSonido(sonido.id)"
                >
                    <span class="sonido-emoji">{{ sonido.emoji }}</span>
                    <span class="sonido-nombre">{{ sonido.nombre }}</span>
                    <span v-if="sonidoActivo === sonido.id" class="sonido-playing">▶ Reproduciendo</span>
                </button>
            </div>

            <div class="volumen-control">
                <span>🔈</span>
                <input type="range" v-model="volumen" min="0" max="100" class="volumen-slider" />
                <span>🔊</span>
                <span class="volumen-valor">{{ volumen }}%</span>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.sonidos-wrapper {
    max-width: 600px;
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

.sonidos-header h1 {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: 0.1em;
}

.subtitulo {
    color: #666;
    font-size: 0.95rem;
    margin: 0;
    text-align: center;
}

.sonidos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    width: 100%;
}

.sonido-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem 1rem;
    border: 2px solid transparent;
    border-radius: 16px;
    cursor: pointer;
    transition: transform 0.2s, border-color 0.2s;
}

.sonido-card:hover {
    transform: translateY(-3px);
}

.sonido-card.activo {
    border-color: #4ECDC4;
    box-shadow: 0 4px 16px rgba(78,205,196,0.3);
}

.sonido-emoji { font-size: 2.5rem; }

.sonido-nombre {
    font-size: 0.85rem;
    font-weight: 600;
    color: #2D2D2D;
    text-align: center;
}

.sonido-playing {
    font-size: 0.75rem;
    color: #3BAFA7;
    font-weight: 600;
}

.volumen-control {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    font-size: 1.2rem;
}

.volumen-slider {
    flex: 1;
    accent-color: #4ECDC4;
}

.volumen-valor {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2D2D2D;
    min-width: 40px;
}
</style>