<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    userName: String,
})

const paso        = ref(1)
const totalPasos  = 5
const avatar      = ref('👤')
const objetivo    = ref('')
const enviando    = ref(false)

const nombre = computed(() => props.userName?.split(' ')[0] ?? 'amigo/a')

const avatares = [
    '😊', '😌', '🦋', '🌸', '🌟', '💫', '🌈', '🍀',
    '🌻', '🦄', '🐬', '🌙', '☀️', '🌊', '🏔️', '🌺',
    '💚', '💙', '💜', '🧡', '❤️', '🤍', '🖤', '💛',
]

const objetivos = [
    { id: 'ansiedad',   label: 'Gestionar la ansiedad',    emoji: '😰' },
    { id: 'tristeza',   label: 'Superar la tristeza',       emoji: '😢' },
    { id: 'estres',     label: 'Reducir el estrés',         emoji: '😤' },
    { id: 'sueno',      label: 'Dormir mejor',              emoji: '😴' },
    { id: 'autoestima', label: 'Mejorar mi autoestima',     emoji: '💪' },
    { id: 'habitos',    label: 'Crear hábitos saludables',  emoji: '🌱' },
    { id: 'conexion',   label: 'Conectar conmigo mismo/a',  emoji: '💙' },
    { id: 'general',    label: 'Bienestar general',         emoji: '✨' },
]

const siguiente = () => {
    if (paso.value < totalPasos) paso.value++
}

const anterior = () => {
    if (paso.value > 1) paso.value--
}

const completar = () => {
    enviando.value = true
    router.post('/onboarding', {
        avatar:             avatar.value,
        objetivo_principal: objetivo.value,
    }, {
        onFinish: () => { enviando.value = false }
    })
}

const progresoPct = computed(() =>
    Math.round(((paso.value - 1) / (totalPasos - 1)) * 100)
)
</script>

