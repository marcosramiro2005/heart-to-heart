<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    stats: Object,
})

const niveles = [
    { nombre: 'Semilla',             min: 0,  max: 0,  emoji: '🌱', color: '#d4edda' },
    { nombre: 'Iniciado',            min: 1,  max: 2,  emoji: '🌿', color: '#d4edda' },
    { nombre: 'Aprendiz',            min: 3,  max: 5,  emoji: '🌳', color: '#E8FAF9' },
    { nombre: 'Explorador',          min: 6,  max: 8,  emoji: '⭐', color: '#fff9c4' },
    { nombre: 'Experto Emocional',   min: 9,  max: 11, emoji: '💫', color: '#fce4ec' },
    { nombre: 'Maestro del Bienestar', min: 12, max: 99, emoji: '🏆', color: '#ffd5d5' },
]
</script>

<template>
    <AppLayout>
        <div class="stats-wrapper">
            <div class="stats-header">
                <h1>📈 Mis estadísticas</h1>
                <p>Tu progreso en Heart to Heart</p>
            </div>

            <div class="niveles-grid">
                <div v-for="nivel in niveles" :key="nivel.nombre"
                    class="nivel-card"
                    :style="{ backgroundColor: nivel.color }">
                    <span class="nc-emoji">{{ nivel.emoji }}</span>
                    <h3>{{ nivel.nombre }}</h3>
                    <span class="nc-req">{{ nivel.min === nivel.max ? `${nivel.min} logros` : `${nivel.min}–${nivel.max === 99 ? '12+' : nivel.max} logros` }}</span>
                </div>
            </div>

            <div class="stats-accesos">
                <Link href="/logros"       class="sa-item">🏆 Ver mis logros</Link>
                <Link href="/mis-emociones" class="sa-item">📊 Dashboard emocional</Link>
                <Link href="/retos"        class="sa-item">🎯 Mis retos</Link>
                <Link href="/perfil"       class="sa-item">👤 Mi perfil</Link>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.stats-wrapper {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.stats-header h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
.stats-header p  { color: #666; margin: 0.25rem 0 0; }

.niveles-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.nivel-card {
    border-radius: 14px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    text-align: center;
}

.nc-emoji { font-size: 2.5rem; }
.nivel-card h3 { font-size: 0.95rem; font-weight: 700; margin: 0; }
.nc-req { font-size: 0.78rem; color: #666; }

.stats-accesos {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
}

.sa-item {
    padding: 1rem;
    background: white;
    border: 1.5px solid #f0f0f0;
    border-radius: 12px;
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 600;
    color: #2D2D2D;
    text-align: center;
    transition: all 0.2s;
}

.sa-item:hover { border-color: #4ECDC4; color: #4ECDC4; transform: translateY(-2px); }

@media (max-width: 700px) {
    .niveles-grid  { grid-template-columns: repeat(2, 1fr); }
    .stats-accesos { grid-template-columns: repeat(2, 1fr); }
}
</style>