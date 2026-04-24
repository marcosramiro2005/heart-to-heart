<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    posts:      Object,
    destacados: Array,
    trending:   Array,
    stats:      Object,
    categoria:  String,
    busqueda:   String,
    orden:      String,
})

const page        = usePage()
const mostrarForm = ref(false)
const busqueda    = ref(props.busqueda || '')
const nuevoPost   = ref({ title: '', content: '', is_anonymous: false, categoria: 'general' })
const enviando    = ref(false)

const categorias = [
    { id: 'todos',      label: 'Todos',       emoji: '🌐' },
    { id: 'general',    label: 'General',      emoji: '💬' },
    { id: 'ansiedad',   label: 'Ansiedad',     emoji: '😰' },
    { id: 'depresion',  label: 'Depresión',    emoji: '😢' },
    { id: 'relaciones', label: 'Relaciones',   emoji: '💙' },
    { id: 'autoestima', label: 'Autoestima',   emoji: '💪' },
    { id: 'duelo',      label: 'Duelo',        emoji: '🕊️' },
    { id: 'otros',      label: 'Otros',        emoji: '🌿' },
]

const ordenes = [
    { id: 'reciente',  label: '🕐 Reciente' },
    { id: 'popular',   label: '❤️ Popular' },
    { id: 'comentado', label: '💬 Más comentado' },
]

const colorCategoria = (cat) => ({
    general:    '#E8FAF9',
    ansiedad:   '#d0eaf8',
    depresion:  '#e8d5f5',
    relaciones: '#fce4ec',
    autoestima: '#fff9c4',
    duelo:      '#e8eaf6',
    otros:      '#d4edda',
}[cat] ?? '#fafafa')

const filtrar = (cat) => {
    router.get('/comunidad', {
        categoria: cat,
        orden:     props.orden,
        busqueda:  busqueda.value,
    }, { preserveState: true })
}

const cambiarOrden = (ord) => {
    router.get('/comunidad', {
        categoria: props.categoria,
        orden:     ord,
        busqueda:  busqueda.value,
    }, { preserveState: true })
}

const buscar = () => {
    router.get('/comunidad', {
        categoria: props.categoria,
        orden:     props.orden,
        busqueda:  busqueda.value,
    })
}

const publicar = () => {
    if (!nuevoPost.value.title.trim() || !nuevoPost.value.content.trim()) return
    enviando.value = true
    router.post('/comunidad', nuevoPost.value, {
        onSuccess: () => {
            mostrarForm.value = false
            nuevoPost.value   = { title: '', content: '', is_anonymous: false, categoria: 'general' }
            enviando.value    = false
        },
        onError: () => { enviando.value = false }
    })
}

const toggleLike = async (postId) => {
    await axios.post(`/comunidad/${postId}/like`)
    router.reload({ only: ['posts'] })
}

const contadorChars = computed(() => nuevoPost.value.content.length)
</script>

