<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const faseActiva = ref(null)
const reflexion  = ref('')
const guardado   = ref(false)

const fases = [
    {
        id: 1,
        titulo: 'Reconocer el sufrimiento',
        emoji: '💙',
        color: '#d0eaf8',
        descripcion: 'El primer paso es reconocer que estás sufriendo sin juzgarte por ello. No minimices lo que sientes ni te digas que "no debería afectarte".',
        ejercicio: 'Escribe en voz alta o mentalmente: "En este momento estoy sufriendo. Esto es difícil. Mi dolor es válido."',
        duracion: '3 minutos'
    },
    {
        id: 2,
        titulo: 'Humanidad compartida',
        emoji: '🤝',
        color: '#d4edda',
        descripcion: 'El sufrimiento es parte de la experiencia humana. No estás solo/a. Millones de personas en este momento también están sufriendo de formas similares.',
        ejercicio: 'Repite: "No estoy solo/a en esto. El sufrimiento es parte de ser humano. Otras personas también sienten esto."',
        duracion: '3 minutos'
    },
    {
        id: 3,
        titulo: 'Amabilidad hacia ti mismo/a',
        emoji: '💗',
        color: '#fce4ec',
        descripcion: 'Trátate con la misma amabilidad que le darías a un buen amigo/a que está sufriendo. ¿Qué le dirías? Dítelo a ti mismo/a.',
        ejercicio: 'Pon una mano en el corazón. Siente su calor. Dite: "Que pueda ser amable conmigo. Que pueda darme la compasión que necesito."',
        duracion: '5 minutos'
    },
    {
        id: 4,
        titulo: 'Carta de autocompasión',
        emoji: '✍️',
        color: '#fff9c4',
        descripcion: 'Escribe una carta a ti mismo/a desde la perspectiva de un amigo/a sabio/a y compasivo/a que te quiere incondicionalmente.',
        ejercicio: 'Empieza con "Querido/a [tu nombre]..." y escribe lo que ese amigo/a compasivo/a te diría sobre lo que estás viviendo ahora.',
        duracion: '10 minutos'
    },
]

const guardarReflexion = () => {
    if (!reflexion.value.trim()) return
    guardado.value = true
    setTimeout(() => guardado.value = false, 3000)
}
</script>

<template>
    <AppLayout>
        <div class="auto-wrapper">

            <div class="auto-header">
                <span>💗</span>
                <h1>AUTOCOMPASIÓN</h1>
            </div>

            <p class="subtitulo">Aprende a tratarte con la misma amabilidad que darías a alguien que quieres</p>

            <div class="auto-intro">
                <h3>¿Qué es la autocompasión?</h3>
                <p>La autocompasión, desarrollada por la Dra. Kristin Neff, es la capacidad de tratarnos a nosotros mismos con bondad cuando sufrimos, fallamos o nos sentimos inadecuados. No es autoindulgencia ni autocompasión victimista — es el antídoto a la autocrítica destructiva.</p>
                <p>Tiene tres componentes: <strong>bondad hacia uno mismo</strong>, <strong>humanidad compartida</strong> y <strong>mindfulness</strong>.</p>
            </div>

            <div class="fases-grid">
                <div
                    v-for="fase in fases"
                    :key="fase.id"
                    class="fase-card"
                    :class="{ activa: faseActiva === fase.id }"
                    :style="{ backgroundColor: fase.color }"
                    @click="faseActiva = faseActiva === fase.id ? null : fase.id"
                >
                    <div class="fc-header">
                        <span class="fc-emoji">{{ fase.emoji }}</span>
                        <div>
                            <span class="fc-num">Paso {{ fase.id }}</span>
                            <h3>{{ fase.titulo }}</h3>
                        </div>
                        <span class="fc-dur">⏱ {{ fase.duracion }}</span>
                    </div>

                    <Transition name="expand">
                        <div v-if="faseActiva === fase.id" class="fc-detalle">
                            <p class="fc-desc">{{ fase.descripcion }}</p>
                            <div class="fc-ejercicio">
                                <span class="fc-ej-label">Ejercicio:</span>
                                <p>{{ fase.ejercicio }}</p>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>

            <div class="auto-reflexion">
                <h3>✍️ Carta de autocompasión</h3>
                <p class="ar-subtitulo">Escribe una carta a ti mismo/a desde la perspectiva de un amigo/a compasivo/a:</p>
                <textarea
                    v-model="reflexion"
                    placeholder="Querido/a [tu nombre]... Lo que estás viviendo es difícil y tiene sentido que te sientas así..."
                    rows="8"
                ></textarea>
                <div class="ar-footer">
                    <span class="ar-chars">{{ reflexion.length }} caracteres</span>
                    <button class="btn-guardar" @click="guardarReflexion" :disabled="!reflexion.trim()">
                        💾 Guardar carta
                    </button>
                </div>
                <div v-if="guardado" class="ar-ok">
                    ✅ Carta guardada. Este acto de amabilidad hacia ti mismo/a importa.
                </div>
            </div>

            <div class="auto-tips">
                <h3>Para practicar a diario</h3>
                <ul>
                    <li>Cuando te critiques, pregúntate: ¿le diría esto a un amigo/a?</li>
                    <li>Pon una mano en el corazón cuando te sientas mal. El contacto físico calma el sistema nervioso.</li>
                    <li>Sustituye "soy un fracaso" por "cometí un error, como cualquier persona".</li>
                    <li>La autocompasión no es excusa para no mejorar, es la base para hacerlo.</li>
                </ul>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.auto-wrapper {
    max-width: 680px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.auto-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fce4ec;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    justify-content: center;
    font-size: 1.8rem;
}

