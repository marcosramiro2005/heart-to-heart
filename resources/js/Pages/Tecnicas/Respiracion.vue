<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onUnmounted } from 'vue'

const tecnicas = [
    {
        id: '4-7-8',
        nombre: 'Respiración 4-7-8',
        emoji: '🫁',
        color: '#d4edda',
        colorAcento: '#2d8a4e',
        descripcion: 'Desarrollada por el Dr. Andrew Weil. Activa el sistema nervioso parasimpático en minutos.',
        beneficio: 'Ansiedad aguda · Insomnio · Estrés',
        ciclos: 4,
        fases: [
            { nombre: 'INHALA',  duracion: 4, color: '#4ECDC4', instruccion: 'Llena los pulmones lentamente' },
            { nombre: 'MANTÉN',  duracion: 7, color: '#9B8EC4', instruccion: 'Retén el aire con calma' },
            { nombre: 'EXHALA',  duracion: 8, color: '#6B9FD4', instruccion: 'Suelta todo el aire por la boca' },
        ]
    },
    {
        id: 'caja',
        nombre: 'Respiración de caja',
        emoji: '⬜',
        color: '#d0eaf8',
        colorAcento: '#3B8BD4',
        descripcion: 'Usada por los Navy SEALs para mantener la calma en situaciones de alto estrés.',
        beneficio: 'Concentración · Control emocional · Estrés',
        ciclos: 5,
        fases: [
            { nombre: 'INHALA',  duracion: 4, color: '#4ECDC4', instruccion: 'Inhala profundamente' },
            { nombre: 'MANTÉN',  duracion: 4, color: '#9B8EC4', instruccion: 'Mantén el aire' },
            { nombre: 'EXHALA',  duracion: 4, color: '#6B9FD4', instruccion: 'Exhala completamente' },
            { nombre: 'PAUSA',   duracion: 4, color: '#FF8C42', instruccion: 'Espera antes de inhalar' },
        ]
    },
    {
        id: 'coherente',
        nombre: 'Respiración coherente',
        emoji: '💫',
        color: '#e8d5f5',
        colorAcento: '#7a4da8',
        descripcion: 'Sincroniza el ritmo cardíaco y reduce el cortisol. 5 respiraciones por minuto.',
        beneficio: 'Equilibrio · Claridad mental · Bienestar general',
        ciclos: 6,
        fases: [
            { nombre: 'INHALA', duracion: 6, color: '#4ECDC4', instruccion: 'Inhala suave y continuo' },
            { nombre: 'EXHALA', duracion: 6, color: '#9B8EC4', instruccion: 'Exhala suave y continuo' },
        ]
    },
]

const tecnicaActiva  = ref(null)
const activo         = ref(false)
const completado     = ref(false)
const faseIdx        = ref(0)
const cicloActual    = ref(0)
const cuenta         = ref(0)
const progresoPct    = ref(0)
let intervalo        = null

const faseActual = computed(() =>
    tecnicaActiva.value?.fases[faseIdx.value] ?? null
)

const escalaCirculo = computed(() => {
    if (!faseActual.value) return 1
    if (faseActual.value.nombre === 'INHALA') return 1 + (1 - cuenta.value / faseActual.value.duracion) * 0.35
    if (faseActual.value.nombre === 'EXHALA') return 1 + (cuenta.value / faseActual.value.duracion) * 0.35
    return 1.3
})

const iniciar = (tec) => {
    tecnicaActiva.value = tec
    activo.value        = true
    completado.value    = false
    faseIdx.value       = 0
    cicloActual.value   = 0
    cuenta.value        = tec.fases[0].duracion
    progresoPct.value   = 0
    clearInterval(intervalo)
    intervalo = setInterval(tick, 1000)
}

const tick = () => {
    cuenta.value--

    // Actualizar progreso total
    const totalSegundos = tecnicaActiva.value.ciclos *
        tecnicaActiva.value.fases.reduce((s, f) => s + f.duracion, 0)
    const segPorCiclo   = tecnicaActiva.value.fases.reduce((s, f) => s + f.duracion, 0)
    const segCompletados = cicloActual.value * segPorCiclo +
        tecnicaActiva.value.fases.slice(0, faseIdx.value).reduce((s, f) => s + f.duracion, 0) +
        (tecnicaActiva.value.fases[faseIdx.value].duracion - cuenta.value)
    progresoPct.value = Math.round((segCompletados / totalSegundos) * 100)

    if (cuenta.value <= 0) {
        const siguienteFase = faseIdx.value + 1
        if (siguienteFase < tecnicaActiva.value.fases.length) {
            faseIdx.value = siguienteFase
            cuenta.value  = tecnicaActiva.value.fases[siguienteFase].duracion
        } else {
            cicloActual.value++
            if (cicloActual.value >= tecnicaActiva.value.ciclos) {
                clearInterval(intervalo)
                activo.value      = false
                completado.value  = true
                progresoPct.value = 100
            } else {
                faseIdx.value = 0
                cuenta.value  = tecnicaActiva.value.fases[0].duracion
            }
        }
    }
}