<template>
    <AppLayout>
        <div class="foro-wrapper">

            <!-- Cabecera -->
            <div class="foro-header">
                <div>
                    <h1>💬 Comunidad Heart to Heart</h1>
                    <p>Un espacio seguro para compartir, escuchar y apoyarse mutuamente</p>
                </div>
                <button class="btn-nuevo-post" @click="mostrarForm = !mostrarForm">
                    {{ mostrarForm ? '✕ Cancelar' : '+ Nuevo post' }}
                </button>
            </div>

            <!-- Stats rápidas -->
            <div class="foro-stats">
                <div class="fs-item">
                    <span class="fs-val">{{ stats.total_posts }}</span>
                    <span class="fs-lab">Posts totales</span>
                </div>
                <div class="fs-item">
                    <span class="fs-val">{{ stats.posts_hoy }}</span>
                    <span class="fs-lab">Hoy</span>
                </div>
                <div class="fs-item">
                    <span class="fs-val">{{ stats.total_usuarios }}</span>
                    <span class="fs-lab">Participantes</span>
                </div>
            </div>

            <!-- Formulario nuevo post -->
            <Transition name="slide-down">
                <div v-if="mostrarForm" class="nuevo-post-form">
                    <h3>✍️ Compartir con la comunidad</h3>

                    <div class="npf-row">
                        <div class="form-group fg-flex">
                            <label>Categoría</label>
                            <select v-model="nuevoPost.categoria" class="npf-select">
                                <option v-for="cat in categorias.filter(c => c.id !== 'todos')"
                                    :key="cat.id" :value="cat.id">
                                    {{ cat.emoji }} {{ cat.label }}
                                </option>
                            </select>
                        </div>
                        <div class="npf-anon">
                            <input type="checkbox" id="anon" v-model="nuevoPost.is_anonymous" />
                            <label for="anon">🎭 Publicar como anónimo/a</label>
                        </div>
                    </div>

                    <input
                        v-model="nuevoPost.title"
                        type="text"
                        placeholder="Título de tu post..."
                        class="npf-titulo"
                        maxlength="200"
                    />

                    <div class="npf-contenido-wrap">
                        <textarea
                            v-model="nuevoPost.content"
                            placeholder="Comparte lo que sientes, una experiencia, un consejo... Este es tu espacio seguro."
                            rows="5"
                            class="npf-contenido"
                            maxlength="2000"
                        ></textarea>
                        <span class="npf-chars">{{ contadorChars }}/2000</span>
                    </div>

                    <button
                        class="btn-publicar"
                        @click="publicar"
                        :disabled="!nuevoPost.title.trim() || !nuevoPost.content.trim() || enviando"
                    >
                        {{ enviando ? '⏳ Publicando...' : '💚 Publicar' }}
                    </button>
                </div>
            </Transition>

            <div class="foro-grid">

                <!-- Columna principal -->
                <div class="foro-main">

                    <!-- Filtros -->
                    <div class="foro-filtros">
                        <div class="cats-scroll">
                            <button
                                v-for="cat in categorias"
                                :key="cat.id"
                                class="cat-btn"
                                :class="{ activa: categoria === cat.id }"
                                @click="filtrar(cat.id)"
                            >
                                {{ cat.emoji }} {{ cat.label }}
                            </button>
                        </div>

                        <div class="filtros-bottom">
                            <div class="orden-btns">
                                <button
                                    v-for="ord in ordenes"
                                    :key="ord.id"
                                    class="ord-btn"
                                    :class="{ activo: orden === ord.id }"
                                    @click="cambiarOrden(ord.id)"
                                >
                                    {{ ord.label }}
                                </button>
                            </div>
                            <div class="buscador-foro">
                                <input
                                    v-model="busqueda"
                                    type="text"
                                    placeholder="Buscar..."
                                    @keyup.enter="buscar"
                                />
                                <button @click="buscar">🔍</button>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de posts -->
                    <div v-if="posts.data.length === 0" class="foro-empty">
                        <span>💬</span>
                        <p>No hay posts en esta categoría. ¡Sé el primero en compartir!</p>
                    </div>

                    <div v-else class="posts-lista">
                        <div
                            v-for="post in posts.data"
                            :key="post.id"
                            class="post-card"
                            :class="{ destacado: post.is_featured }"
                        >
                            <div v-if="post.is_featured" class="post-featured-badge">⭐ Destacado</div>

                            <div class="pc-header">
                                <div class="pc-autor">
                                    <span class="pc-avatar">{{ post.avatar }}</span>
                                    <div>
                                        <span class="pc-nombre">{{ post.autor }}</span>
                                        <span class="pc-fecha">{{ post.fecha }}</span>
                                    </div>
                                </div>
                                <span
                                    class="pc-categoria"
                                    :style="{ backgroundColor: colorCategoria(post.categoria) }"
                                >
                                    {{ categorias.find(c => c.id === post.categoria)?.emoji }}
                                    {{ post.categoria }}
                                </span>
                            </div>

                            <Link :href="`/comunidad/${post.id}`" class="pc-titulo">
                                {{ post.title }}
                            </Link>

                            <p class="pc-content">{{ post.content }}</p>

                            <div class="pc-footer">
                                <button
                                    class="pc-like-btn"
                                    :class="{ liked: post.liked }"
                                    @click="toggleLike(post.id)"
                                >
                                    {{ post.liked ? '❤️' : '🤍' }} {{ post.likes_count }}
                                </button>
                                <Link :href="`/comunidad/${post.id}`" class="pc-comments">
                                    💬 {{ post.comments_count }}
                                </Link>
                                <span class="pc-views">👁 {{ post.views }}</span>
                                <Link :href="`/comunidad/${post.id}`" class="pc-leer">
                                    Leer →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div v-if="posts.last_page > 1" class="paginacion">
                        <button
                            :disabled="posts.current_page === 1"
                            @click="router.get('/comunidad', { categoria, orden, busqueda, page: posts.current_page - 1 })"
                            class="pag-btn"
                        >← Anterior</button>
                        <span>{{ posts.current_page }} / {{ posts.last_page }}</span>
                        <button
                            :disabled="posts.current_page === posts.last_page"
                            @click="router.get('/comunidad', { categoria, orden, busqueda, page: posts.current_page + 1 })"
                            class="pag-btn"
                        >Siguiente →</button>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="foro-sidebar">

                    <!-- Trending -->
                    <div class="sidebar-card" v-if="trending.length > 0">
                        <h3>🔥 Trending esta semana</h3>
                        <div class="trending-lista">
                            <Link
                                v-for="(t, i) in trending"
                                :key="t.id"
                                :href="`/comunidad/${t.id}`"
                                class="trending-item"
                            >
                                <span class="ti-num">{{ i + 1 }}</span>
                                <span class="ti-titulo">{{ t.title }}</span>
                                <span class="ti-score">🔥{{ t.score }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Destacados -->
                    <div class="sidebar-card" v-if="destacados.length > 0">
                        <h3>⭐ Posts destacados</h3>
                        <div class="destacados-lista">
                            <Link
                                v-for="dest in destacados"
                                :key="dest.id"
                                :href="`/comunidad/${dest.id}`"
                                class="dest-item"
                            >
                                <span class="di-avatar">{{ dest.avatar }}</span>
                                <div class="di-info">
                                    <span class="di-titulo">{{ dest.title }}</span>
                                    <span class="di-meta">❤️ {{ dest.likes_count }} · 💬 {{ dest.comments_count }}</span>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Normas -->
                    <div class="sidebar-card normas-card">
                        <h3>📋 Normas de la comunidad</h3>
                        <ul>
                            <li>🤝 Trata a todos con respeto y empatía</li>
                            <li>🔒 Puedes publicar de forma anónima</li>
                            <li>💙 Si alguien está en crisis, recuerda el 024</li>
                            <li>🚫 No se permite contenido dañino o discriminatorio</li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.foro-wrapper {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ── Header ── */
.foro-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.foro-header h1 { font-size: 1.6rem; font-weight: 800; margin: 0; }
.foro-header p  { color: #666; margin: 0.25rem 0 0; font-size: 0.92rem; }

.btn-nuevo-post {
    padding: 0.75rem 1.5rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
    white-space: nowrap;
}

.btn-nuevo-post:hover { background: #3BAFA7; }

/* ── Stats ── */
.foro-stats {
    display: flex;
    gap: 1rem;
}

.fs-item {
    background: white;
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    border: 1px solid #f0f0f0;
    text-align: center;
}

.fs-val { display: block; font-size: 1.5rem; font-weight: 900; color: #4ECDC4; }
.fs-lab { display: block; font-size: 0.72rem; color: #888; font-weight: 600; }

/* ── Formulario ── */
.nuevo-post-form {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 2px solid #4ECDC4;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.nuevo-post-form h3 { font-size: 1rem; font-weight: 700; margin: 0; }

.npf-row {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    flex-wrap: wrap;
}

.fg-flex { display: flex; flex-direction: column; gap: 0.35rem; flex: 1; }
.fg-flex label { font-size: 0.82rem; font-weight: 700; color: #2D2D2D; }

.npf-select {
    padding: 0.6rem 0.75rem;
    border: 2px solid #e8f5f4;
    border-radius: 10px;
    font-size: 0.88rem;
    font-family: inherit;
    outline: none;
    background: white;
    cursor: pointer;
}

.npf-anon {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #555;
    padding-bottom: 0.15rem;
}

.npf-anon input { accent-color: #4ECDC4; cursor: pointer; }
.npf-anon label { cursor: pointer; }

.npf-titulo {
    padding: 0.75rem 1rem;
    border: 2px solid #e8f5f4;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
}

.npf-titulo:focus { border-color: #4ECDC4; }

.npf-contenido-wrap { position: relative; }

.npf-contenido {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e8f5f4;
    border-radius: 10px;
    font-size: 0.92rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    line-height: 1.6;
    transition: border-color 0.2s;
}

.npf-contenido:focus { border-color: #4ECDC4; }

.npf-chars {
    position: absolute;
    bottom: 8px;
    right: 10px;
    font-size: 0.72rem;
    color: #aaa;
}

.btn-publicar {
    padding: 0.85rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
}

.btn-publicar:hover:not(:disabled) { background: #3BAFA7; }
.btn-publicar:disabled { opacity: 0.5; cursor: not-allowed; }

/* Animación formulario */
.slide-down-enter-active, .slide-down-leave-active {
    transition: opacity 0.2s, transform 0.2s;
}
.slide-down-enter-from, .slide-down-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* ── Grid ── */
.foro-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 1.5rem;
    align-items: start;
}

/* ── Filtros ── */
.foro-filtros { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1rem; }

.cats-scroll { display: flex; flex-wrap: wrap; gap: 0.4rem; }

.cat-btn {
    padding: 0.4rem 0.9rem;
    border: 1.5px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
    font-family: inherit;
    white-space: nowrap;
}

.cat-btn.activa { background: #4ECDC4; border-color: #4ECDC4; color: white; }

.filtros-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.orden-btns { display: flex; gap: 0.4rem; }

.ord-btn {
    padding: 0.35rem 0.85rem;
    border: 1.5px solid #e0e0e0;
    border-radius: 16px;
    background: white;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
    font-family: inherit;
}

.ord-btn.activo { background: #E8FAF9; border-color: #4ECDC4; color: #3BAFA7; }

.buscador-foro { display: flex; gap: 0.4rem; }

.buscador-foro input {
    padding: 0.4rem 0.75rem;
    border: 1.5px solid #e0e0e0;
    border-radius: 20px;
    font-size: 0.82rem;
    font-family: inherit;
    outline: none;
    width: 160px;
    transition: border-color 0.2s;
}

.buscador-foro input:focus { border-color: #4ECDC4; }

.buscador-foro button {
    padding: 0.4rem 0.75rem;
    background: #4ECDC4;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.85rem;
}

/* ── Posts ── */
.posts-lista { display: flex; flex-direction: column; gap: 0.75rem; }

.foro-empty {
    text-align: center;
    padding: 3rem;
    background: #fafafa;
    border-radius: 16px;
}

.foro-empty span { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; }
.foro-empty p    { color: #aaa; font-size: 0.9rem; }

.post-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: box-shadow 0.2s, transform 0.2s;
    position: relative;
}

.post-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-1px); }
.post-card.destacado { border-color: #FFD700; }

.post-featured-badge {
    position: absolute;
    top: -1px;
    right: 1rem;
    background: #FFD700;
    color: #2D2D2D;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 0 0 8px 8px;
}

.pc-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
}

.pc-autor { display: flex; align-items: center; gap: 0.6rem; }
.pc-avatar { font-size: 1.3rem; }
.pc-nombre { display: block; font-size: 0.85rem; font-weight: 700; color: #2D2D2D; }
.pc-fecha  { display: block; font-size: 0.72rem; color: #aaa; }

.pc-categoria {
    padding: 0.2rem 0.65rem;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #2D2D2D;
    white-space: nowrap;
}

.pc-titulo {
    font-size: 1rem;
    font-weight: 700;
    color: #2D2D2D;
    text-decoration: none;
    line-height: 1.4;
    transition: color 0.2s;
}

.pc-titulo:hover { color: #4ECDC4; }

.pc-content {
    font-size: 0.88rem;
    color: #666;
    line-height: 1.5;
    margin: 0;
}

.pc-footer {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f0f0f0;
}

.pc-like-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    color: #888;
    padding: 0;
    transition: transform 0.2s;
    font-family: inherit;
}

.pc-like-btn:hover { transform: scale(1.1); }
.pc-like-btn.liked { color: #E63946; }

.pc-comments, .pc-views {
    font-size: 0.82rem;
    color: #aaa;
    text-decoration: none;
}

.pc-leer {
    margin-left: auto;
    font-size: 0.82rem;
    font-weight: 700;
    color: #4ECDC4;
    text-decoration: none;
}

/* ── Paginación ── */
.paginacion {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}

.pag-btn {
    padding: 0.6rem 1.25rem;
    border: 2px solid #4ECDC4;
    background: white;
    color: #3BAFA7;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
}

.pag-btn:hover:not(:disabled) { background: #4ECDC4; color: white; }
.pag-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* ── Sidebar ── */
.sidebar-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    margin-bottom: 1rem;
}

.sidebar-card h3 {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0 0 0.75rem;
    color: #2D2D2D;
}

/* Trending */
.trending-lista { display: flex; flex-direction: column; gap: 0.5rem; }

.trending-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    text-decoration: none;
    padding: 0.4rem 0;
    border-bottom: 1px solid #f0f0f0;
    transition: color 0.2s;
}

.trending-item:last-child { border-bottom: none; }
.trending-item:hover .ti-titulo { color: #4ECDC4; }

.ti-num { font-size: 0.8rem; font-weight: 900; color: #4ECDC4; min-width: 18px; }
.ti-titulo { flex: 1; font-size: 0.82rem; color: #2D2D2D; line-height: 1.3; }
.ti-score  { font-size: 0.75rem; color: #aaa; white-space: nowrap; }

/* Destacados */
.destacados-lista { display: flex; flex-direction: column; gap: 0.75rem; }

.dest-item {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    text-decoration: none;
    transition: opacity 0.2s;
}

.dest-item:hover { opacity: 0.8; }

.di-avatar { font-size: 1.3rem; flex-shrink: 0; }
.di-info   { display: flex; flex-direction: column; gap: 0.15rem; }
.di-titulo { font-size: 0.82rem; font-weight: 600; color: #2D2D2D; line-height: 1.3; }
.di-meta   { font-size: 0.72rem; color: #aaa; }

/* Normas */
.normas-card ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.normas-card li { font-size: 0.82rem; color: #555; line-height: 1.4; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .foro-grid    { grid-template-columns: 1fr; }
    .foro-sidebar { display: none; }
    .foro-stats   { flex-wrap: wrap; }
    .fs-item      { flex: 1; min-width: 80px; }
}

@media (max-width: 600px) {
    .foro-wrapper       { padding: 1.25rem 1rem; }
    .foro-header h1     { font-size: 1.3rem; }
    .filtros-bottom     { flex-direction: column; align-items: flex-start; }
    .orden-btns         { flex-wrap: wrap; }
    .buscador-foro      { width: 100%; }
    .buscador-foro input { flex: 1; width: 100%; }
    .npf-row            { flex-direction: column; }
}

@media (max-width: 480px) {
    .cats-scroll { gap: 0.3rem; }
    .cat-btn     { padding: 0.35rem 0.7rem; font-size: 0.78rem; }
}
</style>