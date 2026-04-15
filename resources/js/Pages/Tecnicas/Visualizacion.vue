<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const visualizacionActiva = ref(null)
const reproduciendo = ref(false)

const visualizaciones = [
    {
        id: 'playa',
        titulo: 'Playa tranquila',
        emoji: '🏖️',
        duracion: '8 min',
        color: '#d0eaf8',
        guia: [
            'Cierra los ojos y respira profundamente tres veces...',
            'Imagina que estás en una playa tranquila. Sientes la arena tibia bajo tus pies...',
            'Escuchas el suave sonido de las olas rompiendo en la orilla...',
            'El sol calienta tu piel suavemente. Una brisa fresca acaricia tu cara...',
            'Caminas lentamente por la orilla. Sientes el agua fresca en tus pies...',
            'Te sientas en la arena y observas el horizonte. El cielo es de un azul profundo...',
            'Cada ola que llega se lleva consigo cualquier preocupación que tengas...',
            'Estás completamente en paz. Este es tu lugar seguro...',
            'Cuando estés listo/a, vuelve lentamente al presente, llevando esta calma contigo...',
        ]
    },
    {
        id: 'bosque',
        titulo: 'Bosque mágico',
        emoji: '🌲',
        duracion: '10 min',
        color: '#d4edda',
        guia: [
            'Cierra los ojos. Respira el aire fresco...',
            'Te encuentras en un bosque tranquilo. La luz del sol se filtra entre las hojas...',
            'Escuchas el canto de los pájaros y el suave murmullo de un arroyo cercano...',
            'Caminas por un sendero de tierra suave. Sientes la energía de los árboles...',
            'Te detienes junto al arroyo. El agua cristalina fluye sobre las piedras...',
            'Te sientas en un tronco y observas la naturaleza a tu alrededor...',
            'Sientes cómo la naturaleza te recarga de energía y calma...',
            'Eres parte de este ecosistema. Estás conectado/a con todo lo que te rodea...',
        ]
    },
    {
        id: 'montana',
        titulo: 'Cima de montaña',
        emoji: '⛰️',
        duracion: '7 min',
        color: '#e8d5f5',
        guia: [
            'Cierra los ojos y respira el aire limpio de la montaña...',
            'Estás en la cima de una montaña hermosa. A tus pies, valles verdes...',
            'El horizonte se extiende hasta donde alcanza la vista...',
            'Sientes la fuerza y la solidez de la montaña bajo tus pies...',
            'Desde aquí arriba, los problemas del día a día parecen pequeños...',
            'Eres libre. Puedes ver con claridad lo que realmente importa...',
            'Llevas contigo esta perspectiva cuando vuelvas al presente...',
        ]
    },
]

let guiaIdx = 0
let guiaIntervalo = null
const textoActual = ref('')
const guiaActiva = ref(false)

const iniciarVisualizacion = (viz) => {
    visualizacionActiva.value = viz
    reproduciendo.value = true
    guiaIdx = 0
    guiaActiva.value = true
    textoActual.value = viz.guia[0]

    guiaIntervalo = setInterval(() => {
        guiaIdx++
        if (guiaIdx >= viz.guia.length) {
            clearInterval(guiaIntervalo)
            reproduciendo.value = false
            textoActual.value = '✨ Visualización completada. Abre los ojos cuando estés listo/a.'
        } else {
            textoActual.value = viz.guia[guiaIdx]
        }
    }, 8000)
}

const detener = () => {
    clearInterval(guiaIntervalo)
    reproduciendo.value = false
    guiaActiva.value = false
    visualizacionActiva.value = null
}
</script>

<template>
    <AppLayout>
        <div class="viz-wrapper">
            <div class="viz-header">
                <span>🌈</span>
                <h1>VISUALIZACIÓN GUIADA</h1>
            </div>

            <p class="subtitulo">Viajes mentales para encontrar la paz interior</p>

            <div v-if="!guiaActiva">
                <p class="viz-intro">Elige un escenario. Cierra los ojos, ponte cómodo/a y deja que tu imaginación te lleve a ese lugar.</p>

                <div class="viz-grid">
                    <button
                        v-for="viz in visualizaciones"
                        :key="viz.id"
                        class="viz-card"
                        :style="{ backgroundColor: viz.color }"
                        @click="iniciarVisualizacion(viz)"
                    >
                        <span class="viz-emoji">{{ viz.emoji }}</span>
                        <h3>{{ viz.titulo }}</h3>
                        <span class="viz-dur">⏱ {{ viz.duracion }}</span>
                    </button>
                </div>
            </div>

            <div v-else class="viz-sesion">
                <div class="viz-activa-header" :style="{ backgroundColor: visualizacionActiva.color }">
                    <span>{{ visualizacionActiva.emoji }}</span>
                    <h3>{{ visualizacionActiva.titulo }}</h3>
                </div>

                <div class="viz-texto">
                    <p>{{ textoActual }}</p>
                </div>

                <div v-if="reproduciendo" class="viz-ondas">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>

                <button class="btn-detener" @click="detener">
                    {{ reproduciendo ? '⏹ Detener' : '← Volver' }}
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.viz-wrapper {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.viz-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #ffd5e5;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.viz-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }

.subtitulo { color: #666; font-size: 0.95rem; text-align: center; }

.viz-intro {
    font-size: 0.92rem;
    color: #555;
    text-align: center;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.viz-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    width: 100%;
}

.viz-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem 1rem;
    border: none;
    border-radius: 16px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.viz-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }

.viz-emoji { font-size: 2.5rem; }
.viz-card h3 { font-size: 0.9rem; font-weight: 700; color: #2D2D2D; margin: 0; text-align: center; }
.viz-dur { font-size: 0.78rem; color: #666; }

.viz-sesion {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.viz-activa-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.viz-activa-header h3 { font-size: 1.1rem; font-weight: 700; margin: 0; }

.viz-texto {
    background: #fafafa;
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    border: 1px solid #f0f0f0;
}

.viz-texto p {
    font-size: 1rem;
    color: #444;
    line-height: 1.8;
    font-style: italic;
    margin: 0;
    animation: fadeIn 1s ease;
}

@keyframes fadeIn {
    from { opacity: 0; } to { opacity: 1; }
}

.viz-ondas {
    display: flex;
    gap: 4px;
    align-items: center;
}

.viz-ondas span {
    width: 4px;
    background: #9B8EC4;
    border-radius: 2px;
    animation: onda 1.2s ease-in-out infinite;
}

.viz-ondas span:nth-child(1) { height: 12px; animation-delay: 0s; }
.viz-ondas span:nth-child(2) { height: 20px; animation-delay: 0.1s; }
.viz-ondas span:nth-child(3) { height: 28px; animation-delay: 0.2s; }
.viz-ondas span:nth-child(4) { height: 20px; animation-delay: 0.3s; }
.viz-ondas span:nth-child(5) { height: 12px; animation-delay: 0.4s; }

@keyframes onda {
    0%, 100% { transform: scaleY(0.5); opacity: 0.5; }
    50%       { transform: scaleY(1);   opacity: 1; }
}

.btn-detener {
    padding: 0.85rem 2rem;
    background: #E63946;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-detener:hover { background: #c0303b; }

@media (max-width: 480px) {
    .viz-grid { grid-template-columns: 1fr; }
}
</style>