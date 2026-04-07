<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    mensajes: Array
})

const mensajes = ref([])
const inputUsuario = ref('')
const cargando = ref(false)
const opcionesActuales = ref([])
const preguntaActual = ref('bienvenida')
const chatRef = ref(null)

const scrollAbajo = async () => {
    await nextTick()
    if (chatRef.value) {
        chatRef.value.scrollTop = chatRef.value.scrollHeight
    }
}

const agregarMensaje = (sender, texto, tecnicas = [], siguientePregunta = null) => {
    mensajes.value.push({
        sender,
        texto,
        tecnicas,
        timestamp: new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
    })

    if (siguientePregunta) {
        opcionesActuales.value = siguientePregunta.opciones || []
        preguntaActual.value = siguientePregunta.pregunta_id
    } else if (sender === 'hearty' && !tecnicas.length) {
        opcionesActuales.value = []
    }

    scrollAbajo()
}

const irATecnica = (ruta) => {
    router.visit(ruta)
}

const enviarMensaje = async (texto = null) => {
    const mensajeTexto = texto || inputUsuario.value.trim()
    if (!mensajeTexto || cargando.value) return

    inputUsuario.value = ''
    opcionesActuales.value = []
    agregarMensaje('user', mensajeTexto)
    cargando.value = true

    try {
        const res = await axios.post('http://127.0.0.1:5000/chat', {
            mensaje: mensajeTexto,
            pregunta_actual: preguntaActual.value
        })

        const data = res.data
        agregarMensaje(
            'hearty',
            data.respuesta,
            data.tecnicas_sugeridas || [],
            data.siguiente_pregunta
        )

        if (data.consejo_del_dia) {
            setTimeout(() => {
                agregarMensaje('hearty', `💡 Consejo del día: ${data.consejo_del_dia}`)
            }, 1000)
        }

    } catch (e) {
        agregarMensaje('hearty', 'Lo siento, algo ha fallado. Por favor inténtalo de nuevo 💙')
    } finally {
        cargando.value = false
    }
}

const cargarInicio = async () => {
    cargando.value = true
    try {
        const res = await axios.get('http://127.0.0.1:5000/inicio')
        const data = res.data
        agregarMensaje('hearty', data.mensaje)
        opcionesActuales.value = data.opciones || []
        preguntaActual.value = data.pregunta_id || 'bienvenida'
    } catch (e) {
        agregarMensaje('hearty', '¡Hola! Soy Hearty 💚 ¿Cómo te sientes hoy?')
        opcionesActuales.value = ['😊 Bien', '😌 Tranquilo/a', '😰 Ansioso/a', '😢 Triste', '😠 Enfadado/a', '😴 Cansado/a']
    } finally {
        cargando.value = false
    }
}

onMounted(() => {
    cargarInicio()
})
</script>

<template>
    <AppLayout>
        <div class="hearty-wrapper">

            <!-- Cabecera del chat -->
            <div class="hearty-header">
                <div class="hearty-avatar">💚</div>
                <div class="hearty-info">
                    <h2>Hearty</h2>
                    <span class="hearty-estado">Tu guía emocional · En línea</span>
                </div>
            </div>

            <!-- Área de mensajes -->
            <div class="chat-area" ref="chatRef">

                <div
                    v-for="(msg, i) in mensajes"
                    :key="i"
                    class="mensaje-fila"
                    :class="msg.sender"
                >
                    <!-- Avatar Hearty -->
                    <div v-if="msg.sender === 'hearty'" class="avatar-hearty">💚</div>

                    <div class="mensaje-bloque">
                        <!-- Burbuja de texto -->
                        <div class="burbuja" :class="msg.sender">
                            {{ msg.texto }}
                        </div>

                        <!-- Tarjetas de técnicas sugeridas -->
                        <div v-if="msg.tecnicas && msg.tecnicas.length" class="tecnicas-sugeridas">
                            <p class="tecnicas-titulo">Te sugiero estas técnicas:</p>
                            <div class="tecnicas-chips">
                                <button
                                    v-for="tecnica in msg.tecnicas"
                                    :key="tecnica.id"
                                    class="tecnica-chip"
                                    @click="irATecnica(tecnica.ruta)"
                                >
                                    {{ tecnica.nombre }}
                                </button>
                            </div>
                        </div>

                        <span class="timestamp">{{ msg.timestamp }}</span>
                    </div>
                </div>

                <!-- Indicador de escritura -->
                <div v-if="cargando" class="mensaje-fila hearty">
                    <div class="avatar-hearty">💚</div>
                    <div class="burbuja hearty escribiendo">
                        <span></span><span></span><span></span>
                    </div>
                </div>

            </div>

            <!-- Opciones rápidas -->
            <div v-if="opcionesActuales.length" class="opciones-rapidas">
                <button
                    v-for="opcion in opcionesActuales"
                    :key="opcion"
                    class="opcion-btn"
                    @click="enviarMensaje(opcion)"
                    :disabled="cargando"
                >
                    {{ opcion }}
                </button>
            </div>

            <!-- Input de texto -->
            <div class="input-area">
                <button class="btn-adjunto" title="Próximamente">+</button>
                <input
                    v-model="inputUsuario"
                    type="text"
                    placeholder="Escribe tu mensaje..."
                    @keyup.enter="enviarMensaje()"
                    :disabled="cargando"
                />
                <button
                    class="btn-enviar"
                    @click="enviarMensaje()"
                    :disabled="cargando || !inputUsuario.trim()"
                >
                    ▶
                </button>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.hearty-wrapper {
    max-width: 700px;
    margin: 0 auto;
    height: calc(100vh - 70px);
    display: flex;
    flex-direction: column;
    padding: 0;
}

