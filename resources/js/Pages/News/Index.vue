<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    articulos: Array,
    total: Number,
    pagina: Number,
    totalPaginas: Number,
    categoria: String,
    busqueda: String,
    error: String,
})

const categorias = [
    { id: 'salud_mental', label: '🧠 Salud mental' },
    { id: 'ansiedad',     label: '😰 Ansiedad' },
    { id: 'depresion',    label: '😢 Depresión' },
    { id: 'mindfulness',  label: '🧘 Mindfulness' },
    { id: 'sueno',        label: '😴 Sueño' },
    { id: 'autoestima',   label: '💪 Autoestima' },
]

const busqueda = ref(props.busqueda || '')
const articulosLocales = ref(props.articulos.map(a => ({ ...a })))

const filtrar = (cat) => {
    router.get('/recursos', { categoria: cat }, { preserveState: false })
}

const buscar = () => {
    router.get('/recursos', { categoria: props.categoria, busqueda: busqueda.value })
}

const cambiarPagina = (p) => {
    router.get('/recursos', { categoria: props.categoria, busqueda: props.busqueda, pagina: p })
}

const toggleGuardar = async (articulo, index) => {
    try {
        const res = await axios.post('/recursos/guardar', {
            url:         articulo.url,
            title:       articulo.title,
            description: articulo.description,
            image_url:   articulo.image_url,
            source_name: articulo.source_name,
        })
        articulosLocales.value[index].is_saved = res.data.saved
    } catch (e) {
        console.error(e)
    }
}

const abrirArticulo = (url) => {
    window.open(url, '_blank', 'noopener')
}
</script>

