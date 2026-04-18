<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onUnmounted } from 'vue'

const activo     = ref(false)
const completado = ref(false)
const paso       = ref(0)
const fase       = ref('tensar')
const cuenta     = ref(0)

const grupos = [
    { nombre: 'Pies',              emoji: '🦶', instruccionTensar: 'Dobla los dedos de los pies hacia abajo con fuerza', instruccionRelajar: 'Suelta completamente los pies. Siente la diferencia.' },
    { nombre: 'Pantorrillas',      emoji: '🦵', instruccionTensar: 'Apunta los pies hacia arriba tensando las pantorrillas', instruccionRelajar: 'Deja que las pantorrillas se relajen completamente.' },
    { nombre: 'Muslos',            emoji: '🦵', instruccionTensar: 'Aprieta los muslos con fuerza', instruccionRelajar: 'Suelta los muslos. Siente cómo se ablandan.' },
    { nombre: 'Abdomen',           emoji: '🫁', instruccionTensar: 'Tensa el abdomen como si fueras a recibir un golpe', instruccionRelajar: 'Suelta el abdomen. Deja que se expanda libremente.' },
    { nombre: 'Manos',             emoji: '✊', instruccionTensar: 'Cierra los puños con fuerza', instruccionRelajar: 'Abre las manos lentamente. Siente el hormigueo.' },
    { nombre: 'Brazos',            emoji: '💪', instruccionTensar: 'Dobla los brazos y tensa los bíceps', instruccionRelajar: 'Deja caer los brazos. Completamente relajados.' },
    { nombre: 'Hombros',           emoji: '🤷', instruccionTensar: 'Sube los hombros hacia las orejas todo lo que puedas', instruccionRelajar: 'Deja caer los hombros. Siente el peso desaparecer.' },
    { nombre: 'Cuello',            emoji: '🦒', instruccionTensar: 'Presiona la cabeza hacia atrás suavemente', instruccionRelajar: 'Relaja el cuello. Deja que la cabeza caiga naturalmente.' },
    { nombre: 'Cara',              emoji: '😬', instruccionTensar: 'Frunce el ceño y aprieta los ojos y la mandíbula', instruccionRelajar: 'Suelta toda la tensión de la cara. Boca entreabierta.' },
    { nombre: 'Todo el cuerpo',    emoji: '🧘', instruccionTensar: 'Tensa todos los músculos a la vez durante 5 segundos', instruccionRelajar: 'Suelta todo de golpe. Siente la ola de relajación recorrer tu cuerpo.' },
]

let intervalo = null

const iniciar = () => {
    activo.value     = true
    completado.value = false
    paso.value       = 0
    fase.value       = 'preparar'
    cuenta.value     = 3

    intervalo = setInterval(tick, 1000)
}