const detener = () => {
    clearInterval(intervalo)
    activo.value        = false
    tecnicaActiva.value = null
    completado.value    = false
}

const totalCiclos = computed(() => tecnicaActiva.value?.ciclos ?? 0)

onUnmounted(() => clearInterval(intervalo))
</script>

<template>
    <AppLayout>
        <div class="resp-wrapper">

            <div class="resp-header">
                <span>🫁</span>
                <h1>RESPIRACIÓN GUIADA</h1>
            </div>

            <p class="subtitulo">Técnicas de respiración con animación guiada para calmar cuerpo y mente</p>

            <!-- Selector de técnica -->
            <div v-if="!activo && !completado" class="tecnicas-selector">
                <div
                    v-for="tec in tecnicas"
                    :key="tec.id"
                    class="tec-card"
                    :style="{ backgroundColor: tec.color }"
                    @click="iniciar(tec)"
                >
                    <div class="tc-top">
                        <span class="tc-emoji">{{ tec.emoji }}</span>
                        <span class="tc-ciclos">{{ tec.ciclos }} ciclos</span>
                    </div>
                    <h3>{{ tec.nombre }}</h3>
                    <p>{{ tec.descripcion }}</p>
                    <div class="tc-beneficio">
                        <span>💡 {{ tec.beneficio }}</span>
                    </div>
                    <div class="tc-fases">
                        <div v-for="fase in tec.fases" :key="fase.nombre" class="tcf-item">
                            <span class="tcf-dot" :style="{ backgroundColor: fase.color }"></span>
                            <span>{{ fase.nombre }} {{ fase.duracion }}s</span>
                        </div>
                    </div>
                    <button class="btn-iniciar-resp" :style="{ backgroundColor: tec.colorAcento }">
                        ▶ Comenzar
                    </button>
                </div>
            </div>

            <!-- Sesión activa -->
            <div v-if="activo" class="resp-sesion">

                <h2 class="rs-titulo">{{ tecnicaActiva.nombre }}</h2>

                <!-- Círculo animado -->
                <div class="circulo-wrapper">
                    <div
                        class="circulo-exterior"
                        :style="{
                            borderColor: faseActual?.color,
                            boxShadow: `0 0 40px ${faseActual?.color}60`,
                        }"
                    ></div>
                    <div
                        class="circulo-principal"
                        :style="{
                            backgroundColor: faseActual?.color + '30',
                            transform: `scale(${escalaCirculo})`,
                            transition: `transform ${faseActual?.duracion ?? 1}s ease-in-out`,
                            borderColor: faseActual?.color,
                        }"
                    >
                        <div class="circulo-inner">
                            <span class="ci-fase" :style="{ color: faseActual?.color }">
                                {{ faseActual?.nombre }}
                            </span>
                            <span class="ci-cuenta">{{ cuenta }}</span>
                            <span class="ci-instruccion">{{ faseActual?.instruccion }}</span>
                        </div>
                    </div>
                </div>

                <!-- Progreso ciclos -->
                <div class="ciclos-progreso">
                    <div v-for="c in totalCiclos" :key="c" class="cp-item"
                        :class="{ completado: c - 1 < cicloActual, activo: c - 1 === cicloActual }">
                        {{ c }}
                    </div>
                </div>

                <!-- Barra progreso total -->
                <div class="progreso-total">
                    <div class="pt-barra">
                        <div class="pt-relleno"
                            :style="{ width: progresoPct + '%', backgroundColor: faseActual?.color }">
                        </div>
                    </div>
                    <span>{{ progresoPct }}%</span>
                </div>

                <!-- Fases del ciclo actual -->
                <div class="fases-indicador">
                    <div
                        v-for="(fase, i) in tecnicaActiva.fases"
                        :key="i"
                        class="fi-item"
                        :class="{ activa: i === faseIdx }"
                        :style="i === faseIdx ? { backgroundColor: fase.color, color: 'white' } : {}"
                    >
                        {{ fase.nombre }}
                    </div>
                </div>

                <button class="btn-detener-resp" @click="detener">⏹ Detener</button>
            </div>

            <!-- Completado -->
            <div v-if="completado" class="resp-completado">
                <span class="rc-emoji">🎉</span>
                <h2>¡Sesión completada!</h2>
                <p>Has completado {{ tecnicaActiva.ciclos }} ciclos de <strong>{{ tecnicaActiva.nombre }}</strong>.</p>
                <p class="rc-beneficio">Tu sistema nervioso se ha calmado. Los efectos duran entre 20 y 40 minutos.</p>
                <div class="rc-bots">
                    <button class="btn-rep-resp" @click="iniciar(tecnicaActiva)">🔄 Repetir</button>
                    <button class="btn-otra-resp" @click="completado = false; tecnicaActiva = null">
                        Otras técnicas
                    </button>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.resp-wrapper {
    max-width: 680px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.resp-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #d4edda;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.resp-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }
.subtitulo { color: #666; font-size: 0.95rem; text-align: center; margin: 0; }

/* ── Selector ── */
.tecnicas-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    width: 100%;
}

.tec-card {
    border-radius: 18px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.2s, box-shadow 0.2s;
}

.tec-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

.tc-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.tc-emoji  { font-size: 1.6rem; }
.tc-ciclos { font-size: 0.72rem; color: #888; font-weight: 600; }

.tec-card h3 { font-size: 0.88rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.tec-card p  { font-size: 0.78rem; color: #555; line-height: 1.4; margin: 0; flex: 1; }

.tc-beneficio {
    background: rgba(255,255,255,0.6);
    border-radius: 8px;
    padding: 0.3rem 0.6rem;
    font-size: 0.72rem;
    color: #444;
}

.tc-fases { display: flex; flex-direction: column; gap: 0.25rem; }

.tcf-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.72rem;
    color: #666;
}

.tcf-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.btn-iniciar-resp {
    padding: 0.6rem;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: opacity 0.2s;
    font-family: inherit;
}

.btn-iniciar-resp:hover { opacity: 0.88; }

/* ── Sesión ── */
.resp-sesion {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    width: 100%;
}

.rs-titulo { font-size: 1.1rem; font-weight: 700; color: #2D2D2D; margin: 0; }

/* ── Círculo animado ── */
.circulo-wrapper {
    position: relative;
    width: 260px;
    height: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.circulo-exterior {
    position: absolute;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    border: 2px solid;
    opacity: 0.3;
    transition: border-color 1s ease;
}

.circulo-principal {
    width: 210px;
    height: 210px;
    border-radius: 50%;
    border: 3px solid;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 1s ease, border-color 1s ease;
}

.circulo-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    text-align: center;
    padding: 1rem;
}

.ci-fase {
    font-size: 0.8rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    transition: color 1s ease;
}

.ci-cuenta {
    font-size: 3.5rem;
    font-weight: 900;
    color: #2D2D2D;
    line-height: 1;
}

.ci-instruccion {
    font-size: 0.72rem;
    color: #666;
    font-style: italic;
    max-width: 140px;
    line-height: 1.3;
}

/* ── Ciclos ── */
.ciclos-progreso { display: flex; gap: 0.5rem; }

.cp-item {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 700;
    color: #aaa;
    transition: all 0.3s;
}

.cp-item.completado { background: #3BAFA7; color: white; }
.cp-item.activo     { background: #4ECDC4; color: white; transform: scale(1.1); }

/* ── Progreso total ── */
.progreso-total {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.pt-barra {
    flex: 1;
    height: 6px;
    background: #f0f0f0;
    border-radius: 3px;
    overflow: hidden;
}

.pt-relleno {
    height: 100%;
    border-radius: 3px;
    transition: width 1s ease;
}

.progreso-total span { font-size: 0.78rem; color: #aaa; font-weight: 600; }

/* ── Fases indicador ── */
.fases-indicador { display: flex; gap: 0.4rem; flex-wrap: wrap; justify-content: center; }

.fi-item {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    background: #f0f0f0;
    color: #aaa;
    transition: all 0.3s;
}

.fi-item.activa { transform: scale(1.05); }

.btn-detener-resp {
    padding: 0.7rem 1.5rem;
    background: #f5f5f5;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    font-family: inherit;
    transition: background 0.2s;
}

.btn-detener-resp:hover { background: #e0e0e0; }

/* ── Completado ── */
.resp-completado {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.rc-emoji { font-size: 3.5rem; }
.resp-completado h2 { font-size: 1.4rem; font-weight: 800; margin: 0; }
.resp-completado p  { font-size: 0.92rem; color: #555; line-height: 1.6; margin: 0; }
.rc-beneficio { background: #E8FAF9; border-radius: 10px; padding: 0.75rem 1rem; color: #3BAFA7; font-style: italic; }

.rc-bots { display: flex; gap: 0.75rem; }

.btn-rep-resp {
    padding: 0.85rem 1.75rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
}

.btn-rep-resp:hover { background: #3BAFA7; }

.btn-otra-resp {
    padding: 0.85rem 1.75rem;
    background: white;
    border: 2px solid #4ECDC4;
    border-radius: 25px;
    color: #3BAFA7;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
}

.btn-otra-resp:hover { background: #4ECDC4; color: white; }

@media (max-width: 600px) {
    .tecnicas-selector { grid-template-columns: 1fr; }
}
</style>