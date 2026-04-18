<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const items     = ref([])
const nuevoItem = ref('')
const categoria = ref('general')
const guardado  = ref(false)
const mostrarTablero = ref(false)

const categorias = [
    { id: 'general',    label: 'General',     emoji: '💚', color: '#E8FAF9' },
    { id: 'personas',   label: 'Personas',    emoji: '👥', color: '#fce4ec' },
    { id: 'momentos',   label: 'Momentos',    emoji: '✨', color: '#fff9c4' },
    { id: 'cuerpo',     label: 'Mi cuerpo',   emoji: '🫀', color: '#d4edda' },
    { id: 'aprendizaje',label: 'Aprendizaje', emoji: '📚', color: '#e8eaf6' },
    { id: 'pequenos',   label: 'Pequeñas cosas', emoji: '🌸', color: '#fce4ec' },
]

const prompts = [
    '¿Qué persona te ha hecho sentir querido/a recientemente?',
    '¿Qué momento del día de hoy fue bueno?',
    '¿Qué capacidad de tu cuerpo aprecias hoy?',
    '¿Qué has aprendido esta semana?',
    '¿Qué pequeña comodidad tienes que muchos no tienen?',
    '¿Qué experiencia reciente te ha hecho sonreír?',
    '¿Qué aspecto de tu vida ha mejorado en el último año?',
    '¿A quién puedes llamar cuando lo necesitas?',
    '¿Qué habilidad tuya estás agradecido/a de tener?',
    '¿Qué beauty en la naturaleza has visto hoy?',
]

const promptActual = ref(prompts[Math.floor(Math.random() * prompts.length)])

const agregarItem = () => {
    if (!nuevoItem.value.trim()) return
    items.value.unshift({
        id:        Date.now(),
        texto:     nuevoItem.value.trim(),
        categoria: categoria.value,
        fecha:     new Date().toLocaleDateString('es-ES'),
        emoji:     categorias.find(c => c.id === categoria.value)?.emoji || '💚',
        color:     categorias.find(c => c.id === categoria.value)?.color || '#E8FAF9',
    })
    nuevoItem.value = ''
    guardado.value  = true
    mostrarTablero.value = true
    setTimeout(() => guardado.value = false, 2000)
    promptActual.value = prompts[Math.floor(Math.random() * prompts.length)]
}

const eliminarItem = (id) => {
    items.value = items.value.filter(i => i.id !== id)
}
</script>

<template>
    <AppLayout>
        <div class="gv-wrapper">

            <div class="gv-header">
                <span>✨</span>
                <h1>GRATITUD VISUAL</h1>
            </div>

            <p class="subtitulo">Construye tu tablero de gratitud y entrena tu mente para ver lo positivo</p>

            <div class="gv-ciencia">
                <h3>¿Por qué funciona?</h3>
                <p>El cerebro tiene un sesgo hacia lo negativo por razones evolutivas. La gratitud lo contrarresta: al escribir cosas positivas, activamos la dopamina y la serotonina. Estudios muestran que 5 minutos diarios de gratitud reducen la depresión hasta en un 35%.</p>
            </div>

            <div class="gv-prompt">
                <span class="gvp-label">💡 Pregunta de reflexión:</span>
                <p>{{ promptActual }}</p>
                <button class="btn-otro-prompt" @click="promptActual = prompts[Math.floor(Math.random() * prompts.length)]">
                    🎲 Otra pregunta
                </button>
            </div>

            <div class="gv-form">
                <h3>Añadir a mi tablero</h3>

                <div class="cats-selector">
                    <button
                        v-for="cat in categorias"
                        :key="cat.id"
                        class="cat-btn"
                        :class="{ activa: categoria === cat.id }"
                        :style="categoria === cat.id ? { backgroundColor: cat.color, borderColor: '#4ECDC4' } : {}"
                        @click="categoria = cat.id"
                    >
                        {{ cat.emoji }} {{ cat.label }}
                    </button>
                </div>

                <div class="gv-input-row">
                    <input
                        v-model="nuevoItem"
                        type="text"
                        placeholder="Escribe algo por lo que estés agradecido/a..."
                        @keyup.enter="agregarItem"
                        maxlength="120"
                    />
                    <button class="btn-agregar" @click="agregarItem" :disabled="!nuevoItem.trim()">
                        + Añadir
                    </button>
                </div>

                <div v-if="guardado" class="gv-ok">✅ Añadido a tu tablero de gratitud</div>
            </div>

            <div v-if="items.length > 0">
                <div class="tablero-header">
                    <h2>Mi tablero de gratitud ({{ items.length }})</h2>
                    <button class="btn-vista" @click="mostrarTablero = !mostrarTablero">
                        {{ mostrarTablero ? '📋 Lista' : '🎨 Tablero' }}
                    </button>
                </div>

                <div v-if="mostrarTablero" class="tablero-grid">
                    <div
                        v-for="item in items"
                        :key="item.id"
                        class="tablero-item"
                        :style="{ backgroundColor: item.color }"
                    >
                        <span class="ti-emoji">{{ item.emoji }}</span>
                        <p>{{ item.texto }}</p>
                        <span class="ti-fecha">{{ item.fecha }}</span>
                        <button class="ti-borrar" @click="eliminarItem(item.id)">×</button>
                    </div>
                </div>

                <div v-else class="tablero-lista">
                    <div
                        v-for="item in items"
                        :key="item.id"
                        class="tl-item"
                        :style="{ borderLeft: `4px solid ${item.color === '#E8FAF9' ? '#4ECDC4' : '#f48fb1'}` }"
                    >
                        <span class="tl-emoji">{{ item.emoji }}</span>
                        <span class="tl-texto">{{ item.texto }}</span>
                        <span class="tl-fecha">{{ item.fecha }}</span>
                        <button class="tl-borrar" @click="eliminarItem(item.id)">×</button>
                    </div>
                </div>
            </div>

            <div v-else class="gv-empty">
                <span>✨</span>
                <p>Tu tablero está vacío. ¡Añade la primera cosa por la que estés agradecido/a hoy!</p>
            </div>

            <div class="gv-tips">
                <h3>Cómo sacarle más partido</h3>
                <ul>
                    <li>Escribe 3 cosas cada noche antes de dormir</li>
                    <li>Sé específico/a: "la sonrisa de María esta mañana" mejor que "mis amigos"</li>
                    <li>Incluye cosas pequeñas: el café caliente, que no llovió, dormir bien</li>
                    <li>En días malos, revisa tu tablero. Te recordará que no todo es oscuridad</li>
                </ul>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.gv-wrapper {
    max-width: 680px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.gv-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fff8e1;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    justify-content: center;
    font-size: 1.8rem;
}

