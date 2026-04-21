<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    entradas:   Array,
    stats:      Object,
    calendario: Object,
})

const contenido   = ref('')
const moodSelec   = ref(null)
const moodScore   = ref(5)
const tagsInput   = ref('')
const enviando    = ref(false)
const vistaActual = ref('escribir')
const entradaVer  = ref(null)

const moods = [
    { id: 'excelente', emoji: '🤩', label: 'Excelente', score: 9, color: '#d4edda' },
    { id: 'bien',      emoji: '😊', label: 'Bien',      score: 7, color: '#E8FAF9' },
    { id: 'normal',    emoji: '😐', label: 'Normal',    score: 5, color: '#fff9c4' },
    { id: 'mal',       emoji: '😔', label: 'Mal',       score: 3, color: '#ffecd2' },
    { id: 'terrible',  emoji: '😢', label: 'Terrible',  score: 1, color: '#ffd5d5' },
]

const prompts = [
    '¿Qué ha sido lo mejor de tu día?',
    '¿Qué te ha preocupado hoy y cómo lo has manejado?',
    '¿Por qué tres cosas estás agradecido/a hoy?',
    '¿Qué emoción ha dominado tu día y por qué?',
    '¿Qué harías diferente si pudieras repetir el día?',
    '¿Qué has aprendido de ti mismo/a hoy?',
    '¿Qué necesitas soltar para sentirte mejor?',
]

const promptHoy = prompts[new Date().getDay() % prompts.length]

const tagsArray = computed(() =>
    tagsInput.value.split(',').map(t => t.trim()).filter(Boolean)
)

const contadorChars = computed(() => contenido.value.length)

const seleccionarMood = (mood) => {
    moodSelec.value = mood
    moodScore.value = mood.score
}

const guardar = () => {
    if (contenido.value.trim().length < 10) return
    enviando.value = true
    router.post('/diario', {
        content:    contenido.value,
        mood:       moodSelec.value?.id ?? null,
        mood_score: moodScore.value,
        tags:       tagsArray.value,
    }, {
        onSuccess: () => {
            contenido.value  = ''
            moodSelec.value  = null
            moodScore.value  = 5
            tagsInput.value  = ''
            enviando.value   = false
            vistaActual.value = 'entradas'
        },
        onError: () => { enviando.value = false }
    })
}

const eliminar = (id) => {
    if (!confirm('¿Eliminar esta entrada?')) return
    router.delete(`/diario/${id}`)
}

const colorMood = (mood) =>
    moods.find(m => m.id === mood)?.color ?? '#fafafa'

const emojiMood = (mood) =>
    moods.find(m => m.id === mood)?.emoji ?? '📝'

// Calendario
const diasDelMes = computed(() => {
    const hoy    = new Date()
    const año    = hoy.getFullYear()
    const mes    = hoy.getMonth()
    const primer = new Date(año, mes, 1).getDay()
    const total  = new Date(año, mes + 1, 0).getDate()
    const blancos = primer === 0 ? 6 : primer - 1
    const dias = []
    for (let i = 0; i < blancos; i++) dias.push(null)
    for (let d = 1; d <= total; d++) dias.push(d)
    return dias
})

const getDia = (dia) => {
    if (!dia) return null
    const hoy = new Date()
    const key = `${hoy.getFullYear()}-${String(hoy.getMonth()+1).padStart(2,'0')}-${String(dia).padStart(2,'0')}`
    return props.calendario[key] ?? null
}

const esHoy = (dia) => dia === new Date().getDate()
</script>

