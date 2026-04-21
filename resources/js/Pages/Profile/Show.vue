<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
    usuario:            Object,
    stats:              Object,
    nivel:              String,
    infoNivel:          Object,
    progresNivel:       Number,
    logros:             Array,
    emocionesRecientes: Array,
    ultimoTest:         Object,
})

const editando       = ref(false)
const cambandoPass   = ref(false)
const eliminando     = ref(false)

const form = ref({
    name:     props.usuario.name,
    bio:      props.usuario.bio,
    location: props.usuario.location,
    avatar:   props.usuario.avatar,
})

const passForm = ref({
    current_password:      '',
    password:              '',
    password_confirmation: '',
})

const deleteForm = ref({ password: '' })

const avatares = [
    '😊','😌','🦋','🌸','🌟','💫','🌈','🍀',
    '🌻','🦄','🐬','🌙','☀️','🌊','🏔️','🌺',
    '💚','💙','💜','🧡','❤️','🤍','🖤','💛',
]

const guardarPerfil = () => {
    router.patch('/perfil', form.value, {
        onSuccess: () => { editando.value = false }
    })
}

const guardarPassword = () => {
    router.patch('/perfil/password', passForm.value, {
        onSuccess: () => {
            cambandoPass.value = false
            passForm.value = { current_password: '', password: '', password_confirmation: '' }
        }
    })
}

const eliminarCuenta = () => {
    router.delete('/perfil', { data: deleteForm.value })
}

const colorEmocion = (e) => ({
    alegría: '#FFD700', calma: '#4ECDC4', ansiedad: '#FF8C42',
    tristeza: '#6B9FD4', enfado: '#E63946', cansancio: '#9B8EC4',
}[e] ?? '#4ECDC4')

const emojiEmocion = (e) => ({
    alegría: '😊', calma: '😌', ansiedad: '😰',
    tristeza: '😢', enfado: '😠', cansancio: '😴',
}[e] ?? '💙')

const objetivoLabel = (id) => ({
    ansiedad:   'Gestionar la ansiedad',
    tristeza:   'Superar la tristeza',
    estres:     'Reducir el estrés',
    sueno:      'Dormir mejor',
    autoestima: 'Mejorar mi autoestima',
    habitos:    'Crear hábitos saludables',
    conexion:   'Conectar conmigo mismo/a',
    general:    'Bienestar general',
}[id] ?? 'Bienestar general')
</script>

