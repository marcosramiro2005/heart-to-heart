<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import { ref } from 'vue'

const props = defineProps({ articulos: Array })
const articulos = ref([...props.articulos])

const eliminarGuardado = async (articulo, index) => {
    await axios.post('/recursos/guardar', {
        url:         articulo.url,
        title:       articulo.title,
        description: articulo.description,
        image_url:   articulo.image_url,
        source_name: articulo.source_name,
    })
    articulos.value.splice(index, 1)
}

const abrirArticulo = (url) => {
    window.open(url, '_blank', 'noopener')
}
</script>

<template>
    <AppLayout>
        <div class="guardadas-wrapper">

            <div class="guardadas-header">
                <Link href="/recursos" class="volver">← Volver a recursos</Link>
                <h1>🔖 Artículos guardados</h1>
                <p>{{ articulos.length }} artículo{{ articulos.length !== 1 ? 's' : '' }} guardado{{ articulos.length !== 1 ? 's' : '' }}</p>
            </div>

            <div v-if="articulos.length === 0" class="empty-state">
                <p>No tienes artículos guardados todavía.</p>
                <Link href="/recursos" class="btn-explorar">Explorar recursos →</Link>
            </div>

            <div class="guardadas-lista">
                <div
                    v-for="(articulo, i) in articulos"
                    :key="articulo.url_hash"
                    class="guardada-card"
                >
                    <div
                        class="guardada-img"
                        :style="articulo.image_url ? `background-image: url(${articulo.image_url})` : ''"
                    >
                        <div v-if="!articulo.image_url" class="img-placeholder">🧠</div>
                    </div>

                    <div class="guardada-body">
                        <div class="guardada-meta">
                            <span class="fuente">{{ articulo.source_name }}</span>
                            <span class="fecha">{{ articulo.published_at }}</span>
                        </div>
                        <h3>{{ articulo.title }}</h3>
                        <p>{{ articulo.description }}</p>
                        <div class="guardada-actions">
                            <button class="btn-leer" @click="abrirArticulo(articulo.url)">
                                Leer artículo →
                            </button>
                            <button class="btn-eliminar" @click="eliminarGuardado(articulo, i)">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.guardadas-wrapper {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.guardadas-header { display: flex; flex-direction: column; gap: 0.4rem; }

.volver {
    color: #3BAFA7;
    font-weight: 600;
    text-decoration: none;
    font-size: 0.9rem;
}

.guardadas-header h1 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #2D2D2D;
    margin: 0;
}

.guardadas-header p { color: #888; margin: 0; font-size: 0.9rem; }

.empty-state {
    text-align: center;
    padding: 3rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    color: #aaa;
}

.btn-explorar {
    padding: 0.75rem 1.5rem;
    background: #4ECDC4;
    color: white;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 700;
}

.guardadas-lista {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.guardada-card {
    display: flex;
    gap: 1.25rem;
    background: white;
    border: 1px solid #f0f0f0;
    border-radius: 16px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}

.guardada-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }

.guardada-img {
    width: 140px;
    min-height: 120px;
    background-color: #E8FAF9;
    background-size: cover;
    background-position: center;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.img-placeholder { font-size: 2.5rem; opacity: 0.4; }

.guardada-body {
    padding: 1.25rem 1.25rem 1.25rem 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 1;
}

.guardada-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.fuente { font-size: 0.78rem; font-weight: 600; color: #3BAFA7; }
.fecha  { font-size: 0.75rem; color: #aaa; }

.guardada-body h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
    line-height: 1.4;
}

.guardada-body p {
    font-size: 0.88rem;
    color: #666;
    margin: 0;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.guardada-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: auto;
}

.btn-leer {
    padding: 0.45rem 1rem;
    background: #E8FAF9;
    color: #3BAFA7;
    border: none;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-leer:hover { background: #4ECDC4; color: white; }

.btn-eliminar {
    padding: 0.45rem 1rem;
    background: #fff5f5;
    color: #E63946;
    border: none;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-eliminar:hover { background: #E63946; color: white; }

@media (max-width: 560px) {
    .guardada-img { width: 100px; min-height: 100px; }
}
</style>