.gv-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; letter-spacing: 0.1em; }
.subtitulo { color: #666; font-size: 0.95rem; text-align: center; margin: 0; }

.gv-ciencia {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    border-left: 4px solid #FFD700;
}

.gv-ciencia h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.5rem; }
.gv-ciencia p  { font-size: 0.88rem; color: #555; line-height: 1.6; margin: 0; }

.gv-prompt {
    background: #fff8e1;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.gvp-label { font-size: 0.78rem; font-weight: 700; color: #f57f17; }
.gv-prompt p { font-size: 0.95rem; color: #2D2D2D; font-style: italic; margin: 0; line-height: 1.5; }

.btn-otro-prompt {
    padding: 0.35rem 0.9rem;
    background: white;
    border: 1.5px solid #FFD700;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    color: #f57f17;
    align-self: flex-start;
    transition: all 0.2s;
}

.btn-otro-prompt:hover { background: #FFD700; color: white; }

.gv-form {
    background: white;
    border-radius: 14px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.gv-form h3 { font-size: 0.95rem; font-weight: 700; margin: 0; }

.cats-selector { display: flex; flex-wrap: wrap; gap: 0.4rem; }

.cat-btn {
    padding: 0.35rem 0.8rem;
    background: white;
    border: 1.5px solid #e0e0e0;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
}

.cat-btn.activa { color: #2D2D2D; border-color: #4ECDC4; }

.gv-input-row { display: flex; gap: 0.6rem; }

.gv-input-row input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
}

.gv-input-row input:focus { border-color: #4ECDC4; }

.btn-agregar {
    padding: 0.75rem 1.25rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
}

.btn-agregar:hover:not(:disabled) { background: #3BAFA7; }
.btn-agregar:disabled { opacity: 0.5; cursor: not-allowed; }

.gv-ok {
    background: #E8FAF9;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    color: #3BAFA7;
    font-weight: 600;
}

.tablero-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tablero-header h2 { font-size: 1rem; font-weight: 700; margin: 0; }

.btn-vista {
    padding: 0.4rem 1rem;
    background: white;
    border: 1.5px solid #4ECDC4;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    color: #3BAFA7;
    transition: all 0.2s;
}

.btn-vista:hover { background: #4ECDC4; color: white; }

.tablero-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-top: 0.75rem;
}

.tablero-item {
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    position: relative;
    min-height: 100px;
}

.ti-emoji { font-size: 1.3rem; }
.tablero-item p { font-size: 0.85rem; color: #2D2D2D; line-height: 1.4; margin: 0; flex: 1; }
.ti-fecha { font-size: 0.7rem; color: #888; }

.ti-borrar {
    position: absolute;
    top: 6px;
    right: 8px;
    background: none;
    border: none;
    font-size: 1rem;
    color: #aaa;
    cursor: pointer;
    line-height: 1;
    padding: 0;
}

.ti-borrar:hover { color: #E63946; }

.tablero-lista {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.tl-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: white;
    border-radius: 8px;
    padding: 0.65rem 0.75rem;
    border: 1px solid #f0f0f0;
    padding-left: 0.75rem;
}

.tl-emoji { font-size: 1.1rem; flex-shrink: 0; }
.tl-texto { flex: 1; font-size: 0.88rem; color: #2D2D2D; }
.tl-fecha { font-size: 0.72rem; color: #aaa; white-space: nowrap; }

.tl-borrar {
    background: none;
    border: none;
    font-size: 1rem;
    color: #aaa;
    cursor: pointer;
    padding: 0;
}

.tl-borrar:hover { color: #E63946; }

.gv-empty {
    text-align: center;
    padding: 2rem;
    color: #aaa;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.5rem;
}

.gv-empty p { font-size: 0.92rem; color: #aaa; margin: 0; }

.gv-tips {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
}

.gv-tips h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem; }
.gv-tips ul  { padding-left: 1.25rem; margin: 0; display: flex; flex-direction: column; gap: 0.4rem; }
.gv-tips li  { font-size: 0.88rem; color: #555; line-height: 1.5; }

@media (max-width: 520px) {
    .tablero-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>