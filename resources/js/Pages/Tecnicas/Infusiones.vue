<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

const infusiones = [
    {
        nombre: 'Manzanilla',
        emoji: '🌼',
        beneficio: 'Reduce la ansiedad y ayuda a conciliar el sueño.',
        color: '#fff9c4'
    },
    {
        nombre: 'Valeriana',
        emoji: '🌿',
        beneficio: 'Calma el sistema nervioso y mejora el descanso.',
        color: '#d4edda'
    },
    {
        nombre: 'Lavanda',
        emoji: '💜',
        beneficio: 'Alivia el estrés y tiene efecto relajante.',
        color: '#e8d5f5'
    },
    {
        nombre: 'Tila',
        emoji: '🌸',
        beneficio: 'Sedante natural, ideal para momentos de tensión.',
        color: '#ffd5d5'
    },
    {
        nombre: 'Melisa',
        emoji: '🍃',
        beneficio: 'Reduce la ansiedad y mejora el estado de ánimo.',
        color: '#d4edda'
    },
    {
        nombre: 'Pasiflora',
        emoji: '🌺',
        beneficio: 'Combate el insomnio y la agitación nerviosa.',
        color: '#fce4ec'
    },
]
</script>

<template>
    <AppLayout>
        <div class="inf-wrapper">
            <div class="inf-header">
                <span>🍵</span>
                <h1>INFUSIONES</h1>
            </div>
            <p class="subtitulo">Plantas naturales para calmar tu mente y cuerpo</p>

            <div class="inf-grid">
                <div
                    v-for="inf in infusiones"
                    :key="inf.nombre"
                    class="inf-card"
                    :style="{ backgroundColor: inf.color }"
                >
                    <span class="inf-emoji">{{ inf.emoji }}</span>
                    <h3>{{ inf.nombre }}</h3>
                    <p>{{ inf.beneficio }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.inf-wrapper {
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.inf-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #e8d5f5;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.inf-header h1 {
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

.inf-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    width: 100%;
}

.inf-card {
    padding: 1.5rem 1rem;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    text-align: center;
}

.inf-emoji { font-size: 2.5rem; }

.inf-card h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
}

.inf-card p {
    font-size: 0.82rem;
    color: #555;
    margin: 0;
    line-height: 1.4;
}

@media (max-width: 600px) {
    .inf-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>