.auto-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; letter-spacing: 0.1em; }
.subtitulo { color: #666; font-size: 0.95rem; text-align: center; margin: 0; }

.auto-intro {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    border-left: 4px solid #f48fb1;
}

.auto-intro h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.5rem; }
.auto-intro p  { font-size: 0.88rem; color: #555; line-height: 1.6; margin: 0 0 0.5rem; }
.auto-intro p:last-child { margin: 0; }
.auto-intro strong { color: #e91e63; }

.fases-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.fase-card {
    border-radius: 14px;
    padding: 1rem 1.25rem;
    cursor: pointer;
    transition: box-shadow 0.2s, transform 0.2s;
    border: 2px solid transparent;
}

.fase-card:hover     { transform: translateX(4px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.fase-card.activa    { border-color: #f48fb1; }

.fc-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.fc-emoji { font-size: 1.6rem; flex-shrink: 0; }
.fc-num   { font-size: 0.72rem; color: #888; font-weight: 600; display: block; }
.fc-header h3 { font-size: 0.95rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.fc-dur   { margin-left: auto; font-size: 0.75rem; color: #888; white-space: nowrap; }

.fc-detalle {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.fc-desc { font-size: 0.9rem; color: #444; line-height: 1.6; margin: 0; }

.fc-ejercicio {
    background: rgba(255,255,255,0.6);
    border-radius: 10px;
    padding: 0.75rem 1rem;
}

.fc-ej-label { font-size: 0.75rem; font-weight: 700; color: #e91e63; display: block; margin-bottom: 0.3rem; }
.fc-ejercicio p { font-size: 0.88rem; color: #444; line-height: 1.5; margin: 0; font-style: italic; }

.expand-enter-active, .expand-leave-active { transition: opacity 0.2s; }
.expand-enter-from, .expand-leave-to { opacity: 0; }

.auto-reflexion {
    background: white;
    border-radius: 14px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.auto-reflexion h3 { font-size: 1rem; font-weight: 700; margin: 0; }
.ar-subtitulo { font-size: 0.85rem; color: #666; margin: 0; }

.auto-reflexion textarea {
    width: 100%;
    padding: 1rem;
    border: 2px solid #f0f0f0;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    line-height: 1.7;
    transition: border-color 0.2s;
}

.auto-reflexion textarea:focus { border-color: #f48fb1; }

.ar-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ar-chars { font-size: 0.75rem; color: #aaa; }

.btn-guardar {
    padding: 0.65rem 1.5rem;
    background: #f48fb1;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s;
    font-size: 0.9rem;
}

.btn-guardar:hover:not(:disabled) { background: #e91e63; }
.btn-guardar:disabled { opacity: 0.5; cursor: not-allowed; }

.ar-ok {
    background: #fce4ec;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    color: #c2185b;
    font-weight: 600;
    font-size: 0.88rem;
    text-align: center;
}

.auto-tips {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
}

.auto-tips h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem; }
.auto-tips ul  { padding-left: 1.25rem; margin: 0; display: flex; flex-direction: column; gap: 0.4rem; }
.auto-tips li  { font-size: 0.88rem; color: #555; line-height: 1.5; }
</style>