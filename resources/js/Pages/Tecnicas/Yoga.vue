<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const paso = ref(0)
const activo = ref(false)
const completado = ref(false)

const posturas = [
    {
        nombre: 'Postura de la montaña',
        emoji: '🧍',
        duracion: 60,
        descripcion: 'De pie, pies juntos o separados al ancho de las caderas. Brazos a los lados, palmas hacia adelante. Respira profundo y siente el suelo bajo tus pies.',
        beneficio: 'Mejora la postura y la concentración.',
        color: '#d4f5e9',
    },
    {
        nombre: 'Postura del niño',
        emoji: '🙇',
        duracion: 90,
        descripcion: 'Arrodíllate y siéntate sobre los talones. Inclina el torso hacia adelante hasta apoyar la frente en el suelo. Extiende los brazos hacia adelante o déjalos a los lados. Respira suavemente.',
        beneficio: 'Libera la tensión de la espalda y calma el sistema nervioso.',
        color: '#e8d5f5',
    },
    {
        nombre: 'Postura del gato-vaca',
        emoji: '🐱',
        duracion: 60,
        descripcion: 'A cuatro patas, manos bajo los hombros y rodillas bajo las caderas. Al inhalar, arquea la espalda hacia abajo (vaca). Al exhalar, redondea la espalda hacia arriba (gato). Repite lentamente.',
        beneficio: 'Moviliza la columna y libera la tensión lumbar.',
        color: '#fff9c4',
    },
    {
        nombre: 'Postura del perro boca abajo',
        emoji: '🐕',
        duracion: 60,
        descripcion: 'Desde cuatro patas, eleva las caderas hacia el cielo formando una V invertida. Pies separados al ancho de las caderas, talones intentando tocar el suelo. Cabeza entre los brazos, mirada al ombligo.',
        beneficio: 'Estira toda la cadena posterior y energiza el cuerpo.',
        color: '#ffd5d5',
    },
    {
        nombre: 'Postura del guerrero I',
        emoji: '🧘',
        duracion: 45,
        descripcion: 'De pie, da un paso largo hacia adelante. La rodilla delantera en ángulo de 90°, pierna trasera extendida. Sube los brazos hacia el cielo con palmas enfrentadas. Mantén la mirada al frente.',
        beneficio: 'Fortalece las piernas y abre el pecho. Aumenta la confianza.',
        color: '#d0eaf8',
    },
    {
        nombre: 'Postura del árbol',
        emoji: '🌳',
        duracion: 45,
        descripcion: 'De pie, apoya el peso en un pie. Dobla la rodilla contraria y apoya la planta del pie en el tobillo, pantorrilla o muslo (nunca en la rodilla). Junta las palmas en el pecho o eleva los brazos.',
        beneficio: 'Mejora el equilibrio y la concentración. Calma la mente.',
        color: '#d4edda',
    },
    {
        nombre: 'Postura de la paloma',
        emoji: '🕊️',
        duracion: 90,
        descripcion: 'Desde cuatro patas, lleva la rodilla derecha hacia la mano derecha y estira la pierna izquierda hacia atrás. Inclínate hacia adelante sobre la pierna delantera. Mantén la cadera nivelada. Repite al otro lado.',
        beneficio: 'Libera la tensión de las caderas donde se acumula el estrés emocional.',
        color: '#ffecd2',
    },
    {
        nombre: 'Postura del cadáver',
        emoji: '😌',
        duracion: 120,
        descripcion: 'Túmbate boca arriba con brazos y piernas ligeramente separados del cuerpo. Palmas hacia arriba. Cierra los ojos. Deja que el cuerpo se relaje completamente sobre el suelo. Solo respira.',
        beneficio: 'Integra los beneficios de la práctica. Profunda relajación.',
        color: '#E8FAF9',
    },
]

let intervalo = null
const tiempoFase = ref(0)
const tiempoTotal = ref(0)

const posturActual = () => posturas[paso.value]

const iniciar = () => {
    activo.value    = true
    completado.value = false
    paso.value      = 0
    tiempoFase.value = posturas[0].duracion
    tiempoTotal.value = posturas.reduce((s, p) => s + p.duracion, 0)

    intervalo = setInterval(() => {
        tiempoFase.value--
        tiempoTotal.value--

        if (tiempoFase.value <= 0) {
            if (paso.value < posturas.length - 1) {
                paso.value++
                tiempoFase.value = posturas[paso.value].duracion
            } else {
                clearInterval(intervalo)
                activo.value    = false
                completado.value = true
            }
        }
    }, 1000)
}

const detener = () => {
    clearInterval(intervalo)
    activo.value = false
    paso.value   = 0
}

const saltar = () => {
    if (paso.value < posturas.length - 1) {
        paso.value++
        tiempoFase.value = posturas[paso.value].duracion
    }
}

