<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const paso       = ref(0)
const activo     = ref(false)
const completado = ref(false)
const respuestas = ref(['', '', '', '', ''])

const pasos = [
    {
        numero: 5, sentido: 'Vista', emoji: '👁️', color: '#d0eaf8',
        instruccion: 'Nombra 5 cosas que puedes VER ahora mismo a tu alrededor.',
        placeholder: 'Ej: una silla, la ventana, mis manos, una lámpara, el techo...'
    },
    {
        numero: 4, sentido: 'Tacto', emoji: '✋', color: '#d4edda',
        instruccion: 'Nombra 4 cosas que puedes TOCAR o sentir físicamente ahora.',
        placeholder: 'Ej: la ropa en mi piel, el suelo bajo mis pies, la silla...'
    },
    {
        numero: 3, sentido: 'Oído', emoji: '👂', color: '#fff9c4',
        instruccion: 'Nombra 3 sonidos que puedes ESCUCHAR en este momento.',
        placeholder: 'Ej: el tráfico, mi respiración, el viento, el teclado...'
    },
    {
        numero: 2, sentido: 'Olfato', emoji: '👃', color: '#ffecd2',
        instruccion: 'Nombra 2 cosas que puedes OLER ahora mismo.',
        placeholder: 'Ej: el ambiente de la habitación, mi ropa, comida...'
    },
    {
        numero: 1, sentido: 'Sabor', emoji: '👄', color: '#ffd5e5',
        instruccion: 'Nombra 1 cosa que puedes SABOREAR en este momento.',
        placeholder: 'Ej: el sabor de mi boca, un chicle, agua...'
    },
]

const iniciar = () => {
    activo.value     = true
    completado.value = false
    paso.value       = 0
    respuestas.value = ['', '', '', '', '']
}

const siguiente = () => {
    if (paso.value < pasos.length - 1) {
        paso.value++
    } else {
        activo.value     = false
        completado.value = true
    }
}
</script>