<template>
    <AppLayout>
        <div class="perfil-wrapper">

            <!-- ── Cabecera del perfil ── -->
            <div class="perfil-hero">
                <div class="ph-bg"></div>
                <div class="ph-content">
                    <div class="ph-avatar-wrap">
                        <div class="ph-avatar">{{ usuario.avatar }}</div>
                        <div class="ph-nivel-badge">
                            {{ infoNivel.emoji }} {{ nivel }}
                        </div>
                    </div>
                    <div class="ph-info">
                        <h1>{{ usuario.name }}</h1>
                        <p v-if="usuario.bio" class="ph-bio">{{ usuario.bio }}</p>
                        <div class="ph-meta">
                            <span v-if="usuario.location">📍 {{ usuario.location }}</span>
                            <span>📅 Miembro desde {{ usuario.creado }}</span>
                            <span>🎯 {{ objetivoLabel(usuario.objetivo) }}</span>
                        </div>
                    </div>
                    <button class="btn-editar-perfil" @click="editando = !editando">
                        {{ editando ? '✕ Cancelar' : '✏️ Editar perfil' }}
                    </button>
                </div>

                <!-- Barra de progreso de nivel -->
                <div class="ph-nivel-progreso">
                    <div class="pnp-info">
                        <span class="pnp-nivel">{{ infoNivel.emoji }} {{ nivel }}</span>
                        <span v-if="infoNivel.siguiente" class="pnp-siguiente">
                            Próximo: {{ infoNivel.siguiente }}
                        </span>
                        <span v-else class="pnp-max">🏆 Nivel máximo</span>
                    </div>
                    <div class="pnp-barra">
                        <div class="pnp-relleno" :style="{ width: progresNivel + '%' }"></div>
                    </div>
                    <span class="pnp-pct">{{ progresNivel }}%</span>
                </div>
            </div>

            <!-- ── Formulario de edición ── -->
            <Transition name="slide-down">
                <div v-if="editando" class="perfil-editar">
                    <h3>✏️ Editar perfil</h3>

                    <div class="pe-avatar-selector">
                        <p>Tu avatar:</p>
                        <div class="peas-grid">
                            <button
                                v-for="av in avatares"
                                :key="av"
                                class="peas-btn"
                                :class="{ seleccionado: form.avatar === av }"
                                @click="form.avatar = av"
                            >{{ av }}</button>
                        </div>
                    </div>

                    <div class="pe-campos">
                        <div class="pe-campo">
                            <label>Nombre</label>
                            <input v-model="form.name" type="text" placeholder="Tu nombre" />
                        </div>
                        <div class="pe-campo">
                            <label>Biografía</label>
                            <textarea v-model="form.bio" rows="3"
                                placeholder="Cuéntanos algo sobre ti..." maxlength="300"></textarea>
                        </div>
                        <div class="pe-campo">
                            <label>Ubicación</label>
                            <input v-model="form.location" type="text" placeholder="Tu ciudad..." />
                        </div>
                    </div>

                    <div class="pe-bots">
                        <button class="btn-guardar-pe" @click="guardarPerfil">
                            💾 Guardar cambios
                        </button>
                        <button class="btn-cancelar-pe" @click="editando = false">
                            Cancelar
                        </button>
                    </div>
                </div>
            </Transition>

            <!-- ── Stats grid ── -->
            <div class="stats-grid-perfil">
                <div class="sgp-item teal">
                    <span class="sgp-val">{{ stats.racha_actual }}</span>
                    <span class="sgp-ico">🔥</span>
                    <span class="sgp-lab">Días de racha</span>
                </div>
                <div class="sgp-item purple">
                    <span class="sgp-val">{{ stats.total_logros }}</span>
                    <span class="sgp-ico">🏆</span>
                    <span class="sgp-lab">Logros</span>
                </div>
                <div class="sgp-item yellow">
                    <span class="sgp-val">{{ stats.emociones_registradas }}</span>
                    <span class="sgp-ico">📊</span>
                    <span class="sgp-lab">Emociones</span>
                </div>
                <div class="sgp-item green">
                    <span class="sgp-val">{{ stats.entradas_diario }}</span>
                    <span class="sgp-ico">📓</span>
                    <span class="sgp-lab">Diario</span>
                </div>
                <div class="sgp-item red">
                    <span class="sgp-val">{{ stats.retos_completados }}</span>
                    <span class="sgp-ico">🎯</span>
                    <span class="sgp-lab">Retos completados</span>
                </div>
                <div class="sgp-item blue">
                    <span class="sgp-val">{{ stats.retos_activos }}</span>
                    <span class="sgp-ico">⚡</span>
                    <span class="sgp-lab">Retos activos</span>
                </div>
            </div>

            <div class="perfil-grid">

                <!-- Columna izquierda -->
                <div class="pg-col">

                    <!-- Último test PHQ-9 -->
                    <div class="perfil-card" v-if="ultimoTest">
                        <h3>🧠 Último test de bienestar</h3>
                        <div class="ut-contenido">
                            <div class="ut-puntuacion">
                                <span class="utp-num">{{ ultimoTest.puntuacion }}</span>
                                <span class="utp-max">/27</span>
                            </div>
                            <div class="ut-info">
                                <span class="uti-nivel">{{ ultimoTest.nivel }}</span>
                                <span class="uti-fecha">{{ ultimoTest.fecha }}</span>
                            </div>
                        </div>
                        <Link href="/test-bienestar" class="btn-ir-test">
                            Hacer nuevo test →
                        </Link>
                    </div>

                    <div class="perfil-card" v-else>
                        <h3>🧠 Test de bienestar</h3>
                        <p class="pc-desc">Todavía no has hecho el test semanal PHQ-9.</p>
                        <Link href="/test-bienestar" class="btn-ir-test">
                            Hacer mi primer test →
                        </Link>
                    </div>

                    <!-- Emociones recientes -->
                    <div class="perfil-card">
                        <h3>💙 Emociones recientes</h3>
                        <div v-if="emocionesRecientes.length === 0" class="pc-empty">
                            Sin registros aún.
                            <Link href="/mis-emociones">Registrar ahora →</Link>
                        </div>
                        <div v-else class="emociones-lista">
                            <div
                                v-for="em in emocionesRecientes"
                                :key="em.fecha"
                                class="el-item"
                                :style="{ borderLeft: `3px solid ${colorEmocion(em.emotion)}` }"
                            >
                                <span class="eli-emoji">{{ emojiEmocion(em.emotion) }}</span>
                                <div class="eli-info">
                                    <span class="eli-emocion">{{ em.emotion }}</span>
                                    <span class="eli-fecha">{{ em.fecha }}</span>
                                </div>
                                <span
                                    class="eli-intensidad"
                                    :style="{ backgroundColor: colorEmocion(em.emotion) + '25', color: colorEmocion(em.emotion) }"
                                >
                                    {{ em.intensity }}/10
                                </span>
                            </div>
                        </div>
                        <Link href="/mis-emociones" class="btn-ver-todas">
                            Ver dashboard emocional →
                        </Link>
                    </div>

                    <!-- Cambiar contraseña -->
                    <div class="perfil-card">
                        <div class="pc-toggle-header" @click="cambandoPass = !cambandoPass">
                            <h3>🔐 Cambiar contraseña</h3>
                            <span>{{ cambandoPass ? '▲' : '▼' }}</span>
                        </div>
                        <Transition name="slide-down">
                            <div v-if="cambandoPass" class="pass-form">
                                <input v-model="passForm.current_password" type="password"
                                    placeholder="Contraseña actual" class="pe-input" />
                                <input v-model="passForm.password" type="password"
                                    placeholder="Nueva contraseña" class="pe-input" />
                                <input v-model="passForm.password_confirmation" type="password"
                                    placeholder="Confirmar nueva contraseña" class="pe-input" />
                                <button class="btn-guardar-pe" @click="guardarPassword">
                                    🔐 Actualizar contraseña
                                </button>
                            </div>
                        </Transition>
                    </div>

                </div>

                <!-- Columna derecha — Logros -->
                <div class="pg-col">
                    <div class="perfil-card logros-card">
                        <div class="lc-header">
                            <h3>🏆 Mis logros ({{ logros.length }})</h3>
                            <Link href="/logros" class="lc-ver-todos">Ver todos →</Link>
                        </div>

                        <div v-if="logros.length === 0" class="pc-empty">
                            Todavía no has desbloqueado logros.
                            <Link href="/logros">Ver logros disponibles →</Link>
                        </div>

                        <div v-else class="logros-grid-perfil">
                            <div
                                v-for="logro in logros"
                                :key="logro.id"
                                class="lgp-item"
                                :title="logro.descripcion"
                            >
                                <span class="lgp-emoji">{{ logro.emoji }}</span>
                                <span class="lgp-nombre">{{ logro.nombre }}</span>
                                <span class="lgp-fecha">{{ logro.fecha }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Zona de peligro -->
                    <div class="perfil-card danger-card">
                        <h3>⚠️ Zona de peligro</h3>
                        <p>Eliminar tu cuenta es una acción irreversible. Se borrarán todos tus datos.</p>
                        <button class="btn-eliminar-cuenta"
                            @click="eliminando = !eliminando">
                            {{ eliminando ? 'Cancelar' : '🗑️ Eliminar mi cuenta' }}
                        </button>
                        <Transition name="slide-down">
                            <div v-if="eliminando" class="eliminar-form">
                                <p class="ef-aviso">Introduce tu contraseña para confirmar:</p>
                                <input v-model="deleteForm.password" type="password"
                                    placeholder="Tu contraseña" class="pe-input" />
                                <button class="btn-confirmar-eliminar"
                                    @click="eliminarCuenta">
                                    Confirmar eliminación
                                </button>
                            </div>
                        </Transition>
                    </div>

                </div>

            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.perfil-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ── Hero ── */
.perfil-hero {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 24px;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    position: relative;
    overflow: hidden;
}

.ph-bg {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(78,205,196,0.1) 0%, transparent 60%);
    pointer-events: none;
}

