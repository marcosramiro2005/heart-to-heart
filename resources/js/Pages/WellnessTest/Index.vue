<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    historial:      Array,
    ultimo:         Object,
    puedeHacerTest: Boolean,
})

const preguntas = [
    'Poco interés o placer en hacer las cosas',
    'Sentirte desanimado/a, deprimido/a o sin esperanza',
    'Problemas para quedarte o permanecer dormido/a, o dormir demasiado',
    'Sentirte cansado/a o con poca energía',
    'Comer poco o en exceso',
    'Sentirte mal contigo mismo/a, o que eres un fracaso',
    'Dificultad para concentrarte en las cosas',
    'Moverte o hablar tan despacio que los demás lo notan, o lo contrario',
    'Pensamientos de que estarías mejor muerto/a o de hacerte daño',
]

const opciones = [
    { valor: 0, label: 'Nunca' },
    { valor: 1, label: 'Varios días' },
    { valor: 2, label: 'Más de la mitad de los días' },
    { valor: 3, label: 'Casi todos los días' },
]

const pestana       = ref('test')
const respuestas    = ref(Array(9).fill(null))
const enviando      = ref(false)
const resultado     = ref(null)
const preguntaActual = ref(0)

const puntuacionTotal = computed(() =>
    respuestas.value.reduce((s, v) => s + (v ?? 0), 0)
)

const todasRespondidas = computed(() =>
    respuestas.value.every(r => r !== null)
)

const progresoPct = computed(() => {
    const respondidas = respuestas.value.filter(r => r !== null).length
    return Math.round((respondidas / preguntas.length) * 100)
})

const responder = (idx, valor) => {
    respuestas.value[idx] = valor
    if (idx < preguntas.length - 1) {
        setTimeout(() => { preguntaActual.value = idx + 1 }, 300)
    }
}

const enviar = async () => {
    if (!todasRespondidas.value) return
    enviando.value = true
    try {
        const res = await axios.post('/test-bienestar/guardar', {
            respuestas:  respuestas.value,
            puntuacion:  puntuacionTotal.value,
        })
        resultado.value = res.data
        pestana.value   = 'resultado'
    } catch (e) {
        console.error(e)
    } finally {
        enviando.value = false
    }
}

const reiniciar = () => {
    respuestas.value    = Array(9).fill(null)
    resultado.value     = null
    preguntaActual.value = 0
    pestana.value       = 'test'
}

const colorNivel = (nivel) => ({
    minimo:          '#d4edda',
    leve:            '#fff9c4',
    moderado:        '#ffecd2',
    moderado_severo: '#ffd5d5',
    severo:          '#ffb3b3',
}[nivel] ?? '#fafafa')

const iconoRec = (tipo) => ({
    positivo:     '✨',
    tecnica:      '🌿',
    profesional:  '👨‍⚕️',
    urgente:      '💙',
    recurso:      '📞',
}[tipo] ?? '💡')
</script>

