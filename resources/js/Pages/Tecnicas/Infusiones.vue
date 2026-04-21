<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onUnmounted } from 'vue'

const infusiones = [
    {
        id: 'manzanilla',
        nombre: 'Manzanilla',
        emoji: '🌼',
        color: '#fff9c4',
        tiempoMin: 5,
        temperatura: '90°C',
        beneficios: ['Calma la ansiedad', 'Mejora el sueño', 'Reduce la inflamación'],
        ritual: 'Pon una cucharadita de flores secas en agua caliente. Tapa y deja reposar. El aroma ya empieza a calmar.',
        consejo: 'Bébela 30 minutos antes de dormir para un efecto óptimo.',
        propiedades: 'Apigenina · Flavonoides · Aceites esenciales',
    },
    {
        id: 'tila',
        nombre: 'Tila',
        emoji: '🌸',
        color: '#fce4ec',
        tiempoMin: 7,
        temperatura: '85°C',
        beneficios: ['Efecto sedante suave', 'Reduce el nerviosismo', 'Alivia la tensión muscular'],
        ritual: 'Usa flores frescas o secas. El agua no debe hervir del todo para preservar los principios activos.',
        consejo: 'Ideal para momentos de estrés agudo o antes de situaciones que te ponen nervioso/a.',
        propiedades: 'Farnesol · Linalool · Flavonoides',
    },
    {
        id: 'valeriana',
        nombre: 'Valeriana',
        emoji: '🌿',
        color: '#d4edda',
        tiempoMin: 10,
        temperatura: '80°C',
        beneficios: ['Mejora la calidad del sueño', 'Reduce la ansiedad', 'Efecto relajante profundo'],
        ritual: 'Tiene un aroma intenso. Añade miel o canela para suavizarlo. Reposar más tiempo potencia el efecto.',
        consejo: 'Sus efectos se potencian con el uso regular durante 2-4 semanas.',
        propiedades: 'Ácido valérico · Isovaleratos · GABA',
    },
    {
        id: 'lavanda',
        nombre: 'Lavanda',
        emoji: '💜',
        color: '#e8d5f5',
        tiempoMin: 5,
        temperatura: '90°C',
        beneficios: ['Reduce el estrés', 'Mejora el ánimo', 'Propiedades ansiolíticas'],
        ritual: 'Usa solo flores secas de grado alimentario. El aroma es tan importante como el sabor para el efecto calmante.',
        consejo: 'También puedes inhalar el vapor mientras reposa para un doble efecto relajante.',
        propiedades: 'Linalool · Linalil acetato · Aceites esenciales',
    },
    {
        id: 'melisa',
        nombre: 'Melisa (Toronjil)',
        emoji: '🍃',
        color: '#E8FAF9',
        tiempoMin: 6,
        temperatura: '90°C',
        beneficios: ['Calma los nervios', 'Mejora la concentración', 'Alivia el estrés digestivo'],
        ritual: 'Hojas frescas o secas. El aroma a limón es una señal de buena calidad. Añade menta para potenciar.',
        consejo: 'Excelente combinada con valeriana para el insomnio o con tila para la ansiedad.',
        propiedades: 'Ácido rosmarínico · Flavonoides · Aceite esencial',
    },
    {
        id: 'pasiflora',
        nombre: 'Pasiflora',
        emoji: '🌺',
        color: '#ffd5e5',
        tiempoMin: 8,
        temperatura: '85°C',
        beneficios: ['Reduce la ansiedad', 'Combate el insomnio', 'Calma el sistema nervioso'],
        ritual: 'Flores y hojas secas. Sabor suave y agradable. Puedes combinarla con manzanilla para potenciar.',
        consejo: 'Una de las más efectivas para la ansiedad según estudios clínicos. Comparable a algunos ansiolíticos suaves.',
        propiedades: 'Crisina · Vitexina · Flavonoides',
    },
]

