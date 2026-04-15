<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const prompts = [
    '¿Qué te está pesando más en este momento? Escríbelo sin filtros.',
    '¿Qué necesitas de ti mismo/a que no te estás dando?',
    '¿Qué te diría tu yo del futuro sobre lo que estás viviendo ahora?',
    '¿Cuál ha sido el momento más difícil de esta semana y qué aprendiste de él?',
    '¿Qué tres cosas te hacen sentir más tú mismo/a?',
    '¿A qué tienes miedo en este momento y de dónde crees que viene ese miedo?',
    '¿Qué límite necesitas poner y no te has atrevido?',
    '¿Cómo te tratas a ti mismo/a cuando cometes un error?',
    '¿Qué persona te inspira y qué cualidad suya te gustaría desarrollar?',
    '¿Qué le dirías a la versión de ti de hace un año?',
]

const promptActual = ref(prompts[Math.floor(Math.random() * prompts.length)])
const texto = ref('')
const guardado = ref(false)

const nuevoPrompt = () => {
    promptActual.value = prompts[Math.floor(Math.random() * prompts.length)]
    texto.value = ''
    guardado.value = false
}

const guardar = () => {
    if (!texto.value.trim()) return
    guardado.value = true
    setTimeout(() => guardado.value = false, 3000)
}
</script>

<template>
    <AppLayout>
        <div class="jour-wrapper">
            <div class="jour-header">
                <span>📝</span>
                <h1>JOURNALING LIBRE</h1>
            </div>

            <p class="subtitulo">Escribe sin filtros. Nadie más verá esto.</p>

            <div class="prompt-card">
                <p class="prompt-label">Pregunta para reflexionar:</p>
                <p class="prompt-texto">{{ promptActual }}</p>
                <button class="btn-nuevo-prompt" @click="nuevoPrompt">
                    🎲 Otra pregunta
                </button>
            </div>

            <div class="jour-area">
                <textarea
                    v-model="texto"
                    placeholder="Escribe aquí, libremente, sin juicios y sin estructura. Deja que las palabras fluyan..."
                    rows="10"
                ></textarea>
                <div class="jour-footer">
                    <span class="char-count">{{ texto.length }} caracteres</span>
                    <button class="btn-guardar" @click="guardar" :disabled="!texto.trim()">
                        💾 Guardar reflexión
                    </button>
                </div>
            </div>

            <div v-if="guardado" class="msg-guardado">
                ✅ Reflexión guardada. Escribir es uno de los actos más valientes de autocuidado.
            </div>

            <div class="jour-tips">
                <h3>Tips para el journaling</h3>
                <ul>
                    <li>No te preocupes por la gramática ni la coherencia</li>
                    <li>Escribe durante al menos 10 minutos sin parar</li>
                    <li>Si te quedas bloqueado/a, escribe "no sé qué escribir" hasta que algo emerja</li>
                    <li>No te juzgues. Todo lo que sientes es válido</li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.jour-wrapper {
    max-width: 640px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.jour-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #e8f4f8;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    justify-content: center;
    font-size: 1.8rem;
}

.jour-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }

.subtitulo { color: #666; font-size: 0.95rem; text-align: center; }

.prompt-card {
    background: #fafafa;
    border-radius: 14px;
    padding: 1.5rem;
    border-left: 4px solid #4ECDC4;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.prompt-label { font-size: 0.78rem; font-weight: 700; color: #4ECDC4; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; }
.prompt-texto { font-size: 1.05rem; color: #2D2D2D; line-height: 1.5; margin: 0; font-style: italic; }

.btn-nuevo-prompt {
    padding: 0.4rem 1rem;
    background: white;
    border: 1.5px solid #4ECDC4;
    border-radius: 20px;
    color: #3BAFA7;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    align-self: flex-start;
    transition: all 0.2s;
}

.btn-nuevo-prompt:hover { background: #4ECDC4; color: white; }

.jour-area { display: flex; flex-direction: column; gap: 0.75rem; }

.jour-area textarea {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid #e8f5f4;
    border-radius: 12px;
    font-size: 0.98rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    line-height: 1.8;
    transition: border-color 0.2s;
    background: #fafffe;
}

.jour-area textarea:focus { border-color: #4ECDC4; }

.jour-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.char-count { font-size: 0.78rem; color: #aaa; }

.btn-guardar {
    padding: 0.65rem 1.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s;
    font-size: 0.9rem;
}

.btn-guardar:hover:not(:disabled) { background: #3BAFA7; }
.btn-guardar:disabled { opacity: 0.5; cursor: not-allowed; }

.msg-guardado {
    background: #E8FAF9;
    border: 2px solid #4ECDC4;
    color: #3BAFA7;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    font-weight: 600;
    text-align: center;
}

.jour-tips {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
}

.jour-tips h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem; }
.jour-tips ul { padding-left: 1.25rem; margin: 0; display: flex; flex-direction: column; gap: 0.4rem; }
.jour-tips li { font-size: 0.88rem; color: #555; }
</style>