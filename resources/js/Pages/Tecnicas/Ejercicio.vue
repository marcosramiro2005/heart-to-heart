<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onUnmounted } from 'vue'

const pestana = ref('rutinas')

const rutinas = [
    {
        id: 'ansiedad',
        nombre: 'Calmar la ansiedad',
        emoji: '😰',
        color: '#d0eaf8',
        descripcion: 'Movimientos suaves que activan el sistema nervioso parasimpático y reducen el cortisol.',
        duracion: '10 min',
        nivel: 'Suave',
        ejercicios: [
            { nombre: 'Marcha en el sitio',       duracion: 60, descripcion: 'Camina en el sitio levantando las rodillas suavemente. Sincroniza con la respiración.' },
            { nombre: 'Rotación de hombros',      duracion: 45, descripcion: 'Rota los hombros hacia atrás 10 veces, luego hacia adelante 10 veces.' },
            { nombre: 'Estiramiento lateral',     duracion: 30, descripcion: 'Inclínate suavemente hacia cada lado con el brazo extendido.' },
            { nombre: 'Respiración con brazos',   duracion: 60, descripcion: 'Sube los brazos al inhalar, baja al exhalar. Lento y consciente.' },
            { nombre: 'Sacudida de manos',        duracion: 30, descripcion: 'Sacude las manos y muñecas para liberar tensión acumulada.' },
            { nombre: 'Balanceo suave',           duracion: 60, descripcion: 'De pie, balancéate de lado a lado suavemente como un árbol en la brisa.' },
        ]
    },
    {
        id: 'tristeza',
        nombre: 'Subir el ánimo',
        emoji: '😢',
        color: '#d4edda',
        descripcion: 'Ejercicios aeróbicos moderados que liberan endorfinas, serotonina y dopamina.',
        duracion: '15 min',
        nivel: 'Moderado',
        ejercicios: [
            { nombre: 'Saltos suaves',            duracion: 45, descripcion: 'Salta suavemente en el sitio. Los saltos activan las endorfinas rápidamente.' },
            { nombre: 'Sentadillas',              duracion: 60, descripcion: 'Baja lentamente, sube con fuerza. 12-15 repeticiones.' },
            { nombre: 'Flexiones de pared',       duracion: 45, descripcion: 'Apóyate en la pared y haz flexiones. Activa el torso y los brazos.' },
            { nombre: 'Desplazamientos laterales',duracion: 60, descripcion: 'Da pasos laterales rápidos de lado a lado durante 1 minuto.' },
            { nombre: 'Boxeo en el aire',         duracion: 60, descripcion: 'Golpea el aire con los puños. Libera frustración y activa el cuerpo.' },
            { nombre: 'Estiramiento final',       duracion: 60, descripcion: 'Estira brazos, piernas y cuello. Respira profundo y termina con gratitud.' },
        ]
    },
    {
        id: 'energia',
        nombre: 'Recuperar energía',
        emoji: '😴',
        color: '#fff9c4',
        descripcion: 'Movimientos activadores suaves cuando estás cansado/a pero necesitas moverte.',
        duracion: '8 min',
        nivel: 'Muy suave',
        ejercicios: [
            { nombre: 'Estiramientos matutinos',  duracion: 60, descripcion: 'Estira los brazos hacia arriba, abre el pecho. Bosteza si te apetece.' },
            { nombre: 'Rotación de cuello',       duracion: 30, descripcion: 'Rota el cuello suavemente. Nunca forzar, solo donde llegues sin dolor.' },
            { nombre: 'Flexión de rodillas',      duracion: 45, descripcion: 'Dobla y estira las rodillas lentamente para activar la circulación.' },
            { nombre: 'Palmas hacia arriba',      duracion: 30, descripcion: 'Extiende los brazos con las palmas hacia arriba. Abre el pecho y respira.' },
            { nombre: 'Giro de torso',            duracion: 45, descripcion: 'Con pies separados, gira el torso de lado a lado. Suave y continuo.' },
            { nombre: 'Caminar despacio',         duracion: 60, descripcion: 'Camina por el espacio disponible, conscientemente, sintiendo cada paso.' },
        ]
    },
    {
        id: 'estres',
        nombre: 'Liberar el estrés',
        emoji: '😠',
        color: '#ffd5d5',
        descripcion: 'Ejercicios de alta intensidad para quemar el exceso de adrenalina y cortisol.',
        duracion: '12 min',
        nivel: 'Intenso',
        ejercicios: [
            { nombre: 'Burpees modificados',      duracion: 45, descripcion: 'Baja al suelo, sube y salta. Tan rápido como puedas durante 45 segundos.' },
            { nombre: 'Mountain climbers',        duracion: 45, descripcion: 'En posición de plancha, alterna las rodillas hacia el pecho rápidamente.' },
            { nombre: 'Sentadillas con salto',    duracion: 45, descripcion: 'Baja en sentadilla y sube con un salto explosivo.' },
            { nombre: 'Plancha',                  duracion: 45, descripcion: 'Mantén la posición de plancha. Activa el core y respira.' },
            { nombre: 'Skipping',                 duracion: 60, descripcion: 'Corre en el sitio levantando las rodillas lo más alto posible.' },
            { nombre: 'Enfriamiento',             duracion: 60, descripcion: 'Baja el ritmo. Marcha suave y respiraciones profundas para volver a la calma.' },
        ]
    },
]

