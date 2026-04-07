<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import axios from 'axios'
import { ref } from 'vue'

const props = defineProps({ post: Object })

const likesCount = ref(props.post.likes_count)
const likedByMe = ref(props.post.liked_by_me)
const cargandoLike = ref(false)

const commentForm = useForm({
    content: '',
    is_anonymous: false,
})

const toggleLike = async () => {
    if (cargandoLike.value) return
    cargandoLike.value = true
    try {
        const res = await axios.post(`/comunidad/${props.post.id}/like`)
        likesCount.value = res.data.likes_count
        likedByMe.value = res.data.liked
    } finally {
        cargandoLike.value = false
    }
}

const submitComment = () => {
    commentForm.post(`/comunidad/${props.post.id}/comentar`, {
        onSuccess: () => commentForm.reset(),
    })
}

const categoriaLabel = {
    ansiedad: '😰 Ansiedad',
    depresion: '😢 Depresión',
    relaciones: '💙 Relaciones',
    autoestima: '💪 Autoestima',
    sueno: '😴 Sueño',
    general: '💬 General',
}
</script>

<template>
    <AppLayout>
        <div class="show-wrapper">

            <!-- Volver -->
            <Link href="/comunidad" class="volver-link">← Volver a la comunidad</Link>

            <!-- Post principal -->
            <div class="post-completo">
                <div class="post-meta">
                    <span class="post-cat-badge">{{ categoriaLabel[post.category] }}</span>
                    <span class="post-fecha">{{ post.created_at }}</span>
                </div>

                <h1 class="post-titulo">{{ post.title }}</h1>
                <p class="post-autor">✍️ {{ post.author_name }}</p>
                <div class="post-contenido">{{ post.content }}</div>

                <!-- Acciones -->
                <div class="post-acciones">
                    <button
                        class="btn-like"
                        :class="{ activo: likedByMe }"
                        @click="toggleLike"
                        :disabled="cargandoLike"
                    >
                        {{ likedByMe ? '❤️' : '🤍' }} {{ likesCount }} Me apoya
                    </button>
                    <span class="comments-count">💬 {{ post.comments.length }} comentarios</span>
                </div>
            </div>

            <!-- Comentarios -->
            <div class="comentarios-seccion">
                <h2>Comentarios</h2>

                <div v-if="post.comments.length === 0" class="no-comments">
                    Sé el primero en responder 💚
                </div>

                <div
                    v-for="comment in post.comments"
                    :key="comment.id"
                    class="comment-card"
                >
                    <div class="comment-header">
                        <span class="comment-autor">{{ comment.author_name }}</span>
                        <span class="comment-fecha">{{ comment.created_at }}</span>
                    </div>
                    <p class="comment-content">{{ comment.content }}</p>
                </div>

                <!-- Formulario comentario -->
                <div class="comment-form">
                    <h3>Añade tu apoyo</h3>
                    <form @submit.prevent="submitComment">
                        <textarea
                            v-model="commentForm.content"
                            placeholder="Escribe algo de apoyo o comparte tu experiencia..."
                            rows="3"
                            maxlength="500"
                        ></textarea>
                        <span v-if="commentForm.errors.content" class="error">
                            {{ commentForm.errors.content }}
                        </span>

                        <div class="comment-actions">
                            <label class="anon-check">
                                <input type="checkbox" v-model="commentForm.is_anonymous" />
                                Comentar anónimamente
                            </label>
                            <button type="submit" :disabled="commentForm.processing" class="btn-comentar">
                                {{ commentForm.processing ? 'Enviando...' : '💬 Comentar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.show-wrapper {
    max-width: 720px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.volver-link {
    color: #3BAFA7;
    font-weight: 600;
    text-decoration: none;
    font-size: 0.9rem;
}

.volver-link:hover { text-decoration: underline; }

.post-completo {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid #f0f0f0;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.post-cat-badge {
    background: #E8FAF9;
    color: #3BAFA7;
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.82rem;
    font-weight: 600;
}

.post-fecha {
    font-size: 0.82rem;
    color: #aaa;
}

.post-titulo {
    font-size: 1.5rem;
    font-weight: 800;
    color: #2D2D2D;
    margin: 0 0 0.5rem;
    line-height: 1.3;
}

.post-autor {
    font-size: 0.85rem;
    color: #888;
    margin: 0 0 1.5rem;
}

.post-contenido {
    font-size: 1rem;
    line-height: 1.8;
    color: #444;
    white-space: pre-wrap;
}

.post-acciones {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
}

.btn-like {
    padding: 0.6rem 1.2rem;
    border: 2px solid #e0e0e0;
    border-radius: 25px;
    background: white;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    color: #555;
}

.btn-like.activo {
    border-color: #E63946;
    color: #E63946;
    background: #fff5f5;
}

.comments-count {
    font-size: 0.9rem;
    color: #888;
}

.comentarios-seccion {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.comentarios-seccion h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
}

.no-comments {
    text-align: center;
    color: #aaa;
    padding: 1.5rem;
    font-size: 0.95rem;
}

.comment-card {
    background: white;
    border: 1px solid #f0f0f0;
    border-radius: 12px;
    padding: 1rem 1.25rem;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.comment-autor {
    font-size: 0.85rem;
    font-weight: 600;
    color: #3BAFA7;
}

.comment-fecha {
    font-size: 0.78rem;
    color: #aaa;
}

.comment-content {
    font-size: 0.92rem;
    color: #444;
    line-height: 1.6;
    margin: 0;
}

.comment-form {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #f0f0f0;
}

.comment-form h3 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 1rem;
    color: #2D2D2D;
}

.comment-form textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e8f5f4;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    transition: border-color 0.2s;
    background: white;
    box-sizing: border-box;
}

.comment-form textarea:focus { border-color: #4ECDC4; }

.comment-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.75rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.anon-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #666;
    cursor: pointer;
}

.btn-comentar {
    padding: 0.65rem 1.5rem;
    background: #4ECDC4;
    color: white;
    border: none;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
    font-size: 0.9rem;
}

.btn-comentar:hover { background: #3BAFA7; }
.btn-comentar:disabled { opacity: 0.6; cursor: not-allowed; }

.error {
    font-size: 0.78rem;
    color: #E63946;
    display: block;
    margin-top: 0.3rem;
}
</style>