<template>
    <AppLayout>
        <div class="news-wrapper">

            <!-- Hero -->
            <div class="news-hero">
                <div>
                    <h1>📰 Recursos y noticias</h1>
                    <p>Mantente informado sobre salud mental y bienestar</p>
                </div>
                <Link href="/recursos/guardados" class="btn-guardados">
                    🔖 Mis guardados
                </Link>
            </div>

            <!-- Buscador -->
            <div class="buscador">
                <input
                    v-model="busqueda"
                    type="text"
                    placeholder="Buscar artículos sobre..."
                    @keyup.enter="buscar"
                />
                <button @click="buscar">🔍 Buscar</button>
            </div>

            <!-- Categorías -->
            <div class="categorias">
                <button
                    v-for="cat in categorias"
                    :key="cat.id"
                    class="cat-btn"
                    :class="{ activa: categoria === cat.id }"
                    @click="filtrar(cat.id)"
                >
                    {{ cat.label }}
                </button>
            </div>

            <!-- Aviso de error/fallback -->
            <div v-if="error" class="aviso-fallback">
                ℹ️ {{ error }}. Mostrando contenido de ejemplo.
            </div>

            <!-- Grid de artículos -->
            <div class="articulos-grid">
                <div
                    v-for="(articulo, i) in articulosLocales"
                    :key="articulo.url_hash"
                    class="articulo-card"
                >
                    <!-- Imagen -->
                    <div
                        class="articulo-img"
                        :style="articulo.image_url ? `background-image: url(${articulo.image_url})` : ''"
                    >
                        <div v-if="!articulo.image_url" class="articulo-img-placeholder">
                            🧠
                        </div>
                        <button
                            class="btn-guardar"
                            :class="{ guardado: articulo.is_saved }"
                            @click.stop="toggleGuardar(articulo, i)"
                            :title="articulo.is_saved ? 'Quitar de guardados' : 'Guardar artículo'"
                        >
                            {{ articulo.is_saved ? '🔖' : '📄' }}
                        </button>
                    </div>

                    <!-- Contenido -->
                    <div class="articulo-body">
                        <div class="articulo-meta">
                            <span class="articulo-fuente">{{ articulo.source_name }}</span>
                            <span class="articulo-fecha">{{ articulo.published_at }}</span>
                        </div>
                        <h3 class="articulo-titulo">{{ articulo.title }}</h3>
                        <p class="articulo-desc">{{ articulo.description }}</p>
                        <button class="btn-leer" @click="abrirArticulo(articulo.url)">
                            Leer artículo →
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sin resultados -->
            <div v-if="articulosLocales.length === 0" class="empty-state">
                <p>No se encontraron artículos para esta búsqueda.</p>
                <p>Prueba con otras palabras clave 🔍</p>
            </div>

            <!-- Paginación -->
            <div v-if="totalPaginas > 1" class="paginacion">
                <button
                    :disabled="pagina === 1"
                    @click="cambiarPagina(pagina - 1)"
                    class="page-btn"
                >← Anterior</button>

                <span class="page-info">Página {{ pagina }} de {{ totalPaginas }}</span>

                <button
                    :disabled="pagina === totalPaginas"
                    @click="cambiarPagina(pagina + 1)"
                    class="page-btn"
                >Siguiente →</button>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.news-wrapper {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.news-hero {
    background: #4ECDC4;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.news-hero h1 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0 0 0.3rem;
}

.news-hero p {
    color: #2D2D2D;
    margin: 0;
    font-size: 0.95rem;
}

.btn-guardados {
    padding: 0.7rem 1.4rem;
    background: white;
    color: #3BAFA7;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: transform 0.2s;
    white-space: nowrap;
}

.btn-guardados:hover { transform: translateY(-2px); }

.buscador {
    display: flex;
    gap: 0.5rem;
}

.buscador input {
    flex: 1;
    padding: 0.75rem 1.2rem;
    border: 2px solid #e0e0e0;
    border-radius: 25px;
    font-size: 0.95rem;
    outline: none;
    font-family: inherit;
    transition: border-color 0.2s;
}

.buscador input:focus { border-color: #4ECDC4; }

.buscador button {
    padding: 0.75rem 1.4rem;
    background: #4ECDC4;
    color: white;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.9rem;
    white-space: nowrap;
    transition: background 0.2s;
}

.buscador button:hover { background: #3BAFA7; }

.categorias {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.cat-btn {
    padding: 0.45rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    color: #2D2D2D;
}

.cat-btn.activa {
    background: #4ECDC4;
    border-color: #4ECDC4;
    color: white;
}

.aviso-fallback {
    background: #fff9c4;
    border: 1px solid #f0d060;
    color: #666;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-size: 0.88rem;
}

.articulos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

.articulo-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.2s, transform 0.2s;
}

.articulo-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    transform: translateY(-3px);
}

.articulo-img {
    height: 160px;
    background-color: #E8FAF9;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.articulo-img-placeholder {
    font-size: 3rem;
    opacity: 0.4;
}

.btn-guardar {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: white;
    border: none;
    font-size: 1rem;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.btn-guardar:hover { transform: scale(1.1); }
.btn-guardar.guardado { background: #E8FAF9; }

.articulo-body {
    padding: 1rem 1.25rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 1;
}

.articulo-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.articulo-fuente {
    font-size: 0.75rem;
    font-weight: 600;
    color: #3BAFA7;
}

.articulo-fecha {
    font-size: 0.72rem;
    color: #aaa;
}

.articulo-titulo {
    font-size: 0.95rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.articulo-desc {
    font-size: 0.85rem;
    color: #666;
    line-height: 1.5;
    margin: 0;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.btn-leer {
    margin-top: auto;
    padding: 0.5rem 1rem;
    background: #E8FAF9;
    color: #3BAFA7;
    border: none;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    align-self: flex-start;
}

.btn-leer:hover { background: #4ECDC4; color: white; }

.empty-state {
    text-align: center;
    color: #aaa;
    padding: 3rem;
    font-size: 1rem;
    line-height: 2;
}

.paginacion {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}

.page-btn {
    padding: 0.6rem 1.2rem;
    border: 2px solid #4ECDC4;
    background: white;
    color: #3BAFA7;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.9rem;
}

.page-btn:hover:not(:disabled) {
    background: #4ECDC4;
    color: white;
}

.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.page-info {
    font-size: 0.9rem;
    color: #666;
}

@media (max-width: 900px) {
    .articulos-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 580px) {
    .articulos-grid { grid-template-columns: 1fr; }
}
</style>