const rutinaActiva    = ref(null)
const ejercicioActual = ref(0)
const activo          = ref(false)
const completado      = ref(false)
const cuenta          = ref(0)
const faseDescanso    = ref(false)
const DESCANSO        = 15
let intervalo         = null

const iniciarRutina = (rutina) => {
    rutinaActiva.value    = rutina
    ejercicioActual.value = 0
    activo.value          = true
    completado.value      = false
    faseDescanso.value    = false
    pestana.value         = 'sesion'
    cuenta.value          = rutina.ejercicios[0].duracion
    clearInterval(intervalo)
    intervalo = setInterval(tick, 1000)
}

const tick = () => {
    cuenta.value--
    if (cuenta.value <= 0) {
        if (faseDescanso.value) {
            faseDescanso.value = false
            if (ejercicioActual.value < rutinaActiva.value.ejercicios.length - 1) {
                ejercicioActual.value++
                cuenta.value = rutinaActiva.value.ejercicios[ejercicioActual.value].duracion
            } else {
                clearInterval(intervalo)
                activo.value     = false
                completado.value = true
            }
        } else {
            if (ejercicioActual.value < rutinaActiva.value.ejercicios.length - 1) {
                faseDescanso.value = true
                cuenta.value       = DESCANSO
            } else {
                clearInterval(intervalo)
                activo.value     = false
                completado.value = true
            }
        }
    }
}

const detener = () => {
    clearInterval(intervalo)
    activo.value       = false
    faseDescanso.value = false
    pestana.value      = 'rutinas'
    rutinaActiva.value = null
}

const saltar = () => {
    clearInterval(intervalo)
    if (faseDescanso.value) {
        faseDescanso.value = false
        if (ejercicioActual.value < rutinaActiva.value.ejercicios.length - 1) {
            ejercicioActual.value++
            cuenta.value = rutinaActiva.value.ejercicios[ejercicioActual.value].duracion
        } else {
            activo.value     = false
            completado.value = true
            return
        }
    } else {
        if (ejercicioActual.value < rutinaActiva.value.ejercicios.length - 1) {
            faseDescanso.value = true
            cuenta.value       = DESCANSO
        } else {
            activo.value     = false
            completado.value = true
            return
        }
    }
    intervalo = setInterval(tick, 1000)
}

const progresoPct = () => {
    if (!rutinaActiva.value) return 0
    return Math.round((ejercicioActual.value / rutinaActiva.value.ejercicios.length) * 100)
}

const colorNivel = (nivel) => ({
    'Muy suave': '#d4edda',
    'Suave':     '#d0eaf8',
    'Moderado':  '#fff9c4',
    'Intenso':   '#ffd5d5',
}[nivel] ?? '#fafafa')

onUnmounted(() => clearInterval(intervalo))
</script>

