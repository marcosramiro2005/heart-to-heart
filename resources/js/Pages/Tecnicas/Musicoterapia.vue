<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onUnmounted } from 'vue'

const categorias = [
    {
        id: 'calma',
        nombre: 'Calma y relajación',
        emoji: '😌',
        color: '#d0eaf8',
        descripcion: 'Música para reducir la ansiedad y encontrar la paz interior',
        bpm: '40-60 BPM',
        ejemplos: ['Piano clásico', 'Música ambient', 'Sonatas de Chopin', 'Erik Satie'],
        ejercicio: 'Cierra los ojos. Deja que la música te guíe. No intentes pensar en nada, solo escucha.'
    },
    {
        id: 'energia',
        nombre: 'Energía y motivación',
        emoji: '⚡',
        color: '#fff9c4',
        descripcion: 'Música para aumentar la energía y la motivación cuando estás decaído/a',
        bpm: '120-140 BPM',
        ejemplos: ['Rock progresivo', 'Pop energético', 'Electronic', 'Hip-hop instrumental'],
        ejercicio: 'Mueve el cuerpo mientras escuchas. Deja que la energía de la música entre en ti.'
    },
    {
        id: 'tristeza',
        nombre: 'Procesar emociones',
        emoji: '💙',
        color: '#e8eaf6',
        descripcion: 'Música para acompañar y procesar la tristeza de forma saludable',
        bpm: '60-80 BPM',
        ejemplos: ['Blues', 'Soul', 'Música clásica romántica', 'Jazz melancólico'],
        ejercicio: 'Permite que la música valide lo que sientes. La tristeza expresada en arte es catártica.'
    },
    {
        id: 'concentracion',
        nombre: 'Concentración',
        emoji: '🎯',
        color: '#d4edda',
        descripcion: 'Música para mejorar el enfoque y la productividad',
        bpm: '60-70 BPM',
        ejemplos: ['Lo-fi hip hop', 'Música barroca', 'Bach', 'Sonidos binaurales'],
        ejercicio: 'Úsala de fondo mientras estudias o trabajas. Sin letra para no distraerse.'
    },
    {
        id: 'dormir',
        nombre: 'Conciliar el sueño',
        emoji: '🌙',
        color: '#e8d5f5',
        descripcion: 'Música para preparar el cuerpo y la mente para el descanso',
        bpm: '40-60 BPM',
        ejemplos: ['ASMR musical', 'Música delta', 'Cuencos tibetanos', 'Música de naturaleza'],
        ejercicio: 'Pon el volumen bajo. Escucha con los ojos cerrados. Deja que te lleve al sueño.'
    },
    {
        id: 'alegria',
        nombre: 'Alegría y gratitud',
        emoji: '🌟',
        color: '#ffd5d5',
        descripcion: 'Música para amplificar el bienestar y la gratitud cuando estás bien',
        bpm: '100-120 BPM',
        ejemplos: ['Música del mundo', 'Reggae', 'Bossa nova', 'Jazz alegre'],
        ejercicio: 'Canta aunque sea mal. Bailar y cantar son dos de los mejores antidepresivos naturales.'
    },
]

const categoriaActiva = ref(null)

const actividades = [
    { titulo: 'Escucha activa', emoji: '🎧', descripcion: 'Dedica 15 minutos a escuchar música SIN hacer nada más. Solo escucha. Nota los instrumentos, el ritmo, las emociones que evoca.' },
    { titulo: 'Diario musical', emoji: '📓', descripcion: 'Escribe cómo te hace sentir la música que escuchas. Las canciones son espejos de nuestro estado emocional.' },
    { titulo: 'Playlist emocional', emoji: '🎵', descripcion: 'Crea playlists etiquetadas por emoción: "Para cuando estoy triste", "Para cuando necesito energía", etc.' },
    { titulo: 'Movimiento libre', emoji: '💃', descripcion: 'Pon música y muévete como quieras durante 5 minutos. Sin coreografía, sin juicio. El movimiento libera emociones atrapadas.' },
]
</script>

