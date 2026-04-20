<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    graficaLinea:     Array,
    graficaDona:      Array,
    calendario:       Object,
    stats:            Object,
    insights:         Array,
    ultimosRegistros: Array,
    mesActual:        String,
})

// ── Registro de emoción ──
const emociones = [
    { id: 'alegría',   emoji: '😊', label: 'Alegría',   color: '#FFD700' },
    { id: 'calma',     emoji: '😌', label: 'Calma',     color: '#4ECDC4' },
    { id: 'ansiedad',  emoji: '😰', label: 'Ansiedad',  color: '#FF8C42' },
    { id: 'tristeza',  emoji: '😢', label: 'Tristeza',  color: '#6B9FD4' },
    { id: 'enfado',    emoji: '😠', label: 'Enfado',    color: '#E63946' },
    { id: 'cansancio', emoji: '😴', label: 'Cansancio', color: '#9B8EC4' },
]

const emocionSeleccionada = ref('')
const intensidad          = ref(5)
const nota                = ref('')
const enviando            = ref(false)
const registrado          = ref(false)

const colorEmocion = (id) => emociones.find(e => e.id === id)?.color ?? '#4ECDC4'

const registrarEmocion = async () => {
    if (!emocionSeleccionada.value) return
    enviando.value = true
    try {
        await axios.post('/mis-emociones/registrar', {
            emotion:   emocionSeleccionada.value,
            intensity: intensidad.value,
            note:      nota.value,
        })
        registrado.value         = true
        emocionSeleccionada.value = ''
        intensidad.value          = 5
        nota.value                = ''
        setTimeout(() => registrado.value = false, 3000)
    } catch (e) {
        console.error(e)
    } finally {
        enviando.value = false
    }
}

// ── Calendario ──
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

const nombreMes = computed(() => {
    return new Date().toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })
})

const getDiaCalendario = (dia) => {
    if (!dia) return null
    const hoy  = new Date()
    const key  = `${hoy.getFullYear()}-${String(hoy.getMonth()+1).padStart(2,'0')}-${String(dia).padStart(2,'0')}`
    return props.calendario[key] ?? null
}

const esHoy = (dia) => {
    return dia === new Date().getDate()
}

// ── Insights ──
const iconoInsight = (tipo) => ({
    logro:       '🏆',
    positivo:    '✨',
    atencion:    '💙',
    info:        '💡',
    recordatorio:'📓',
}[tipo] ?? '💡')

const colorInsight = (tipo) => ({
    logro:       '#fff9c4',
    positivo:    '#d4edda',
    atencion:    '#d0eaf8',
    info:        '#fafafa',
    recordatorio:'#ffecd2',
}[tipo] ?? '#fafafa')

// ── Gráfica simple de barras ──
const maxIntensidad = computed(() => {
    if (!props.graficaLinea?.length) return 10
    return Math.max(...props.graficaLinea.map(d => d.intensidad), 10)
})
</script>