const formatear = (s) => `${Math.floor(s / 60)}:${(s % 60).toString().padStart(2, '0')}`

import { onUnmounted } from 'vue'
onUnmounted(() => clearInterval(intervalo))
</script>

<template>
    <AppLayout>
        <div class="yoga-wrapper">

            <!-- Cabecera -->
            <div class="yoga-header">
                <span>🤸</span>
                <h1>YOGA SUAVE</h1>
            </div>

            <p class="subtitulo">Una secuencia de posturas para liberar la tensión y reconectar con tu cuerpo</p>

            <!-- Pantalla de inicio -->
            <div v-if="!activo && !completado" class="yoga-inicio">

                <!-- Resumen de la sesión -->
                <div class="sesion-info">
                    <div class="si-item">
                        <span class="si-valor">{{ posturas.length }}</span>
                        <span class="si-label">Posturas</span>
                    </div>
                    <div class="si-item">
                        <span class="si-valor">~12</span>
                        <span class="si-label">Minutos</span>
                    </div>
                    <div class="si-item">
                        <span class="si-valor">🌿</span>
                        <span class="si-label">Suave</span>
                    </div>
                </div>

                <!-- Lista de posturas -->
                <div class="posturas-lista">
                    <div
                        v-for="(postura, i) in posturas"
                        :key="i"
                        class="postura-preview"
                        :style="{ borderLeft: `4px solid ${postura.color === '#E8FAF9' ? '#4ECDC4' : postura.color}` }"
                    >
                        <span class="pp-emoji">{{ postura.emoji }}</span>
                        <div class="pp-info">
                            <span class="pp-nombre">{{ postura.nombre }}</span>
                            <span class="pp-dur">{{ postura.duracion }}s · {{ postura.beneficio }}</span>
                        </div>
                    </div>
                </div>

                <div class="yoga-tips">
                    <h3>Antes de empezar</h3>
                    <ul>
                        <li>Busca un espacio con suficiente sitio para estirarte</li>
                        <li>Usa ropa cómoda y si tienes esterilla, úsala</li>
                        <li>Nunca fuerces una postura si sientes dolor</li>
                        <li>Adapta cada postura a lo que tu cuerpo necesite hoy</li>
                    </ul>
                </div>

                <button class="btn-yoga" @click="iniciar">
                    🤸 Comenzar sesión de yoga
                </button>
            </div>

            <!-- Sesión activa -->
            <div v-if="activo" class="yoga-sesion">

                <!-- Progreso general -->
                <div class="yoga-progreso">
                    <span class="yp-texto">Postura {{ paso + 1 }} de {{ posturas.length }}</span>
                    <div class="yp-barra">
                        <div
                            class="yp-relleno"
                            :style="{ width: `${((paso) / posturas.length) * 100}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Tarjeta de postura actual -->
                <div
                    class="postura-card"
                    :style="{ backgroundColor: posturActual().color }"
                >
                    <div class="pc-emoji">{{ posturActual().emoji }}</div>
                    <h2>{{ posturActual().nombre }}</h2>

                    <!-- Temporizador circular -->
                    <div class="temporizador">
                        <svg viewBox="0 0 80 80" width="80" height="80">
                            <circle cx="40" cy="40" r="34" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="6"/>
                            <circle
                                cx="40" cy="40" r="34"
                                fill="none"
                                stroke="white"
                                stroke-width="6"
                                stroke-linecap="round"
                                stroke-dasharray="213.6"
                                :stroke-dashoffset="213.6 * (1 - tiempoFase / posturActual().duracion)"
                                transform="rotate(-90 40 40)"
                                style="transition: stroke-dashoffset 1s linear"
                            />
                        </svg>
                        <span class="temp-tiempo">{{ tiempoFase }}s</span>
                    </div>

                    <p class="pc-descripcion">{{ posturActual().descripcion }}</p>

                    <div class="pc-beneficio">
                        <span>💡 {{ posturActual().beneficio }}</span>
                    </div>
                </div>

                <!-- Siguiente postura preview -->
                <div v-if="paso < posturas.length - 1" class="siguiente-preview">
                    <span class="sp-label">Siguiente:</span>
                    <span class="sp-emoji">{{ posturas[paso + 1].emoji }}</span>
                    <span class="sp-nombre">{{ posturas[paso + 1].nombre }}</span>
                </div>
                <div v-else class="siguiente-preview">
                    <span class="sp-label">Última postura 🎉</span>
                </div>

                <!-- Controles -->
                <div class="yoga-controles">
                    <button class="btn-detener" @click="detener">⏹ Detener</button>
                    <button class="btn-saltar" @click="saltar" :disabled="paso >= posturas.length - 1">
                        Saltar →
                    </button>
                </div>

            </div>

            <!-- Completado -->
            <div v-if="completado" class="yoga-completado">
                <div class="comp-emoji">🌟</div>
                <h2>¡Sesión completada!</h2>
                <p>Has completado las {{ posturas.length }} posturas. Tu cuerpo y mente te lo agradecen.</p>
                <p class="comp-consejo">
                    Tómate unos minutos en silencio antes de continuar con tu día.
                    Los beneficios del yoga se integran en el descanso posterior.
                </p>
                <button class="btn-yoga" @click="completado = false; paso = 0">
                    🔄 Repetir sesión
                </button>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.yoga-wrapper {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.yoga-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #d4f5e9;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.yoga-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; letter-spacing: 0.1em; }

.subtitulo { color: #666; font-size: 0.95rem; text-align: center; }

/* ── Inicio ── */
.yoga-inicio {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.sesion-info {
    display: flex;
    gap: 2rem;
    background: #f9f9f9;
    border-radius: 14px;
    padding: 1.25rem 2rem;
    border: 1px solid #f0f0f0;
}

.si-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.si-valor { font-size: 1.6rem; font-weight: 800; color: #4ECDC4; }
.si-label { font-size: 0.78rem; color: #888; }

.posturas-lista {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 300px;
    overflow-y: auto;
}

.postura-preview {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 1rem;
    background: #fafafa;
    border-radius: 8px;
    padding-left: 0.75rem;
}

.pp-emoji { font-size: 1.3rem; flex-shrink: 0; }

.pp-info {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}

.pp-nombre { font-size: 0.88rem; font-weight: 600; color: #2D2D2D; }
.pp-dur    { font-size: 0.75rem; color: #888; }

.yoga-tips {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    width: 100%;
}

.yoga-tips h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem; color: #2D2D2D; }
.yoga-tips ul  { padding-left: 1.25rem; margin: 0; display: flex; flex-direction: column; gap: 0.4rem; }
.yoga-tips li  { font-size: 0.88rem; color: #555; line-height: 1.5; }

.btn-yoga {
    padding: 0.9rem 2.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background 0.2s, transform 0.2s;
}

.btn-yoga:hover { background: #3BAFA7; transform: translateY(-2px); }

/* ── Sesión activa ── */
.yoga-sesion {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
}

.yoga-progreso { width: 100%; display: flex; flex-direction: column; gap: 0.4rem; }

.yp-texto { font-size: 0.82rem; color: #888; font-weight: 600; }

.yp-barra {
    height: 6px;
    background: #f0f0f0;
    border-radius: 3px;
    overflow: hidden;
}

.yp-relleno {
    height: 100%;
    background: #4ECDC4;
    border-radius: 3px;
    transition: width 0.5s ease;
}

.postura-card {
    width: 100%;
    border-radius: 20px;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    text-align: center;
    transition: background-color 0.5s ease;
}

.pc-emoji { font-size: 3.5rem; }

.postura-card h2 {
    font-size: 1.2rem;
    font-weight: 800;
    color: #2D2D2D;
    margin: 0;
}

/* ── Temporizador circular ── */
.temporizador {
    position: relative;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.temp-tiempo {
    position: absolute;
    font-size: 1rem;
    font-weight: 800;
    color: #2D2D2D;
}

.pc-descripcion {
    font-size: 0.92rem;
    color: #444;
    line-height: 1.7;
    margin: 0;
}

.pc-beneficio {
    background: rgba(255,255,255,0.6);
    border-radius: 20px;
    padding: 0.4rem 1rem;
    font-size: 0.82rem;
    color: #555;
    font-weight: 600;
}

/* ── Siguiente preview ── */
.siguiente-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f5f5f5;
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}

.sp-label { color: #888; font-size: 0.78rem; }
.sp-emoji { font-size: 1rem; }
.sp-nombre { color: #2D2D2D; font-weight: 600; }

/* ── Controles ── */
.yoga-controles {
    display: flex;
    gap: 0.75rem;
}

.btn-detener {
    padding: 0.7rem 1.5rem;
    background: #f5f5f5;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: background 0.2s;
}

.btn-detener:hover { background: #e0e0e0; }

.btn-saltar {
    padding: 0.7rem 1.5rem;
    background: white;
    border: 2px solid #4ECDC4;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    color: #3BAFA7;
    transition: all 0.2s;
}

.btn-saltar:hover:not(:disabled) { background: #4ECDC4; color: white; }
.btn-saltar:disabled { opacity: 0.4; cursor: not-allowed; }

/* ── Completado ── */
.yoga-completado {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 2rem;
}

.comp-emoji { font-size: 4rem; }

.yoga-completado h2 {
    font-size: 1.4rem;
    font-weight: 800;
    color: #2D2D2D;
    margin: 0;
}

.yoga-completado p {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.6;
    margin: 0;
}

.comp-consejo {
    background: #E8FAF9;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    color: #3BAFA7 !important;
    font-style: italic;
}
</style>