.ph-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
}

.ph-avatar-wrap { position: relative; flex-shrink: 0; }

.ph-avatar {
    width: 80px;
    height: 80px;
    background: rgba(78,205,196,0.15);
    border-radius: 50%;
    border: 3px solid #4ECDC4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
}

.ph-nivel-badge {
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    background: #4ECDC4;
    color: white;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 10px;
    white-space: nowrap;
}

.ph-info { flex: 1; }
.ph-info h1 { font-size: 1.5rem; font-weight: 800; color: white; margin: 0 0 0.4rem; }
.ph-bio { font-size: 0.88rem; color: rgba(255,255,255,0.65); margin: 0 0 0.5rem; font-style: italic; }

.ph-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.ph-meta span { font-size: 0.78rem; color: rgba(255,255,255,0.5); }

.btn-editar-perfil {
    padding: 0.65rem 1.25rem;
    background: rgba(78,205,196,0.2);
    border: 1.5px solid #4ECDC4;
    border-radius: 25px;
    color: #4ECDC4;
    font-weight: 700;
    cursor: pointer;
    font-size: 0.85rem;
    font-family: inherit;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-editar-perfil:hover { background: #4ECDC4; color: white; }

/* Progreso nivel */
.ph-nivel-progreso {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    z-index: 1;
}

.pnp-info { display: flex; flex-direction: column; min-width: 160px; }
.pnp-nivel    { font-size: 0.82rem; font-weight: 700; color: #4ECDC4; }
.pnp-siguiente{ font-size: 0.72rem; color: rgba(255,255,255,0.4); }
.pnp-max      { font-size: 0.72rem; color: #FFD700; }

.pnp-barra {
    flex: 1;
    height: 8px;
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
    overflow: hidden;
}

.pnp-relleno {
    height: 100%;
    background: linear-gradient(90deg, #4ECDC4, #3BAFA7);
    border-radius: 4px;
    transition: width 0.8s ease;
}

.pnp-pct { font-size: 0.78rem; color: #4ECDC4; font-weight: 700; min-width: 36px; }

/* ── Formulario edición ── */
.perfil-editar {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 2px solid #4ECDC4;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.perfil-editar h3 { font-size: 1rem; font-weight: 700; margin: 0; }

.pe-avatar-selector p { font-size: 0.85rem; font-weight: 700; color: #2D2D2D; margin: 0 0 0.5rem; }

.peas-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 0.3rem;
}

.peas-btn {
    aspect-ratio: 1;
    font-size: 1.2rem;
    border: 2px solid transparent;
    border-radius: 8px;
    background: #fafafa;
    cursor: pointer;
    transition: all 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.peas-btn:hover       { background: #E8FAF9; border-color: #4ECDC4; }
.peas-btn.seleccionado{ background: #E8FAF9; border-color: #4ECDC4; }

.pe-campos { display: flex; flex-direction: column; gap: 0.75rem; }

.pe-campo { display: flex; flex-direction: column; gap: 0.35rem; }
.pe-campo label { font-size: 0.82rem; font-weight: 700; color: #2D2D2D; }

.pe-campo input,
.pe-campo textarea,
.pe-input {
    padding: 0.7rem 0.9rem;
    border: 1.5px solid #e8e8e8;
    border-radius: 10px;
    font-size: 0.9rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
    width: 100%;
}

.pe-campo input:focus,
.pe-campo textarea:focus,
.pe-input:focus { border-color: #4ECDC4; }

.pe-bots { display: flex; gap: 0.75rem; }

.btn-guardar-pe {
    padding: 0.75rem 1.75rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
}

.btn-guardar-pe:hover { background: #3BAFA7; }

.btn-cancelar-pe {
    padding: 0.75rem 1.25rem;
    background: white;
    border: 1.5px solid #e0e0e0;
    border-radius: 25px;
    color: #888;
    cursor: pointer;
    font-family: inherit;
}

/* ── Stats grid ── */
.stats-grid-perfil {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0.75rem;
}

.sgp-item {
    border-radius: 14px;
    padding: 1.25rem 1rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
}

.sgp-item.teal   { background: #E8FAF9; }
.sgp-item.purple { background: #e8d5f5; }
.sgp-item.yellow { background: #fff9c4; }
.sgp-item.green  { background: #d4edda; }
.sgp-item.red    { background: #ffd5d5; }
.sgp-item.blue   { background: #d0eaf8; }

.sgp-val { font-size: 1.8rem; font-weight: 900; color: #2D2D2D; line-height: 1; }
.sgp-ico { font-size: 1.1rem; }
.sgp-lab { font-size: 0.68rem; color: #666; font-weight: 600; }

/* ── Grid perfil ── */
.perfil-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    align-items: start;
}

.pg-col { display: flex; flex-direction: column; gap: 1.25rem; }

/* ── Cards ── */
.perfil-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.perfil-card h3 { font-size: 0.95rem; font-weight: 700; margin: 0; color: #2D2D2D; }

.pc-desc { font-size: 0.88rem; color: #888; margin: 0; }

.pc-empty {
    font-size: 0.85rem;
    color: #aaa;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.pc-empty a { color: #4ECDC4; text-decoration: none; font-weight: 600; }

/* Último test */
.ut-contenido { display: flex; align-items: center; gap: 1rem; }
.utp-num { font-size: 2.5rem; font-weight: 900; color: #9B8EC4; line-height: 1; }
.utp-max { font-size: 1rem; color: #aaa; }
.uti-nivel { display: block; font-size: 0.88rem; font-weight: 700; color: #2D2D2D; text-transform: capitalize; }
.uti-fecha { display: block; font-size: 0.75rem; color: #aaa; }

.btn-ir-test {
    padding: 0.6rem 1.25rem;
    background: #E8FAF9;
    color: #3BAFA7;
    font-weight: 700;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.82rem;
    text-align: center;
    transition: all 0.2s;
}

.btn-ir-test:hover { background: #4ECDC4; color: white; }

/* Emociones */
.emociones-lista { display: flex; flex-direction: column; gap: 0.4rem; }

.el-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.5rem 0.6rem;
    background: #fafafa;
    border-radius: 8px;
}

.eli-emoji   { font-size: 1.1rem; }
.eli-info    { flex: 1; }
.eli-emocion { display: block; font-size: 0.82rem; font-weight: 600; color: #2D2D2D; text-transform: capitalize; }
.eli-fecha   { display: block; font-size: 0.7rem; color: #aaa; }

.eli-intensidad {
    padding: 0.2rem 0.55rem;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 700;
}

.btn-ver-todas {
    font-size: 0.82rem;
    color: #4ECDC4;
    text-decoration: none;
    font-weight: 700;
    text-align: center;
}

/* Contraseña */
.pc-toggle-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.pass-form { display: flex; flex-direction: column; gap: 0.6rem; }

/* Logros */
.lc-header { display: flex; justify-content: space-between; align-items: center; }
.lc-ver-todos { font-size: 0.82rem; color: #4ECDC4; text-decoration: none; font-weight: 700; }

.logros-grid-perfil {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}

.lgp-item {
    background: #fafafa;
    border-radius: 10px;
    padding: 0.75rem 0.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
    text-align: center;
    border: 1px solid #f0f0f0;
    transition: transform 0.2s;
}

.lgp-item:hover { transform: translateY(-2px); }

.lgp-emoji  { font-size: 1.5rem; }
.lgp-nombre { font-size: 0.7rem; font-weight: 700; color: #2D2D2D; }
.lgp-fecha  { font-size: 0.62rem; color: #aaa; }

/* Zona peligro */
.danger-card { border-color: #ffd5d5; }
.danger-card h3 { color: #E63946; }
.danger-card p  { font-size: 0.85rem; color: #666; margin: 0; line-height: 1.5; }

.btn-eliminar-cuenta {
    padding: 0.65rem 1.25rem;
    background: white;
    border: 1.5px solid #E63946;
    border-radius: 25px;
    color: #E63946;
    font-weight: 700;
    cursor: pointer;
    font-size: 0.85rem;
    font-family: inherit;
    transition: all 0.2s;
    align-self: flex-start;
}

.btn-eliminar-cuenta:hover { background: #E63946; color: white; }

.eliminar-form { display: flex; flex-direction: column; gap: 0.6rem; }
.ef-aviso { font-size: 0.82rem; color: #E63946; margin: 0; font-weight: 600; }

.btn-confirmar-eliminar {
    padding: 0.65rem 1.25rem;
    background: #E63946;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
}

.btn-confirmar-eliminar:hover { background: #c0303b; }

/* Animaciones */
.slide-down-enter-active, .slide-down-leave-active { transition: opacity 0.2s, transform 0.2s; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }

/* Responsive */
@media (max-width: 800px) {
    .perfil-grid       { grid-template-columns: 1fr; }
    .stats-grid-perfil { grid-template-columns: repeat(3, 1fr); }
    .peas-grid         { grid-template-columns: repeat(8, 1fr); }
}

@media (max-width: 500px) {
    .stats-grid-perfil { grid-template-columns: repeat(2, 1fr); }
}
</style>