<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    usuario:            Object,
    stats:              Object,
    historialEmocional: Array,
    ultimosPosts:       Array,
    logros:             Array,
})

const seccionActiva = ref('perfil')

const AVATARES = [
    '😊','😌','🧘','💪','🌸','🌟','🦋','🌈',
    '🍀','🌻','🐬','🦁','🌙','⭐','🔥','💚'
]

const COLORES = [
    '#4ECDC4','#FF8C42','#6B9FD4','#9B8EC4',
    '#E63946','#FFD700','#4CAF50','#FF69B4'
]

const form = useForm({
    name:           props.usuario.name,
    bio:            props.usuario.bio || '',
    location:       props.usuario.location || '',
    birth_date:     props.usuario.birth_date || '',
    avatar:         props.usuario.avatar || '😊',
    profile_public: props.usuario.profile_public,
    show_in_forum:  props.usuario.show_in_forum,
    theme_color:    props.usuario.theme_color || '#4ECDC4',
})

const passwordForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
})

const deleteForm = useForm({ password: '' })

const mostrarEliminar = ref(false)

const guardarPerfil = () => {
    form.patch('/perfil')
}

const cambiarPassword = () => {
    passwordForm.patch('/perfil/password', {
        onSuccess: () => passwordForm.reset()
    })
}

const eliminarCuenta = () => {
    deleteForm.delete('/perfil', {
        onSuccess: () => { mostrarEliminar.value = false }
    })
}

const iniciales = (nombre) => {
    return nombre?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || '??'
}

const categoriaLabel = {
    ansiedad:   '😰 Ansiedad',
    depresion:  '😢 Depresión',
    relaciones: '💙 Relaciones',
    autoestima: '💪 Autoestima',
    sueno:      '😴 Sueño',
    general:    '💬 General',
}
</script>

