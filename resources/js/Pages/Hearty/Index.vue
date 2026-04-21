<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, nextTick, onMounted, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

const page   = usePage()
const user   = computed(() => page.props.auth?.user)
const nombre = computed(() => user.value?.name ?? '')
const avatar = computed(() => user.value?.avatar ?? '👤')

const mensajes        = ref([])
const input           = ref('')
const cargando        = ref(false)
const chatRef         = ref(null)
const emocionActual   = ref(null)
const tecnicasActivas = ref([])
const iniciado        = ref(false)
const opcionesRapidas = ref([])

const colorEmocion = computed(() => ({
    ansiedad:  { bg: '#d0eaf8', text: '#1a6fa8', border: '#3B8BD4' },
    tristeza:  { bg: '#e8d5f5', text: '#5a2d82', border: '#9B8EC4' },
    estres:    { bg: '#ffd5d5', text: '#8b1a1a', border: '#E63946' },
    cansancio: { bg: '#fff9c4', text: '#856404', border: '#FFD700' },
    enfado:    { bg: '#ffecd2', text: '#8b4513', border: '#FF8C42' },
    soledad:   { bg: '#e8eaf6', text: '#3949ab', border: '#5C6BC0' },
    alegria:   { bg: '#d4edda', text: '#155724', border: '#4ECDC4' },
    crisis:    { bg: '#fff5f5', text: '#721c24', border: '#E63946' },
}[emocionActual.value] ?? { bg: '#E8FAF9', text: '#3BAFA7', border: '#4ECDC4' }))

const scrollAbajo = async () => {
    await nextTick()
    if (chatRef.value) {
        chatRef.value.scrollTop = chatRef.value.scrollHeight
    }
}

const agregarMensaje = (sender, texto, tecnicas = [], emocion = null) => {
    mensajes.value.push({
        id:       Date.now() + Math.random(),
        sender,
        texto,
        tecnicas: tecnicas || [],
        emocion,
        hora:     new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }),
    })
    scrollAbajo()
}

const iniciarChat = async () => {
    iniciado.value = true
    cargando.value = true
    try {
        const res = await axios.get(`http://127.0.0.1:5000/inicio?nombre=${encodeURIComponent(nombre.value)}&sesiones=0`)
        agregarMensaje('hearty', res.data.mensaje)
        opcionesRapidas.value = res.data.opciones ?? []
    } catch (e) {
        agregarMensaje('hearty', `¡Hola${nombre.value ? ', ' + nombre.value.split(' ')[0] : ''}! Soy Hearty 💚 Estoy aquí para escucharte. ¿Cómo te sientes hoy?`)
        opcionesRapidas.value = ['😊 Bien', '😌 Tranquilo/a', '😰 Ansioso/a', '😢 Triste', '😠 Enfadado/a', '😴 Cansado/a']
    } finally {
        cargando.value = false
    }
}

const enviar = async (textoDirecto = null) => {
    const texto = textoDirecto || input.value.trim()
    if (!texto || cargando.value) return

    input.value           = ''
    opcionesRapidas.value = []
    agregarMensaje('user', texto)
    cargando.value = true

    try {
        const historialParaEnviar = mensajes.value.slice(-10).map(m => ({
            sender: m.sender,
            texto:  m.texto,
        }))

        const res = await axios.post('http://127.0.0.1:5000/chat', {
            mensaje:        texto,
            nombre:         nombre.value,
            historial_chat: historialParaEnviar,
            sesiones:       0,
        })

        const data = res.data

        if (data.emocion_detectada) {
            emocionActual.value = data.emocion_detectada
        }

        if (data.tecnicas_sugeridas?.length) {
            tecnicasActivas.value = data.tecnicas_sugeridas
        }

        agregarMensaje('hearty', data.respuesta, data.tecnicas_sugeridas, data.emocion_detectada)

        if (data.frase_motivacional && !data.es_crisis) {
            setTimeout(() => {
                agregarMensaje('hearty', `✨ ${data.frase_motivacional}`)
            }, 1200)
        }

    } catch (e) {
        agregarMensaje('hearty', 'Lo siento, algo ha fallado. Por favor inténtalo de nuevo 💙')
    } finally {
        cargando.value = false
    }
}

const limpiarChat = () => {
    mensajes.value        = []
    emocionActual.value   = null
    tecnicasActivas.value = []
    iniciado.value        = false
    setTimeout(iniciarChat, 100)
}

const irA = (ruta) => {
    router.visit(ruta)
}

onMounted(() => {
    iniciarChat()
})
</script>