<template>
    <div class="ob-page">

        <!-- Fondo animado -->
        <div class="ob-bg">
            <div class="ob-circle c1"></div>
            <div class="ob-circle c2"></div>
            <div class="ob-circle c3"></div>
        </div>

        <div class="ob-card">

            <!-- Barra de progreso -->
            <div class="ob-progreso">
                <div class="obp-barra">
                    <div class="obp-relleno" :style="{ width: progresoPct + '%' }"></div>
                </div>
                <span class="obp-paso">{{ paso }}/{{ totalPasos }}</span>
            </div>

            <!-- ── PASO 1 — Bienvenida ── -->
            <Transition name="slide-paso" mode="out-in">
                <div v-if="paso === 1" key="1" class="ob-paso">
                    <div class="ob-logo">
                        <img src="/images/logo.png" alt="Heart to Heart" />
                        <span>HEART TO HEART</span>
                    </div>
                    <div class="ob-big-emoji">💚</div>
                    <h1>¡Hola, {{ nombre }}!</h1>
                    <p class="ob-subtitulo">
                        Bienvenido/a a Heart to Heart. Estás a punto de empezar
                        un viaje de cuidado personal que puede cambiarlo todo.
                    </p>
                    <p class="ob-desc">
                        Solo tardamos <strong>2 minutos</strong> en configurar
                        tu experiencia personalizada. ¿Empezamos?
                    </p>
                    <button class="btn-ob-primary" @click="siguiente">
                        💚 Empezar mi viaje →
                    </button>
                </div>
            </Transition>

            <!-- ── PASO 2 — Elige tu avatar ── -->
            <Transition name="slide-paso" mode="out-in">
                <div v-if="paso === 2" key="2" class="ob-paso">
                    <div class="ob-paso-badge">Paso 1 de 3</div>
                    <h2>Elige tu avatar</h2>
                    <p>Este emoji te representará en Heart to Heart</p>

                    <div class="ob-avatar-actual">
                        {{ avatar }}
                    </div>

                    <div class="ob-avatares-grid">
                        <button
                            v-for="av in avatares"
                            :key="av"
                            class="ob-avatar-btn"
                            :class="{ seleccionado: avatar === av }"
                            @click="avatar = av"
                        >
                            {{ av }}
                        </button>
                    </div>

                    <div class="ob-nav">
                        <button class="btn-ob-sec" @click="anterior">← Atrás</button>
                        <button class="btn-ob-primary" @click="siguiente">
                            Continuar →
                        </button>
                    </div>
                </div>
            </Transition>

            <!-- ── PASO 3 — Objetivo principal ── -->
            <Transition name="slide-paso" mode="out-in">
                <div v-if="paso === 3" key="3" class="ob-paso">
                    <div class="ob-paso-badge">Paso 2 de 3</div>
                    <h2>¿Cuál es tu objetivo principal?</h2>
                    <p>Personalizaremos Heart to Heart según lo que más necesitas</p>

                    <div class="ob-objetivos-grid">
                        <button
                            v-for="obj in objetivos"
                            :key="obj.id"
                            class="ob-objetivo-btn"
                            :class="{ seleccionado: objetivo === obj.id }"
                            @click="objetivo = obj.id"
                        >
                            <span class="oob-emoji">{{ obj.emoji }}</span>
                            <span class="oob-label">{{ obj.label }}</span>
                        </button>
                    </div>

                    <div class="ob-nav">
                        <button class="btn-ob-sec" @click="anterior">← Atrás</button>
                        <button class="btn-ob-primary" @click="siguiente"
                            :disabled="!objetivo">
                            Continuar →
                        </button>
                    </div>
                </div>
            </Transition>

            <!-- ── PASO 4 — Tour de funciones ── -->
            <Transition name="slide-paso" mode="out-in">
                <div v-if="paso === 4" key="4" class="ob-paso">
                    <div class="ob-paso-badge">Paso 3 de 3</div>
                    <h2>Esto es lo que te espera</h2>
                    <p>Heart to Heart tiene todo lo que necesitas</p>

                    <div class="ob-features">
                        <div class="obf-item">
                            <span class="obf-emoji">💬</span>
                            <div>
                                <strong>Hearty</strong>
                                <p>Tu asistente emocional disponible 24/7. Siempre aquí para escucharte.</p>
                            </div>
                        </div>
                        <div class="obf-item">
                            <span class="obf-emoji">🌿</span>
                            <div>
                                <strong>15+ técnicas de bienestar</strong>
                                <p>Respiración, meditación, yoga, visualización y mucho más.</p>
                            </div>
                        </div>
                        <div class="obf-item">
                            <span class="obf-emoji">📊</span>
                            <div>
                                <strong>Dashboard emocional</strong>
                                <p>Registra tus emociones y descubre tus patrones.</p>
                            </div>
                        </div>
                        <div class="obf-item">
                            <span class="obf-emoji">🏆</span>
                            <div>
                                <strong>Logros y retos</strong>
                                <p>Gamificación positiva para mantener tus hábitos.</p>
                            </div>
                        </div>
                        <div class="obf-item">
                            <span class="obf-emoji">👥</span>
                            <div>
                                <strong>Comunidad</strong>
                                <p>Conecta con personas que entienden cómo te sientes.</p>
                            </div>
                        </div>
                        <div class="obf-item">
                            <span class="obf-emoji">🆘</span>
                            <div>
                                <strong>Modo SOS</strong>
                                <p>Técnicas de emergencia cuando más lo necesitas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="ob-nav">
                        <button class="btn-ob-sec" @click="anterior">← Atrás</button>
                        <button class="btn-ob-primary" @click="siguiente">
                            ¡Casi listo! →
                        </button>
                    </div>
                </div>
            </Transition>

            <!-- ── PASO 5 — Listo ── -->
            <Transition name="slide-paso" mode="out-in">
                <div v-if="paso === 5" key="5" class="ob-paso ob-paso-final">
                    <div class="ob-confetti">🎉</div>
                    <div class="ob-avatar-final">{{ avatar }}</div>
                    <h1>¡Todo listo, {{ nombre }}!</h1>
                    <p class="ob-subtitulo">
                        Tu perfil está configurado. Hearty te está esperando
                        para conocerte y acompañarte en este camino.
                    </p>

                    <div class="ob-resumen">
                        <div class="obr-item">
                            <span>Tu avatar:</span>
                            <span class="obr-valor">{{ avatar }}</span>
                        </div>
                        <div class="obr-item">
                            <span>Tu objetivo:</span>
                            <span class="obr-valor">
                                {{ objetivos.find(o => o.id === objetivo)?.label ?? 'Bienestar general' }}
                            </span>
                        </div>
                    </div>

                    <button
                        class="btn-ob-final"
                        @click="completar"
                        :disabled="enviando"
                    >
                        <span v-if="enviando">⏳ Preparando tu espacio...</span>
                        <span v-else">💚 Entrar a Heart to Heart</span>
                    </button>

                    <p class="ob-aviso">
                        ⚠️ Recuerda que Heart to Heart es una herramienta de apoyo
                        y no sustituye la atención psicológica profesional.
                        Si estás en crisis llama al <strong>024</strong>.
                    </p>
                </div>
            </Transition>

        </div>
    </div>
