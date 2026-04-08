<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    recursos:   Object,
    destacados: Array,
    savedIds:   Array,
    categoria:  String,
    tipo:       String,
    busqueda:   String,
})

const categorias = [
    { id: 'todos',        label: '🌐 Todos' },
    { id: 'ansiedad',     label: '😰 Ansiedad' },
    { id: 'depresion',    label: '😢 Depresión' },
    { id: 'mindfulness',  label: '🧘 Mindfulness' },
    { id: 'sueno',        label: '😴 Sueño' },
    { id: 'autoestima',   label: '💪 Autoestima' },
    { id: 'relaciones',   label: '💙 Relaciones' },
    { id: 'alimentacion', label: '🥗 Alimentación' },
    { id: 'ejercicio',    label: '🏃 Ejercicio' },
]

const tipos = [
    { id: 'todos',    label: 'Todos los tipos' },
    { id: 'article',  label: '📄 Artículos' },
    { id: 'exercise', label: '🧘 Ejercicios' },
    { id: 'video',    label: '🎥 Vídeos' },
    { id: 'podcast',  label: '🎙️ Podcasts' },
]

const busqueda   = ref(props.busqueda || '')
const savedLocal = ref([...props.savedIds])

const filtrar = (cat) => {
    router.get('/biblioteca', { categoria: cat, tipo: props.tipo }, { preserveState: true })
}

const filtrarTipo = (tipo) => {
    router.get('/biblioteca', { categoria: props.categoria, tipo }, { preserveState: true })
}

const buscar = () => {
    router.get('/biblioteca', { categoria: props.categoria, tipo: props.tipo, busqueda: busqueda.value })
}

const toggleGuardar = async (recursoId) => {
    await axios.post(`/biblioteca/${recursoId}/guardar`)
    if (savedLocal.value.includes(recursoId)) {
        savedLocal.value = savedLocal.value.filter(id => id !== recursoId)
    } else {
        savedLocal.value.push(recursoId)
    }
}

const estaGuardado = (id) => savedLocal.value.includes(id)
</script>