const tick = () => {
    cuenta.value--

    if (cuenta.value <= 0) {
        if (fase.value === 'preparar') {
            fase.value   = 'tensar'
            cuenta.value = 7
        } else if (fase.value === 'tensar') {
            fase.value   = 'relajar'
            cuenta.value = 10
        } else if (fase.value === 'relajar') {
            if (paso.value < grupos.length - 1) {
                paso.value++
                fase.value   = 'preparar'
                cuenta.value = 3
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
    activo.value = false
    paso.value   = 0
    fase.value   = 'tensar'
}

const colorFase = () => ({
    preparar: '#E8FAF9',
    tensar:   '#ffd5d5',
    relajar:  '#d4edda',
}[fase.value] ?? '#E8FAF9')

const textoFase = () => ({
    preparar: 'Prepárate...',
    tensar:   '⬆️ TENSA',
    relajar:  '⬇️ RELAJA',
}[fase.value] ?? '')

onUnmounted(() => clearInterval(intervalo))
</script>

<template>
    <AppLayout>
        <div class="rm-wrapper">

            <div class="rm-header">
                <span>💆</span>
                <h1>RELAJACIÓN MUSCULAR PROGRESIVA</h1>
            </div>

            <p class="subtitulo">Técnica de Jacobson para liberar la tensión acumulada en el cuerpo</p>

            <div class="rm-info">
                <h3>¿Cómo funciona?</h3>
                <p>La relajación muscular progresiva consiste en tensar y relajar grupos musculares de forma sistemática. Al tensar un músculo y luego soltarlo, experimentamos una relajación más profunda que si simplemente intentáramos relajarnos. Es especialmente efectiva para la ansiedad y el insomnio.</p>
            </div>

            <div v-if="!activo && !completado" class="rm-inicio">
                <div class="grupos-preview">
                    <div v-for="(g, i) in grupos" :key="i" class="gp-item">
                        <span>{{ g.emoji }}</span>
                        <span>{{ g.nombre }}</span>
                    </div>
                </div>
                <p class="rm-consejo">Siéntate o túmbate en un lugar cómodo. La sesión dura aproximadamente 15 minutos.</p>
                <button class="btn-rm" @click="iniciar">💆 Comenzar relajación</button>
            </div>

            <div v-if="activo" class="rm-sesion">
                <div class="rm-progreso">
                    <span class="rmp-texto">Grupo {{ paso + 1 }} de {{ grupos.length }}</span>
                    <div class="rmp-barra">
                        <div class="rmp-relleno" :style="{ width: `${(paso / grupos.length) * 100}%` }"></div>
                    </div>
                </div>

                <div class="rm-card" :style="{ backgroundColor: colorFase() }">
                    <span class="rmc-emoji">{{ grupos[paso].emoji }}</span>
                    <h2>{{ grupos[paso].nombre }}</h2>
                    <div class="rmc-fase">{{ textoFase() }}</div>
                    <div class="rmc-cuenta">{{ cuenta }}</div>
                    <p class="rmc-instruccion">
                        {{ fase === 'relajar' ? grupos[paso].instruccionRelajar : grupos[paso].instruccionTensar }}
                    </p>
                </div>

                <button class="btn-rm-detener" @click="detener">⏹ Detener</button>
            </div>

            <div v-if="completado" class="rm-completado">
                <span class="comp-e">🌟</span>
                <h2>¡Sesión completada!</h2>
                <p>Has recorrido todos los grupos musculares. Tu cuerpo debería sentirse significativamente más relajado ahora.</p>
                <p class="rm-c-consejo">Tómate unos minutos en silencio antes de levantarte. Los efectos duran horas.</p>
                <button class="btn-rm" @click="iniciar">🔄 Repetir sesión</button>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.rm-wrapper {
    max-width: 560px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.rm-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #e0f2f1;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    width: 100%;
    justify-content: center;
    font-size: 1.8rem;
}

.rm-header h1 { font-size: 1rem; font-weight: 700; margin: 0; letter-spacing: 0.05em; }
.subtitulo { color: #666; font-size: 0.92rem; text-align: center; }

.rm-info {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    width: 100%;
    border-left: 4px solid #4ECDC4;
}

.rm-info h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.5rem; }
.rm-info p  { font-size: 0.88rem; color: #555; line-height: 1.6; margin: 0; }

.rm-inicio { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }

.grupos-preview {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
    width: 100%;
}

.gp-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    background: #fafafa;
    border-radius: 8px;
    padding: 0.5rem;
    font-size: 0.72rem;
    color: #666;
    font-weight: 600;
}

.gp-item span:first-child { font-size: 1.2rem; }

.rm-consejo { font-size: 0.88rem; color: #666; text-align: center; line-height: 1.5; }

.btn-rm {
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

.btn-rm:hover { background: #3BAFA7; transform: translateY(-2px); }

.rm-sesion { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }

.rm-progreso { width: 100%; display: flex; flex-direction: column; gap: 0.4rem; }
.rmp-texto { font-size: 0.82rem; color: #888; font-weight: 600; }
.rmp-barra { height: 6px; background: #f0f0f0; border-radius: 3px; overflow: hidden; }
.rmp-relleno { height: 100%; background: #4ECDC4; border-radius: 3px; transition: width 0.5s; }

.rm-card {
    width: 100%;
    border-radius: 20px;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    text-align: center;
    transition: background-color 0.5s ease;
}

.rmc-emoji { font-size: 3rem; }
.rm-card h2 { font-size: 1.3rem; font-weight: 800; color: #2D2D2D; margin: 0; }

.rmc-fase {
    font-size: 1rem;
    font-weight: 700;
    color: #2D2D2D;
    letter-spacing: 0.05em;
}

.rmc-cuenta {
    font-size: 3.5rem;
    font-weight: 900;
    color: #2D2D2D;
    line-height: 1;
}

.rmc-instruccion {
    font-size: 0.92rem;
    color: #444;
    line-height: 1.6;
    margin: 0;
    font-style: italic;
}

.btn-rm-detener {
    padding: 0.7rem 1.5rem;
    background: #f5f5f5;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: background 0.2s;
}

.btn-rm-detener:hover { background: #e0e0e0; }

.rm-completado {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
}

.comp-e { font-size: 3.5rem; }
.rm-completado h2 { font-size: 1.3rem; font-weight: 800; margin: 0; }
.rm-completado p  { font-size: 0.92rem; color: #555; line-height: 1.6; margin: 0; }
.rm-c-consejo {
    background: #E8FAF9;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    color: #3BAFA7 !important;
    font-style: italic;
}
</style>