<template>
    <AppLayout>
        <div class="ed-wrapper">

            <!-- Cabecera -->
            <div class="ed-header">
                <div>
                    <h1>📊 Mis emociones</h1>
                    <p>Registra cómo te sientes y descubre tus patrones emocionales</p>
                </div>
                <div class="ed-racha">
                    <span class="racha-num">{{ stats.racha_actual }}</span>
                    <span class="racha-label">días seguidos 🔥</span>
                </div>
            </div>

            <!-- Stats rápidas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="sc-valor">{{ stats.total_registros }}</span>
                    <span class="sc-label">Registros totales</span>
                </div>
                <div class="stat-card">
                    <span class="sc-valor">{{ stats.dias_registrados }}</span>
                    <span class="sc-label">Días este mes</span>
                </div>
                <div class="stat-card">
                    <span class="sc-valor">{{ stats.intensidad_media }}</span>
                    <span class="sc-label">Intensidad media</span>
                </div>
                <div class="stat-card">
                    <span class="sc-valor" style="font-size: 1.3rem">
                        {{ emociones.find(e => e.id === stats.emocion_frecuente)?.emoji || '💙' }}
                    </span>
                    <span class="sc-label">Más frecuente</span>
                </div>
            </div>

            <div class="ed-grid">

                <!-- Columna izquierda -->
                <div class="ed-col">

                    <!-- Registrar emoción -->
                    <div class="ed-card">
                        <h2>¿Cómo te sientes ahora?</h2>

                        <div class="emociones-grid">
                            <button
                                v-for="em in emociones"
                                :key="em.id"
                                class="em-btn"
                                :class="{ activa: emocionSeleccionada === em.id }"
                                :style="emocionSeleccionada === em.id
                                    ? { backgroundColor: em.color + '30', borderColor: em.color }
                                    : {}"
                                @click="emocionSeleccionada = em.id"
                            >
                                <span class="em-emoji">{{ em.emoji }}</span>
                                <span class="em-label">{{ em.label }}</span>
                            </button>
                        </div>

                        <div v-if="emocionSeleccionada" class="intensidad-control">
                            <label>
                                Intensidad:
                                <strong :style="{ color: colorEmocion(emocionSeleccionada) }">
                                    {{ intensidad }}/10
                                </strong>
                            </label>
                            <input
                                type="range"
                                v-model="intensidad"
                                min="1"
                                max="10"
                                :style="{ accentColor: colorEmocion(emocionSeleccionada) }"
                            />
                            <div class="int-labels">
                                <span>Suave</span>
                                <span>Moderado</span>
                                <span>Intenso</span>
                            </div>
                        </div>

                        <textarea
                            v-model="nota"
                            placeholder="¿Quieres añadir una nota? (opcional)"
                            rows="3"
                            class="nota-input"
                        ></textarea>

                        <div v-if="registrado" class="registro-ok">
                            ✅ ¡Emoción registrada! Sigue cuidándote 💚
                        </div>

                        <button
                            class="btn-registrar"
                            @click="registrarEmocion"
                            :disabled="!emocionSeleccionada || enviando"
                        >
                            {{ enviando ? 'Guardando...' : '💾 Registrar emoción' }}
                        </button>
                    </div>

                    <!-- Insights -->
                    <div class="ed-card">
                        <h2>💡 Insights personalizados</h2>
                        <div class="insights-lista">
                            <div
                                v-for="(insight, i) in insights"
                                :key="i"
                                class="insight-item"
                                :style="{ backgroundColor: colorInsight(insight.tipo) }"
                            >
                                <span class="insight-icono">{{ iconoInsight(insight.tipo) }}</span>
                                <p>{{ insight.mensaje }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Últimos registros -->
                    <div class="ed-card">
                        <h2>🕐 Últimos registros</h2>
                        <div v-if="ultimosRegistros.length === 0" class="empty-state">
                            No hay registros aún. ¡Registra tu primera emoción!
                        </div>
                        <div v-else class="registros-lista">
                            <div
                                v-for="reg in ultimosRegistros"
                                :key="reg.id"
                                class="reg-item"
                                :style="{ borderLeft: `4px solid ${colorEmocion(reg.emotion)}` }"
                            >
                                <span class="reg-emoji">{{ reg.emoji }}</span>
                                <div class="reg-info">
                                    <span class="reg-emocion">{{ reg.emotion }}</span>
                                    <span class="reg-nota" v-if="reg.note">{{ reg.note }}</span>
                                    <span class="reg-fecha">{{ reg.fecha }} · {{ reg.hora }}</span>
                                </div>
                                <div class="reg-intensidad">
                                    <span
                                        class="reg-int-badge"
                                        :style="{ backgroundColor: colorEmocion(reg.emotion) + '25', color: colorEmocion(reg.emotion) }"
                                    >
                                        {{ reg.intensity }}/10
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Columna derecha -->
                <div class="ed-col">

                    <!-- Calendario emocional -->
                    <div class="ed-card">
                        <h2>📅 Calendario emocional</h2>
                        <p class="cal-mes">{{ nombreMes }}</p>

                        <div class="calendario">
                            <div class="cal-dias-semana">
                                <span v-for="d in ['L','M','X','J','V','S','D']" :key="d">{{ d }}</span>
                            </div>
                            <div class="cal-grid">
                                <div
                                    v-for="(dia, i) in diasDelMes"
                                    :key="i"
                                    class="cal-dia"
                                    :class="{
                                        'vacio': !dia,
                                        'hoy': dia && esHoy(dia),
                                        'con-registro': dia && getDiaCalendario(dia)
                                    }"
                                    :style="getDiaCalendario(dia)
                                        ? { backgroundColor: colorEmocion(getDiaCalendario(dia).emocion) + '30', borderColor: colorEmocion(getDiaCalendario(dia).emocion) }
                                        : {}"
                                    :title="getDiaCalendario(dia) ? `${getDiaCalendario(dia).emocion} (${getDiaCalendario(dia).intensidad}/10)` : ''"
                                >
                                    <span class="cal-num">{{ dia }}</span>
                                    <span v-if="getDiaCalendario(dia)" class="cal-emoji">
                                        {{ emociones.find(e => e.id === getDiaCalendario(dia).emocion)?.emoji }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="cal-leyenda">
                            <div v-for="em in emociones" :key="em.id" class="ley-item">
                                <span class="ley-punto" :style="{ backgroundColor: em.color }"></span>
                                <span>{{ em.emoji }} {{ em.label }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfica de barras últimos 14 días -->
                    <div class="ed-card">
                        <h2>📈 Intensidad últimos 14 días</h2>
                        <div v-if="graficaLinea.length === 0" class="empty-state">
                            Sin datos suficientes aún
                        </div>
                        <div v-else class="grafica-barras">
                            <div
                                v-for="(punto, i) in graficaLinea.slice(-14)"
                                :key="i"
                                class="gb-col"
                            >
                                <div class="gb-barra-wrap">
                                    <div
                                        class="gb-barra"
                                        :style="{
                                            height: `${(punto.intensidad / maxIntensidad) * 100}%`,
                                            backgroundColor: punto.intensidad >= 7 ? '#E63946' : punto.intensidad >= 4 ? '#FF8C42' : '#4ECDC4'
                                        }"
                                    ></div>
                                </div>
                                <span class="gb-label">{{ punto.fecha?.slice(-2) }}</span>
                            </div>
                        </div>
                        <div class="grafica-leyenda">
                            <span class="gl-item" style="color: #4ECDC4">● Baja</span>
                            <span class="gl-item" style="color: #FF8C42">● Media</span>
                            <span class="gl-item" style="color: #E63946">● Alta</span>
                        </div>
                    </div>

                    <!-- Distribución de emociones -->
                    <div class="ed-card">
                        <h2>🥧 Este mes</h2>
                        <div v-if="graficaDona.length === 0" class="empty-state">
                            Sin datos este mes
                        </div>
                        <div v-else class="distribucion">
                            <div
                                v-for="item in graficaDona"
                                :key="item.emocion"
                                class="dist-item"
                            >
                                <div class="dist-info">
                                    <span>{{ emociones.find(e => e.id === item.emocion)?.emoji }}</span>
                                    <span class="dist-nombre">{{ item.emocion }}</span>
                                </div>
                                <div class="dist-barra-wrap">
                                    <div
                                        class="dist-barra"
                                        :style="{
                                            width: `${(item.total / Math.max(...graficaDona.map(d => d.total))) * 100}%`,
                                            backgroundColor: colorEmocion(item.emocion)
                                        }"
                                    ></div>
                                </div>
                                <span class="dist-total">{{ item.total }}x</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.ed-wrapper {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.ed-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.ed-header h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
.ed-header p  { color: #666; margin: 0.25rem 0 0; }

.ed-racha {
    background: #fff9c4;
    border: 2px solid #FFD700;
    border-radius: 16px;
    padding: 0.75rem 1.5rem;
    text-align: center;
}

.racha-num   { display: block; font-size: 2rem; font-weight: 900; color: #f57f17; }
.racha-label { display: block; font-size: 0.78rem; color: #888; font-weight: 600; }

/* ── Stats ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.stat-card {
    background: white;
    border-radius: 14px;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
    text-align: center;
}

.sc-valor { font-size: 1.8rem; font-weight: 900; color: #4ECDC4; }
.sc-label { font-size: 0.78rem; color: #888; font-weight: 600; }

/* ── Grid principal ── */
.ed-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    align-items: start;
}

.ed-col { display: flex; flex-direction: column; gap: 1.5rem; }

.ed-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.ed-card h2 { font-size: 1rem; font-weight: 700; margin: 0; color: #2D2D2D; }

/* ── Emociones ── */
.emociones-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}

.em-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    padding: 0.75rem 0.5rem;
    border: 2px solid #f0f0f0;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.em-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
.em-btn.activa { transform: translateY(-2px); }

.em-emoji { font-size: 1.5rem; }
.em-label { font-size: 0.75rem; font-weight: 600; color: #2D2D2D; }

/* ── Intensidad ── */
.intensidad-control {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.intensidad-control label { font-size: 0.88rem; font-weight: 600; color: #2D2D2D; }
.intensidad-control input { width: 100%; height: 6px; cursor: pointer; }

.int-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.72rem;
    color: #aaa;
}

.nota-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #f0f0f0;
    border-radius: 10px;
    font-size: 0.9rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    transition: border-color 0.2s;
}

.nota-input:focus { border-color: #4ECDC4; }

.registro-ok {
    background: #E8FAF9;
    border: 1.5px solid #4ECDC4;
    border-radius: 10px;
    padding: 0.6rem 1rem;
    font-size: 0.85rem;
    color: #3BAFA7;
    font-weight: 600;
    text-align: center;
}

.btn-registrar {
    padding: 0.85rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background 0.2s;
    width: 100%;
}

.btn-registrar:hover:not(:disabled) { background: #3BAFA7; }
.btn-registrar:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Insights ── */
.insights-lista { display: flex; flex-direction: column; gap: 0.6rem; }

.insight-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    border-radius: 10px;
}

.insight-icono { font-size: 1.2rem; flex-shrink: 0; }
.insight-item p { font-size: 0.85rem; color: #444; line-height: 1.5; margin: 0; }

/* ── Últimos registros ── */
.registros-lista { display: flex; flex-direction: column; gap: 0.5rem; }

.reg-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 0.75rem;
    background: #fafafa;
    border-radius: 8px;
    padding-left: 0.75rem;
}

.reg-emoji { font-size: 1.3rem; flex-shrink: 0; }
.reg-info  { flex: 1; display: flex; flex-direction: column; gap: 0.1rem; }
.reg-emocion { font-size: 0.85rem; font-weight: 700; color: #2D2D2D; text-transform: capitalize; }
.reg-nota    { font-size: 0.78rem; color: #666; }
.reg-fecha   { font-size: 0.72rem; color: #aaa; }

.reg-int-badge {
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 700;
}

/* ── Calendario ── */
.cal-mes { font-size: 0.88rem; color: #888; text-align: center; text-transform: capitalize; margin: 0; }

.cal-dias-semana {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    margin-bottom: 4px;
}

.cal-dias-semana span {
    text-align: center;
    font-size: 0.72rem;
    font-weight: 700;
    color: #aaa;
    padding: 0.3rem 0;
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
    cursor: default;
    transition: transform 0.1s;
}

.cal-dia:not(.vacio):hover { transform: scale(1.05); }
.cal-dia.vacio { border: none; background: transparent; }

.cal-dia.hoy {
    border-color: #4ECDC4;
    background: #E8FAF9;
}

.cal-num  { font-size: 0.72rem; font-weight: 600; color: #2D2D2D; }
.cal-emoji { font-size: 0.7rem; }

.cal-leyenda {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.ley-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.72rem;
    color: #666;
}

.ley-punto {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* ── Gráfica barras ── */
.grafica-barras {
    display: flex;
    align-items: flex-end;
    gap: 4px;
    height: 120px;
    padding: 0 0.5rem;
}

.gb-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    gap: 4px;
}

.gb-barra-wrap {
    flex: 1;
    width: 100%;
    display: flex;
    align-items: flex-end;
}

.gb-barra {
    width: 100%;
    border-radius: 4px 4px 0 0;
    min-height: 4px;
    transition: height 0.3s ease;
}

.gb-label { font-size: 0.65rem; color: #aaa; }

.grafica-leyenda {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.gl-item { font-size: 0.78rem; font-weight: 600; }

/* ── Distribución ── */
.distribucion { display: flex; flex-direction: column; gap: 0.6rem; }

.dist-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.dist-info {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    width: 90px;
    flex-shrink: 0;
}

.dist-nombre { font-size: 0.78rem; font-weight: 600; color: #2D2D2D; text-transform: capitalize; }

.dist-barra-wrap {
    flex: 1;
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

.dist-barra {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
    min-width: 4px;
}

.dist-total { font-size: 0.75rem; color: #888; font-weight: 600; min-width: 24px; text-align: right; }

.empty-state { color: #aaa; font-size: 0.88rem; text-align: center; padding: 1rem; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .ed-grid   { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 480px) {
    .stats-grid     { grid-template-columns: repeat(2, 1fr); }
    .emociones-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>