<template>
    <AppLayout>
        <div class="perfil-wrapper">

            <!-- Cabecera del perfil -->
            <div class="perfil-hero" :style="{ background: form.theme_color }">
                <div class="perfil-avatar-grande">
                    {{ form.avatar || iniciales(usuario.name) }}
                </div>
                <div class="perfil-hero-info">
                    <h1>{{ usuario.name }}</h1>
                    <p v-if="usuario.bio">{{ usuario.bio }}</p>
                    <div class="perfil-meta">
                        <span v-if="usuario.location">📍 {{ usuario.location }}</span>
                        <span>📅 Miembro desde {{ usuario.miembro_desde }}</span>
                        <span>🏆 {{ stats.puntos }} puntos</span>
                    </div>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="stats-rapidas">
                <div class="stat-rapida">
                    <span class="sr-valor">{{ stats.total_registros }}</span>
                    <span class="sr-label">Registros emocionales</span>
                </div>
                <div class="stat-rapida">
                    <span class="sr-valor">{{ stats.sesiones_respira }}</span>
                    <span class="sr-label">Sesiones de respiración</span>
                </div>
                <div class="stat-rapida">
                    <span class="sr-valor">{{ stats.posts_foro }}</span>
                    <span class="sr-label">Posts en el foro</span>
                </div>
                <div class="stat-rapida">
                    <span class="sr-valor">{{ stats.logros }}</span>
                    <span class="sr-label">Logros desbloqueados</span>
                </div>
                <div class="stat-rapida">
                    <span class="sr-valor">{{ stats.dias_activo }}</span>
                    <span class="sr-label">Días activo</span>
                </div>
            </div>

            <!-- Pestañas de navegación -->
            <div class="tabs">
                <button
                    v-for="tab in [
                        { id: 'perfil',    label: '👤 Mi perfil' },
                        { id: 'actividad', label: '📊 Actividad' },
                        { id: 'logros',    label: '🏆 Mis logros' },
                        { id: 'seguridad', label: '🔒 Seguridad' },
                    ]"
                    :key="tab.id"
                    class="tab-btn"
                    :class="{ activa: seccionActiva === tab.id }"
                    @click="seccionActiva = tab.id"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- ── PESTAÑA PERFIL ── -->
            <div v-if="seccionActiva === 'perfil'" class="tab-contenido">
                <form @submit.prevent="guardarPerfil">

                    <!-- Avatar -->
                    <div class="campo-grupo">
                        <label class="campo-label">Elige tu avatar</label>
                        <div class="avatar-grid">
                            <button
                                v-for="av in AVATARES"
                                :key="av"
                                type="button"
                                class="avatar-opcion"
                                :class="{ seleccionado: form.avatar === av }"
                                @click="form.avatar = av"
                            >
                                {{ av }}
                            </button>
                        </div>
                    </div>

                    <!-- Nombre -->
                    <div class="campo-grupo">
                        <label class="campo-label">Nombre</label>
                        <input v-model="form.name" type="text" class="campo-input" maxlength="100" required />
                        <span v-if="form.errors.name" class="campo-error">{{ form.errors.name }}</span>
                    </div>

                    <!-- Bio -->
                    <div class="campo-grupo">
                        <label class="campo-label">Sobre mí <span class="opcional">(opcional)</span></label>
                        <textarea
                            v-model="form.bio"
                            class="campo-input"
                            rows="3"
                            maxlength="300"
                            placeholder="Cuéntanos algo sobre ti..."
                        ></textarea>
                        <span class="char-count">{{ form.bio.length }}/300</span>
                    </div>

                    <!-- Localización -->
                    <div class="campo-grupo">
                        <label class="campo-label">Ciudad <span class="opcional">(opcional)</span></label>
                        <input
                            v-model="form.location"
                            type="text"
                            class="campo-input"
                            maxlength="100"
                            placeholder="Ej: Zaragoza, España"
                        />
                    </div>

                    <!-- Fecha de nacimiento -->
                    <div class="campo-grupo">
                        <label class="campo-label">Fecha de nacimiento <span class="opcional">(opcional)</span></label>
                        <input v-model="form.birth_date" type="date" class="campo-input" />
                    </div>

                    <!-- Color de perfil -->
                    <div class="campo-grupo">
                        <label class="campo-label">Color de tu perfil</label>
                        <div class="colores-grid">
                            <button
                                v-for="color in COLORES"
                                :key="color"
                                type="button"
                                class="color-opcion"
                                :class="{ seleccionado: form.theme_color === color }"
                                :style="{ backgroundColor: color }"
                                @click="form.theme_color = color"
                            ></button>
                        </div>
                    </div>

                    <!-- Privacidad -->
                    <div class="campo-grupo">
                        <label class="campo-label">Privacidad</label>
                        <div class="privacidad-opciones">
                            <label class="toggle-fila">
                                <div class="toggle-wrap">
                                    <input type="checkbox" v-model="form.profile_public" />
                                    <span class="toggle-slider"></span>
                                </div>
                                <div class="toggle-texto">
                                    <span>Perfil público</span>
                                    <small>Otros usuarios pueden ver tu perfil</small>
                                </div>
                            </label>
                            <label class="toggle-fila">
                                <div class="toggle-wrap">
                                    <input type="checkbox" v-model="form.show_in_forum" />
                                    <span class="toggle-slider"></span>
                                </div>
                                <div class="toggle-texto">
                                    <span>Mostrar nombre en el foro</span>
                                    <small>Si está desactivado, tus posts serán siempre anónimos</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-guardar" :disabled="form.processing">
                            {{ form.processing ? 'Guardando...' : '💾 Guardar cambios' }}
                        </button>
                        <div v-if="$page.props.flash?.success" class="msg-exito">
                            ✅ {{ $page.props.flash.success }}
                        </div>
                    </div>

                </form>
            </div>

            <!-- ── PESTAÑA ACTIVIDAD ── -->
            <div v-if="seccionActiva === 'actividad'" class="tab-contenido">

                <div class="actividad-grid">
                    <!-- Historial emocional reciente -->
                    <div class="actividad-card">
                        <h3>🎭 Últimas emociones registradas</h3>
                        <div v-if="historialEmocional.length === 0" class="empty-mini">
                            Aún no has registrado ninguna emoción
                        </div>
                        <div v-else class="emocion-lista">
                            <div
                                v-for="registro in historialEmocional"
                                :key="registro.recorded_at"
                                class="emocion-fila"
                            >
                                <div class="emocion-punto" :style="{ backgroundColor: registro.color }"></div>
                                <span class="emocion-nombre">{{ registro.emotion }}</span>
                                <span class="emocion-intensidad">{{ registro.intensity }}/10</span>
                                <span class="emocion-fecha">{{ registro.recorded_at }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Últimos posts del foro -->
                    <div class="actividad-card">
                        <h3>💬 Últimas publicaciones en el foro</h3>
                        <div v-if="ultimosPosts.length === 0" class="empty-mini">
                            Todavía no has publicado nada en el foro
                        </div>
                        <div v-else class="posts-lista">
                            <div
                                v-for="post in ultimosPosts"
                                :key="post.id"
                                class="post-mini"
                            >
                                <span class="post-cat-badge">{{ categoriaLabel[post.category] }}</span>
                                <span class="post-titulo-mini">{{ post.title }}</span>
                                <span class="post-meta-mini">❤️ {{ post.likes }} · {{ post.created_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen numérico -->
                <div class="resumen-numerico">
                    <h3>📈 Resumen de actividad</h3>
                    <div class="resumen-grid-2">
                        <div class="resumen-item">
                            <span class="ri-valor">{{ stats.total_registros }}</span>
                            <span class="ri-label">Total de registros emocionales</span>
                        </div>
                        <div class="resumen-item">
                            <span class="ri-valor">{{ stats.sesiones_respira }}</span>
                            <span class="ri-label">Sesiones de respiración completadas</span>
                        </div>
                        <div class="resumen-item">
                            <span class="ri-valor">{{ stats.mensajes_hearty }}</span>
                            <span class="ri-label">Mensajes enviados a Hearty</span>
                        </div>
                        <div class="resumen-item">
                            <span class="ri-valor">{{ stats.dias_activo }}</span>
                            <span class="ri-label">Días con registro emocional</span>
                        </div>
                        <div class="resumen-item">
                            <span class="ri-valor">{{ stats.posts_foro }}</span>
                            <span class="ri-label">Publicaciones en la comunidad</span>
                        </div>
                        <div class="resumen-item">
                            <span class="ri-valor">{{ stats.puntos }}</span>
                            <span class="ri-label">Puntos de bienestar acumulados</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── PESTAÑA LOGROS ── -->
            <div v-if="seccionActiva === 'logros'" class="tab-contenido">
                <div class="logros-perfil-header">
                    <h3>Últimos logros desbloqueados</h3>
                    <a href="/logros" class="ver-todos">Ver todos →</a>
                </div>

                <div v-if="logros.length === 0" class="empty-mini">
                    Aún no has desbloqueado ningún logro. ¡Empieza registrando tu emoción de hoy!
                </div>

                <div class="logros-perfil-grid">
                    <div
                        v-for="logro in logros"
                        :key="logro.name"
                        class="logro-perfil-card"
                        :style="{ borderColor: logro.color }"
                    >
                        <div class="logro-perfil-icono" :style="{ backgroundColor: logro.color + '30' }">
                            {{ logro.emoji }}
                        </div>
                        <span class="logro-perfil-nombre">{{ logro.name }}</span>
                    </div>
                </div>

                <div class="puntos-resumen">
                    <div class="pr-item">
                        <span class="pr-valor">{{ stats.logros }}</span>
                        <span class="pr-label">Logros obtenidos</span>
                    </div>
                    <div class="pr-item">
                        <span class="pr-valor">{{ stats.puntos }}</span>
                        <span class="pr-label">Puntos totales</span>
                    </div>
                </div>
            </div>

            <!-- ── PESTAÑA SEGURIDAD ── -->
            <div v-if="seccionActiva === 'seguridad'" class="tab-contenido">

                <!-- Cambiar contraseña -->
                <div class="seguridad-card">
                    <h3>🔑 Cambiar contraseña</h3>
                    <form @submit.prevent="cambiarPassword">
                        <div class="campo-grupo">
                            <label class="campo-label">Contraseña actual</label>
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                class="campo-input"
                                placeholder="Tu contraseña actual"
                            />
                            <span v-if="passwordForm.errors.current_password" class="campo-error">
                                {{ passwordForm.errors.current_password }}
                            </span>
                        </div>
                        <div class="campo-grupo">
                            <label class="campo-label">Nueva contraseña</label>
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                class="campo-input"
                                placeholder="Mínimo 8 caracteres"
                            />
                            <span v-if="passwordForm.errors.password" class="campo-error">
                                {{ passwordForm.errors.password }}
                            </span>
                        </div>
                        <div class="campo-grupo">
                            <label class="campo-label">Confirmar nueva contraseña</label>
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="campo-input"
                                placeholder="Repite la nueva contraseña"
                            />
                        </div>
                        <button type="submit" class="btn-guardar" :disabled="passwordForm.processing">
                            {{ passwordForm.processing ? 'Actualizando...' : '🔑 Cambiar contraseña' }}
                        </button>
                    </form>
                </div>

                <!-- Información de la cuenta -->
                <div class="seguridad-card">
                    <h3>📧 Información de la cuenta</h3>
                    <p class="info-cuenta">
                        <span class="ic-label">Correo electrónico:</span>
                        <span class="ic-valor">{{ usuario.email }}</span>
                    </p>
                    <p class="info-cuenta">
                        <span class="ic-label">Miembro desde:</span>
                        <span class="ic-valor">{{ usuario.miembro_desde }}</span>
                    </p>
                </div>

                <!-- Zona de peligro -->
                <div class="seguridad-card peligro">
                    <h3>⚠️ Zona de peligro</h3>
                    <p class="peligro-texto">
                        Eliminar tu cuenta es una acción permanente. Se borrarán todos tus datos,
                        registros emocionales, posts y logros. Esta acción no se puede deshacer.
                    </p>
                    <button
                        class="btn-eliminar-cuenta"
                        @click="mostrarEliminar = !mostrarEliminar"
                    >
                        🗑️ Eliminar mi cuenta
                    </button>

                    <div v-if="mostrarEliminar" class="confirmar-eliminar">
                        <p>Introduce tu contraseña para confirmar:</p>
                        <form @submit.prevent="eliminarCuenta">
                            <input
                                v-model="deleteForm.password"
                                type="password"
                                class="campo-input"
                                placeholder="Tu contraseña actual"
                            />
                            <span v-if="deleteForm.errors.password" class="campo-error">
                                {{ deleteForm.errors.password }}
                            </span>
                            <div class="eliminar-acciones">
                                <button type="button" class="btn-cancelar" @click="mostrarEliminar = false">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-confirmar-eliminar" :disabled="deleteForm.processing">
                                    {{ deleteForm.processing ? 'Eliminando...' : 'Confirmar eliminación' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.perfil-wrapper {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 1.5rem 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ── Hero ── */
.perfil-hero {
    border-radius: 0 0 20px 20px;
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    margin: 0 -1.5rem;
}

.perfil-avatar-grande {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    flex-shrink: 0;
    border: 3px solid rgba(255,255,255,0.5);
}

.perfil-hero-info h1 {
    font-size: 1.6rem;
    font-weight: 800;
    color: white;
    margin: 0 0 0.3rem;
}

.perfil-hero-info p {
    color: rgba(255,255,255,0.9);
    margin: 0 0 0.5rem;
    font-size: 0.95rem;
}

.perfil-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.perfil-meta span {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.85);
    background: rgba(255,255,255,0.2);
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
}

/* ── Stats rápidas ── */
.stats-rapidas {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.75rem;
}

.stat-rapida {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
    border: 1px solid #f0f0f0;
    text-align: center;
}

.sr-valor {
    font-size: 1.4rem;
    font-weight: 800;
    color: #4ECDC4;
}

.sr-label {
    font-size: 0.72rem;
    color: #888;
    line-height: 1.3;
}

/* ── Tabs ── */
.tabs {
    display: flex;
    gap: 0.5rem;
    border-bottom: 2px solid #f0f0f0;
    flex-wrap: wrap;
}

.tab-btn {
    padding: 0.65rem 1.25rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 0.9rem;
    font-weight: 600;
    color: #888;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: -2px;
}

.tab-btn:hover { color: #4ECDC4; }
.tab-btn.activa {
    color: #4ECDC4;
    border-bottom-color: #4ECDC4;
}

/* ── Contenido de tabs ── */
.tab-contenido {
    background: white;
    border-radius: 16px;
    padding: 1.75rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* ── Campos del formulario ── */
.campo-grupo {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.campo-label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #2D2D2D;
}

.opcional {
    font-weight: 400;
    color: #aaa;
    font-size: 0.82rem;
}

.campo-input {
    padding: 0.75rem 1rem;
    border: 2px solid #e8f5f4;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
    background: #fafafa;
    resize: vertical;
    width: 100%;
    box-sizing: border-box;
}

.campo-input:focus { border-color: #4ECDC4; }

.campo-error {
    font-size: 0.78rem;
    color: #E63946;
}

.char-count {
    font-size: 0.75rem;
    color: #aaa;
    text-align: right;
}

/* ── Avatares ── */
.avatar-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.avatar-opcion {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 2px solid #e0e0e0;
    background: white;
    font-size: 1.4rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-opcion:hover { border-color: #4ECDC4; transform: scale(1.1); }
.avatar-opcion.seleccionado { border-color: #4ECDC4; background: #E8FAF9; }

/* ── Colores ── */
.colores-grid {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.color-opcion {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 3px solid transparent;
    cursor: pointer;
    transition: transform 0.2s, border-color 0.2s;
}

.color-opcion:hover { transform: scale(1.15); }
.color-opcion.seleccionado { border-color: #2D2D2D; transform: scale(1.15); }

/* ── Privacidad toggles ── */
.privacidad-opciones {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.toggle-fila {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.toggle-wrap {
    position: relative;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
}

.toggle-wrap input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}

.toggle-slider {
    position: absolute;
    inset: 0;
    background: #ddd;
    border-radius: 12px;
    transition: background 0.2s;
}

.toggle-slider::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    transition: transform 0.2s;
}

.toggle-wrap input:checked + .toggle-slider { background: #4ECDC4; }
.toggle-wrap input:checked + .toggle-slider::after { transform: translateX(20px); }

.toggle-texto { display: flex; flex-direction: column; gap: 0.1rem; }
.toggle-texto span { font-size: 0.9rem; font-weight: 600; color: #2D2D2D; }
.toggle-texto small { font-size: 0.78rem; color: #888; }

/* ── Acciones ── */
.form-actions { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }

.btn-guardar {
    padding: 0.85rem 2rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.2s;
    font-size: 0.95rem;
}

.btn-guardar:hover:not(:disabled) { background: #3BAFA7; }
.btn-guardar:disabled { opacity: 0.6; cursor: not-allowed; }

.msg-exito {
    background: #E8FAF9;
    color: #3BAFA7;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    font-size: 0.88rem;
    font-weight: 600;
}

/* ── Actividad ── */
.actividad-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.actividad-card {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.actividad-card h3 { font-size: 0.95rem; font-weight: 700; color: #2D2D2D; margin: 0; }

.empty-mini { text-align: center; color: #aaa; padding: 1rem; font-size: 0.88rem; }

.emocion-lista { display: flex; flex-direction: column; gap: 0.5rem; }

.emocion-fila {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.4rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.emocion-punto {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.emocion-nombre { flex: 1; font-size: 0.85rem; color: #2D2D2D; }
.emocion-intensidad { font-size: 0.82rem; font-weight: 700; color: #4ECDC4; }
.emocion-fecha { font-size: 0.75rem; color: #aaa; }

.posts-lista { display: flex; flex-direction: column; gap: 0.6rem; }

.post-mini {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.post-cat-badge {
    font-size: 0.72rem;
    background: #E8FAF9;
    color: #3BAFA7;
    padding: 0.15rem 0.5rem;
    border-radius: 8px;
    width: fit-content;
    font-weight: 600;
}

.post-titulo-mini { font-size: 0.88rem; font-weight: 600; color: #2D2D2D; }
.post-meta-mini { font-size: 0.75rem; color: #aaa; }

/* ── Resumen numérico ── */
.resumen-numerico { background: #fafafa; border-radius: 12px; padding: 1.25rem; }
.resumen-numerico h3 { font-size: 0.95rem; font-weight: 700; color: #2D2D2D; margin: 0 0 1rem; }

.resumen-grid-2 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

.resumen-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
    text-align: center;
    padding: 0.75rem;
    background: white;
    border-radius: 10px;
    border: 1px solid #f0f0f0;
}

.ri-valor { font-size: 1.4rem; font-weight: 800; color: #4ECDC4; }
.ri-label { font-size: 0.75rem; color: #888; line-height: 1.3; }

/* ── Logros del perfil ── */
.logros-perfil-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logros-perfil-header h3 { font-size: 0.95rem; font-weight: 700; color: #2D2D2D; margin: 0; }

.ver-todos {
    font-size: 0.85rem;
    color: #4ECDC4;
    font-weight: 600;
    text-decoration: none;
}

.logros-perfil-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

.logro-perfil-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: #fafafa;
    border-radius: 12px;
    border: 2px solid;
    text-align: center;
}

.logro-perfil-icono {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
}

.logro-perfil-nombre { font-size: 0.82rem; font-weight: 600; color: #2D2D2D; }

.puntos-resumen {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.pr-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
    background: #E8FAF9;
    border-radius: 12px;
    padding: 1rem 2rem;
}

.pr-valor { font-size: 2rem; font-weight: 800; color: #4ECDC4; }
.pr-label { font-size: 0.82rem; color: #3BAFA7; }

/* ── Seguridad ── */
.seguridad-card {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.seguridad-card h3 { font-size: 1rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.seguridad-card.peligro { border: 2px solid #ffd5d5; background: #fff5f5; }

.peligro-texto { font-size: 0.88rem; color: #666; line-height: 1.6; margin: 0; }

.btn-eliminar-cuenta {
    padding: 0.75rem 1.5rem;
    background: #E63946;
    color: white;
    border: none;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    font-size: 0.9rem;
    align-self: flex-start;
    transition: background 0.2s;
}

.btn-eliminar-cuenta:hover { background: #c0303b; }

.confirmar-eliminar {
    background: white;
    border-radius: 10px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    border: 1px solid #ffd5d5;
}

.confirmar-eliminar p { font-size: 0.9rem; font-weight: 600; color: #2D2D2D; margin: 0; }

.eliminar-acciones { display: flex; gap: 0.75rem; }

.btn-cancelar {
    padding: 0.65rem 1.25rem;
    background: #f5f5f5;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    color: #666;
}

.btn-confirmar-eliminar {
    padding: 0.65rem 1.25rem;
    background: #E63946;
    color: white;
    border: none;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-confirmar-eliminar:hover:not(:disabled) { background: #c0303b; }
.btn-confirmar-eliminar:disabled { opacity: 0.6; cursor: not-allowed; }

.info-cuenta {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    font-size: 0.9rem;
    margin: 0;
}

.ic-label { font-weight: 600; color: #555; min-width: 160px; }
.ic-valor { color: #2D2D2D; }

/* ── Responsive ── */
@media (max-width: 700px) {
    .stats-rapidas { grid-template-columns: repeat(3, 1fr); }
    .actividad-grid { grid-template-columns: 1fr; }
    .resumen-grid-2 { grid-template-columns: repeat(2, 1fr); }
    .logros-perfil-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 480px) {
    .stats-rapidas { grid-template-columns: repeat(2, 1fr); }
}
</style>