<template>
    <AppLayout>
        <div class="lib-wrapper">

            <!-- Hero -->
            <div class="lib-hero">
                <div>
                    <h1>📚 Biblioteca de recursos</h1>
                    <p>Artículos, ejercicios y guías escritos por nuestro equipo para tu bienestar</p>
                </div>
                <Link href="/biblioteca/guardados" class="btn-guardados">
                    🔖 Mis guardados
                </Link>
            </div>

            <!-- Destacados -->
            <div v-if="destacados.length > 0 && categoria === 'todos' && !busqueda" class="destacados-section">
                <h2 class="sec-titulo">⭐ Destacados</h2>
                <div class="destacados-grid">
                    <Link
                        v-for="rec in destacados"
                        :key="rec.id"
                        :href="`/biblioteca/${rec.id}`"
                        class="destacado-card"
                        :style="{ borderTop: `4px solid ${rec.category_color}` }"
                    >
                        <div class="dest-header">
                            <span class="dest-tipo">{{ rec.type_label }}</span>
                            <span class="dest-tiempo">⏱ {{ rec.read_time }} min</span>
                        </div>
                        <h3>{{ rec.title }}</h3>
                        <p>{{ rec.summary }}</p>
                        <span class="dest-cat" :style="{ backgroundColor: rec.category_color }">
                            {{ rec.category }}
                        </span>
                    </Link>
                </div>
            </div>

            <!-- Filtros -->
            <div class="filtros-section">
                <div class="buscador">
                    <input
                        v-model="busqueda"
                        type="text"
                        placeholder="Buscar en la biblioteca..."
                        @keyup.enter="buscar"
                    />
                    <button @click="buscar">🔍</button>
                </div>

                <div class="cats-scroll">
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

                <div class="tipos-row">
                    <button
                        v-for="t in tipos"
                        :key="t.id"
                        class="tipo-btn"
                        :class="{ activo: tipo === t.id }"
                        @click="filtrarTipo(t.id)"
                    >
                        {{ t.label }}
                    </button>
                </div>
            </div>

            <!-- Grid de recursos -->
            <div class="recursos-grid">
                <div v-if="recursos.data.length === 0" class="empty-state">
                    No hay recursos para esta búsqueda. Prueba con otra categoría 🔍
                </div>

                <div
                    v-for="rec in recursos.data"
                    :key="rec.id"
                    class="recurso-card"
                >
                    <!-- Cabecera de color -->
                    <div class="rc-color-bar" :style="{ backgroundColor: rec.category_color }">
                        <span class="rc-tipo">{{ rec.type_label }}</span>
                        <button
                            class="rc-guardar"
                            :class="{ guardado: estaGuardado(rec.id) }"
                            @click.prevent="toggleGuardar(rec.id)"
                        >
                            {{ estaGuardado(rec.id) ? '🔖' : '📄' }}
                        </button>
                    </div>

                    <!-- Contenido -->
                    <div class="rc-body">
                        <h3>
                            <Link :href="`/biblioteca/${rec.id}`" class="rc-titulo">
                                {{ rec.title }}
                            </Link>
                        </h3>
                        <p class="rc-summary">{{ rec.summary }}</p>
                        <div class="rc-footer">
                            <span class="rc-tiempo">⏱ {{ rec.read_time }} min</span>
                            <span class="rc-views">👁 {{ rec.views }}</span>
                            <Link :href="`/biblioteca/${rec.id}`" class="rc-leer">
                                Leer →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <div v-if="recursos.last_page > 1" class="paginacion">
                <button
                    :disabled="recursos.current_page === 1"
                    @click="router.get('/biblioteca', { categoria, tipo, busqueda, page: recursos.current_page - 1 })"
                    class="page-btn"
                >← Anterior</button>
                <span class="page-info">{{ recursos.current_page }} / {{ recursos.last_page }}</span>
                <button
                    :disabled="recursos.current_page === recursos.last_page"
                    @click="router.get('/biblioteca', { categoria, tipo, busqueda, page: recursos.current_page + 1 })"
                    class="page-btn"
                >Siguiente →</button>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.lib-wrapper {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.lib-hero {
    background: #4ECDC4;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.lib-hero h1 { font-size: 1.6rem; font-weight: 800; color: #1a1a1a; margin: 0 0 0.3rem; }
.lib-hero p  { color: #2D2D2D; margin: 0; font-size: 0.95rem; }

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

/* ── Destacados ── */
.sec-titulo {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0 0 1rem;
}

.destacados-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.destacado-card {
    background: white;
    border-radius: 14px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    text-decoration: none;
    border: 1px solid #f0f0f0;
    transition: box-shadow 0.2s, transform 0.2s;
}

.destacado-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    transform: translateY(-3px);
}

.dest-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dest-tipo  { font-size: 0.78rem; color: #888; font-weight: 600; }
.dest-tiempo{ font-size: 0.75rem; color: #aaa; }

.destacado-card h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
    line-height: 1.4;
}

.destacado-card p {
    font-size: 0.85rem;
    color: #666;
    line-height: 1.5;
    margin: 0;
    flex: 1;
}

.dest-cat {
    padding: 0.2rem 0.7rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #2D2D2D;
    width: fit-content;
}

/* ── Filtros ── */
.filtros-section {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.buscador { display: flex; gap: 0.5rem; }

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
    padding: 0.75rem 1.2rem;
    background: #4ECDC4;
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.2s;
}

.buscador button:hover { background: #3BAFA7; }

.cats-scroll {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.cat-btn {
    padding: 0.4rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    color: #2D2D2D;
    white-space: nowrap;
}

.cat-btn.activa { background: #4ECDC4; border-color: #4ECDC4; color: white; }

.tipos-row { display: flex; flex-wrap: wrap; gap: 0.4rem; }

.tipo-btn {
    padding: 0.3rem 0.9rem;
    border: 1.5px solid #e0e0e0;
    border-radius: 16px;
    background: white;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
}

.tipo-btn.activo { background: #E8FAF9; border-color: #4ECDC4; color: #3BAFA7; }

/* ── Grid de recursos ── */
.recursos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    color: #aaa;
    padding: 3rem;
}

.recurso-card {
    background: white;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.2s, transform 0.2s;
}

.recurso-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    transform: translateY(-3px);
}

.rc-color-bar {
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rc-tipo { font-size: 0.78rem; font-weight: 600; color: #2D2D2D; }

.rc-guardar {
    background: rgba(255,255,255,0.6);
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rc-guardar:hover     { background: rgba(255,255,255,0.9); }
.rc-guardar.guardado  { background: rgba(255,255,255,0.9); }

.rc-body {
    padding: 1rem 1.25rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 1;
}

.rc-titulo {
    font-size: 0.95rem;
    font-weight: 700;
    color: #2D2D2D;
    text-decoration: none;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rc-titulo:hover { color: #4ECDC4; }

.rc-summary {
    font-size: 0.84rem;
    color: #666;
    line-height: 1.5;
    margin: 0;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rc-footer {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: auto;
}

.rc-tiempo, .rc-views { font-size: 0.75rem; color: #aaa; }

.rc-leer {
    margin-left: auto;
    font-size: 0.85rem;
    font-weight: 700;
    color: #4ECDC4;
    text-decoration: none;
}

.rc-leer:hover { color: #3BAFA7; }

/* ── Paginación ── */
.paginacion {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
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

.page-btn:hover:not(:disabled) { background: #4ECDC4; color: white; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-info { font-size: 0.9rem; color: #666; }

@media (max-width: 900px) {
    .destacados-grid { grid-template-columns: 1fr; }
    .recursos-grid   { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 580px) {
    .recursos-grid { grid-template-columns: 1fr; }
}
</style>