const infusionActiva = ref(null)
const tiempoRestante = ref(0)
const activo         = ref(false)
const completado     = ref(false)
const paso           = ref(1) // 1: elegir, 2: preparar, 3: reposar, 4: disfrutar
let intervalo        = null

const seleccionar = (inf) => {
    infusionActiva.value = inf
    paso.value           = 2
}

const iniciarReposo = () => {
    paso.value           = 3
    tiempoRestante.value = infusionActiva.value.tiempoMin * 60
    activo.value         = true
    clearInterval(intervalo)
    intervalo = setInterval(() => {
        tiempoRestante.value--
        if (tiempoRestante.value <= 0) {
            clearInterval(intervalo)
            activo.value = false
            paso.value   = 4
        }
    }, 1000)
}

const formatear = (s) => {
    const m = Math.floor(s / 60)
    const seg = s % 60
    return `${m}:${seg.toString().padStart(2, '0')}`
}

const progresoPct = () => {
    if (!infusionActiva.value) return 0
    const total = infusionActiva.value.tiempoMin * 60
    return Math.round(((total - tiempoRestante.value) / total) * 100)
}

const reiniciar = () => {
    clearInterval(intervalo)
    infusionActiva.value = null
    activo.value         = false
    completado.value     = false
    paso.value           = 1
}

onUnmounted(() => clearInterval(intervalo))
</script>

<template>
    <AppLayout>
        <div class="inf-wrapper">

            <div class="inf-header">
                <span>🍵</span>
                <h1>INFUSIONES RELAJANTES</h1>
            </div>

            <p class="subtitulo">El ritual de preparar una infusión es tan terapéutico como beberla</p>

            <!-- Paso 1 — Elegir infusión -->
            <div v-if="paso === 1" class="inf-grid">
                <div
                    v-for="inf in infusiones"
                    :key="inf.id"
                    class="inf-card"
                    :style="{ backgroundColor: inf.color }"
                    @click="seleccionar(inf)"
                >
                    <div class="ic-top">
                        <span class="ic-emoji">{{ inf.emoji }}</span>
                        <span class="ic-tiempo">⏱ {{ inf.tiempoMin }} min</span>
                    </div>
                    <h3>{{ inf.nombre }}</h3>
                    <div class="ic-beneficios">
                        <span v-for="b in inf.beneficios" :key="b" class="icb-tag">{{ b }}</span>
                    </div>
                    <button class="btn-inf">Preparar →</button>
                </div>
            </div>

            <!-- Paso 2 — Preparación -->
            <div v-if="paso === 2 && infusionActiva" class="inf-preparacion">
                <div class="ip-header" :style="{ backgroundColor: infusionActiva.color }">
                    <span class="ip-emoji">{{ infusionActiva.emoji }}</span>
                    <div>
                        <h2>{{ infusionActiva.nombre }}</h2>
                        <span>Temperatura: {{ infusionActiva.temperatura }}</span>
                    </div>
                </div>

                <div class="ip-ritual">
                    <h3>🫖 Ritual de preparación</h3>
                    <p>{{ infusionActiva.ritual }}</p>
                </div>

                <div class="ip-propiedades">
                    <span class="ipr-label">Principios activos:</span>
                    <span>{{ infusionActiva.propiedades }}</span>
                </div>

                <div class="ip-pasos">
                    <div class="ipp-item">
                        <span class="ipp-num">1</span>
                        <span>Calienta el agua a {{ infusionActiva.temperatura }}</span>
                    </div>
                    <div class="ipp-item">
                        <span class="ipp-num">2</span>
                        <span>Añade la infusión a la taza</span>
                    </div>
                    <div class="ipp-item">
                        <span class="ipp-num">3</span>
                        <span>Vierte el agua y tapa la taza</span>
                    </div>
                    <div class="ipp-item">
                        <span class="ipp-num">4</span>
                        <span>Pulsa el temporizador y espera {{ infusionActiva.tiempoMin }} minutos</span>
                    </div>
                </div>

                <button class="btn-iniciar-inf" @click="iniciarReposo">
                    ⏱ Iniciar temporizador ({{ infusionActiva.tiempoMin }} min)
                </button>

                <button class="btn-volver-inf" @click="paso = 1">← Elegir otra</button>
            </div>

            <!-- Paso 3 — Temporizador -->
            <div v-if="paso === 3 && infusionActiva" class="inf-timer">

                <div class="it-taza">
                    <span class="it-emoji">{{ infusionActiva.emoji }}</span>
                    <div class="it-vapor">
                        <span></span><span></span><span></span>
                    </div>
                </div>

                <div class="it-tiempo">{{ formatear(tiempoRestante) }}</div>
                <p class="it-label">Tiempo de reposo</p>

                <div class="it-barra">
                    <div class="it-relleno"
                        :style="{ width: progresoPct() + '%', backgroundColor: infusionActiva.color === '#fff9c4' ? '#f5c842' : '#4ECDC4' }">
                    </div>
                </div>

                <div class="it-consejo">
                    <span>💆</span>
                    <p>Mientras esperas, cierra los ojos y respira el aroma. Inhala profundo y siente cómo te relaja.</p>
                </div>

                <button class="btn-cancelar-inf" @click="reiniciar">Cancelar</button>
            </div>

            <!-- Paso 4 — Disfrutar -->
            <div v-if="paso === 4 && infusionActiva" class="inf-disfrutar">
                <span class="id-emoji">☕</span>
                <h2>¡Tu {{ infusionActiva.nombre }} está lista!</h2>
                <p>{{ infusionActiva.consejo }}</p>

                <div class="id-ritual-final">
                    <h3>Cómo disfrutarla al máximo</h3>
                    <div class="idrf-item">
                        <span>🪑</span>
                        <span>Siéntate en un lugar cómodo y tranquilo</span>
                    </div>
                    <div class="idrf-item">
                        <span>📵</span>
                        <span>Deja el móvil a un lado durante 10 minutos</span>
                    </div>
                    <div class="idrf-item">
                        <span>👃</span>
                        <span>Inhala el aroma antes del primer sorbo</span>
                    </div>
                    <div class="idrf-item">
                        <span>🐌</span>
                        <span>Bébela despacio, saboreando cada sorbo</span>
                    </div>
                </div>

                <button class="btn-reiniciar-inf" @click="reiniciar">
                    🍵 Preparar otra infusión
                </button>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.inf-wrapper {
    max-width: 750px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    align-items: center;
}

