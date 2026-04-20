<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    retosDisponibles: Array,
    misRetos:         Array,
    retosCompletados: Number,
})

const pestanaActiva = ref('mis-retos')
const filtroTipo    = ref('todos')

const retos7  = props.retosDisponibles.filter(r => r.type === '7days')
const retos30 = props.retosDisponibles.filter(r => r.type === '30days')

const unirse = (challengeId) => {
    router.post(`/retos/${challengeId}/unirse`)
}

const completarDia = (userChallengeId) => {
    router.post(`/retos/${userChallengeId}/completar-dia`)
}

const abandonar = (userChallengeId) => {
    if (!confirm('¿Seguro que quieres abandonar este reto?')) return
    router.post(`/retos/${userChallengeId}/abandonar`)
}

const categoriaColor = (cat) => ({
    ansiedad:    '#d0eaf8',
    mindfulness: '#e8d5f5',
    autoestima:  '#fce4ec',
    sueno:       '#e8eaf6',
    ejercicio:   '#ffd5d5',
    relaciones:  '#E8FAF9',
    salud:       '#d4edda',
    general:     '#fff9c4',
}[cat] ?? '#fafafa')
</script>

<template>
    <AppLayout>
        <div class="ch-wrapper">

            <!-- Cabecera -->
            <div class="ch-header">
                <div>
                    <h1>🎯 Retos de bienestar</h1>
                    <p>Construye hábitos saludables día a día con retos de 7 y 30 días</p>
                </div>
                <div class="ch-stats">
                    <div class="chs-item">
                        <span class="chs-val">{{ misRetos.length }}</span>
                        <span class="chs-lab">En progreso</span>
                    </div>
                    <div class="chs-item">
                        <span class="chs-val">{{ retosCompletados }}</span>
                        <span class="chs-lab">Completados 🏆</span>
                    </div>
                </div>
            </div>

            <!-- Pestañas -->
            <div class="ch-tabs">
                <button
                    class="ch-tab"
                    :class="{ activa: pestanaActiva === 'mis-retos' }"
                    @click="pestanaActiva = 'mis-retos'"
                >
                    💪 Mis retos activos ({{ misRetos.length }})
                </button>
                <button
                    class="ch-tab"
                    :class="{ activa: pestanaActiva === 'explorar' }"
                    @click="pestanaActiva = 'explorar'"
                >
                    🔍 Explorar retos
                </button>
            </div>

            <!-- Mis retos activos -->
            <div v-if="pestanaActiva === 'mis-retos'">
                <div v-if="misRetos.length === 0" class="ch-empty">
                    <span>🎯</span>
                    <h3>Todavía no has empezado ningún reto</h3>
                    <p>Explora los retos disponibles y empieza a construir hábitos saludables.</p>
                    <button class="btn-explorar" @click="pestanaActiva = 'explorar'">
                        Ver retos disponibles →
                    </button>
                </div>

                <div v-else class="mis-retos-grid">
                    <div
                        v-for="reto in misRetos"
                        :key="reto.id"
                        class="mi-reto-card"
                        :style="{ borderTop: `4px solid ${reto.challenge.color === '#fff9c4' ? '#FFD700' : reto.challenge.color}` }"
                    >
                        <div class="mrc-header">
                            <span class="mrc-emoji">{{ reto.challenge.emoji }}</span>
                            <div class="mrc-info">
                                <h3>{{ reto.challenge.title }}</h3>
                                <span class="mrc-inicio">Iniciado el {{ reto.started_at }}</span>
                            </div>
                        </div>

                        <!-- Progreso -->
                        <div class="mrc-progreso">
                            <div class="mrc-prog-header">
                                <span>Día {{ reto.current_day }} de {{ reto.challenge.duration_days }}</span>
                                <span class="mrc-porcentaje">{{ reto.progreso }}%</span>
                            </div>
                            <div class="mrc-barra">
                                <div
                                    class="mrc-relleno"
                                    :style="{ width: `${reto.progreso}%` }"
                                ></div>
                            </div>
                            <span class="mrc-restantes">{{ reto.dias_restantes }} días restantes</span>
                        </div>

                        <!-- Calendario de días -->
                        <div class="mrc-calendario">
                            <div
                                v-for="d in reto.challenge.duration_days"
                                :key="d"
                                class="mrc-dia"
                                :class="{
                                    completado: reto.completed_days.includes(new Date(new Date(reto.started_at.split('/').reverse().join('-')).setDate(new Date(reto.started_at.split('/').reverse().join('-')).getDate() + d - 1)).toISOString().slice(0,10)),
                                    hoy: d === reto.current_day
                                }"
                                :title="`Día ${d}`"
                            >
                                {{ d <= 9 ? d : '' }}
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="mrc-acciones">
                            <button
                                v-if="!reto.completado_hoy"
                                class="btn-completar-dia"
                                @click="completarDia(reto.id)"
                            >
                                ✅ Completar día {{ reto.current_day }}
                            </button>
                            <div v-else class="dia-completado-badge">
                                🎉 ¡Día de hoy completado!
                            </div>
                            <button class="btn-abandonar" @click="abandonar(reto.id)">
                                Abandonar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Explorar retos -->
            <div v-if="pestanaActiva === 'explorar'">

                <!-- Retos 7 días -->
                <div class="ch-seccion">
                    <div class="ch-sec-header">
                        <h2>⚡ Retos de 7 días</h2>
                        <span class="ch-sec-badge">Semana de hábito</span>
                    </div>
                    <div class="retos-grid">
                        <div
                            v-for="reto in retos7"
                            :key="reto.id"
                            class="reto-card"
                            :style="{ backgroundColor: reto.color }"
                        >
                            <div class="rc-top">
                                <span class="rc-emoji">{{ reto.emoji }}</span>
                                <span class="rc-categoria" :style="{ backgroundColor: categoriaColor(reto.category) }">
                                    {{ reto.category }}
                                </span>
                            </div>
                            <h3>{{ reto.title }}</h3>
                            <p>{{ reto.description }}</p>
                            <div class="rc-footer">
                                <span class="rc-dur">📅 {{ reto.duration_days }} días</span>
                                <div v-if="reto.user_challenge">
                                    <span v-if="reto.user_challenge.status === 'active'" class="rc-activo">
                                        En progreso ({{ reto.user_challenge.progreso }}%)
                                    </span>
                                    <span v-else-if="reto.user_challenge.status === 'completed'" class="rc-completado">
                                        🏆 Completado
                                    </span>
                                </div>
                                <button
                                    v-else
                                    class="btn-unirse"
                                    @click="unirse(reto.id)"
                                >
                                    + Unirme
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Retos 30 días -->
                <div class="ch-seccion">
                    <div class="ch-sec-header">
                        <h2>🌱 Retos de 30 días</h2>
                        <span class="ch-sec-badge">Transformación de hábito</span>
                    </div>
                    <div class="retos-grid">
                        <div
                            v-for="reto in retos30"
                            :key="reto.id"
                            class="reto-card"
                            :style="{ backgroundColor: reto.color }"
                        >
                            <div class="rc-top">
                                <span class="rc-emoji">{{ reto.emoji }}</span>
                                <span class="rc-categoria" :style="{ backgroundColor: categoriaColor(reto.category) }">
                                    {{ reto.category }}
                                </span>
                            </div>
                            <h3>{{ reto.title }}</h3>
                            <p>{{ reto.description }}</p>
                            <div class="rc-footer">
                                <span class="rc-dur">📅 {{ reto.duration_days }} días</span>
                                <div v-if="reto.user_challenge">
                                    <span v-if="reto.user_challenge.status === 'active'" class="rc-activo">
                                        En progreso ({{ reto.user_challenge.progreso }}%)
                                    </span>
                                    <span v-else-if="reto.user_challenge.status === 'completed'" class="rc-completado">
                                        🏆 Completado
                                    </span>
                                </div>
                                <button
                                    v-else
                                    class="btn-unirse"
                                    @click="unirse(reto.id)"
                                >
                                    + Unirme
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.ch-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.ch-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.ch-header h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
.ch-header p  { color: #666; margin: 0.25rem 0 0; }

.ch-stats { display: flex; gap: 1rem; }

.chs-item {
    background: white;
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    text-align: center;
    border: 1px solid #f0f0f0;
    min-width: 90px;
}

.chs-val { display: block; font-size: 1.8rem; font-weight: 900; color: #4ECDC4; }
.chs-lab { display: block; font-size: 0.72rem; color: #888; font-weight: 600; }

/* ── Tabs ── */
.ch-tabs { display: flex; gap: 0.5rem; }

.ch-tab {
    padding: 0.6rem 1.25rem;
    border: 2px solid #f0f0f0;
    border-radius: 25px;
    background: white;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
}

.ch-tab.activa { background: #4ECDC4; border-color: #4ECDC4; color: white; }

/* ── Empty ── */
.ch-empty {
    text-align: center;
    padding: 3rem 2rem;
    background: #fafafa;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.ch-empty span { font-size: 3rem; }
.ch-empty h3   { font-size: 1.1rem; font-weight: 700; margin: 0; }
.ch-empty p    { font-size: 0.92rem; color: #666; margin: 0; }

.btn-explorar {
    padding: 0.75rem 1.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    margin-top: 0.5rem;
    transition: background 0.2s;
}

.btn-explorar:hover { background: #3BAFA7; }

/* ── Mis retos ── */
.mis-retos-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

.mi-reto-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.mrc-header { display: flex; align-items: center; gap: 0.75rem; }
.mrc-emoji  { font-size: 2rem; flex-shrink: 0; }
.mrc-info h3 { font-size: 0.95rem; font-weight: 700; margin: 0; }
.mrc-inicio  { font-size: 0.75rem; color: #aaa; }

.mrc-progreso { display: flex; flex-direction: column; gap: 0.4rem; }

.mrc-prog-header {
    display: flex;
    justify-content: space-between;
    font-size: 0.82rem;
    font-weight: 600;
    color: #2D2D2D;
}

.mrc-porcentaje { color: #4ECDC4; }

.mrc-barra {
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

.mrc-relleno {
    height: 100%;
    background: #4ECDC4;
    border-radius: 4px;
    transition: width 0.5s ease;
}

.mrc-restantes { font-size: 0.75rem; color: #aaa; }

/* ── Calendario de días ── */
.mrc-calendario {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.mrc-dia {
    width: 22px;
    height: 22px;
    border-radius: 4px;
    background: #f0f0f0;
    font-size: 0.65rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #aaa;
    transition: all 0.2s;
}

.mrc-dia.completado { background: #4ECDC4; color: white; }
.mrc-dia.hoy        { border: 2px solid #4ECDC4; color: #4ECDC4; background: white; }

.mrc-acciones { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }

.btn-completar-dia {
    flex: 1;
    padding: 0.75rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.btn-completar-dia:hover { background: #3BAFA7; }

.dia-completado-badge {
    flex: 1;
    padding: 0.75rem;
    background: #E8FAF9;
    color: #3BAFA7;
    font-weight: 700;
    border-radius: 12px;
    font-size: 0.9rem;
    text-align: center;
}

.btn-abandonar {
    padding: 0.75rem 1rem;
    background: white;
    border: 1.5px solid #f0f0f0;
    border-radius: 12px;
    color: #aaa;
    font-size: 0.82rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-abandonar:hover { border-color: #E63946; color: #E63946; }

/* ── Explorar ── */
.ch-seccion { display: flex; flex-direction: column; gap: 1rem; }

.ch-sec-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.ch-sec-header h2 { font-size: 1.1rem; font-weight: 700; margin: 0; }

.ch-sec-badge {
    padding: 0.25rem 0.75rem;
    background: #E8FAF9;
    color: #3BAFA7;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    border: 1px solid #4ECDC4;
}

.retos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.reto-card {
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    border: 1px solid rgba(0,0,0,0.06);
    transition: transform 0.2s, box-shadow 0.2s;
}

.reto-card:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(0,0,0,0.08); }

.rc-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.rc-emoji { font-size: 1.8rem; }

.rc-categoria {
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #2D2D2D;
}

.reto-card h3 { font-size: 0.92rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.reto-card p  { font-size: 0.82rem; color: #555; line-height: 1.5; margin: 0; flex: 1; }

.rc-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.rc-dur { font-size: 0.78rem; color: #888; font-weight: 600; }

.btn-unirse {
    padding: 0.4rem 1rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.82rem;
    transition: background 0.2s;
}

.btn-unirse:hover { background: #3BAFA7; }

.rc-activo    { font-size: 0.78rem; color: #3BAFA7; font-weight: 600; }
.rc-completado { font-size: 0.78rem; color: #f57f17; font-weight: 600; }

/* ── Responsive ── */
@media (max-width: 800px) {
    .mis-retos-grid { grid-template-columns: 1fr; }
    .retos-grid     { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 520px) {
    .retos-grid { grid-template-columns: 1fr; }
}
</style>