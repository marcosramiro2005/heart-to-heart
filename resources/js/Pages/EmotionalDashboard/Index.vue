<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    evolucion:    Object,
    distribucion: Object,
    racha:        Object,
    registroHoy:  Object,
    stats:        Object,
    historial:    Array,
})

const mostrarFormulario = ref(!props.registroHoy)
const chartLinea  = ref(null)
const chartDona   = ref(null)

const emociones = [
    { label: '😊 Alegría',   color: '#FFD700' },
    { label: '😌 Calma',     color: '#4ECDC4' },
    { label: '😰 Ansiedad',  color: '#FF8C42' },
    { label: '😢 Tristeza',  color: '#6B9FD4' },
    { label: '😠 Enfado',    color: '#E63946' },
    { label: '😴 Cansancio', color: '#9B8EC4' },
]

const actividades = [
    '🏠 En casa', '💼 Trabajando', '🎓 Estudiando',
    '🏃 Ejercicio', '👥 Con amigos', '🌿 En naturaleza'
]

const form = useForm({
    emotion:   '',
    intensity: 5,
    notes:     '',
    activity:  '',
})

const submit = () => {
    form.post('/mis-emociones/registrar', {
        onSuccess: () => mostrarFormulario.value = false
    })
}

const exportarPDF = async () => {
    window.print()
}

