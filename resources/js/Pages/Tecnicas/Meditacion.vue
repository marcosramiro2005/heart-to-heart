<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onUnmounted } from 'vue'

const duracion   = ref(10)
const activo     = ref(false)
const fase       = ref('listo')
const segundos   = ref(0)
const faseNombre = ref('Prepárate')

const FASES_MEDITACION = [
    { nombre: 'Cierra los ojos',       duracion: 10, instruccion: 'Ponte cómodo/a y cierra suavemente los ojos' },
    { nombre: 'Respira',               duracion: 30, instruccion: 'Lleva la atención a tu respiración natural' },
    { nombre: 'Escanea tu cuerpo',     duracion: 60, instruccion: 'Recorre mentalmente tu cuerpo de pies a cabeza' },
    { nombre: 'Observa tus pensamientos', duracion: 60, instruccion: 'Deja que los pensamientos pasen como nubes' },
    { nombre: 'Vuelve al presente',    duracion: 30, instruccion: 'Regresa suavemente a tu respiración' },
    { nombre: 'Termina',               duracion: 10, instruccion: 'Abre los ojos lentamente cuando estés listo/a' },
]

let intervalo  = null
let faseIdx    = 0
let tiempoFase = 0

const instruccionActual = ref(FASES_MEDITACION[0].instruccion)

const iniciar = () => {
    activo.value    = true
    faseIdx         = 0
    tiempoFase      = 0
    segundos.value  = duracion.value * 60
    faseNombre.value = FASES_MEDITACION[0].nombre
    instruccionActual.value = FASES_MEDITACION[0].instruccion

    intervalo = setInterval(() => {
        segundos.value--
        tiempoFase++

        if (tiempoFase >= FASES_MEDITACION[faseIdx].duracion) {
            tiempoFase = 0
            faseIdx    = (faseIdx + 1) % FASES_MEDITACION.length
            faseNombre.value      = FASES_MEDITACION[faseIdx].nombre
            instruccionActual.value = FASES_MEDITACION[faseIdx].instruccion
        }

        if (segundos.value <= 0) {
            clearInterval(intervalo)
            activo.value = false
            fase.value   = 'completado'
        }
    }, 1000)
}

const detener = () => {
    clearInterval(intervalo)
    activo.value = false
    fase.value   = 'listo'
}

const tiempoFormato = () => {
    const m = Math.floor(segundos.value / 60)
    const s = segundos.value % 60
    return `${m}:${s.toString().padStart(2, '0')}`
}

onUnmounted(() => clearInterval(intervalo))
</script>

<template>
    <AppLayout>
        <div class="med-wrapper">

            <div class="med-header">
                <span>🧘</span>
                <h1>MEDITACIÓN GUIADA</h1>
            </div>

            <p class="subtitulo">Encuentra la calma en el momento presente</p>

            <!-- Círculo de meditación -->
            <div class="med-circulo" :class="{ activo }">
                <div class="circulo-pulso"></div>
                <div class="circulo-centro">
                    <span class="fase-nombre">{{ activo ? faseNombre : (fase === 'completado' ? '✓ Completado' : 'Pulsa para empezar') }}</span>
                    <span v-if="activo" class="fase-tiempo">{{ tiempoFormato() }}</span>
                </div>
            </div>

            <!-- Instrucción actual -->
            <div v-if="activo" class="instruccion-card">
                <p>{{ instruccionActual }}</p>
            </div>

            <!-- Duración -->
            <div class="duracion-control">
                <label>Duración: <strong>{{ duracion }} min</strong></label>
                <div class="dur-botones">
                    <button @click="duracion = Math.max(5, duracion - 5)" :disabled="activo">-5</button>
                    <span>{{ duracion }}</span>
                    <button @click="duracion = Math.min(60, duracion + 5)" :disabled="activo">+5</button>
                </div>
            </div>

            <button class="btn-med" @click="activo ? detener() : iniciar()" :class="{ activo }">
                {{ activo ? 'Detener' : '🧘 Comenzar meditación' }}
            </button>

            <div v-if="fase === 'completado'" class="msg-ok">
                🎉 ¡Meditación completada! Tu mente ha descansado.
            </div>

            <!-- Consejos -->
            <div class="consejos-med">
                <h3>Consejos para meditar mejor</h3>
                <ul>
                    <li>Busca un lugar tranquilo donde no te molesten</li>
                    <li>Siéntate con la espalda recta pero sin tensión</li>
                    <li>Si la mente se distrae, vuelve suavemente a la respiración</li>
                    <li>No hay meditación "mala". El esfuerzo ya es valioso</li>
                    <li>Empieza con 5-10 minutos y ve aumentando progresivamente</li>
                </ul>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.med-wrapper {
    max-width: 500px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.med-header {
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

.med-header h1 {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: 0.1em;
}

.subtitulo { color: #666; font-size: 0.95rem; margin: 0; }

/* ── Círculo ── */
.med-circulo {
    position: relative;
    width: 240px;
    height: 240px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.circulo-pulso {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #e8d5f5;
    transition: transform 3s ease-in-out;
}

.med-circulo.activo .circulo-pulso {
    animation: pulso-med 4s ease-in-out infinite;
}

@keyframes pulso-med {
    0%, 100% { transform: scale(0.85); background: #e8d5f5; }
    50%       { transform: scale(1.05); background: #d4b8f0; }
}

.circulo-centro {
    position: relative;
    width: 170px;
    height: 170px;
    border-radius: 50%;
    background: #9B8EC4;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    box-shadow: 0 6px 24px rgba(155,142,196,0.4);
}

.fase-nombre {
    font-size: 0.85rem;
    font-weight: 700;
    color: white;
    text-align: center;
    padding: 0 0.5rem;
}

.fase-tiempo {
    font-size: 1.3rem;
    font-weight: 800;
    color: white;
}

.instruccion-card {
    background: #f5f0ff;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    text-align: center;
    border-left: 4px solid #9B8EC4;
}

.instruccion-card p {
    font-size: 0.95rem;
    color: #5a4a7a;
    font-style: italic;
    margin: 0;
}

.duracion-control {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.duracion-control label { font-size: 0.95rem; font-weight: 600; color: #2D2D2D; }

.dur-botones {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.dur-botones button {
    padding: 0.3rem 0.8rem;
    border: 2px solid #9B8EC4;
    background: white;
    color: #9B8EC4;
    border-radius: 20px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}

.dur-botones button:hover:not(:disabled) { background: #9B8EC4; color: white; }
.dur-botones button:disabled { opacity: 0.4; cursor: not-allowed; }
.dur-botones span { font-weight: 700; }

.btn-med {
    padding: 0.9rem 2.5rem;
    background: #9B8EC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background 0.2s;
}

.btn-med:hover { background: #7a6da8; }
.btn-med.activo { background: #E63946; }

.msg-ok {
    background: #f5f0ff;
    border: 2px solid #9B8EC4;
    color: #7a6da8;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-align: center;
}

.consejos-med {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    width: 100%;
}

.consejos-med h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0 0 0.75rem;
}

.consejos-med ul {
    padding-left: 1.25rem;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.consejos-med li {
    font-size: 0.88rem;
    color: #555;
    line-height: 1.5;
}
</style>