.inf-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fff9c4;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.inf-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }
.subtitulo { color: #666; font-size: 0.95rem; text-align: center; }

/* ── Grid ── */
.inf-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    width: 100%;
}

.inf-card {
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.2s, box-shadow 0.2s;
}

.inf-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }

.ic-top { display: flex; justify-content: space-between; align-items: flex-start; }
.ic-emoji { font-size: 1.8rem; }
.ic-tiempo { font-size: 0.72rem; color: #888; font-weight: 600; }

.inf-card h3 { font-size: 0.92rem; font-weight: 700; color: #2D2D2D; margin: 0; }

.ic-beneficios { display: flex; flex-direction: column; gap: 0.2rem; }
.icb-tag { font-size: 0.72rem; color: #555; }

.btn-inf {
    padding: 0.5rem;
    background: rgba(255,255,255,0.7);
    border: 1.5px solid rgba(0,0,0,0.1);
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.82rem;
    font-weight: 700;
    color: #2D2D2D;
    transition: background 0.2s;
    font-family: inherit;
    margin-top: auto;
}

.btn-inf:hover { background: white; }

/* ── Preparación ── */
.inf-preparacion {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.ip-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    border-radius: 14px;
    padding: 1.25rem;
}

.ip-emoji { font-size: 2.5rem; }
.ip-header h2 { font-size: 1.2rem; font-weight: 700; margin: 0; }
.ip-header span { font-size: 0.82rem; color: #666; }

.ip-ritual {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    border-left: 4px solid #4ECDC4;
}

.ip-ritual h3 { font-size: 0.9rem; font-weight: 700; margin: 0 0 0.5rem; }
.ip-ritual p  { font-size: 0.88rem; color: #555; line-height: 1.6; margin: 0; }

.ip-propiedades {
    font-size: 0.78rem;
    color: #888;
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.ipr-label { font-weight: 700; color: #666; }

.ip-pasos { display: flex; flex-direction: column; gap: 0.6rem; }

.ipp-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.88rem;
    color: #444;
}

.ipp-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 0.78rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.btn-iniciar-inf {
    padding: 0.9rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-size: 0.95rem;
    font-family: inherit;
    transition: background 0.2s;
}

.btn-iniciar-inf:hover { background: #3BAFA7; }

.btn-volver-inf {
    padding: 0.6rem;
    background: none;
    border: none;
    color: #888;
    cursor: pointer;
    font-size: 0.85rem;
    font-family: inherit;
    text-align: center;
}

/* ── Temporizador ── */
.inf-timer {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    width: 100%;
}

.it-taza {
    position: relative;
    font-size: 5rem;
    text-align: center;
}

.it-vapor {
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
}

.it-vapor span {
    width: 3px;
    height: 20px;
    background: linear-gradient(to top, rgba(78,205,196,0.5), transparent);
    border-radius: 2px;
    animation: vapor 2s ease-in-out infinite;
}

.it-vapor span:nth-child(2) { animation-delay: 0.4s; }
.it-vapor span:nth-child(3) { animation-delay: 0.8s; }

@keyframes vapor {
    0%   { transform: translateY(0) scaleX(1); opacity: 0.6; }
    50%  { transform: translateY(-15px) scaleX(1.5); opacity: 0.3; }
    100% { transform: translateY(-30px) scaleX(0.5); opacity: 0; }
}

.it-tiempo {
    font-size: 4.5rem;
    font-weight: 900;
    color: #4ECDC4;
    line-height: 1;
    font-variant-numeric: tabular-nums;
}

.it-label { font-size: 0.85rem; color: #888; margin: 0; }

.it-barra {
    width: 100%;
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

.it-relleno {
    height: 100%;
    border-radius: 4px;
    transition: width 1s linear;
}

.it-consejo {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    background: #fafafa;
    border-radius: 12px;
    padding: 1rem;
    font-size: 0.88rem;
    color: #555;
    line-height: 1.5;
}

.it-consejo span:first-child { font-size: 1.3rem; flex-shrink: 0; }
.it-consejo p { margin: 0; }

.btn-cancelar-inf {
    padding: 0.6rem 1.5rem;
    background: none;
    border: 1px solid #ddd;
    border-radius: 25px;
    color: #888;
    cursor: pointer;
    font-size: 0.85rem;
    font-family: inherit;
    transition: all 0.2s;
}

.btn-cancelar-inf:hover { border-color: #E63946; color: #E63946; }

/* ── Disfrutar ── */
.inf-disfrutar {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    width: 100%;
}

.id-emoji { font-size: 4rem; }
.inf-disfrutar h2 { font-size: 1.4rem; font-weight: 800; margin: 0; }
.inf-disfrutar > p { font-size: 0.95rem; color: #555; line-height: 1.6; max-width: 480px; margin: 0; font-style: italic; }

.id-ritual-final {
    background: #fafafa;
    border-radius: 14px;
    padding: 1.25rem;
    width: 100%;
    text-align: left;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.id-ritual-final h3 { font-size: 0.9rem; font-weight: 700; margin: 0; color: #888; text-transform: uppercase; letter-spacing: 0.05em; }

.idrf-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.9rem;
    color: #444;
}

.idrf-item span:first-child { font-size: 1.2rem; }

.btn-reiniciar-inf {
    padding: 0.9rem 2rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.95rem;
    font-family: inherit;
    transition: background 0.2s;
}

.btn-reiniciar-inf:hover { background: #3BAFA7; }

@media (max-width: 600px) {
    .inf-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>