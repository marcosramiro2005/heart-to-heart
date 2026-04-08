<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    recurso:      Object,
    relacionados: Array,
})

const guardado = ref(props.recurso.is_saved)

const toggleGuardar = async () => {
    await axios.post(`/biblioteca/${props.recurso.id}/guardar`)
    guardado.value = !guardado.value
}
</script>

<template>
    <AppLayout>
        <div class="show-wrapper">

            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <Link href="/biblioteca">← Biblioteca</Link>
                <span>/</span>
                <span>{{ recurso.category }}</span>
            </div>

            <!-- Artículo principal -->
            <article class="articulo">
                <div class="art-header" :style="{ backgroundColor: recurso.category_color }">
                    <span class="art-tipo">{{ recurso.type_label }}</span>
                    <span class="art-tiempo">⏱ {{ recurso.read_time }} min de lectura</span>
                </div>

                <div class="art-body">
                    <h1>{{ recurso.title }}</h1>
                    <p class="art-summary">{{ recurso.summary }}</p>

                    <div class="art-meta">
                        <span class="art-views">👁 {{ recurso.views }} lecturas</span>
                        <button
                            class="btn-guardar-art"
                            :class="{ guardado }"
                            @click="toggleGuardar"
                        >
                            {{ guardado ? '🔖 Guardado' : '📄 Guardar' }}
                        </button>
                    </div>

                    <hr class="separador" />

                    <div class="art-content">
                        {{ recurso.content }}
                    </div>

                    <div v-if="recurso.external_url" class="art-externo">
                        <a :href="recurso.external_url" target="_blank" rel="noopener">
                            🔗 Ver recurso externo →
                        </a>
                    </div>
                </div>
            </article>

            <!-- Recursos relacionados -->
            <div v-if="relacionados.length > 0" class="relacionados">
                <h2>También puede interesarte</h2>
                <div class="relacionados-grid">
                    <Link
                        v-for="rel in relacionados"
                        :key="rel.id"
                        :href="`/biblioteca/${rel.id}`"
                        class="rel-card"
                    >
                        <div class="rel-bar" :style="{ backgroundColor: rel.category_color }"></div>
                        <div class="rel-body">
                            <span class="rel-tipo">{{ rel.type_label }}</span>
                            <h3>{{ rel.title }}</h3>
                            <span class="rel-tiempo">⏱ {{ rel.read_time }} min</span>
                        </div>
                    </Link>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.show-wrapper {
    max-width: 780px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.88rem;
    color: #888;
}

.breadcrumb a { color: #4ECDC4; text-decoration: none; font-weight: 600; }
.breadcrumb a:hover { text-decoration: underline; }

.articulo {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #f0f0f0;
}

.art-header {
    padding: 0.75rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.art-tipo  { font-size: 0.85rem; font-weight: 700; color: #2D2D2D; }
.art-tiempo{ font-size: 0.82rem; color: #555; }

.art-body { padding: 2rem; display: flex; flex-direction: column; gap: 1rem; }

.art-body h1 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #2D2D2D;
    line-height: 1.3;
    margin: 0;
}

.art-summary {
    font-size: 1rem;
    color: #555;
    line-height: 1.6;
    font-style: italic;
    margin: 0;
    border-left: 4px solid #4ECDC4;
    padding-left: 1rem;
}

.art-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.art-views { font-size: 0.82rem; color: #aaa; }

.btn-guardar-art {
    padding: 0.5rem 1.2rem;
    background: #E8FAF9;
    color: #3BAFA7;
    border: 2px solid #4ECDC4;
    border-radius: 25px;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-guardar-art:hover { background: #4ECDC4; color: white; }
.btn-guardar-art.guardado { background: #4ECDC4; color: white; }

.separador { border: none; border-top: 2px solid #f0f0f0; margin: 0; }

.art-content {
    font-size: 1rem;
    color: #444;
    line-height: 1.9;
    white-space: pre-wrap;
}

.art-externo {
    background: #E8FAF9;
    border-radius: 10px;
    padding: 1rem 1.25rem;
}

.art-externo a {
    color: #3BAFA7;
    font-weight: 700;
    text-decoration: none;
    font-size: 0.95rem;
}

/* ── Relacionados ── */
.relacionados h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0 0 1rem;
}

.relacionados-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

.rel-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #f0f0f0;
    text-decoration: none;
    transition: box-shadow 0.2s, transform 0.2s;
    display: flex;
    flex-direction: column;
}

.rel-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.08); transform: translateY(-2px); }

.rel-bar { height: 6px; }

.rel-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.rel-tipo { font-size: 0.75rem; color: #888; font-weight: 600; }

.rel-body h3 {
    font-size: 0.88rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
    line-height: 1.4;
}

.rel-tiempo { font-size: 0.75rem; color: #aaa; }

@media (max-width: 600px) {
    .relacionados-grid { grid-template-columns: 1fr; }
}
</style>