<template>
    <AppLayout>
        <div class="hearty-page">

            <!-- Sidebar izquierdo -->
            <div class="hearty-sidebar">

                <div class="hs-perfil">
                    <div class="hsp-avatar">
                        <span>💚</span>
                        <div class="hsp-online"></div>
                    </div>
                    <div class="hsp-info">
                        <h3>Hearty</h3>
                        <span>Tu asistente emocional</span>
                    </div>
                </div>

                <div v-if="emocionActual" class="hs-emocion"
                    :style="{ backgroundColor: colorEmocion.bg, borderColor: colorEmocion.border }">
                    <span class="hse-label">Emoción detectada</span>
                    <span class="hse-valor" :style="{ color: colorEmocion.text }">
                        {{ emocionActual }}
                    </span>
                </div>

                <div v-if="tecnicasActivas.length" class="hs-tecnicas">
                    <h4>💡 Técnicas para ti</h4>
                    <div
                        v-for="tec in tecnicasActivas"
                        :key="tec.ruta"
                        class="hst-item"
                        @click="irA(tec.ruta)"
                    >
                        <span class="hsti-nombre">{{ tec.nombre }}</span>
                        <span class="hsti-por_que">{{ tec.por_que }}</span>
                        <span class="hsti-ir">Ir →</span>
                    </div>
                </div>

                <div class="hs-info-card">
                    <p>💙 Todo lo que le cuentes a Hearty es privado. Nadie más puede leerlo.</p>
                </div>

                <div class="hs-info-card crisis-card">
                    <p>🆘 Si estás en crisis llama al <strong>024</strong> — gratuito y disponible 24h.</p>
                </div>

                <button class="btn-limpiar" @click="limpiarChat">
                    🔄 Nueva conversación
                </button>

            </div>

            <!-- Chat principal -->
            <div class="hearty-chat">

                <div class="hc-header">
                    <div class="hch-left">
                        <div class="hch-avatar">💚</div>
                        <div class="hch-info">
                            <h2>Hearty</h2>
                            <span class="hch-estado">
                                <span class="hch-punto"></span>
                                En línea · Listo para escucharte
                            </span>
                        </div>
                    </div>
                    <div class="hch-right">
                        <span class="hch-badge">🔒 Privado</span>
                    </div>
                </div>

                <div class="hc-mensajes" ref="chatRef">

                    <div class="hcm-bienvenida">
                        <span class="hcmb-emoji">💚</span>
                        <p>Este es tu espacio seguro. Cuéntame lo que necesites.</p>
                    </div>

                    <div
                        v-for="msg in mensajes"
                        :key="msg.id"
                        class="hcm-wrapper"
                        :class="msg.sender === 'user' ? 'hcm-user-wrapper' : 'hcm-hearty-wrapper'"
                    >
                        <div v-if="msg.sender === 'hearty'" class="hcm-hearty-avatar">💚</div>

                        <div class="hcm-bubble-wrap">
                            <div
                                class="hcm-bubble"
                                :class="msg.sender === 'user' ? 'hcm-bubble-user' : 'hcm-bubble-hearty'"
                            >
                                <p class="hcm-texto" v-html="msg.texto
                                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                                    .replace(/\n/g, '<br>')">
                                </p>
                                <span class="hcm-hora">{{ msg.hora }}</span>
                            </div>

                            <div v-if="msg.tecnicas && msg.tecnicas.length" class="hcm-tecnicas">
                                <div
                                    v-for="tec in msg.tecnicas"
                                    :key="tec.ruta"
                                    class="hcmt-card"
                                    @click="irA(tec.ruta)"
                                >
                                    <div class="hcmtc-top">
                                        <span class="hcmtc-nombre">{{ tec.nombre }}</span>
                                        <span class="hcmtc-ir">→</span>
                                    </div>
                                    <span class="hcmtc-porq">{{ tec.por_que }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="msg.sender === 'user'" class="hcm-user-avatar">
                            {{ avatar }}
                        </div>
                    </div>

                    <div v-if="cargando" class="hcm-wrapper hcm-hearty-wrapper">
                        <div class="hcm-hearty-avatar">💚</div>
                        <div class="hcm-bubble hcm-bubble-hearty hcm-typing">
                            <span></span><span></span><span></span>
                        </div>
                    </div>

                </div>

                <div v-if="opcionesRapidas.length && !cargando" class="hc-opciones">
                    <button
                        v-for="op in opcionesRapidas"
                        :key="op"
                        class="hco-btn"
                        @click="enviar(op)"
                    >
                        {{ op }}
                    </button>
                </div>

                <div class="hc-input-area">
                    <div class="hcia-inner">
                        <textarea
                            v-model="input"
                            placeholder="Escribe lo que sientes... Estoy aquí para escucharte 💚"
                            class="hcia-input"
                            rows="1"
                            @keydown.enter.exact.prevent="enviar()"
                            @keydown.enter.shift.exact="input += '\n'"
                            @input="e => {
                                e.target.style.height = 'auto'
                                e.target.style.height = Math.min(e.target.scrollHeight, 120) + 'px'
                            }"
                        ></textarea>
                        <button
                            class="hcia-send"
                            @click="enviar()"
                            :disabled="!input.trim() || cargando"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13"
                                    stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <p class="hcia-hint">Enter para enviar · Shift+Enter para nueva línea</p>
                </div>

            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.hearty-page {
    display: grid;
    grid-template-columns: 280px 1fr;
    height: calc(100vh - 62px);
    overflow: hidden;
    background: #f5f5f5;
}

