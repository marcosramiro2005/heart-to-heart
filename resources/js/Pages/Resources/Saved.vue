<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    recursos: Array,
})

const lista = ref([...props.recursos])

const toggleGuardar = async (recursoId) => {
    await axios.post(`/biblioteca/${recursoId}/guardar`)
    lista.value = lista.value.filter(r => r.id !== recursoId)
}
</script>

<template>
    <AppLayout>
        <div class="saved-wrapper">

            <!-- Cabecera -->
            <div class="saved-hero">
                <div>
                    <h1>🔖 Mis guardados</h1>
                    <p>Recursos que has guardado para leer más tarde</p>
                </div>
                <Link href="/biblioteca" class="btn-volver">← Volver a la biblioteca</Link>
            </div>

            <!-- Estado vacío -->
            <div v-if="lista.length === 0" class="empty-state">
                <p>No tienes recursos guardados aún.</p>
                <Link href="/biblioteca" class="btn-explorar">Explorar la biblioteca →</Link>
            </div>

            <!-- Grid de recursos guardados -->
            <div v-else class="recursos-grid">
                <div
                    v-for="rec in lista"
                    :key="rec.id"
                    class="recurso-card"
                >
                    <div class="rc-color-bar" :style="{ backgroundColor: rec.category_color }">
                        <span class="rc-tipo">{{ rec.type_label }}</span>
                        <button
                            class="rc-guardar guardado"
                            @click.prevent="toggleGuardar(rec.id)"
                            title="Quitar de guardados"
                        >
                            🔖
                        </button>
                    </div>

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

        </div>
    </AppLayout>
</template>

<style scoped>
.saved-wrapper {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.saved-hero {
    background: #4ECDC4;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.saved-hero h1 { font-size: 1.6rem; font-weight: 800; color: #1a1a1a; margin: 0 0 0.3rem; }
.saved-hero p  { color: #2D2D2D; margin: 0; font-size: 0.95rem; }

.btn-volver {
    padding: 0.7rem 1.4rem;
    background: white;
    color: #3BAFA7;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    white-space: nowrap;
    transition: transform 0.2s;
}

.btn-volver:hover { transform: translateY(-2px); }

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    color: #aaa;
    font-size: 1rem;
}

.btn-explorar {
    padding: 0.7rem 1.6rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.btn-explorar:hover { background: #3BAFA7; }

.recursos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
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

@media (max-width: 900px) {
    .recursos-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 580px) {
    .recursos-grid { grid-column: 1fr; }
}
</style>
