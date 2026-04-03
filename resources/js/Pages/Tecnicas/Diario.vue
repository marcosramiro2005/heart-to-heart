<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import axios from 'axios'

const entradas = ref([
    { texto: '¿Por qué estás agradecido/a hoy?', placeholder: 'Escribe aquí...' },
    { texto: '¿Qué momento positivo has vivido?', placeholder: 'Describe tu momento...' },
    { texto: '¿Qué persona ha hecho tu día mejor?', placeholder: 'Cuéntanos...' },
])

const respuestas = ref(['', '', ''])
const guardado = ref(false)

const guardar = async () => {
    // Aquí se guardará en la BD cuando conectemos el backend
    guardado.value = true
    setTimeout(() => guardado.value = false, 3000)
}
</script>

<template>
    <AppLayout>
        <div class="diario-wrapper">

            <div class="diario-header">
                <span>📓</span>
                <h1>DIARIO DE GRATITUD</h1>
            </div>

            <p class="subtitulo">Tómate un momento para reflexionar sobre lo bueno de hoy</p>

            <div class="entradas-lista">
                <div
                    v-for="(entrada, i) in entradas"
                    :key="i"
                    class="entrada-card"
                >
                    <label>{{ entrada.texto }}</label>
                    <textarea
                        v-model="respuestas[i]"
                        :placeholder="entrada.placeholder"
                        rows="3"
                    ></textarea>
                </div>
            </div>

            <button class="btn-guardar" @click="guardar">
                💾 Guardar entrada de hoy
            </button>

            <div v-if="guardado" class="msg-guardado">
                ✅ ¡Entrada guardada! Sigue así, cada día cuenta.
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.diario-wrapper {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    align-items: center;
}

.diario-header {
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

.diario-header h1 {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: 0.1em;
}

.subtitulo {
    color: #666;
    font-size: 0.95rem;
    margin: 0;
    text-align: center;
}

.entradas-lista {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.entrada-card {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.entrada-card label {
    font-weight: 600;
    font-size: 0.95rem;
    color: #2D2D2D;
}

.entrada-card textarea {
    padding: 0.75rem 1rem;
    border: 2px solid #E8FAF9;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    transition: border-color 0.2s;
    background: #fafafa;
}

.entrada-card textarea:focus {
    border-color: #4ECDC4;
}

.btn-guardar {
    padding: 0.9rem 2.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 0.95rem;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-guardar:hover { background: #3BAFA7; }

.msg-guardado {
    background: #E8FAF9;
    border: 2px solid #4ECDC4;
    color: #3BAFA7;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-align: center;
    width: 100%;
}
</style>