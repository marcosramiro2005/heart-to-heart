<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onUnmounted } from 'vue'
import axios from 'axios'

const duracion = ref(5)
const activo = ref(false)
const fase = ref('listo')
const progreso = ref(0)
const tiempoRestante = ref(0)

let intervalo = null
let tiempoInicio = null

const FASES = [
    { nombre: 'INSPIRA', duracion: 4, color: '#4ECDC4' },
    { nombre: 'MANTÉN', duracion: 4, color: '#45B7B0' },
    { nombre: 'ESPIRA', duracion: 6, color: '#3BAFA7' },
]

const faseActual = ref(0)
const segundosFase = ref(0)

const textoFase = computed(() => {
    if (!activo.value) return fase.value === 'listo' ? 'PULSA PARA EMPEZAR' : 'COMPLETADO ✓'
    return FASES[faseActual.value].nombre
})

const colorCirculo = computed(() => {
    if (!activo.value) return '#4ECDC4'
    return FASES[faseActual.value].color
})

const escalaCirculo = computed(() => {
    if (!activo.value) return 1
    const f = FASES[faseActual.value]
    if (f.nombre === 'INSPIRA') {
        return 0.7 + (segundosFase.value / f.duracion) * 0.5
    } else if (f.nombre === 'MANTÉN') {
        return 1.2
    } else {
        return 1.2 - (segundosFase.value / f.duracion) * 0.5
    }
})

const iniciar = () => {
    if (activo.value) {
        detener()
        return
    }

    activo.value = true
    fase.value = 'activo'
    faseActual.value = 0
    segundosFase.value = 0
    tiempoRestante.value = duracion.value * 60

    intervalo = setInterval(() => {
        tiempoRestante.value--
        segundosFase.value++

        if (segundosFase.value >= FASES[faseActual.value].duracion) {
            segundosFase.value = 0
            faseActual.value = (faseActual.value + 1) % FASES.length
        }

        if (tiempoRestante.value <= 0) {
            completar()
        }
    }, 1000)
}

const detener = () => {
    clearInterval(intervalo)
    activo.value = false
    fase.value = 'listo'
    faseActual.value = 0
    segundosFase.value = 0
    progreso.value = 0
}

const completar = () => {
    clearInterval(intervalo)
    activo.value = false
    fase.value = 'completado'

    // Guardar sesión en la base de datos
    axios.post('/api/breathing-sessions', {
        technique: 'respiracion',
        duration_minutes: duracion.value
    })
}

const tiempoFormato = computed(() => {
    const m = Math.floor(tiempoRestante.value / 60)
    const s = tiempoRestante.value % 60
    return `${m}:${s.toString().padStart(2, '0')}`
})

onUnmounted(() => {
    clearInterval(intervalo)
})
</script>

<template>
    <AppLayout>
        <div class="resp-wrapper">

            <!-- Cabecera -->
            <div class="resp-header">
                <span class="resp-icon">🫁</span>
                <h1>RESPIRACIÓN</h1>
            </div>

            <p class="resp-subtitulo">Mantén la calma</p>

            <!-- Círculo animado -->
            <div class="circulo-contenedor" @click="iniciar">
                <div
                    class="circulo-exterior"
                    :style="{
                        transform: `scale(${escalaCirculo * 1.25})`,
                        backgroundColor: colorCirculo + '40',
                        transition: activo ? 'transform 1s ease-in-out, background-color 1s' : 'none'
                    }"
                ></div>
                <div
                    class="circulo-principal"
                    :style="{
                        transform: `scale(${escalaCirculo})`,
                        backgroundColor: colorCirculo,
                        transition: activo ? 'transform 1s ease-in-out, background-color 1s' : 'none'
                    }"
                >
                    <span class="circulo-texto">{{ textoFase }}</span>
                    <span v-if="activo" class="circulo-tiempo">{{ tiempoFormato }}</span>
                </div>
            </div>

            <!-- Indicador de fase -->
            <div v-if="activo" class="fases-indicador">
                <div
                    v-for="(f, i) in FASES"
                    :key="i"
                    class="fase-punto"
                    :class="{ activa: faseActual === i }"
                ></div>
            </div>

            <!-- Control de duración -->
            <div class="duracion-control">
                <span class="duracion-label">Duración: {{ duracion }} min</span>
                <div class="duracion-botones">
                    <button @click="duracion = Math.max(1, duracion - 1)" :disabled="activo">−</button>
                    <span>{{ duracion }}</span>
                    <button @click="duracion = Math.min(30, duracion + 1)" :disabled="activo">+</button>
                </div>
            </div>

            <!-- Botón principal -->
            <button class="btn-iniciar" @click="iniciar" :class="{ activo: activo }">
                {{ activo ? 'DETENER EJERCICIO' : 'INICIAR EJERCICIO' }}
            </button>

            <!-- Mensaje completado -->
            <div v-if="fase === 'completado'" class="msg-completado">
                🎉 ¡Sesión completada! Tu mente agradece el descanso.
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.resp-wrapper {
    max-width: 500px;
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
}

.resp-icon { font-size: 1.8rem; }

.resp-header h1 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
    letter-spacing: 0.1em;
}

.resp-subtitulo {
    font-size: 1rem;
    color: #666;
    margin: 0;
}

/* ── Círculo ── */
.circulo-contenedor {
    position: relative;
    width: 260px;
    height: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.circulo-exterior {
    position: absolute;
    width: 200px;
    height: 200px;
    border-radius: 50%;
}

.circulo-principal {
    position: relative;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    box-shadow: 0 8px 32px rgba(78,205,196,0.3);
}

.circulo-texto {
    font-size: 0.9rem;
    font-weight: 700;
    color: white;
    letter-spacing: 0.05em;
    text-align: center;
    padding: 0 0.5rem;
}

.circulo-tiempo {
    font-size: 1.2rem;
    font-weight: 800;
    color: white;
}

/* ── Fases indicador ── */
.fases-indicador {
    display: flex;
    gap: 0.5rem;
}

.fase-punto {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #ccc;
    transition: background 0.3s;
}

.fase-punto.activa {
    background: #4ECDC4;
}

/* ── Duración ── */
.duracion-control {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.duracion-label {
    font-size: 0.95rem;
    color: #2D2D2D;
    font-weight: 600;
}

.duracion-botones {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.duracion-botones button {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 2px solid #4ECDC4;
    background: white;
    color: #4ECDC4;
    font-size: 1.2rem;
    font-weight: 700;
    cursor: pointer;
    line-height: 1;
    transition: background 0.2s;
}

.duracion-botones button:hover:not(:disabled) {
    background: #4ECDC4;
    color: white;
}

.duracion-botones button:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.duracion-botones span {
    font-weight: 700;
    font-size: 1.1rem;
    min-width: 24px;
    text-align: center;
}

/* ── Botón iniciar ── */
.btn-iniciar {
    padding: 0.9rem 2.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 0.95rem;
    letter-spacing: 0.05em;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
}

.btn-iniciar:hover { background: #3BAFA7; }
.btn-iniciar.activo { background: #E63946; }
.btn-iniciar.activo:hover { background: #c0303b; }

/* ── Completado ── */
.msg-completado {
    background: #E8FAF9;
    border: 2px solid #4ECDC4;
    color: #3BAFA7;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-align: center;
}
</style>