/* ── Cabecera ── */
.hearty-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: #4ECDC4;
    color: white;
}

.hearty-avatar {
    font-size: 2rem;
    background: white;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hearty-info h2 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
}

.hearty-estado {
    font-size: 0.8rem;
    opacity: 0.9;
}

/* ── Área de chat ── */
.chat-area {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    background: #f9f9f9;
}

/* ── Mensajes ── */
.mensaje-fila {
    display: flex;
    gap: 0.6rem;
    align-items: flex-end;
}

.mensaje-fila.user {
    flex-direction: row-reverse;
}

.avatar-hearty {
    width: 36px;
    height: 36px;
    background: #E8FAF9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.mensaje-bloque {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    max-width: 75%;
}

.mensaje-fila.user .mensaje-bloque {
    align-items: flex-end;
}

.burbuja {
    padding: 0.75rem 1rem;
    border-radius: 18px;
    font-size: 0.95rem;
    line-height: 1.5;
    word-break: break-word;
}

.burbuja.hearty {
    background: #4ECDC4;
    color: white;
    border-bottom-left-radius: 4px;
}

.burbuja.user {
    background: white;
    color: #2D2D2D;
    border: 1px solid #e0e0e0;
    border-bottom-right-radius: 4px;
}

/* ── Animación escribiendo ── */
.escribiendo {
    display: flex;
    gap: 4px;
    align-items: center;
    padding: 0.75rem 1.2rem;
}

.escribiendo span {
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    animation: typing 1.2s infinite;
}

.escribiendo span:nth-child(2) { animation-delay: 0.2s; }
.escribiendo span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
    30% { transform: translateY(-6px); opacity: 1; }
}

/* ── Técnicas sugeridas ── */
.tecnicas-sugeridas {
    margin-top: 0.4rem;
}

.tecnicas-titulo {
    font-size: 0.8rem;
    color: #666;
    margin: 0 0 0.4rem;
}

.tecnicas-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tecnica-chip {
    padding: 0.4rem 0.9rem;
    background: white;
    border: 2px solid #4ECDC4;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #3BAFA7;
    cursor: pointer;
    transition: background 0.2s;
}

.tecnica-chip:hover {
    background: #4ECDC4;
    color: white;
}

.timestamp {
    font-size: 0.72rem;
    color: #aaa;
}

/* ── Opciones rápidas ── */
.opciones-rapidas {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: white;
    border-top: 1px solid #f0f0f0;
}

.opcion-btn {
    padding: 0.5rem 1rem;
    background: #E8FAF9;
    border: 1.5px solid #4ECDC4;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #2D2D2D;
    cursor: pointer;
    transition: background 0.2s;
}

.opcion-btn:hover:not(:disabled) {
    background: #4ECDC4;
    color: white;
}

.opcion-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ── Input ── */
.input-area {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: white;
    border-top: 1px solid #f0f0f0;
}

.btn-adjunto {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid #ddd;
    background: white;
    font-size: 1.2rem;
    color: #999;
    cursor: pointer;
    flex-shrink: 0;
}

.input-area input {
    flex: 1;
    padding: 0.65rem 1rem;
    border: 1.5px solid #e0e0e0;
    border-radius: 25px;
    font-size: 0.95rem;
    outline: none;
    font-family: inherit;
    transition: border-color 0.2s;
}

.input-area input:focus {
    border-color: #4ECDC4;
}

.btn-enviar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: #4ECDC4;
    color: white;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.2s;
    flex-shrink: 0;
}

.btn-enviar:hover:not(:disabled) {
    background: #3BAFA7;
}

.btn-enviar:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}
</style>