</template>

<style scoped>
.ob-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #f0fffe 0%, #e8f8f5 50%, #ffeef0 100%);
}

/* ── Fondo animado ── */
.ob-bg { position: absolute; inset: 0; pointer-events: none; }

.ob-circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.12;
    animation: flotar 6s ease-in-out infinite;
}

.ob-circle.c1 {
    width: 400px; height: 400px;
    background: #4ECDC4;
    top: -100px; right: -80px;
    animation-delay: 0s;
}

.ob-circle.c2 {
    width: 300px; height: 300px;
    background: #E63946;
    bottom: -80px; left: -60px;
    animation-delay: 2s;
}

.ob-circle.c3 {
    width: 200px; height: 200px;
    background: #9B8EC4;
    top: 40%; left: 10%;
    animation-delay: 4s;
}

@keyframes flotar {
    0%, 100% { transform: translateY(0) scale(1); }
    50%       { transform: translateY(-20px) scale(1.05); }
}

/* ── Card ── */
.ob-card {
    background: white;
    border-radius: 28px;
    padding: 2.5rem;
    max-width: 520px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.12);
    position: relative;
    z-index: 1;
}

/* ── Progreso ── */
.ob-progreso {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.obp-barra {
    flex: 1;
    height: 6px;
    background: #f0f0f0;
    border-radius: 3px;
    overflow: hidden;
}

.obp-relleno {
    height: 100%;
    background: #4ECDC4;
    border-radius: 3px;
    transition: width 0.5s ease;
}

.obp-paso {
    font-size: 0.78rem;
    color: #aaa;
    font-weight: 600;
    white-space: nowrap;
}

/* ── Pasos ── */
.ob-paso {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    text-align: center;
    min-height: 400px;
    justify-content: center;
}

.ob-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ob-logo img  { width: 36px; }
.ob-logo span { font-weight: 700; font-size: 0.82rem; color: #E63946; letter-spacing: 0.08em; }

.ob-big-emoji { font-size: 4rem; animation: pulso-ob 2s ease-in-out infinite; }

@keyframes pulso-ob {
    0%, 100% { transform: scale(1); }
    50%       { transform: scale(1.1); }
}

.ob-paso h1 { font-size: 1.8rem; font-weight: 900; color: #1a1a1a; margin: 0; }
.ob-paso h2 { font-size: 1.4rem; font-weight: 800; color: #1a1a1a; margin: 0; }
.ob-paso p  { font-size: 0.92rem; color: #666; line-height: 1.6; margin: 0; }

.ob-subtitulo { font-size: 1rem !important; color: #555 !important; max-width: 380px; }
.ob-desc      { font-size: 0.88rem !important; color: #888 !important; }
.ob-desc strong { color: #4ECDC4; }

.ob-paso-badge {
    padding: 0.3rem 0.9rem;
    background: #E8FAF9;
    color: #3BAFA7;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    border: 1.5px solid #4ECDC4;
}

/* ── Avatar ── */
.ob-avatar-actual {
    font-size: 5rem;
    width: 100px;
    height: 100px;
    background: #E8FAF9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #4ECDC4;
    transition: all 0.2s;
}

.ob-avatares-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 0.4rem;
    width: 100%;
}

.ob-avatar-btn {
    aspect-ratio: 1;
    font-size: 1.4rem;
    border: 2px solid transparent;
    border-radius: 10px;
    background: #fafafa;
    cursor: pointer;
    transition: all 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ob-avatar-btn:hover       { background: #E8FAF9; border-color: #4ECDC4; transform: scale(1.1); }
.ob-avatar-btn.seleccionado { background: #E8FAF9; border-color: #4ECDC4; transform: scale(1.1); }

/* ── Objetivos ── */
.ob-objetivos-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.6rem;
    width: 100%;
}

.ob-objetivo-btn {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.75rem 1rem;
    border: 2px solid #f0f0f0;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
    text-align: left;
}

.ob-objetivo-btn:hover       { border-color: #4ECDC4; background: #E8FAF9; }
.ob-objetivo-btn.seleccionado { border-color: #4ECDC4; background: #E8FAF9; }

.oob-emoji { font-size: 1.3rem; flex-shrink: 0; }
.oob-label { font-size: 0.85rem; font-weight: 600; color: #2D2D2D; }

/* ── Features ── */
.ob-features {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    text-align: left;
}

.obf-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    background: #fafafa;
    border-radius: 12px;
    padding: 0.85rem;
}

.obf-emoji { font-size: 1.5rem; flex-shrink: 0; }
.obf-item strong { display: block; font-size: 0.88rem; color: #2D2D2D; margin-bottom: 0.1rem; }
.obf-item p      { font-size: 0.78rem; color: #888; margin: 0; line-height: 1.4; }

/* ── Paso final ── */
.ob-paso-final { gap: 1rem !important; }

.ob-confetti { font-size: 3rem; animation: bounce 0.6s ease; }

@keyframes bounce {
    0%   { transform: scale(0); }
    60%  { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.ob-avatar-final {
    font-size: 4rem;
    width: 88px;
    height: 88px;
    background: #E8FAF9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #4ECDC4;
}

.ob-resumen {
    width: 100%;
    background: #fafafa;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.obr-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.88rem;
    color: #666;
}

.obr-valor { font-weight: 700; color: #2D2D2D; }

.ob-aviso {
    font-size: 0.75rem !important;
    color: #aaa !important;
    line-height: 1.5;
    max-width: 400px;
}

.ob-aviso strong { color: #E63946; }

/* ── Botones ── */
.btn-ob-primary {
    padding: 0.9rem 2.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s, transform 0.2s;
    width: 100%;
}

.btn-ob-primary:hover:not(:disabled) { background: #3BAFA7; transform: translateY(-2px); }
.btn-ob-primary:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-ob-sec {
    padding: 0.9rem 1.5rem;
    background: white;
    border: 2px solid #f0f0f0;
    border-radius: 25px;
    color: #888;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
}

.btn-ob-sec:hover { border-color: #4ECDC4; color: #4ECDC4; }

.btn-ob-final {
    padding: 1rem 2.5rem;
    background: linear-gradient(135deg, #4ECDC4, #3BAFA7);
    color: white;
    font-weight: 800;
    font-size: 1.05rem;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
    width: 100%;
    box-shadow: 0 6px 20px rgba(78,205,196,0.4);
}

.btn-ob-final:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(78,205,196,0.5); }
.btn-ob-final:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── Navegación ── */
.ob-nav {
    display: flex;
    gap: 0.75rem;
    width: 100%;
}

.ob-nav .btn-ob-primary { flex: 2; }
.ob-nav .btn-ob-sec     { flex: 1; }

/* ── Transición entre pasos ── */
.slide-paso-enter-active,
.slide-paso-leave-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}

.slide-paso-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.slide-paso-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}

/* ── Responsive ── */
@media (max-width: 480px) {
    .ob-avatares-grid { grid-template-columns: repeat(5, 1fr); }
}
</style>