onMounted(async () => {
    const Chart = (await import('chart.js/auto')).default

    // ── Gráfica de línea — evolución ──
    if (chartLinea.value && Object.keys(props.evolucion).length > 0) {
        new Chart(chartLinea.value, {
            type: 'line',
            data: {
                labels: Object.keys(props.evolucion),
                datasets: [{
                    label: 'Intensidad emocional',
                    data: Object.values(props.evolucion),
                    borderColor: '#4ECDC4',
                    backgroundColor: 'rgba(78,205,196,0.1)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#4ECDC4',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `Intensidad: ${ctx.raw}/10`
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 10,
                        ticks: { stepSize: 2 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        })
    }

    // ── Gráfica de dona — distribución ──
    if (chartDona.value && Object.keys(props.distribucion).length > 0) {
        const colores = Object.keys(props.distribucion).map(e =>
            emociones.find(em => em.label === e)?.color ?? '#ccc'
        )

        new Chart(chartDona.value, {
            type: 'doughnut',
            data: {
                labels: Object.keys(props.distribucion),
                datasets: [{
                    data: Object.values(props.distribucion),
                    backgroundColor: colores,
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, font: { size: 12 } }
                    }
                },
                cutout: '65%',
            }
        })
    }
})
</script>

<template>
    <AppLayout>
        <div class="ed-wrapper">

            <!-- Cabecera -->
            <div class="ed-hero">
                <div>
                    <h1>📊 Mis emociones</h1>
                    <p>Conoce tus patrones emocionales y cuida tu bienestar</p>
                </div>
                <button class="btn-exportar" @click="exportarPDF">
                    📄 Exportar informe
                </button>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="stats-grid">
                <div class="stat-card racha">
                    <span class="stat-emoji">{{ racha.emoji }}</span>
                    <div class="stat-info">
                        <span class="stat-valor">{{ racha.dias }} días</span>
                        <span class="stat-label">{{ racha.mensaje }}</span>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-emoji">📝</span>
                    <div class="stat-info">
                        <span class="stat-valor">{{ stats.total_registros }}</span>
                        <span class="stat-label">Registros totales</span>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-emoji">💫</span>
                    <div class="stat-info">
                        <span class="stat-valor">{{ stats.emocion_frecuente }}</span>
                        <span class="stat-label">Emoción frecuente</span>
                    </div>
                </div>
                <div class="stat-card">
                    <span class="stat-emoji">🫁</span>
                    <div class="stat-info">
                        <span class="stat-valor">{{ stats.sesiones_respira }}</span>
                        <span class="stat-label">Sesiones respiración</span>
                    </div>
                </div>
            </div>

            <!-- Registro de hoy -->
            <div class="registro-hoy-section">
                <div class="section-header">
                    <h2>¿Cómo te sientes hoy?</h2>
                    <button
                        v-if="registroHoy && !mostrarFormulario"
                        class="btn-editar"
                        @click="mostrarFormulario = true"
                    >
                        ✏️ Editar
                    </button>
                </div>

                <!-- Ya registrado hoy -->
                <div v-if="registroHoy && !mostrarFormulario" class="registrado-hoy">
                    <div class="registrado-badge"
                        :style="{ backgroundColor: registroHoy.color ?? '#4ECDC4' }">
                        <span>{{ registroHoy.emotion }}</span>
                    </div>
                    <div class="registrado-info">
                        <div class="intensidad-bar">
                            <span>Intensidad: {{ registroHoy.intensity }}/10</span>
                            <div class="bar-fondo">
                                <div
                                    class="bar-relleno"
                                    :style="{
                                        width: `${registroHoy.intensity * 10}%`,
                                        backgroundColor: registroHoy.color ?? '#4ECDC4'
                                    }"
                                ></div>
                            </div>
                        </div>
                        <p v-if="registroHoy.notes" class="registrado-nota">
                            "{{ registroHoy.notes }}"
                        </p>
                    </div>
                </div>

                <!-- Formulario de registro -->
                <div v-if="mostrarFormulario" class="form-registro">
                    <form @submit.prevent="submit">

                        <div class="field">
                            <label>¿Qué emoción predomina?</label>
                            <div class="emociones-selector">
                                <button
                                    v-for="em in emociones"
                                    :key="em.label"
                                    type="button"
                                    class="em-btn"
                                    :class="{ selected: form.emotion === em.label }"
                                    :style="form.emotion === em.label ? { backgroundColor: em.color, borderColor: em.color } : {}"
                                    @click="form.emotion = em.label"
                                >
                                    {{ em.label }}
                                </button>
                            </div>
                            <span v-if="form.errors.emotion" class="error">{{ form.errors.emotion }}</span>
                        </div>

                        <div class="field">
                            <label>Intensidad: <strong>{{ form.intensity }}/10</strong></label>
                            <input
                                type="range"
                                v-model="form.intensity"
                                min="1"
                                max="10"
                                class="intensidad-slider"
                            />
                            <div class="slider-labels">
                                <span>Muy leve</span>
                                <span>Moderada</span>
                                <span>Muy intensa</span>
                            </div>
                        </div>

                        <div class="field">
                            <label>¿Qué estás haciendo hoy?</label>
                            <div class="actividades-selector">
                                <button
                                    v-for="act in actividades"
                                    :key="act"
                                    type="button"
                                    class="act-btn"
                                    :class="{ selected: form.activity === act }"
                                    @click="form.activity = act"
                                >
                                    {{ act }}
                                </button>
                            </div>
                        </div>

                        <div class="field">
                            <label>Nota personal (opcional)</label>
                            <textarea
                                v-model="form.notes"
                                placeholder="¿Qué ha pasado hoy? ¿Cómo lo estás llevando?"
                                rows="3"
                                maxlength="300"
                            ></textarea>
                            <span class="char-count">{{ form.notes.length }}/300</span>
                        </div>

                        <button
                            type="submit"
                            class="btn-guardar"
                            :disabled="form.processing || !form.emotion"
                        >
                            {{ form.processing ? 'Guardando...' : '💚 Guardar emoción de hoy' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Gráficas -->
            <div class="graficas-grid">

                <!-- Evolución últimos 30 días -->
                <div class="grafica-card grande">
                    <h2>Evolución últimos 30 días</h2>
                    <div v-if="Object.keys(evolucion).length === 0" class="grafica-empty">
                        Registra tus emociones para ver tu evolución 📈
                    </div>
                    <canvas v-else ref="chartLinea" height="100"></canvas>
                </div>

                <!-- Distribución emocional -->
                <div class="grafica-card">
                    <h2>Distribución emocional</h2>
                    <div v-if="Object.keys(distribucion).length === 0" class="grafica-empty">
                        Sin datos todavía 🌱
                    </div>
                    <canvas v-else ref="chartDona"></canvas>
                </div>

            </div>

            <!-- Historial reciente -->
            <div class="historial-section">
                <h2>Historial reciente</h2>
                <div v-if="historial.length === 0" class="historial-empty">
                    Aún no tienes registros. ¡Empieza hoy! 💚
                </div>
                <div class="historial-lista">
                    <div
                        v-for="registro in historial"
                        :key="registro.id"
                        class="historial-item"
                    >
                        <div
                            class="historial-color"
                            :style="{ backgroundColor: registro.color }"
                        ></div>
                        <div class="historial-info">
                            <span class="historial-emocion">{{ registro.emotion }}</span>
                            <span class="historial-nota" v-if="registro.notes">
                                {{ registro.notes }}
                            </span>
                        </div>
                        <div class="historial-right">
                            <span class="historial-intensidad">{{ registro.intensity }}/10</span>
                            <span class="historial-fecha">{{ registro.recorded_at }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.ed-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* ── Hero ── */
.ed-hero {
    background: #4ECDC4;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.ed-hero h1 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0 0 0.3rem;
}

.ed-hero p { color: #2D2D2D; margin: 0; font-size: 0.95rem; }

.btn-exportar {
    padding: 0.7rem 1.4rem;
    background: white;
    color: #3BAFA7;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: transform 0.2s;
}

.btn-exportar:hover { transform: translateY(-2px); }

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
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid #f0f0f0;
    transition: box-shadow 0.2s;
}

.stat-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.07); }
.stat-card.racha { border-left: 4px solid #4ECDC4; }

.stat-emoji { font-size: 2rem; }

.stat-info {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.stat-valor {
    font-size: 1.1rem;
    font-weight: 800;
    color: #2D2D2D;
}

.stat-label {
    font-size: 0.78rem;
    color: #888;
}

/* ── Registro hoy ── */
.registro-hoy-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
}

.section-header h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
}

.btn-editar {
    padding: 0.4rem 0.9rem;
    background: #E8FAF9;
    border: none;
    border-radius: 20px;
    color: #3BAFA7;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
}

.registrado-hoy {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.registrado-badge {
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    white-space: nowrap;
}

.registrado-info { flex: 1; }

.intensidad-bar { display: flex; flex-direction: column; gap: 0.4rem; }
.intensidad-bar span { font-size: 0.85rem; color: #666; }

.bar-fondo {
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

.bar-relleno {
    height: 100%;
    border-radius: 4px;
    transition: width 0.5s ease;
}

.registrado-nota {
    font-size: 0.88rem;
    color: #888;
    font-style: italic;
    margin: 0.5rem 0 0;
}

/* ── Formulario ── */
.form-registro { display: flex; flex-direction: column; gap: 1.25rem; }

.field { display: flex; flex-direction: column; gap: 0.5rem; }

.field label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #2D2D2D;
}

.emociones-selector, .actividades-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.em-btn, .act-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    font-size: 0.88rem;
    cursor: pointer;
    transition: all 0.2s;
    color: #2D2D2D;
}

.em-btn.selected { color: white; }
.act-btn.selected { background: #4ECDC4; border-color: #4ECDC4; color: white; }

.intensidad-slider {
    width: 100%;
    accent-color: #4ECDC4;
    height: 6px;
}

.slider-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #aaa;
}

.field textarea {
    padding: 0.75rem 1rem;
    border: 2px solid #e8f5f4;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    transition: border-color 0.2s;
    background: #fafafa;
}

.field textarea:focus { border-color: #4ECDC4; }

.char-count { font-size: 0.75rem; color: #aaa; text-align: right; }

.btn-guardar {
    padding: 0.9rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background 0.2s;
}

.btn-guardar:hover:not(:disabled) { background: #3BAFA7; }
.btn-guardar:disabled { opacity: 0.5; cursor: not-allowed; }

.error { font-size: 0.78rem; color: #E63946; }

/* ── Gráficas ── */
.graficas-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.25rem;
}

.grafica-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
}

.grafica-card h2 {
    font-size: 1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0 0 1.25rem;
}

.grafica-empty {
    text-align: center;
    color: #aaa;
    padding: 2rem;
    font-size: 0.9rem;
}

/* ── Historial ── */
.historial-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
}

.historial-section h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0 0 1rem;
}

.historial-empty {
    text-align: center;
    color: #aaa;
    padding: 2rem;
}

.historial-lista { display: flex; flex-direction: column; gap: 0.6rem; }

.historial-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.85rem 1rem;
    background: #fafafa;
    border-radius: 10px;
    transition: background 0.2s;
}

.historial-item:hover { background: #f0f0f0; }

.historial-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.historial-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.historial-emocion {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2D2D2D;
}

.historial-nota {
    font-size: 0.8rem;
    color: #888;
    font-style: italic;
}

.historial-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.2rem;
}

.historial-intensidad {
    font-size: 0.85rem;
    font-weight: 700;
    color: #4ECDC4;
}

.historial-fecha {
    font-size: 0.75rem;
    color: #aaa;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .graficas-grid { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
    .stats-grid { grid-template-columns: 1fr; }
}

/* ── Print ── */
@media print {
    .btn-exportar, .form-registro, .btn-editar { display: none; }
    .ed-wrapper { padding: 0; }
}
</style>