<template>
    <AppLayout>
        <div class="diary-wrapper">

            <!-- Cabecera -->
            <div class="diary-header">
                <div>
                    <h1>📓 Mi diario emocional</h1>
                    <p>Un espacio privado para reflexionar y conocerte mejor</p>
                </div>
                <div class="dh-stats">
                    <div class="dhs-item">
                        <span class="dhs-val">{{ stats.total }}</span>
                        <span class="dhs-lab">Entradas</span>
                    </div>
                    <div class="dhs-item">
                        <span class="dhs-val">{{ stats.racha }}🔥</span>
                        <span class="dhs-lab">Días seguidos</span>
                    </div>
                    <div class="dhs-item">
                        <span class="dhs-val">{{ stats.esta_semana }}</span>
                        <span class="dhs-lab">Esta semana</span>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="diary-tabs">
                <button
                    v-for="tab in [
                        { id: 'escribir', label: '✍️ Escribir hoy' },
                        { id: 'entradas', label: '📚 Mis entradas' },
                        { id: 'calendario', label: '📅 Calendario' },
                    ]"
                    :key="tab.id"
                    class="dt-btn"
                    :class="{ activa: vistaActual === tab.id }"
                    @click="vistaActual = tab.id"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- ── ESCRIBIR ── -->
            <div v-if="vistaActual === 'escribir'" class="diary-escribir">

                <!-- Prompt del día -->
                <div class="prompt-dia">
                    <span>💭</span>
                    <div>
                        <span class="pd-label">Reflexión del día</span>
                        <p>{{ promptHoy }}</p>
                    </div>
                </div>

                <!-- Selector de mood -->
                <div class="mood-selector">
                    <p class="ms-label">¿Cómo te sientes ahora mismo?</p>
                    <div class="ms-grid">
                        <button
                            v-for="mood in moods"
                            :key="mood.id"
                            class="ms-btn"
                            :class="{ seleccionado: moodSelec?.id === mood.id }"
                            :style="moodSelec?.id === mood.id ? { backgroundColor: mood.color, borderColor: '#4ECDC4' } : {}"
                            @click="seleccionarMood(mood)"
                        >
                            <span class="ms-emoji">{{ mood.emoji }}</span>
                            <span class="ms-nombre">{{ mood.label }}</span>
                        </button>
                    </div>
                </div>

                <!-- Área de escritura -->
                <div class="escritura-area">
                    <div class="ea-toolbar">
                        <span class="ea-fecha">{{ new Date().toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' }) }}</span>
                        <span class="ea-chars" :class="{ warn: contadorChars > 4500 }">{{ contadorChars }}/5000</span>
                    </div>
                    <textarea
                        v-model="contenido"
                        :placeholder="`${promptHoy}\n\nEscribe libremente, sin juzgarte...`"
                        class="ea-textarea"
                        rows="12"
                        maxlength="5000"
                    ></textarea>
                </div>

                <!-- Tags -->
                <div class="tags-input">
                    <label>🏷️ Etiquetas (separadas por comas)</label>
                    <input
                        v-model="tagsInput"
                        type="text"
                        placeholder="trabajo, familia, estrés, logros..."
                        class="ti-input"
                    />
                    <div v-if="tagsArray.length > 0" class="ti-preview">
                        <span v-for="tag in tagsArray" :key="tag" class="ti-tag">{{ tag }}</span>
                    </div>
                </div>

                <button
                    class="btn-guardar-diary"
                    @click="guardar"
                    :disabled="contenido.trim().length < 10 || enviando"
                >
                    <span v-if="enviando">⏳ Guardando...</span>
                    <span v-else">💾 Guardar entrada</span>
                </button>

                <p class="diary-privacidad">
                    🔒 Tus entradas son completamente privadas. Solo tú puedes verlas.
                </p>

            </div>

            <!-- ── ENTRADAS ── -->
            <div v-if="vistaActual === 'entradas'">

                <div v-if="entradas.length === 0" class="diary-empty">
                    <span>📓</span>
                    <h3>Todavía no has escrito nada</h3>
                    <p>El diario es tu espacio privado para reflexionar. Escribe tu primera entrada hoy.</p>
                    <button class="btn-primera" @click="vistaActual = 'escribir'">
                        ✍️ Escribir primera entrada
                    </button>
                </div>

                <div v-else class="entradas-lista">
                    <div
                        v-for="entrada in entradas"
                        :key="entrada.id"
                        class="entrada-card"
                        :style="{ borderLeft: `4px solid ${colorMood(entrada.mood)}` }"
                    >
                        <div class="ec-header">
                            <div class="ec-meta">
                                <span class="ec-emoji">{{ emojiMood(entrada.mood) }}</span>
                                <div>
                                    <span class="ec-dia">{{ entrada.dia }}</span>
                                    <span class="ec-hora">{{ entrada.hora }} · {{ entrada.hace }}</span>
                                </div>
                            </div>
                            <div class="ec-acciones">
                                <div v-if="entrada.tags?.length" class="ec-tags">
                                    <span v-for="tag in entrada.tags.slice(0,3)" :key="tag" class="ec-tag">
                                        {{ tag }}
                                    </span>
                                </div>
                                <button class="ec-del" @click="eliminar(entrada.id)" title="Eliminar">🗑️</button>
                            </div>
                        </div>
                        <p class="ec-preview">{{ entrada.content.slice(0, 220) }}{{ entrada.content.length > 220 ? '...' : '' }}</p>
                        <button
                            v-if="entrada.content.length > 220"
                            class="ec-leer-mas"
                            @click="entradaVer = entradaVer?.id === entrada.id ? null : entrada"
                        >
                            {{ entradaVer?.id === entrada.id ? 'Leer menos ↑' : 'Leer más ↓' }}
                        </button>
                        <div v-if="entradaVer?.id === entrada.id" class="ec-completo">
                            {{ entrada.content }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── CALENDARIO ── -->
            <div v-if="vistaActual === 'calendario'" class="diary-calendario">

                <div class="dc-stats">
                    <div class="dcs-item" v-for="mood in moods" :key="mood.id"
                        :style="{ backgroundColor: mood.color }">
                        <span>{{ mood.emoji }}</span>
                        <span>{{ entradas.filter(e => e.mood === mood.id).length }} días</span>
                    </div>
                </div>

                <div class="cal-wrapper">
                    <div class="cal-dias-semana">
                        <span v-for="d in ['L','M','X','J','V','S','D']" :key="d">{{ d }}</span>
                    </div>
                    <div class="cal-grid">
                        <div
                            v-for="(dia, i) in diasDelMes"
                            :key="i"
                            class="cal-dia"
                            :class="{
                                vacio: !dia,
                                hoy: dia && esHoy(dia),
                                'con-entrada': dia && getDia(dia)
                            }"
                            :style="getDia(dia) ? { backgroundColor: colorMood(getDia(dia).mood) } : {}"
                            :title="getDia(dia) ? `${getDia(dia).count} entrada(s)` : ''"
                        >
                            <span class="cal-num">{{ dia }}</span>
                            <span v-if="getDia(dia)" class="cal-emoji">
                                {{ emojiMood(getDia(dia).mood) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="cal-leyenda">
                    <div v-for="mood in moods" :key="mood.id" class="cl-item">
                        <span class="cl-punto" :style="{ backgroundColor: mood.color }"></span>
                        <span>{{ mood.emoji }} {{ mood.label }}</span>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.diary-wrapper {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.diary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.diary-header h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
.diary-header p  { color: #666; margin: 0.25rem 0 0; font-size: 0.92rem; }

.dh-stats { display: flex; gap: 1rem; }

.dhs-item {
    background: white;
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    border: 1px solid #f0f0f0;
    text-align: center;
    min-width: 80px;
}

.dhs-val { display: block; font-size: 1.4rem; font-weight: 900; color: #4ECDC4; }
.dhs-lab { display: block; font-size: 0.72rem; color: #888; font-weight: 600; }

/* Tabs */
.diary-tabs { display: flex; gap: 0.5rem; flex-wrap: wrap; }

.dt-btn {
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

.dt-btn.activa { background: #4ECDC4; border-color: #4ECDC4; color: white; }

/* Escribir */
.diary-escribir { display: flex; flex-direction: column; gap: 1.25rem; }

.prompt-dia {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    background: linear-gradient(135deg, #E8FAF9, #f0fffe);
    border-radius: 14px;
    padding: 1.25rem;
    border-left: 4px solid #4ECDC4;
}

.prompt-dia span:first-child { font-size: 1.5rem; flex-shrink: 0; }
.pd-label { display: block; font-size: 0.72rem; font-weight: 700; color: #3BAFA7; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.25rem; }
.prompt-dia p { font-size: 0.95rem; color: #2D2D2D; margin: 0; font-style: italic; line-height: 1.5; }

/* Mood selector */
.mood-selector { display: flex; flex-direction: column; gap: 0.75rem; }
.ms-label { font-size: 0.88rem; font-weight: 700; color: #2D2D2D; margin: 0; }

.ms-grid { display: flex; gap: 0.6rem; flex-wrap: wrap; }

.ms-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1rem;
    border: 2px solid #f0f0f0;
    border-radius: 25px;
    background: white;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
}

.ms-btn:hover     { border-color: #4ECDC4; }
.ms-emoji         { font-size: 1.2rem; }
.ms-nombre        { font-size: 0.82rem; font-weight: 600; color: #2D2D2D; }

/* Escritura */
.escritura-area { display: flex; flex-direction: column; gap: 0; }

.ea-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f5f5f5;
    padding: 0.6rem 1rem;
    border-radius: 12px 12px 0 0;
    border: 1.5px solid #e8e8e8;
    border-bottom: none;
}

.ea-fecha { font-size: 0.8rem; color: #888; font-weight: 600; text-transform: capitalize; }
.ea-chars { font-size: 0.75rem; color: #aaa; }
.ea-chars.warn { color: #E63946; }

.ea-textarea {
    width: 100%;
    padding: 1.25rem;
    border: 1.5px solid #e8e8e8;
    border-radius: 0 0 12px 12px;
    font-size: 0.97rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    line-height: 1.8;
    min-height: 280px;
    color: #2D2D2D;
    transition: border-color 0.2s;
}

.ea-textarea:focus { border-color: #4ECDC4; }

/* Tags */
.tags-input { display: flex; flex-direction: column; gap: 0.5rem; }
.tags-input label { font-size: 0.85rem; font-weight: 700; color: #2D2D2D; }

.ti-input {
    padding: 0.7rem 1rem;
    border: 1.5px solid #e8e8e8;
    border-radius: 10px;
    font-size: 0.9rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
}

.ti-input:focus { border-color: #4ECDC4; }

.ti-preview { display: flex; flex-wrap: wrap; gap: 0.35rem; }

.ti-tag {
    padding: 0.25rem 0.7rem;
    background: #E8FAF9;
    border: 1.5px solid #4ECDC4;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #3BAFA7;
}

.btn-guardar-diary {
    padding: 0.95rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s, transform 0.2s;
}

.btn-guardar-diary:hover:not(:disabled) { background: #3BAFA7; transform: translateY(-1px); }
.btn-guardar-diary:disabled { opacity: 0.5; cursor: not-allowed; }

.diary-privacidad {
    text-align: center;
    font-size: 0.78rem;
    color: #aaa;
    margin: 0;
}

/* Entradas */
.diary-empty {
    text-align: center;
    padding: 3rem;
    background: #fafafa;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.diary-empty span { font-size: 3rem; }
.diary-empty h3   { font-size: 1.1rem; font-weight: 700; margin: 0; }
.diary-empty p    { font-size: 0.9rem; color: #666; margin: 0; }

.btn-primera {
    padding: 0.75rem 1.75rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    margin-top: 0.5rem;
}

.entradas-lista { display: flex; flex-direction: column; gap: 1rem; }

.entrada-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: box-shadow 0.2s;
}

.entrada-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }

.ec-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
}

.ec-meta  { display: flex; align-items: center; gap: 0.6rem; }
.ec-emoji { font-size: 1.5rem; }
.ec-dia   { display: block; font-size: 0.85rem; font-weight: 700; color: #2D2D2D; text-transform: capitalize; }
.ec-hora  { display: block; font-size: 0.72rem; color: #aaa; }

.ec-acciones { display: flex; align-items: center; gap: 0.5rem; }

.ec-tags { display: flex; gap: 0.3rem; flex-wrap: wrap; }

.ec-tag {
    padding: 0.15rem 0.55rem;
    background: #f0f0f0;
    border-radius: 10px;
    font-size: 0.7rem;
    color: #666;
    font-weight: 500;
}

.ec-del {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    opacity: 0.4;
    transition: opacity 0.2s;
    padding: 0;
}

.ec-del:hover { opacity: 1; }

.ec-preview {
    font-size: 0.9rem;
    color: #555;
    line-height: 1.7;
    margin: 0;
    white-space: pre-wrap;
}

.ec-leer-mas {
    background: none;
    border: none;
    color: #4ECDC4;
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    padding: 0;
    font-family: inherit;
}

.ec-completo {
    font-size: 0.9rem;
    color: #444;
    line-height: 1.7;
    white-space: pre-wrap;
    background: #fafafa;
    border-radius: 8px;
    padding: 1rem;
}

/* Calendario */
.diary-calendario { display: flex; flex-direction: column; gap: 1.25rem; }

.dc-stats {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.dcs-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.85rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #2D2D2D;
}

.cal-wrapper {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
}

.cal-dias-semana {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin-bottom: 6px;
}

.cal-dias-semana span {
    text-align: center;
    font-size: 0.72rem;
    font-weight: 700;
    color: #aaa;
    padding: 0.25rem 0;
}

.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
}

.cal-dia {
    aspect-ratio: 1;
    border-radius: 8px;
    border: 1.5px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1px;
    transition: transform 0.15s;
}

.cal-dia:not(.vacio):hover { transform: scale(1.08); cursor: pointer; }
.cal-dia.vacio  { border: none; background: transparent; }
.cal-dia.hoy    { border-color: #4ECDC4; background: #E8FAF9; }

.cal-num  { font-size: 0.72rem; font-weight: 600; color: #2D2D2D; }
.cal-emoji { font-size: 0.65rem; }

.cal-leyenda {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.cl-item {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    color: #555;
}

.cl-punto {
    width: 10px;
    height: 10px;
    border-radius: 3px;
    flex-shrink: 0;
}
</style>