/* ── Sidebar ── */
.hearty-sidebar {
    background: white;
    border-right: 1px solid #f0f0f0;
    padding: 1.5rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    overflow-y: auto;
}

.hs-perfil {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.hsp-avatar {
    position: relative;
    width: 48px;
    height: 48px;
    background: #E8FAF9;
    border-radius: 50%;
    border: 2px solid #4ECDC4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.hsp-online {
    position: absolute;
    bottom: 1px;
    right: 1px;
    width: 12px;
    height: 12px;
    background: #4ECDC4;
    border-radius: 50%;
    border: 2px solid white;
    animation: pulso-online 2s ease-in-out infinite;
}

@keyframes pulso-online {
    0%, 100% { box-shadow: 0 0 0 0 rgba(78,205,196,0.4); }
    50%       { box-shadow: 0 0 0 4px rgba(78,205,196,0); }
}

.hsp-info h3   { font-size: 0.95rem; font-weight: 700; margin: 0; color: #2D2D2D; }
.hsp-info span { font-size: 0.75rem; color: #888; }

.hs-emocion {
    border-radius: 12px;
    padding: 0.75rem 1rem;
    border: 1.5px solid;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    transition: all 0.3s ease;
}

.hse-label { font-size: 0.7rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.06em; }
.hse-valor { font-size: 0.95rem; font-weight: 700; text-transform: capitalize; }

.hs-tecnicas { display: flex; flex-direction: column; gap: 0.5rem; }

.hs-tecnicas h4 {
    font-size: 0.8rem;
    font-weight: 700;
    color: #888;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.hst-item {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    padding: 0.75rem;
    background: #fafafa;
    border-radius: 10px;
    border: 1px solid #f0f0f0;
    transition: all 0.2s;
    cursor: pointer;
}

.hst-item:hover { background: #E8FAF9; border-color: #4ECDC4; }

.hsti-nombre  { font-size: 0.82rem; font-weight: 700; color: #2D2D2D; }
.hsti-por_que { font-size: 0.72rem; color: #666; line-height: 1.3; }
.hsti-ir      { font-size: 0.72rem; color: #4ECDC4; font-weight: 700; align-self: flex-end; }

.hs-info-card {
    background: #f5faff;
    border-radius: 10px;
    padding: 0.75rem;
    border-left: 3px solid #4ECDC4;
}

.hs-info-card p  { font-size: 0.78rem; color: #555; margin: 0; line-height: 1.5; }

.crisis-card          { background: #fff5f5; border-left-color: #E63946; }
.crisis-card strong   { color: #E63946; }

.btn-limpiar {
    padding: 0.65rem;
    background: white;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #888;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
    margin-top: auto;
}

.btn-limpiar:hover { border-color: #4ECDC4; color: #4ECDC4; }

/* ── Chat ── */
.hearty-chat {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    background: #f8fffe;
}

.hc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    background: white;
    border-bottom: 1px solid #f0f0f0;
    flex-shrink: 0;
    box-shadow: 0 1px 8px rgba(0,0,0,0.04);
}

.hch-left  { display: flex; align-items: center; gap: 0.75rem; }

.hch-avatar {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 2px 8px rgba(78,205,196,0.35);
}

.hch-info h2 { font-size: 0.95rem; font-weight: 700; margin: 0; color: #2D2D2D; }

.hch-estado {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    color: #4ECDC4;
    font-weight: 600;
}

.hch-punto {
    width: 7px;
    height: 7px;
    background: #4ECDC4;
    border-radius: 50%;
    animation: pulso-dot 2s ease-in-out infinite;
}

@keyframes pulso-dot {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.4; }
}

.hch-badge {
    padding: 0.3rem 0.75rem;
    background: #E8FAF9;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
    color: #3BAFA7;
    border: 1px solid #4ECDC4;
}

/* Mensajes */
.hc-mensajes {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    scroll-behavior: smooth;
}

.hcm-bienvenida {
    text-align: center;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    opacity: 0.5;
}

.hcmb-emoji        { font-size: 2rem; }
.hcm-bienvenida p  { font-size: 0.82rem; color: #888; margin: 0; font-style: italic; }

.hcm-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 0.6rem;
    animation: msgEntra 0.3s ease;
}

@keyframes msgEntra {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.hcm-user-wrapper   { flex-direction: row-reverse; }
.hcm-hearty-wrapper { flex-direction: row; }

.hcm-hearty-avatar {
    width: 34px;
    height: 34px;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(78,205,196,0.3);
}

.hcm-user-avatar {
    width: 34px;
    height: 34px;
    background: #E8FAF9;
    border: 2px solid #4ECDC4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}

.hcm-bubble-wrap {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-width: 70%;
}

.hcm-bubble {
    padding: 0.85rem 1.1rem;
    border-radius: 18px;
}

.hcm-bubble-hearty {
    background: white;
    border-radius: 4px 18px 18px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
}

.hcm-bubble-user {
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    border-radius: 18px 4px 18px 18px;
    box-shadow: 0 2px 10px rgba(78,205,196,0.35);
}

.hcm-bubble-user .hcm-texto { color: white; }
.hcm-bubble-user .hcm-hora  { color: rgba(255,255,255,0.7); }

.hcm-texto {
    font-size: 0.92rem;
    color: #2D2D2D;
    line-height: 1.65;
    margin: 0 0 0.3rem;
}

.hcm-hora {
    font-size: 0.65rem;
    color: #bbb;
    display: block;
    text-align: right;
}

/* Typing indicator */
.hcm-typing {
    display: flex !important;
    align-items: center;
    gap: 5px;
    padding: 1rem 1.1rem !important;
    width: 68px;
}

.hcm-typing span {
    width: 8px;
    height: 8px;
    background: #4ECDC4;
    border-radius: 50%;
    animation: typing-bounce 1.2s ease-in-out infinite;
}

.hcm-typing span:nth-child(2) { animation-delay: 0.2s; }
.hcm-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing-bounce {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30%            { transform: translateY(-7px); opacity: 1; }
}

/* Técnicas en el mensaje */
.hcm-tecnicas {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.hcmt-card {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding: 0.7rem 0.9rem;
    background: white;
    border-radius: 12px;
    border: 1.5px solid #E8FAF9;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    cursor: pointer;
}

.hcmt-card:hover { border-color: #4ECDC4; background: #f5fffe; transform: translateX(2px); }

.hcmtc-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.hcmtc-nombre { font-size: 0.82rem; font-weight: 700; color: #2D2D2D; }
.hcmtc-ir     { font-size: 0.8rem; color: #4ECDC4; font-weight: 700; }
.hcmtc-porq   { font-size: 0.72rem; color: #666; line-height: 1.3; }

/* Opciones rápidas */
.hc-opciones {
    padding: 0.75rem 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    background: white;
    border-top: 1px solid #f0f0f0;
}

.hco-btn {
    padding: 0.45rem 1rem;
    border: 1.5px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    color: #555;
    font-family: inherit;
    transition: all 0.15s;
}

.hco-btn:hover { border-color: #4ECDC4; color: #4ECDC4; background: #E8FAF9; transform: translateY(-1px); }

/* Input */
.hc-input-area {
    padding: 0.75rem 1.5rem 1rem;
    background: white;
    border-top: 1px solid #f0f0f0;
    flex-shrink: 0;
}

.hcia-inner {
    display: flex;
    align-items: flex-end;
    gap: 0.6rem;
    background: #f5f5f5;
    border-radius: 16px;
    padding: 0.5rem 0.5rem 0.5rem 1rem;
    border: 1.5px solid #e8e8e8;
    transition: border-color 0.2s, background 0.2s;
}

.hcia-inner:focus-within { border-color: #4ECDC4; background: white; }

.hcia-input {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 0.92rem;
    font-family: inherit;
    outline: none;
    resize: none;
    min-height: 24px;
    max-height: 120px;
    color: #2D2D2D;
    line-height: 1.5;
}

.hcia-input::placeholder { color: #aaa; }

.hcia-send {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    transition: opacity 0.2s, transform 0.2s;
    box-shadow: 0 2px 8px rgba(78,205,196,0.4);
}

.hcia-send:hover:not(:disabled) { opacity: 0.9; transform: scale(1.05); }
.hcia-send:disabled { opacity: 0.35; cursor: not-allowed; box-shadow: none; }

.hcia-hint {
    font-size: 0.68rem;
    color: #ccc;
    margin: 0.4rem 0 0;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .hearty-page     { grid-template-columns: 1fr; }
    .hearty-sidebar  { display: none; }
    .hcm-bubble-wrap { max-width: 85%; }
}
</style>