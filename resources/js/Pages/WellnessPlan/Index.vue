<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
    planActual: Object,
    historial:  Array,
    objetivo:   String,
})

const objetivoSelec = ref(props.objetivo ?? 'general')
const generando     = ref(false)

const objetivos = [
    { id: 'ansiedad',   label: 'Gestionar la ansiedad',   emoji: '😰', color: '#d0eaf8' },
    { id: 'tristeza',   label: 'Superar la tristeza',      emoji: '😢', color: '#d4edda' },
    { id: 'estres',     label: 'Reducir el estrés',        emoji: '😤', color: '#ffd5d5' },
    { id: 'sueno',      label: 'Dormir mejor',             emoji: '😴', color: '#e8eaf6' },
    { id: 'general',    label: 'Bienestar general',        emoji: '✨', color: '#fff9c4' },
]

const diasSemana = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo']

const hoy = new Date().toLocaleDateString('es-ES', { weekday: 'long' })
    .replace(/^\w/, c => c.toUpperCase())

const estaHecho = (dia) => {
    if (!props.planActual) return false
    return props.planActual.dias_check?.some(d => {
        const fecha = new Date(d)
        return fecha.toLocaleDateString('es-ES', { weekday: 'long' })
            .replace(/^\w/, c => c.toUpperCase()) === dia
    })
}

const esHoy = (dia) => dia === hoy

const generar = () => {
    generando.value = true
    router.post('/mi-plan/generar', {
        objetivo: objetivoSelec.value
    }, {
        onSuccess: () => {
            generando.value = false
        },
        onError: () => {
            generando.value = false
        },
        onFinish: () => {
            generando.value = false
        }
    })
}

const completarDia = () => {
    if (!props.planActual) return
    router.post(`/mi-plan/${props.planActual.id}/completar`)
}

const colorObj = (id) => objetivos.find(o => o.id === id)?.color ?? '#fafafa'
const emojiObj = (id) => objetivos.find(o => o.id === id)?.emoji ?? '✨'
</script>