<template>
    <AppLayout>
        <div class="ej-wrapper">

            <div class="ej-header">
                <span>🏃</span>
                <h1>EJERCICIO TERAPÉUTICO</h1>
            </div>

            <p class="subtitulo">El movimiento es uno de los antidepresivos naturales más efectivos que existen</p>

            <!-- Tabs -->
            <div class="ej-tabs" v-if="!activo && !completado">
                <button :class="['ej-tab', { activa: pestana === 'rutinas' }]" @click="pestana = 'rutinas'">
                    🏋️ Rutinas
                </button>
                <button :class="['ej-tab', { activa: pestana === 'info' }]" @click="pestana = 'info'">
                    💡 ¿Por qué funciona?
                </button>
            </div>

            <!-- Lista de rutinas -->
            <div v-if="pestana === 'rutinas' && !activo && !completado" class="rutinas-grid">
                <div
                    v-for="rutina in rutinas"
                    :key="rutina.id"
                    class="rutina-card"
                    :style="{ backgroundColor: rutina.color }"
                >
                    <div class="rc-top">
                        <span class="rc-emoji">{{ rutina.emoji }}</span>
                        <div class="rc-badges">
                            <span class="rc-nivel" :style="{ backgroundColor: colorNivel(rutina.nivel) }">
                                {{ rutina.nivel }}
                            </span>
                            <span class="rc-dur">⏱ {{ rutina.duracion }}</span>
                        </div>
                    </div>
                    <h3>{{ rutina.nombre }}</h3>
                    <p>{{ rutina.descripcion }}</p>

                    <div class="rc-ejercicios-preview">
                        <span v-for="(ej, i) in rutina.ejercicios" :key="i" class="rcp-item">
                            {{ ej.nombre }}
                        </span>
                    </div>

                    <button class="btn-iniciar" @click="iniciarRutina(rutina)">
                        ▶ Empezar rutina
                    </button>
                </div>
            </div>

            <!-- Info científica -->
            <div v-if="pestana === 'info' && !activo && !completado" class="info-section">
                <div class="info-card">
                    <span class="info-emoji">🧠</span>
                    <h3>El ejercicio y el cerebro</h3>
                    <p>El ejercicio aeróbico libera BDNF (factor neurotrófico cerebral), que actúa como "fertilizante" para el cerebro. También aumenta la serotonina, dopamina y norepinefrina, los mismos neurotransmisores que regulan los antidepresivos.</p>
                </div>
                <div class="info-card">
                    <span class="info-emoji">⏱️</span>
                    <h3>¿Cuánto es suficiente?</h3>
                    <p>Solo 20-30 minutos de ejercicio moderado 3-5 veces por semana producen mejoras significativas en el estado de ánimo. Los efectos se notan desde la primera sesión gracias a la liberación de endorfinas.</p>
                </div>
                <div class="info-card">
                    <span class="info-emoji">💡</span>
                    <h3>Tip para empezar</h3>
                    <p>Si no tienes ganas, empieza con solo 5 minutos. Una vez en movimiento, el cuerpo suele pedir más. La motivación viene después de la acción, no antes.</p>
                </div>
            </div>

            <!-- Sesión activa -->
            <div v-if="activo || (pestana === 'sesion' && activo)" class="sesion-activa">

                <!-- Progreso general -->
                <div class="sa-progreso">
                    <div class="sap-header">
                        <span class="sap-rutina">{{ rutinaActiva.emoji }} {{ rutinaActiva.nombre }}</span>
                        <span class="sap-pct">{{ progresoPct() }}%</span>
                    </div>
                    <div class="sap-barra">
                        <div class="sap-relleno" :style="{ width: progresoPct() + '%' }"></div>
                    </div>
                    <div class="sap-pasos">
                        <div
                            v-for="(ej, i) in rutinaActiva.ejercicios"
                            :key="i"
                            class="sap-paso"
                            :class="{
                                completado: i < ejercicioActual,
                                activo: i === ejercicioActual && !faseDescanso
                            }"
                        ></div>
                    </div>
                </div>

                <!-- Descanso -->
                <div v-if="faseDescanso" class="descanso-card">
                    <span class="dc-emoji">😮‍💨</span>
                    <h2>Descanso</h2>
                    <div class="dc-cuenta">{{ cuenta }}</div>
                    <p>Respira profundo y prepárate para el siguiente ejercicio</p>
                    <p class="dc-siguiente">
                        Siguiente: <strong>{{ rutinaActiva.ejercicios[ejercicioActual + 1]?.nombre }}</strong>
                    </p>
                </div>

                <!-- Ejercicio activo -->
                <div v-else class="ejercicio-card" :style="{ backgroundColor: rutinaActiva.color }">
                    <div class="ec-num">{{ ejercicioActual + 1 }}/{{ rutinaActiva.ejercicios.length }}</div>
                    <h2>{{ rutinaActiva.ejercicios[ejercicioActual].nombre }}</h2>

                    <!-- Temporizador circular SVG -->
                    <div class="ec-timer">
                        <svg viewBox="0 0 100 100" width="140" height="140">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="8"/>
                            <circle
                                cx="50" cy="50" r="42"
                                fill="none"
                                stroke="white"
                                stroke-width="8"
                                stroke-linecap="round"
                                :stroke-dasharray="263.9"
                                :stroke-dashoffset="263.9 * (1 - cuenta / rutinaActiva.ejercicios[ejercicioActual].duracion)"
                                transform="rotate(-90 50 50)"
                                style="transition: stroke-dashoffset 1s linear"
                            />
                            <text x="50" y="50" text-anchor="middle" dominant-baseline="central"
                                fill="white" font-size="22" font-weight="bold" font-family="Arial">
                                {{ cuenta }}
                            </text>
                            <text x="50" y="65" text-anchor="middle" dominant-baseline="central"
                                fill="rgba(255,255,255,0.7)" font-size="8" font-family="Arial">
                                seg
                            </text>
                        </svg>
                    </div>

                    <p class="ec-descripcion">{{ rutinaActiva.ejercicios[ejercicioActual].descripcion }}</p>
                </div>

                <!-- Controles -->
                <div class="sa-controles">
                    <button class="btn-detener-ej" @click="detener">⏹ Detener</button>
                    <button class="btn-saltar-ej" @click="saltar">
                        {{ faseDescanso ? 'Saltar descanso →' : 'Siguiente →' }}
                    </button>
                </div>

            </div>

            <!-- Completado -->
            <div v-if="completado" class="ej-completado">
                <span class="comp-emoji">🎉</span>
                <h2>¡Rutina completada!</h2>
                <p>Has terminado <strong>{{ rutinaActiva.nombre }}</strong>. Tu cuerpo acaba de liberar endorfinas, serotonina y dopamina. ¡Bien hecho!</p>

                <div class="comp-stats">
                    <div class="cs-item">
                        <span class="cs-val">{{ rutinaActiva.ejercicios.length }}</span>
                        <span class="cs-lab">Ejercicios</span>
                    </div>
                    <div class="cs-item">
                        <span class="cs-val">{{ rutinaActiva.duracion }}</span>
                        <span class="cs-lab">Duración</span>
                    </div>
                    <div class="cs-item">
                        <span class="cs-val">🔥</span>
                        <span class="cs-lab">¡Completado!</span>
                    </div>
                </div>

                <div class="comp-botones">
                    <button class="btn-repetir" @click="iniciarRutina(rutinaActiva)">🔄 Repetir</button>
                    <button class="btn-otra" @click="completado = false; rutinaActiva = null; pestana = 'rutinas'">
                        Ver otras rutinas
                    </button>
                </div>

                <p class="comp-consejo">
                    💡 Recuerda registrar cómo te sientes ahora en el dashboard emocional. El ejercicio debería haber mejorado tu estado de ánimo.
                </p>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.ej-wrapper {
    max-width: 780px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.ej-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #ffd5d5;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    justify-content: center;
    font-size: 1.8rem;
}

