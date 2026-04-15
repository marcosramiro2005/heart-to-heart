<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const paso = ref(0)
const activo = ref(false)

const pasos = [
    { zona: 'Punto de kárate', emoji: '✋', descripcion: 'Golpea el lado de la mano (entre el meñique y la muñeca) con los dedos de la otra mano.', rondas: 8 },
    { zona: 'Parte superior de la cabeza', emoji: '👆', descripcion: 'Con los dedos índice y medio, da pequeños golpecitos en la coronilla.', rondas: 7 },
    { zona: 'Ceja interna', emoji: '👁️', descripcion: 'Golpea el inicio de la ceja, cerca del puente de la nariz.', rondas: 7 },
    { zona: 'Rabillo del ojo', emoji: '👁️', descripcion: 'Golpea el hueso del extremo externo del ojo.', rondas: 7 },
    { zona: 'Bajo el ojo', emoji: '😌', descripcion: 'Golpea el hueso bajo el centro del ojo.', rondas: 7 },
    { zona: 'Bajo la nariz', emoji: '👃', descripcion: 'Golpea el espacio entre la nariz y el labio superior.', rondas: 7 },
    { zona: 'Barbilla', emoji: '🫦', descripcion: 'Golpea la zona entre el labio inferior y la barbilla.', rondas: 7 },
    { zona: 'Clavícula', emoji: '🫀', descripcion: 'Golpea justo debajo del extremo de la clavícula.', rondas: 7 },
    { zona: 'Bajo el brazo', emoji: '💪', descripcion: 'Golpea el lateral del torso, a la altura de la axila.', rondas: 7 },
]

const siguiente = () => {
    if (paso.value < pasos.length - 1) paso.value++
    else { activo.value = false; paso.value = 0 }
}
</script>

<template>
    <AppLayout>
        <div class="tap-wrapper">
            <div class="tap-header">
                <span>👆</span>
                <h1>EFT TAPPING</h1>
            </div>

            <p class="subtitulo">Técnica de acupresión emocional para reducir el estrés y la ansiedad</p>

            <div class="tap-info">
                <h3>¿Qué es el EFT Tapping?</h3>
                <p>El EFT (Emotional Freedom Techniques) combina acupresión y psicología. Consiste en dar pequeños golpecitos en puntos de meridianos de energía mientras te concentras en el problema emocional que quieres trabajar. Está respaldado por estudios científicos como herramienta para reducir el cortisol y la ansiedad.</p>
            </div>

            <div v-if="!activo" class="tap-inicio">
                <p>Antes de empezar, piensa en algo que te cause estrés o ansiedad. Dale una puntuación del 1 al 10 en cuanto a intensidad. Después del tapping, vuelve a puntuarlo.</p>
                <button class="btn-tap" @click="activo = true; paso = 0">
                    👆 Comenzar secuencia de tapping
                </button>
            </div>

            <div v-else class="tap-sesion">
                <div class="tap-progreso">
                    <span class="tap-paso-num">Paso {{ paso + 1 }} de {{ pasos.length }}</span>
                    <div class="tap-barra">
                        <div class="tap-relleno" :style="{ width: `${((paso + 1) / pasos.length) * 100}%` }"></div>
                    </div>
                </div>

                <div class="tap-card">
                    <div class="tap-emoji">{{ pasos[paso].emoji }}</div>
                    <h3>{{ pasos[paso].zona }}</h3>
                    <p>{{ pasos[paso].descripcion }}</p>
                    <p class="tap-rondas">Repite {{ pasos[paso].rondas }} veces mientras dices en voz alta o mentalmente cómo te sientes.</p>
                </div>

                <div class="tap-botones">
                    <button v-if="paso > 0" class="btn-tap-sec" @click="paso--">← Anterior</button>
                    <button class="btn-tap" @click="siguiente">
                        {{ paso < pasos.length - 1 ? 'Siguiente punto →' : '✓ Terminar' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.tap-wrapper {
    max-width: 580px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.tap-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #ffecd2;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.tap-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }

.subtitulo { color: #666; font-size: 0.95rem; text-align: center; }

.tap-info {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    width: 100%;
}

.tap-info h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.5rem; }
.tap-info p  { font-size: 0.88rem; color: #555; line-height: 1.6; margin: 0; }

.tap-inicio {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    text-align: center;
}

.tap-inicio p { font-size: 0.92rem; color: #555; line-height: 1.6; }

.btn-tap {
    padding: 0.85rem 2rem;
    background: #FF8C42;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background 0.2s;
}

.btn-tap:hover { background: #e07030; }

.tap-sesion {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.tap-progreso { display: flex; flex-direction: column; gap: 0.5rem; }
.tap-paso-num { font-size: 0.82rem; color: #888; font-weight: 600; }

.tap-barra {
    height: 6px;
    background: #f0f0f0;
    border-radius: 3px;
    overflow: hidden;
}

.tap-relleno {
    height: 100%;
    background: #FF8C42;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.tap-card {
    background: #fff8f3;
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    border: 2px solid #ffecd2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.tap-emoji { font-size: 3rem; }
.tap-card h3 { font-size: 1.1rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.tap-card p  { font-size: 0.92rem; color: #555; line-height: 1.6; margin: 0; }
.tap-rondas  { font-size: 0.85rem; color: #FF8C42; font-weight: 600; font-style: italic; }

.tap-botones { display: flex; gap: 0.75rem; justify-content: center; }

.btn-tap-sec {
    padding: 0.85rem 1.5rem;
    background: white;
    color: #FF8C42;
    font-weight: 700;
    border: 2px solid #FF8C42;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-tap-sec:hover { background: #FF8C42; color: white; }
</style>