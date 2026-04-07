<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    logros:        Array,
    puntosTotales: Number,
    nivel:         Object,
    totalUnlocked: Number,
    totalLogros:   Number,
})

const categorias = {
    racha:       { label: '🔥 Rachas',       color: '#FF8C42' },
    bienestar:   { label: '🫁 Bienestar',    color: '#4ECDC4' },
    social:      { label: '💬 Social',       color: '#6B9FD4' },
    explorador:  { label: '🌟 Explorador',   color: '#9B8EC4' },
}

const logrosPorCategoria = computed(() => {
    const grupos = {}
    for (const cat in categorias) {
        grupos[cat] = props.logros.filter(l => l.category === cat)
    }
    return grupos
})

const porcentajeCompletado = computed(() =>
    Math.round((props.totalUnlocked / props.totalLogros) * 100)
)
</script>

<template>
    <AppLayout>
        <div class="ach-wrapper">

            <!-- Hero con nivel -->
            <div class="ach-hero" :style="{ background: nivel.color }">
                <div class="nivel-info">
                    <span class="nivel-emoji">{{ nivel.emoji }}</span>
                    <div>
                        <h1>{{ nivel.nombre }}</h1>
                        <p>{{ puntosTotales }} puntos acumulados</p>
                    </div>
                </div>
                <div class="nivel-progreso">
                    <div class="progreso-texto">
                        <span>Próximo nivel: {{ nivel.siguiente }}</span>
                        <span>{{ nivel.progreso }}%</span>
                    </div>
                    <div class="progreso-barra">
                        <div
                            class="progreso-relleno"
                            :style="{ width: `${nivel.progreso}%` }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div class="resumen-grid">
                <div class="resumen-card">
                    <span class="resumen-valor">{{ totalUnlocked }}</span>
                    <span class="resumen-label">Logros desbloqueados</span>
                </div>
                <div class="resumen-card">
                    <span class="resumen-valor">{{ totalLogros - totalUnlocked }}</span>
                    <span class="resumen-label">Por descubrir</span>
                </div>
                <div class="resumen-card">
                    <span class="resumen-valor">{{ porcentajeCompletado }}%</span>
                    <span class="resumen-label">Completado</span>
                </div>
                <div class="resumen-card">
                    <span class="resumen-valor">{{ puntosTotales }}</span>
                    <span class="resumen-label">Puntos totales</span>
                </div>
            </div>

            <!-- Barra de progreso general -->
            <div class="progreso-general">
                <div class="pg-header">
                    <span>Progreso general</span>
                    <span>{{ totalUnlocked }}/{{ totalLogros }}</span>
                </div>
                <div class="pg-barra">
                    <div
                        class="pg-relleno"
                        :style="{ width: `${porcentajeCompletado}%` }"
                    ></div>
                </div>
            </div>

            <!-- Logros por categoría -->
            <div
                v-for="(cat, catKey) in categorias"
                :key="catKey"
                class="categoria-seccion"
            >
                <h2 class="cat-titulo">{{ cat.label }}</h2>
                <div class="logros-grid">
                    <div
                        v-for="logro in logrosPorCategoria[catKey]"
                        :key="logro.code"
                        class="logro-card"
                        :class="{ bloqueado: !logro.unlocked }"
                    >
                        <div
                            class="logro-icono"
                            :style="{
                                backgroundColor: logro.unlocked ? logro.color : '#f0f0f0'
                            }"
                        >
                            <span>{{ logro.unlocked ? logro.emoji : '🔒' }}</span>
                        </div>
                        <div class="logro-info">
                            <span class="logro-nombre">{{ logro.name }}</span>
                            <span class="logro-desc">{{ logro.description }}</span>
                            <span class="logro-puntos" :style="{ color: logro.unlocked ? logro.color : '#aaa' }">
                                +{{ logro.points }} pts
                            </span>
                        </div>
                        <div v-if="logro.unlocked" class="logro-check">✓</div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.ach-wrapper {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* ── Hero ── */
.ach-hero {
    border-radius: 16px;
    padding: 1.75rem 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.nivel-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.nivel-emoji { font-size: 3rem; }

.nivel-info h1 {
    font-size: 1.8rem;
    font-weight: 800;
    color: white;
    margin: 0 0 0.2rem;
}

.nivel-info p { color: rgba(255,255,255,0.85); margin: 0; font-size: 1rem; }

.nivel-progreso { display: flex; flex-direction: column; gap: 0.5rem; }

.progreso-texto {
    display: flex;
    justify-content: space-between;
    color: rgba(255,255,255,0.9);
    font-size: 0.85rem;
    font-weight: 600;
}

.progreso-barra {
    height: 10px;
    background: rgba(255,255,255,0.3);
    border-radius: 5px;
    overflow: hidden;
}

.progreso-relleno {
    height: 100%;
    background: white;
    border-radius: 5px;
    transition: width 0.8s ease;
}

/* ── Resumen ── */
.resumen-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.resumen-card {
    background: white;
    border-radius: 14px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
    border: 1px solid #f0f0f0;
    text-align: center;
}

.resumen-valor {
    font-size: 1.8rem;
    font-weight: 800;
    color: #4ECDC4;
}

.resumen-label { font-size: 0.78rem; color: #888; }

/* ── Progreso general ── */
.progreso-general {
    background: white;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.pg-header {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    font-weight: 600;
    color: #2D2D2D;
}

.pg-barra {
    height: 12px;
    background: #f0f0f0;
    border-radius: 6px;
    overflow: hidden;
}

.pg-relleno {
    height: 100%;
    background: linear-gradient(90deg, #4ECDC4, #3BAFA7);
    border-radius: 6px;
    transition: width 0.8s ease;
}

/* ── Categorías ── */
.cat-titulo {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0 0 0.75rem;
}

.logros-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.logro-card {
    background: white;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid #f0f0f0;
    transition: box-shadow 0.2s, transform 0.2s;
    position: relative;
}

.logro-card:not(.bloqueado):hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.logro-card.bloqueado {
    opacity: 0.55;
}

.logro-icono {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    flex-shrink: 0;
}

.logro-info {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    flex: 1;
}

.logro-nombre {
    font-size: 0.92rem;
    font-weight: 700;
    color: #2D2D2D;
}

.logro-desc {
    font-size: 0.78rem;
    color: #888;
    line-height: 1.4;
}

.logro-puntos {
    font-size: 0.78rem;
    font-weight: 700;
}

.logro-check {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 20px;
    height: 20px;
    background: #4ECDC4;
    color: white;
    border-radius: 50%;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

/* ── Responsive ── */
@media (max-width: 600px) {
    .resumen-grid { grid-template-columns: repeat(2, 1fr); }
    .logros-grid  { grid-template-columns: 1fr; }
}
</style>