<template>
    <AppLayout>
        <div class="music-wrapper">

            <div class="music-header">
                <span>🎶</span>
                <h1>MUSICOTERAPIA</h1>
            </div>

            <p class="subtitulo">Usa la música como herramienta para regular tus emociones y mejorar tu bienestar</p>

            <div class="music-intro">
                <h3>¿Qué es la musicoterapia?</h3>
                <p>La musicoterapia es el uso terapéutico de la música para mejorar el bienestar emocional, mental y físico. No necesitas saber música ni tocar ningún instrumento. La música actúa directamente sobre el sistema límbico, la parte del cerebro que procesa las emociones.</p>
            </div>

            <h2 class="sec-titulo">Música según tu estado de ánimo</h2>

            <div class="cats-grid">
                <div
                    v-for="cat in categorias"
                    :key="cat.id"
                    class="cat-card"
                    :class="{ activa: categoriaActiva === cat.id }"
                    :style="{ backgroundColor: cat.color }"
                    @click="categoriaActiva = categoriaActiva === cat.id ? null : cat.id"
                >
                    <div class="cc-top">
                        <span class="cc-emoji">{{ cat.emoji }}</span>
                        <div>
                            <h3>{{ cat.nombre }}</h3>
                            <span class="cc-bpm">{{ cat.bpm }}</span>
                        </div>
                    </div>
                    <p class="cc-desc">{{ cat.descripcion }}</p>

                    <Transition name="expand">
                        <div v-if="categoriaActiva === cat.id" class="cc-detalle">
                            <div class="cc-ejemplos">
                                <span class="cc-ej-label">Ejemplos:</span>
                                <div class="cc-chips">
                                    <span v-for="ej in cat.ejemplos" :key="ej" class="cc-chip">{{ ej }}</span>
                                </div>
                            </div>
                            <div class="cc-ejercicio">
                                <span class="cc-ej-label">Cómo practicarlo:</span>
                                <p>{{ cat.ejercicio }}</p>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>

            <h2 class="sec-titulo">Actividades de musicoterapia</h2>

            <div class="acts-grid">
                <div v-for="act in actividades" :key="act.titulo" class="act-card">
                    <span class="act-emoji">{{ act.emoji }}</span>
                    <h3>{{ act.titulo }}</h3>
                    <p>{{ act.descripcion }}</p>
                </div>
            </div>

            <div class="music-dato">
                <span>🧠</span>
                <p>Dato científico: escuchar música que te gusta libera dopamina, el mismo neurotransmisor que se activa con el chocolate o el ejercicio. La música actúa literalmente como una recompensa para el cerebro.</p>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.music-wrapper {
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.music-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #e8eaf6;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    justify-content: center;
    font-size: 1.8rem;
}

.music-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; letter-spacing: 0.1em; }
.subtitulo { color: #666; font-size: 0.95rem; text-align: center; margin: 0; }

.music-intro {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    border-left: 4px solid #7986cb;
}

.music-intro h3 { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.5rem; }
.music-intro p  { font-size: 0.88rem; color: #555; line-height: 1.6; margin: 0; }

.sec-titulo { font-size: 1rem; font-weight: 700; color: #2D2D2D; margin: 0; }

.cats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.cat-card {
    border-radius: 14px;
    padding: 1rem;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}

.cat-card:hover  { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.cat-card.activa { border-color: #7986cb; }

.cc-top {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 0.4rem;
}

.cc-emoji { font-size: 1.5rem; flex-shrink: 0; }
.cc-top h3 { font-size: 0.9rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.cc-bpm { font-size: 0.72rem; color: #888; }
.cc-desc { font-size: 0.82rem; color: #555; line-height: 1.4; margin: 0; }

.cc-detalle {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.cc-ej-label { font-size: 0.72rem; font-weight: 700; color: #7986cb; display: block; margin-bottom: 0.3rem; }
.cc-chips { display: flex; flex-wrap: wrap; gap: 0.3rem; }
.cc-chip {
    padding: 0.2rem 0.6rem;
    background: rgba(255,255,255,0.7);
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #2D2D2D;
}

.cc-ejercicio p { font-size: 0.82rem; color: #444; line-height: 1.5; margin: 0; font-style: italic; }

.expand-enter-active, .expand-leave-active { transition: opacity 0.2s; }
.expand-enter-from, .expand-leave-to { opacity: 0; }

.acts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.act-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.act-emoji { font-size: 1.6rem; }
.act-card h3 { font-size: 0.9rem; font-weight: 700; color: #2D2D2D; margin: 0; }
.act-card p  { font-size: 0.82rem; color: #555; line-height: 1.5; margin: 0; }

.music-dato {
    display: flex;
    gap: 0.75rem;
    background: #e8eaf6;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    align-items: flex-start;
}

.music-dato span { font-size: 1.3rem; flex-shrink: 0; }
.music-dato p { font-size: 0.85rem; color: #555; line-height: 1.6; margin: 0; }

@media (max-width: 520px) {
    .cats-grid { grid-template-columns: 1fr; }
    .acts-grid { grid-template-columns: 1fr; }
}
</style>