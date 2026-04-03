<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const ejercicios = [
    {
        nombre: 'Caminata consciente',
        emoji: '🚶',
        duracion: '20 min',
        descripcion: 'Camina lentamente prestando atención a cada paso y tu respiración.',
        color: '#ffd5d5'
    },
    {
        nombre: 'Yoga suave',
        emoji: '🧘',
        duracion: '15 min',
        descripcion: 'Posturas suaves para liberar tensión acumulada en el cuerpo.',
        color: '#d4edda'
    },
    {
        nombre: 'Estiramiento',
        emoji: '🤸',
        duracion: '10 min',
        descripcion: 'Estira los principales grupos musculares para relajar el cuerpo.',
        color: '#d0eaf8'
    },
    {
        nombre: 'Baile libre',
        emoji: '💃',
        duracion: '10 min',
        descripcion: 'Mueve tu cuerpo libremente con tu música favorita.',
        color: '#fff9c4'
    },
]

const seleccionado = ref(null)
</script>

<template>
    <AppLayout>
        <div class="ej-wrapper">
            <div class="ej-header">
                <span>🏃</span>
                <h1>EJERCICIO</h1>
            </div>
            <p class="subtitulo">El movimiento libera endorfinas y mejora tu estado de ánimo</p>

            <div class="ej-grid">
                <div
                    v-for="ej in ejercicios"
                    :key="ej.nombre"
                    class="ej-card"
                    :class="{ seleccionado: seleccionado === ej.nombre }"
                    :style="{ backgroundColor: ej.color }"
                    @click="seleccionado = ej.nombre"
                >
                    <span class="ej-emoji">{{ ej.emoji }}</span>
                    <h3>{{ ej.nombre }}</h3>
                    <span class="ej-duracion">⏱ {{ ej.duracion }}</span>
                    <p>{{ ej.descripcion }}</p>
                </div>
            </div>

            <button v-if="seleccionado" class="btn-empezar">
                ▶ Empezar {{ seleccionado }}
            </button>
        </div>
    </AppLayout>
</template>

<style scoped>
.ej-wrapper {
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.ej-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #ffd5d5;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.ej-header h1 {
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

.ej-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    width: 100%;
}

.ej-card {
    padding: 1.5rem;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.2s, border-color 0.2s;
}

.ej-card:hover { transform: translateY(-3px); }

.ej-card.seleccionado {
    border-color: #4ECDC4;
    box-shadow: 0 4px 16px rgba(78,205,196,0.3);
}

.ej-emoji { font-size: 2.5rem; }

.ej-card h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
}

.ej-duracion {
    font-size: 0.82rem;
    color: #3BAFA7;
    font-weight: 600;
}

.ej-card p {
    font-size: 0.85rem;
    color: #555;
    margin: 0;
    line-height: 1.4;
}

.btn-empezar {
    padding: 0.9rem 2.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 0.95rem;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-empezar:hover { background: #3BAFA7; }
</style>