<template>
    <AppLayout>
        <div class="plan-wrapper">

            <!-- Cabecera -->
            <div class="plan-header">
                <div>
                    <h1>🌱 Mi plan semanal</h1>
                    <p>Un plan de bienestar personalizado según tu objetivo</p>
                </div>
                <div v-if="planActual" class="ph-progreso">
                    <div class="php-circulo">
                        <svg viewBox="0 0 60 60" width="60" height="60">
                            <circle cx="30" cy="30" r="24" fill="none"
                                stroke="#f0f0f0" stroke-width="5"/>
                            <circle cx="30" cy="30" r="24" fill="none"
                                stroke="#4ECDC4" stroke-width="5"
                                stroke-linecap="round"
                                :stroke-dasharray="150.8"
                                :stroke-dashoffset="150.8 * (1 - planActual.progreso / 100)"
                                transform="rotate(-90 30 30)"
                                style="transition: stroke-dashoffset 0.5s ease"
                            />
                            <text x="30" y="30" text-anchor="middle"
                                dominant-baseline="central"
                                fill="#2D2D2D" font-size="11"
                                font-weight="900" font-family="Arial">
                                {{ planActual.progreso }}%
                            </text>
                        </svg>
                    </div>
                    <div>
                        <span class="php-label">Progreso semanal</span>
                        <span class="php-dias">{{ planActual.dias_completados }}/7 días</span>
                    </div>
                </div>
            </div>

            <!-- Sin plan — Generador -->
            <div v-if="!planActual" class="plan-generador">
                <div class="pg-icon">🌱</div>
                <h2>Genera tu plan de esta semana</h2>
                <p>Selecciona tu objetivo principal y crearemos un plan de 7 días con técnicas personalizadas para ti.</p>

                <div class="pg-objetivos">
                    <button
                        v-for="obj in objetivos"
                        :key="obj.id"
                        class="pgo-btn"
                        :class="{ seleccionado: objetivoSelec === obj.id }"
                        :style="objetivoSelec === obj.id ? { backgroundColor: obj.color, borderColor: '#4ECDC4' } : {}"
                        @click="objetivoSelec = obj.id"
                    >
                        <span class="pgob-emoji">{{ obj.emoji }}</span>
                        <span class="pgob-label">{{ obj.label }}</span>
                    </button>
                </div>

                <button class="btn-generar" @click="generar" :disabled="generando">
                    <span v-if="generando">⏳ Generando plan...</span>
                    <span v-else>🌱 Generar mi plan semanal</span>
                </button>
            </div>

            <!-- Plan activo -->
            <div v-if="planActual" class="plan-activo">

                <!-- Info del plan -->
                <div class="pa-info" :style="{ backgroundColor: colorObj(planActual.objetivo) }">
                    <div class="pai-left">
                        <span class="pai-emoji">{{ emojiObj(planActual.objetivo) }}</span>
                        <div>
                            <h3>{{ objetivos.find(o => o.id === planActual.objetivo)?.label ?? 'Bienestar' }}</h3>
                            <span>Semana del {{ planActual.semana_inicio }}</span>
                        </div>
                    </div>
                    <div class="pai-right">
                        <button
                            v-if="!estaHecho(hoy)"
                            class="btn-completar-hoy"
                            @click="completarDia"
                        >
                            ✅ Completar día de hoy
                        </button>
                        <div v-else class="hoy-completado">
                            🎉 ¡Hoy completado!
                        </div>
                    </div>
                </div>

                <!-- Actividades de la semana -->
                <div class="actividades-grid">
                    <div
                        v-for="(act, i) in planActual.actividades"
                        :key="i"
                        class="act-card"
                        :class="{
                            hecha: estaHecho(act.dia),
                            hoy: esHoy(act.dia),
                            futura: !estaHecho(act.dia) && !esHoy(act.dia)
                        }"
                    >
                        <div class="ac-dia">
                            <span class="acd-nombre">{{ act.dia }}</span>
                            <span v-if="esHoy(act.dia)" class="acd-badge">HOY</span>
                            <span v-if="estaHecho(act.dia)" class="acd-hecha">✓</span>
                        </div>
                        <div class="ac-actividad">
                            <span class="aca-emoji">{{ act.emoji }}</span>
                            <div class="aca-info">
                                <span class="aca-nombre">{{ act.nombre }}</span>
                                <span class="aca-desc">{{ act.desc }}</span>
                                <span class="aca-dur">⏱ {{ act.duracion }}</span>
                            </div>
                        </div>
                        <Link :href="act.ruta" class="ac-btn">
                            {{ estaHecho(act.dia) ? '✓ Hecho' : 'Practicar →' }}
                        </Link>
                    </div>
                </div>

                <!-- Regenerar plan -->
                <div class="plan-regenerar">
                    <p>¿Quieres un plan diferente?</p>
                    <div class="pr-objetivos">
                        <button
                            v-for="obj in objetivos"
                            :key="obj.id"
                            class="pro-btn"
                            :class="{ activo: objetivoSelec === obj.id }"
                            @click="objetivoSelec = obj.id"
                        >
                            {{ obj.emoji }} {{ obj.label }}
                        </button>
                    </div>
                    <button class="btn-regenerar" @click="generar" :disabled="generando">
                        🔄 Regenerar plan
                    </button>
                </div>
            </div>

            <!-- Historial -->
            <div v-if="historial.length > 1" class="plan-historial">
                <h3>📊 Historial de planes</h3>
                <div class="ph-lista">
                    <div v-for="plan in historial.slice(1)" :key="plan.id" class="phl-item">
                        <span class="phli-emoji">{{ emojiObj(plan.objetivo) }}</span>
                        <div class="phli-info">
                            <span class="phli-obj">{{ objetivos.find(o => o.id === plan.objetivo)?.label }}</span>
                            <span class="phli-fecha">Semana del {{ plan.semana_inicio }}</span>
                        </div>
                        <div class="phli-progreso">
                            <div class="phlip-barra">
                                <div class="phlip-relleno"
                                    :style="{ width: plan.progreso + '%', backgroundColor: plan.completado ? '#4ECDC4' : '#FFD700' }">
                                </div>
                            </div>
                            <span>{{ plan.dias_completados }}/7</span>
                        </div>
                        <span v-if="plan.completado" class="phli-badge">🏆</span>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.plan-wrapper {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.plan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.plan-header h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
.plan-header p  { color: #666; margin: 0.25rem 0 0; }

.ph-progreso { display: flex; align-items: center; gap: 0.75rem; }
.php-label   { display: block; font-size: 0.72rem; color: #888; font-weight: 600; }
.php-dias    { display: block; font-size: 0.88rem; font-weight: 700; color: #4ECDC4; }

/* Generador */
.plan-generador {
    background: white;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    text-align: center;
}

.pg-icon { font-size: 4rem; animation: pulso 2s ease-in-out infinite; }

@keyframes pulso {
    0%, 100% { transform: scale(1); }
    50%       { transform: scale(1.1); }
}

.plan-generador h2 { font-size: 1.4rem; font-weight: 800; margin: 0; }
.plan-generador p  { font-size: 0.95rem; color: #666; max-width: 480px; margin: 0; line-height: 1.6; }

.pg-objetivos {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    justify-content: center;
    width: 100%;
}

.pgo-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    border: 2px solid #f0f0f0;
    border-radius: 25px;
    background: white;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
}

.pgo-btn:hover { border-color: #4ECDC4; }
.pgo-btn.seleccionado { border-color: #4ECDC4; }

.pgob-emoji { font-size: 1.2rem; }
.pgob-label { font-size: 0.88rem; font-weight: 600; color: #2D2D2D; }

.btn-generar {
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
}

.btn-generar:hover:not(:disabled) { background: #3BAFA7; transform: translateY(-2px); }
.btn-generar:disabled { opacity: 0.6; cursor: not-allowed; }

/* Plan activo */
.plan-activo { display: flex; flex-direction: column; gap: 1.25rem; }

.pa-info {
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.pai-left { display: flex; align-items: center; gap: 0.75rem; }
.pai-emoji { font-size: 2rem; }
.pai-left h3   { font-size: 1rem; font-weight: 700; margin: 0; color: #2D2D2D; }
.pai-left span { font-size: 0.78rem; color: #666; }

.btn-completar-hoy {
    padding: 0.75rem 1.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    font-size: 0.9rem;
    transition: background 0.2s;
    white-space: nowrap;
}

.btn-completar-hoy:hover { background: #3BAFA7; }

.hoy-completado {
    padding: 0.75rem 1.5rem;
    background: #d4edda;
    color: #2d8a4e;
    font-weight: 700;
    border-radius: 25px;
    font-size: 0.9rem;
}

/* Actividades */
.actividades-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.6rem;
}

.act-card {
    background: white;
    border-radius: 14px;
    padding: 1rem 0.75rem;
    border: 2px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.act-card.hecha   { background: #E8FAF9; border-color: #4ECDC4; opacity: 0.85; }
.act-card.hoy     { border-color: #4ECDC4; box-shadow: 0 4px 16px rgba(78,205,196,0.2); }
.act-card.futura  { opacity: 0.7; }

.ac-dia {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.25rem;
}

.acd-nombre { font-size: 0.72rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.05em; }
.acd-badge  { background: #4ECDC4; color: white; font-size: 0.6rem; font-weight: 700; padding: 0.1rem 0.35rem; border-radius: 6px; }
.acd-hecha  { color: #4ECDC4; font-weight: 700; font-size: 0.85rem; }

.ac-actividad { display: flex; flex-direction: column; gap: 0.4rem; flex: 1; }
.aca-emoji    { font-size: 1.6rem; }
.aca-nombre   { font-size: 0.78rem; font-weight: 700; color: #2D2D2D; line-height: 1.3; }
.aca-desc     { font-size: 0.7rem; color: #666; line-height: 1.3; }
.aca-dur      { font-size: 0.7rem; color: #aaa; font-weight: 600; }

.ac-btn {
    padding: 0.4rem 0.6rem;
    background: #f0f0f0;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.72rem;
    font-weight: 700;
    color: #2D2D2D;
    text-align: center;
    transition: all 0.2s;
    display: block;
}

.act-card.hecha .ac-btn { background: #4ECDC4; color: white; }
.ac-btn:hover           { background: #4ECDC4; color: white; }

/* Regenerar */
.plan-regenerar {
    background: #fafafa;
    border-radius: 14px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
}

.plan-regenerar p { font-size: 0.85rem; color: #888; margin: 0; font-weight: 600; }

.pr-objetivos { display: flex; flex-wrap: wrap; gap: 0.4rem; }

.pro-btn {
    padding: 0.35rem 0.85rem;
    border: 1.5px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
    font-family: inherit;
}

.pro-btn.activo { background: #E8FAF9; border-color: #4ECDC4; color: #3BAFA7; }

.btn-regenerar {
    padding: 0.65rem 1.5rem;
    background: white;
    border: 2px solid #4ECDC4;
    color: #3BAFA7;
    font-weight: 700;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    font-size: 0.88rem;
    transition: all 0.2s;
}

.btn-regenerar:hover { background: #4ECDC4; color: white; }

/* Historial */
.plan-historial { display: flex; flex-direction: column; gap: 0.75rem; }
.plan-historial h3 { font-size: 1rem; font-weight: 700; margin: 0; }

.ph-lista { display: flex; flex-direction: column; gap: 0.5rem; }

.phl-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: white;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    border: 1px solid #f0f0f0;
}

.phli-emoji { font-size: 1.3rem; flex-shrink: 0; }

.phli-info    { flex: 1; }
.phli-obj     { display: block; font-size: 0.85rem; font-weight: 700; color: #2D2D2D; }
.phli-fecha   { display: block; font-size: 0.72rem; color: #aaa; }

.phli-progreso {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 120px;
}

.phlip-barra {
    flex: 1;
    height: 6px;
    background: #f0f0f0;
    border-radius: 3px;
    overflow: hidden;
}

.phlip-relleno {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s ease;
}

.phli-progreso span { font-size: 0.75rem; color: #888; font-weight: 600; }
.phli-badge { font-size: 1.1rem; }

/* Responsive */
@media (max-width: 900px) {
    .actividades-grid { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 600px) {
    .actividades-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>