.ej-header h1 { font-size: 1.2rem; font-weight: 700; margin: 0; letter-spacing: 0.08em; }
.subtitulo { color: #666; font-size: 0.95rem; text-align: center; margin: 0; }

/* ── Tabs ── */
.ej-tabs { display: flex; gap: 0.5rem; }

.ej-tab {
    padding: 0.55rem 1.25rem;
    border: 2px solid #f0f0f0;
    border-radius: 25px;
    background: white;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
    font-family: inherit;
}

.ej-tab.activa { background: #4ECDC4; border-color: #4ECDC4; color: white; }

/* ── Rutinas ── */
.rutinas-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

.rutina-card {
    border-radius: 18px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    border: 1px solid rgba(0,0,0,0.06);
    transition: transform 0.2s, box-shadow 0.2s;
}

.rutina-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }

.rc-top { display: flex; justify-content: space-between; align-items: flex-start; }
.rc-emoji { font-size: 2rem; }
.rc-badges { display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem; }

.rc-nivel {
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 700;
    color: #2D2D2D;
}

.rc-dur { font-size: 0.75rem; color: #888; font-weight: 600; }

.rutina-card h3 { font-size: 1rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.rutina-card p  { font-size: 0.85rem; color: #555; line-height: 1.5; margin: 0; flex: 1; }

.rc-ejercicios-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
}

.rcp-item {
    padding: 0.2rem 0.55rem;
    background: rgba(255,255,255,0.6);
    border-radius: 8px;
    font-size: 0.72rem;
    color: #444;
    font-weight: 500;
}

.btn-iniciar {
    padding: 0.75rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.2s;
    font-family: inherit;
}

.btn-iniciar:hover { background: #3BAFA7; }

/* ── Info ── */
.info-section { display: flex; flex-direction: column; gap: 1rem; }

.info-card {
    background: white;
    border-radius: 14px;
    padding: 1.25rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    border: 1px solid #f0f0f0;
}

.info-emoji { font-size: 1.8rem; flex-shrink: 0; }
.info-card h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.35rem; }
.info-card p  { font-size: 0.88rem; color: #555; line-height: 1.6; margin: 0; }

/* ── Sesión activa ── */
.sesion-activa {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
}

.sa-progreso { width: 100%; display: flex; flex-direction: column; gap: 0.5rem; }

.sap-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sap-rutina { font-size: 0.9rem; font-weight: 700; color: #2D2D2D; }
.sap-pct    { font-size: 0.88rem; color: #4ECDC4; font-weight: 700; }

.sap-barra {
    height: 6px;
    background: #f0f0f0;
    border-radius: 3px;
    overflow: hidden;
}

.sap-relleno {
    height: 100%;
    background: #4ECDC4;
    border-radius: 3px;
    transition: width 0.5s ease;
}

.sap-pasos { display: flex; gap: 4px; }

.sap-paso {
    flex: 1;
    height: 4px;
    background: #f0f0f0;
    border-radius: 2px;
    transition: background 0.3s;
}

.sap-paso.completado { background: #3BAFA7; }
.sap-paso.activo     { background: #4ECDC4; }

/* ── Descanso ── */
.descanso-card {
    width: 100%;
    background: #f5f5f5;
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.dc-emoji { font-size: 3rem; }
.descanso-card h2 { font-size: 1.3rem; font-weight: 800; margin: 0; color: #2D2D2D; }
.dc-cuenta { font-size: 4rem; font-weight: 900; color: #4ECDC4; line-height: 1; }
.descanso-card p  { font-size: 0.9rem; color: #666; margin: 0; }
.dc-siguiente     { font-size: 0.88rem; color: #888; }
.dc-siguiente strong { color: #2D2D2D; }

/* ── Ejercicio activo ── */
.ejercicio-card {
    width: 100%;
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    transition: background-color 0.5s;
}

.ec-num { font-size: 0.82rem; color: rgba(0,0,0,0.4); font-weight: 700; }

.ejercicio-card h2 {
    font-size: 1.4rem;
    font-weight: 800;
    color: #2D2D2D;
    margin: 0;
}

.ec-timer { display: flex; align-items: center; justify-content: center; }

.ec-descripcion {
    font-size: 0.95rem;
    color: #444;
    line-height: 1.6;
    margin: 0;
    max-width: 400px;
    font-style: italic;
}

/* ── Controles ── */
.sa-controles { display: flex; gap: 0.75rem; }

.btn-detener-ej {
    padding: 0.75rem 1.5rem;
    background: #f5f5f5;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: background 0.2s;
    font-family: inherit;
}

.btn-detener-ej:hover { background: #e0e0e0; }

.btn-saltar-ej {
    padding: 0.75rem 2rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s;
    font-family: inherit;
}

.btn-saltar-ej:hover { background: #3BAFA7; }

/* ── Completado ── */
.ej-completado {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    padding: 1rem;
}

.comp-emoji { font-size: 4rem; }
.ej-completado h2 { font-size: 1.5rem; font-weight: 800; color: #2D2D2D; margin: 0; }
.ej-completado > p { font-size: 0.95rem; color: #555; line-height: 1.6; margin: 0; }

.comp-stats {
    display: flex;
    gap: 2rem;
    background: #fafafa;
    border-radius: 16px;
    padding: 1.25rem 2rem;
}

.cs-item { display: flex; flex-direction: column; align-items: center; gap: 0.2rem; }
.cs-val  { font-size: 1.5rem; font-weight: 900; color: #4ECDC4; }
.cs-lab  { font-size: 0.75rem; color: #888; }

.comp-botones { display: flex; gap: 0.75rem; }

.btn-repetir {
    padding: 0.85rem 2rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s;
    font-family: inherit;
}

.btn-repetir:hover { background: #3BAFA7; }

.btn-otra {
    padding: 0.85rem 2rem;
    background: white;
    border: 2px solid #4ECDC4;
    border-radius: 25px;
    color: #3BAFA7;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}

.btn-otra:hover { background: #4ECDC4; color: white; }

.comp-consejo {
    background: #E8FAF9;
    border-radius: 12px;
    padding: 1rem;
    font-size: 0.85rem;
    color: #3BAFA7;
    font-style: italic;
    max-width: 500px;
}

@media (max-width: 600px) {
    .rutinas-grid { grid-template-columns: 1fr; }
}
</style>