<template>
    <AppLayout>
        <div class="wt-wrapper">

            <div class="wt-header">
                <span>🧠</span>
                <h1>TEST DE BIENESTAR</h1>
            </div>

            <p class="subtitulo">Evaluación semanal de tu estado emocional basada en el cuestionario PHQ-9</p>

            <div class="wt-aviso">
                <span>ℹ️</span>
                <p>Este test es una herramienta de autoconocimiento, no un diagnóstico clínico. Si tienes dudas sobre tu salud mental, consulta con un profesional.</p>
            </div>

            <!-- Tabs -->
            <div class="wt-tabs">
                <button :class="['wt-tab', { activa: pestana === 'test' }]" @click="pestana = 'test'">
                    📝 Test semanal
                </button>
                <button :class="['wt-tab', { activa: pestana === 'historial' }]" @click="pestana = 'historial'">
                    📊 Mi historial
                </button>
            </div>

            <!-- ── TAB TEST ── -->
            <div v-if="pestana === 'test' && !resultado">

                <!-- No puede hacer el test -->
                <div v-if="!puedeHacerTest" class="wt-espera">
                    <span class="we-emoji">⏳</span>
                    <h3>Ya completaste el test esta semana</h3>
                    <p>Último test: <strong>{{ ultimo?.fecha }}</strong></p>
                    <p>Tu puntuación fue <strong>{{ ultimo?.puntuacion }}/27</strong> — {{ ultimo?.interpretacion?.titulo }}</p>
                    <p class="we-info">El test está diseñado para hacerse semanalmente para poder observar tu evolución a lo largo del tiempo.</p>
                    <button class="btn-ver-hist" @click="pestana = 'historial'">
                        Ver mi historial →
                    </button>
                </div>

                <!-- Puede hacer el test -->
                <div v-else class="wt-test">

                    <!-- Barra de progreso -->
                    <div class="wt-progreso">
                        <div class="wtp-barra">
                            <div class="wtp-relleno" :style="{ width: progresoPct + '%' }"></div>
                        </div>
                        <span>{{ respuestas.filter(r => r !== null).length }}/{{ preguntas.length }}</span>
                    </div>

                    <p class="wt-instruccion">
                        Durante las últimas <strong>2 semanas</strong>, ¿con qué frecuencia te has sentido molestado/a por los siguientes problemas?
                    </p>

                    <!-- Preguntas -->
                    <div class="preguntas-lista">
                        <div
                            v-for="(pregunta, idx) in preguntas"
                            :key="idx"
                            class="pregunta-item"
                            :class="{ activa: idx === preguntaActual, respondida: respuestas[idx] !== null }"
                            @click="preguntaActual = idx"
                        >
                            <div class="pi-header">
                                <span class="pi-num">{{ idx + 1 }}</span>
                                <p class="pi-texto">{{ pregunta }}</p>
                                <span v-if="respuestas[idx] !== null" class="pi-check">✓</span>
                            </div>

                            <Transition name="expand">
                                <div v-if="idx === preguntaActual" class="pi-opciones">
                                    <button
                                        v-for="opcion in opciones"
                                        :key="opcion.valor"
                                        class="pio-btn"
                                        :class="{ seleccionada: respuestas[idx] === opcion.valor }"
                                        @click.stop="responder(idx, opcion.valor)"
                                    >
                                        <span class="pio-punto" :class="`p${opcion.valor}`"></span>
                                        {{ opcion.label }}
                                    </button>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <!-- Pregunta 9 especial (suicidio) -->
                    <div v-if="respuestas[8] >= 1" class="wt-alerta-9">
                        <span>💙</span>
                        <p>Has indicado pensamientos de hacerte daño. Si estás en crisis, llama al <strong>024</strong> ahora mismo. Es gratuito y confidencial.</p>
                    </div>

                    <div class="wt-submit">
                        <div class="ws-puntuacion" v-if="respuestas.filter(r => r !== null).length > 0">
                            Puntuación parcial: <strong>{{ puntuacionTotal }}/27</strong>
                        </div>
                        <button
                            class="btn-enviar-test"
                            :disabled="!todasRespondidas || enviando"
                            @click="enviar"
                        >
                            {{ enviando ? '⏳ Analizando...' : '📊 Ver mi resultado' }}
                        </button>
                    </div>

                </div>
            </div>

            <!-- ── TAB RESULTADO ── -->
            <div v-if="resultado" class="wt-resultado">

                <div
                    class="wr-tarjeta"
                    :style="{ backgroundColor: resultado.interpretacion.color }"
                >
                    <span class="wr-emoji">{{ resultado.interpretacion.emoji }}</span>
                    <h2>{{ resultado.interpretacion.titulo }}</h2>
                    <div class="wr-puntuacion">
                        <span class="wrp-num">{{ resultado.puntuacion }}</span>
                        <span class="wrp-max">/27</span>
                    </div>
                    <p>{{ resultado.interpretacion.descripcion }}</p>
                </div>

                <!-- Escala visual -->
                <div class="wr-escala">
                    <div class="wre-barra">
                        <div
                            class="wre-marcador"
                            :style="{ left: (resultado.puntuacion / 27 * 100) + '%' }"
                        ></div>
                        <div class="wre-zona z1" title="Mínimo (0-4)"></div>
                        <div class="wre-zona z2" title="Leve (5-9)"></div>
                        <div class="wre-zona z3" title="Moderado (10-14)"></div>
                        <div class="wre-zona z4" title="Mod-Severo (15-19)"></div>
                        <div class="wre-zona z5" title="Severo (20-27)"></div>
                    </div>
                    <div class="wre-labels">
                        <span>Mínimo</span>
                        <span>Leve</span>
                        <span>Moderado</span>
                        <span>M-Severo</span>
                        <span>Severo</span>
                    </div>
                </div>

                <!-- Recomendaciones -->
                <div class="wr-recomendaciones">
                    <h3>💡 Recomendaciones personalizadas</h3>
                    <div
                        v-for="(rec, i) in resultado.recomendaciones"
                        :key="i"
                        class="wrr-item"
                    >
                        <span class="wrr-icono">{{ iconoRec(rec.tipo) }}</span>
                        <p>{{ rec.texto }}</p>
                        <Link v-if="rec.ruta" :href="rec.ruta" class="wrr-btn">
                            Ir →
                        </Link>
                    </div>
                </div>

                <div class="wr-bots">
                    <button class="btn-nuevo-test" @click="reiniciar">
                        🔄 Repetir test
                    </button>
                    <button class="btn-ver-hist2" @click="pestana = 'historial'; resultado = null">
                        📊 Ver historial
                    </button>
                </div>
            </div>

            <!-- ── TAB HISTORIAL ── -->
            <div v-if="pestana === 'historial'" class="wt-historial">

                <div v-if="historial.length === 0" class="wh-empty">
                    <span>📊</span>
                    <p>Todavía no has completado ningún test. ¡Empieza hoy!</p>
                    <button class="btn-ir-test" @click="pestana = 'test'">Hacer el test →</button>
                </div>

                <div v-else>
                    <!-- Gráfica simple de evolución -->
                    <div class="wh-grafica">
                        <h3>Tu evolución</h3>
                        <div class="whg-barras">
                            <div
                                v-for="(test, i) in [...historial].reverse()"
                                :key="i"
                                class="whgb-col"
                            >
                                <div class="whgb-barra-wrap">
                                    <div
                                        class="whgb-barra"
                                        :style="{
                                            height: (test.puntuacion / 27 * 100) + '%',
                                            backgroundColor: colorNivel(test.nivel)
                                        }"
                                        :title="`${test.puntuacion}/27 — ${test.fecha}`"
                                    ></div>
                                </div>
                                <span class="whgb-fecha">{{ test.fecha.slice(0,5) }}</span>
                            </div>
                        </div>
                        <div class="whg-leyenda">
                            <span class="whl-item" style="background:#d4edda">Mínimo</span>
                            <span class="whl-item" style="background:#fff9c4">Leve</span>
                            <span class="whl-item" style="background:#ffecd2">Moderado</span>
                            <span class="whl-item" style="background:#ffd5d5">M-Severo</span>
                            <span class="whl-item" style="background:#ffb3b3">Severo</span>
                        </div>
                    </div>

                    <!-- Lista de tests -->
                    <div class="wh-lista">
                        <div
                            v-for="test in historial"
                            :key="test.id"
                            class="whl-item-card"
                            :style="{ borderLeft: `4px solid ${colorNivel(test.nivel)}` }"
                        >
                            <div class="whlic-info">
                                <span class="whlic-emoji">{{ test.interpretacion.emoji }}</span>
                                <div>
                                    <span class="whlic-titulo">{{ test.interpretacion.titulo }}</span>
                                    <span class="whlic-fecha">{{ test.fecha }}</span>
                                </div>
                            </div>
                            <div class="whlic-punt">
                                <span class="whlicp-num">{{ test.puntuacion }}</span>
                                <span class="whlicp-max">/27</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.wt-wrapper {
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.wt-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #e8d5f5;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    justify-content: center;
    font-size: 1.8rem;
}