<template>
    <AppLayout>
        <div class="gr-wrapper">

            <div class="gr-header">
                <span>🌍</span>
                <h1>TÉCNICA 5-4-3-2-1</h1>
            </div>

            <p class="subtitulo">Grounding — Ancla tu mente al presente para detener la ansiedad</p>

            <div class="gr-explicacion">
                <h3>¿Qué es el grounding?</h3>
                <p>Esta técnica usa los 5 sentidos para interrumpir el ciclo de pensamientos ansiosos y traer tu mente al momento presente. Es especialmente efectiva durante ataques de pánico o momentos de ansiedad intensa. Solo tarda unos minutos y puedes hacerla en cualquier lugar.</p>
            </div>

            <div v-if="!activo && !completado" class="gr-inicio">
                <div class="gr-pasos-preview">
                    <div
                        v-for="p in pasos"
                        :key="p.numero"
                        class="gpp-item"
                        :style="{ borderLeft: `4px solid ${p.color}`, backgroundColor: p.color + '80' }"
                    >
                        <span class="gpp-num">{{ p.numero }}</span>
                        <span class="gpp-emoji">{{ p.emoji }}</span>
                        <span class="gpp-nombre">{{ p.sentido }}</span>
                    </div>
                </div>
                <p class="gr-consejo">Busca un lugar tranquilo. Respira profundo antes de empezar.</p>
                <button class="btn-gr" @click="iniciar">🌍 Empezar grounding</button>
            </div>

            <div v-if="activo" class="gr-sesion">
                <div class="gr-progreso">
                    <div
                        v-for="(p, i) in pasos"
                        :key="i"
                        class="gp-punto"
                        :class="{ activo: i === paso, completado: i < paso }"
                    >
                        {{ p.numero }}
                    </div>
                </div>

                <div class="gr-card" :style="{ backgroundColor: pasos[paso].color }">
                    <div class="gc-numero">{{ pasos[paso].numero }}</div>
                    <span class="gc-emoji">{{ pasos[paso].emoji }}</span>
                    <h2>{{ pasos[paso].sentido }}</h2>
                    <p>{{ pasos[paso].instruccion }}</p>
                    <textarea
                        v-model="respuestas[paso]"
                        :placeholder="pasos[paso].placeholder"
                        rows="3"
                        class="gc-textarea"
                    ></textarea>
                </div>

                <div class="gr-botones">
                    <button v-if="paso > 0" class="btn-prev" @click="paso--">← Anterior</button>
                    <button class="btn-gr" @click="siguiente">
                        {{ paso < pasos.length - 1 ? 'Siguiente sentido →' : '✓ Terminar' }}
                    </button>
                </div>
            </div>

            <div v-if="completado" class="gr-completado">
                <span class="comp-emoji">🌟</span>
                <h2>¡Técnica completada!</h2>
                <p>Has recorrido los 5 sentidos y traído tu mente al momento presente. ¿Cómo te sientes ahora comparado con antes?</p>
                <div class="gr-resumen">
                    <div v-for="(p, i) in pasos" :key="i" class="gr-res-item">
                        <span class="gr-res-sentido">{{ p.emoji }} {{ p.sentido }}:</span>
                        <span class="gr-res-texto">{{ respuestas[i] || '(sin completar)' }}</span>
                    </div>
                </div>
                <button class="btn-gr" @click="iniciar">🔄 Repetir</button>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.gr-wrapper {
    max-width: 580px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.gr-header {
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

.gr-header h1 { font-size: 1.1rem; font-weight: 700; margin: 0; letter-spacing: 0.08em; }
.subtitulo { color: #666; font-size: 0.92rem; text-align: center; }

.gr-explicacion {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    width: 100%;
    border-left: 4px solid #4ECDC4;
}

.gr-explicacion h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.5rem; }
.gr-explicacion p  { font-size: 0.88rem; color: #555; line-height: 1.6; margin: 0; }

.gr-inicio { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }

.gr-pasos-preview { width: 100%; display: flex; flex-direction: column; gap: 0.5rem; }

.gpp-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 1rem;
    border-radius: 8px;
}

.gpp-num   { font-size: 1.3rem; font-weight: 800; color: #4ECDC4; min-width: 28px; }
.gpp-emoji { font-size: 1.2rem; }
.gpp-nombre { font-size: 0.9rem; font-weight: 600; color: #2D2D2D; }

.gr-consejo { font-size: 0.88rem; color: #666; text-align: center; }

.btn-gr {
    padding: 0.85rem 2rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background 0.2s, transform 0.2s;
}

.btn-gr:hover { background: #3BAFA7; transform: translateY(-2px); }

.gr-sesion { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }

.gr-progreso { display: flex; gap: 0.5rem; }

.gp-punto {
    width: 36px;
    height: 36px;
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

.gp-punto.activo    { background: #4ECDC4; color: white; transform: scale(1.1); }
.gp-punto.completado { background: #3BAFA7; color: white; }

.gr-card {
    width: 100%;
    border-radius: 18px;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    text-align: center;
    transition: background-color 0.4s ease;
}

.gc-numero {
    font-size: 3rem;
    font-weight: 900;
    color: rgba(0,0,0,0.12);
    line-height: 1;
}

.gc-emoji { font-size: 2.5rem; margin-top: -0.5rem; }

.gr-card h2 { font-size: 1.3rem; font-weight: 800; color: #2D2D2D; margin: 0; }
.gr-card p  { font-size: 0.95rem; color: #444; line-height: 1.6; margin: 0; }

.gc-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid rgba(255,255,255,0.7);
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    background: rgba(255,255,255,0.75);
    transition: border-color 0.2s, background 0.2s;
}

.gc-textarea:focus { border-color: #4ECDC4; background: white; }

.gr-botones { display: flex; gap: 0.75rem; }

.btn-prev {
    padding: 0.85rem 1.5rem;
    background: white;
    border: 2px solid #4ECDC4;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    color: #3BAFA7;
    transition: all 0.2s;
}

.btn-prev:hover { background: #4ECDC4; color: white; }

.gr-completado {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    width: 100%;
}

.comp-emoji { font-size: 3.5rem; }
.gr-completado h2 { font-size: 1.3rem; font-weight: 800; color: #2D2D2D; margin: 0; }
.gr-completado p  { font-size: 0.92rem; color: #555; line-height: 1.6; margin: 0; }

.gr-resumen {
    width: 100%;
    background: #fafafa;
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    text-align: left;
}

.gr-res-item {
    display: flex;
    gap: 0.5rem;
    font-size: 0.88rem;
    padding: 0.4rem 0;
    border-bottom: 1px solid #f0f0f0;
    flex-wrap: wrap;
}

.gr-res-sentido { font-weight: 700; color: #4ECDC4; white-space: nowrap; }
.gr-res-texto   { color: #555; }
</style>