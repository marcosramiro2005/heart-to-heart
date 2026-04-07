<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
    posts: Object,
    categoria: String,
    busqueda: String,
})

const categorias = [
    { id: 'todos', label: '🌐 Todos', color: '#f0f0f0' },
    { id: 'ansiedad', label: '😰 Ansiedad', color: '#d0eaf8' },
    { id: 'depresion', label: '😢 Depresión', color: '#e8d5f5' },
    { id: 'relaciones', label: '💙 Relaciones', color: '#ffd5d5' },
    { id: 'autoestima', label: '💪 Autoestima', color: '#d4edda' },
    { id: 'sueno', label: '😴 Sueño', color: '#fff9c4' },
    { id: 'general', label: '💬 General', color: '#E8FAF9' },
]

const busqueda = ref(props.busqueda || '')
const mostrarFormulario = ref(false)

const filtrar = (cat) => {
    router.get('/comunidad', { categoria: cat, busqueda: busqueda.value }, { preserveState: true })
}

const buscar = () => {
    router.get('/comunidad', { categoria: props.categoria, busqueda: busqueda.value }, { preserveState: true })
}

const getCategoriaColor = (cat) => {
    return categorias.find(c => c.id === cat)?.color || '#f0f0f0'
}

const getCategoriaLabel = (cat) => {
    return categorias.find(c => c.id === cat)?.label || cat
}
</script>

<template>
    <AppLayout>
        <div class="forum-wrapper">

            <!-- Cabecera -->
            <div class="forum-hero">
                <div class="forum-hero-text">
                    <h1>💬 Comunidad</h1>
                    <p>Un espacio seguro para compartir, escuchar y acompañar</p>
                </div>
                <button class="btn-nuevo-post" @click="mostrarFormulario = true">
                    + Compartir experiencia
                </button>
            </div>

            <!-- Buscador -->
            <div class="buscador">
                <input
                    v-model="busqueda"
                    type="text"
                    placeholder="Buscar en la comunidad..."
                    @keyup.enter="buscar"
                />
                <button @click="buscar">🔍</button>
            </div>

            <!-- Filtros de categoría -->
            <div class="categorias-filtro">
                <button
                    v-for="cat in categorias"
                    :key="cat.id"
                    class="cat-btn"
                    :class="{ activa: categoria === cat.id }"
                    :style="{ backgroundColor: categoria === cat.id ? '#4ECDC4' : cat.color }"
                    @click="filtrar(cat.id)"
                >
                    {{ cat.label }}
                </button>
            </div>

            <!-- Lista de posts -->
            <div class="posts-lista">
                <div v-if="posts.data.length === 0" class="empty-state">
                    <p>No hay publicaciones en esta categoría todavía.</p>
                    <p>¡Sé el primero en compartir tu experiencia! 💚</p>
                </div>

                <div
                    v-for="post in posts.data"
                    :key="post.id"
                    class="post-card"
                    :class="{ pinned: post.is_pinned }"
                >
                    <div class="post-header">
                        <span
                            class="post-categoria"
                            :style="{ backgroundColor: getCategoriaColor(post.category) }"
                        >
                            {{ getCategoriaLabel(post.category) }}
                        </span>
                        <span v-if="post.is_pinned" class="pin-badge">📌 Destacado</span>
                        <span class="post-fecha">{{ post.created_at }}</span>
                    </div>

                    <Link :href="`/comunidad/${post.id}`" class="post-titulo">
                        {{ post.title }}
                    </Link>

                    <p class="post-preview">{{ post.content }}...</p>

                    <div class="post-footer">
                        <span class="post-autor">✍️ {{ post.author_name }}</span>
                        <div class="post-stats">
                            <span class="stat" :class="{ liked: post.liked_by_me }">
                                ❤️ {{ post.likes_count }}
                            </span>
                            <span class="stat">💬 {{ post.comments_count }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <div class="paginacion" v-if="posts.last_page > 1">
                <button
                    v-for="page in posts.last_page"
                    :key="page"
                    class="page-btn"
                    :class="{ activa: page === posts.current_page }"
                    @click="router.get('/comunidad', { categoria, busqueda, page })"
                >
                    {{ page }}
                </button>
            </div>

        </div>

        <!-- Modal nuevo post -->
        <div v-if="mostrarFormulario" class="modal-overlay" @click.self="mostrarFormulario = false">
            <NuevoPost @cerrar="mostrarFormulario = false" />
        </div>

    </AppLayout>
</template>

<script>
import NuevoPost from './NuevoPost.vue'
export default { components: { NuevoPost } }
</script>

<style scoped>
.forum-wrapper {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.forum-hero {
    background: #4ECDC4;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.forum-hero-text h1 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0 0 0.3rem;
}

.forum-hero-text p {
    color: #2D2D2D;
    margin: 0;
    font-size: 0.95rem;
}

.btn-nuevo-post {
    padding: 0.75rem 1.5rem;
    background: white;
    color: #3BAFA7;
    font-weight: 700;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: transform 0.2s;
    white-space: nowrap;
}

.btn-nuevo-post:hover { transform: translateY(-2px); }

.buscador {
    display: flex;
    gap: 0.5rem;
}

.buscador input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 25px;
    font-size: 0.95rem;
    outline: none;
    transition: border-color 0.2s;
    font-family: inherit;
}

.buscador input:focus { border-color: #4ECDC4; }

.buscador button {
    padding: 0.75rem 1rem;
    background: #4ECDC4;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 1rem;
}

.categorias-filtro {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.cat-btn {
    padding: 0.4rem 1rem;
    border: 2px solid transparent;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    color: #2D2D2D;
}

.cat-btn.activa {
    color: white;
    border-color: #3BAFA7;
}

.posts-lista {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #666;
    font-size: 1rem;
    line-height: 2;
}

.post-card {
    background: white;
    border: 1px solid #f0f0f0;
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    transition: box-shadow 0.2s;
}

.post-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.post-card.pinned {
    border-left: 4px solid #4ECDC4;
}

.post-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.post-categoria {
    padding: 0.2rem 0.7rem;
    border-radius: 12px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #2D2D2D;
}

.pin-badge {
    font-size: 0.78rem;
    color: #3BAFA7;
    font-weight: 600;
}

.post-fecha {
    font-size: 0.78rem;
    color: #aaa;
    margin-left: auto;
}

.post-titulo {
    font-size: 1.05rem;
    font-weight: 700;
    color: #2D2D2D;
    text-decoration: none;
    transition: color 0.2s;
}

.post-titulo:hover { color: #4ECDC4; }

.post-preview {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.5;
    margin: 0;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.3rem;
}

.post-autor {
    font-size: 0.82rem;
    color: #888;
}

.post-stats {
    display: flex;
    gap: 1rem;
}

.stat {
    font-size: 0.85rem;
    color: #888;
}

.stat.liked { color: #E63946; }

.paginacion {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.page-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid #e0e0e0;
    background: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.page-btn.activa {
    background: #4ECDC4;
    border-color: #4ECDC4;
    color: white;
}

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}
</style>