.wt-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }
.subtitulo { color: #666; font-size: 0.95rem; text-align: center; margin: 0; }

.wt-aviso {
    display: flex;
    gap: 0.6rem;
    align-items: flex-start;
    background: #fafafa;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.82rem;
    color: #666;
    line-height: 1.5;
    border: 1px solid #f0f0f0;
}

.wt-aviso span:first-child { flex-shrink: 0; }
.wt-aviso p { margin: 0; }

/* Tabs */
.wt-tabs { display: flex; gap: 0.5rem; }

.wt-tab {
    padding: 0.6rem 1.25rem;
    border: 2px solid #f0f0f0;
    border-radius: 25px;
    background: white;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
    font-family: inherit;
}

.wt-tab.activa { background: #9B8EC4; border-color: #9B8EC4; color: white; }

/* Espera */
.wt-espera {
    text-align: center;
    background: #fafafa;
    border-radius: 16px;
    padding: 2.5rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.we-emoji { font-size: 3rem; }
.wt-espera h3 { font-size: 1.1rem; font-weight: 700; margin: 0; }
.wt-espera p  { font-size: 0.9rem; color: #555; margin: 0; }
.we-info { font-size: 0.82rem; color: #aaa; font-style: italic; }

.btn-ver-hist {
    padding: 0.75rem 1.5rem;
    background: #9B8EC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    margin-top: 0.5rem;
}

/* Test */
.wt-test { display: flex; flex-direction: column; gap: 1.25rem; }

.wt-progreso { display: flex; align-items: center; gap: 0.75rem; }

.wtp-barra {
    flex: 1;
    height: 6px;
    background: #f0f0f0;
    border-radius: 3px;
    overflow: hidden;
}

.wtp-relleno {
    height: 100%;
    background: #9B8EC4;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.wt-progreso span { font-size: 0.78rem; color: #aaa; font-weight: 600; }

.wt-instruccion {
    font-size: 0.92rem;
    color: #555;
    line-height: 1.6;
    margin: 0;
    background: #f5f0ff;
    border-radius: 10px;
    padding: 0.75rem 1rem;
}

/* Preguntas */
.preguntas-lista { display: flex; flex-direction: column; gap: 0.5rem; }

.pregunta-item {
    background: white;
    border: 2px solid #f0f0f0;
    border-radius: 14px;
    overflow: hidden;
    cursor: pointer;
    transition: border-color 0.2s;
}

.pregunta-item.activa    { border-color: #9B8EC4; }
.pregunta-item.respondida { border-color: #d4edda; }

.pi-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
}

.pi-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    font-weight: 700;
    color: #888;
    flex-shrink: 0;
}

.pregunta-item.activa .pi-num    { background: #9B8EC4; color: white; }
.pregunta-item.respondida .pi-num { background: #4ECDC4; color: white; }

.pi-texto {
    flex: 1;
    font-size: 0.9rem;
    color: #2D2D2D;
    margin: 0;
    line-height: 1.4;
}

.pi-check { color: #4ECDC4; font-weight: 700; font-size: 1rem; }

.pi-opciones {
    padding: 0 1rem 1rem;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.4rem;
}

.pio-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 0.75rem;
    border: 2px solid #f0f0f0;
    border-radius: 10px;
    background: white;
    font-size: 0.82rem;
    font-weight: 600;
    color: #555;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
    text-align: left;
}

.pio-btn:hover      { border-color: #9B8EC4; color: #9B8EC4; }
.pio-btn.seleccionada { border-color: #9B8EC4; background: #f5f0ff; color: #7a4da8; }

.pio-punto {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.pio-punto.p0 { background: #d4edda; }
.pio-punto.p1 { background: #fff9c4; }
.pio-punto.p2 { background: #ffd5d5; }
.pio-punto.p3 { background: #ffb3b3; }

/* Animación preguntas */
.expand-enter-active, .expand-leave-active { transition: opacity 0.2s; }
.expand-enter-from, .expand-leave-to { opacity: 0; }

/* Alerta pregunta 9 */
.wt-alerta-9 {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    background: #E8FAF9;
    border: 2px solid #4ECDC4;
    border-radius: 12px;
    padding: 1rem;
    font-size: 0.88rem;
    color: #555;
    line-height: 1.5;
}

.wt-alerta-9 span:first-child { font-size: 1.3rem; flex-shrink: 0; }
.wt-alerta-9 p { margin: 0; }
.wt-alerta-9 strong { color: #E63946; }

/* Submit */
.wt-submit {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.ws-puntuacion {
    font-size: 0.85rem;
    color: #888;
}

.ws-puntuacion strong { color: #9B8EC4; }

.btn-enviar-test {
    padding: 0.9rem 2.5rem;
    background: #9B8EC4;
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

.btn-enviar-test:hover:not(:disabled) { background: #7a6da8; transform: translateY(-1px); }
.btn-enviar-test:disabled { opacity: 0.5; cursor: not-allowed; }

/* Resultado */
.wt-resultado { display: flex; flex-direction: column; gap: 1.5rem; }

.wr-tarjeta {
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.wr-emoji { font-size: 3.5rem; }
.wr-tarjeta h2 { font-size: 1.3rem; font-weight: 800; color: #2D2D2D; margin: 0; }

.wr-puntuacion {
    display: flex;
    align-items: baseline;
    gap: 0.2rem;
}

.wrp-num { font-size: 3rem; font-weight: 900; color: #2D2D2D; line-height: 1; }
.wrp-max { font-size: 1.2rem; color: #888; }

.wr-tarjeta p { font-size: 0.92rem; color: #444; line-height: 1.6; max-width: 480px; margin: 0; }

/* Escala */
.wr-escala { display: flex; flex-direction: column; gap: 0.4rem; }

.wre-barra {
    height: 20px;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    position: relative;
}

.wre-zona {
    flex: 1;
    height: 100%;
}

.wre-zona.z1 { background: #d4edda; }
.wre-zona.z2 { background: #fff9c4; }
.wre-zona.z3 { background: #ffecd2; }
.wre-zona.z4 { background: #ffd5d5; }
.wre-zona.z5 { background: #ffb3b3; }

.wre-marcador {
    position: absolute;
    top: -2px;
    width: 4px;
    height: 24px;
    background: #2D2D2D;
    border-radius: 2px;
    transform: translateX(-50%);
    transition: left 0.5s ease;
}

.wre-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.7rem;
    color: #aaa;
}

/* Recomendaciones */
.wr-recomendaciones { display: flex; flex-direction: column; gap: 0.75rem; }

.wr-recomendaciones h3 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
}

.wrr-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fafafa;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    border: 1px solid #f0f0f0;
}

.wrr-icono { font-size: 1.3rem; flex-shrink: 0; }
.wrr-item p { flex: 1; font-size: 0.88rem; color: #444; line-height: 1.5; margin: 0; }

.wrr-btn {
    padding: 0.4rem 0.9rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.78rem;
    white-space: nowrap;
    transition: background 0.2s;
}

.wrr-btn:hover { background: #3BAFA7; }

.wr-bots { display: flex; gap: 0.75rem; justify-content: center; }

.btn-nuevo-test {
    padding: 0.85rem 1.75rem;
    background: #9B8EC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
}

.btn-nuevo-test:hover { background: #7a6da8; }

.btn-ver-hist2 {
    padding: 0.85rem 1.75rem;
    background: white;
    border: 2px solid #9B8EC4;
    border-radius: 25px;
    color: #7a6da8;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
}

.btn-ver-hist2:hover { background: #9B8EC4; color: white; }

/* Historial */
.wh-empty {
    text-align: center;
    padding: 3rem;
    background: #fafafa;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.wh-empty span { font-size: 2.5rem; }
.wh-empty p    { font-size: 0.9rem; color: #666; margin: 0; }

.btn-ir-test {
    padding: 0.75rem 1.5rem;
    background: #9B8EC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
}

/* Gráfica historial */
.wh-grafica {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
    margin-bottom: 1rem;
}

.wh-grafica h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 1rem; }

.whg-barras {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    height: 100px;
}

.whgb-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    gap: 4px;
}

.whgb-barra-wrap {
    flex: 1;
    width: 100%;
    display: flex;
    align-items: flex-end;
}

.whgb-barra {
    width: 100%;
    border-radius: 4px 4px 0 0;
    min-height: 6px;
    transition: height 0.3s ease;
    cursor: pointer;
}

.whgb-fecha { font-size: 0.65rem; color: #aaa; }

.whg-leyenda {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.75rem;
}

.whl-item {
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #2D2D2D;
}

/* Lista historial */
.wh-lista { display: flex; flex-direction: column; gap: 0.5rem; }

.whl-item-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    border: 1px solid #f0f0f0;
    padding-left: 0.75rem;
}

.whlic-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.whlic-emoji { font-size: 1.5rem; }

.whlic-titulo { display: block; font-size: 0.88rem; font-weight: 700; color: #2D2D2D; }
.whlic-fecha  { display: block; font-size: 0.75rem; color: #aaa; }

.whlic-punt { display: flex; align-items: baseline; gap: 0.15rem; }
.whlicp-num { font-size: 1.4rem; font-weight: 900; color: #9B8EC4; }
.whlicp-max { font-size: 0.8rem; color: #aaa; }
</style>