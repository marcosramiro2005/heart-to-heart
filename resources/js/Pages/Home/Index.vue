<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'

const tecnicas = [
    {
        id: 'respiracion',
        nombre: 'Respiración',
        color: '#d4edda',
        icon: '🫁',
        ruta: '/respiracion'
    },
    {
        id: 'sonidos',
        nombre: 'Sonidos Relajantes',
        color: '#d0eaf8',
        icon: '🎵',
        ruta: '/sonidos'
    },
    {
        id: 'diario',
        nombre: 'Diario de Gratitud',
        color: '#fff9c4',
        icon: '📓',
        ruta: '/diario'
    },
    {
        id: 'infusiones',
        nombre: 'Infusiones',
        color: '#e8d5f5',
        icon: '🍵',
        ruta: '/infusiones'
    },
    {
        id: 'ejercicio',
        nombre: 'Ejercicio',
        color: '#ffd5d5',
        icon: '🏃',
        ruta: '/ejercicio'
    },
]

const irATecnica = (ruta) => {
    router.visit(ruta)
}

const irAHearty = () => {
    router.visit('/hearty')
}
</script>

<template>
    <AppLayout>

        <!-- ── Hero ── -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Tus emociones<br/>importan</h1>
                    <p>Un espacio seguro para cuidar tu bienestar mental cada día.</p>
                    <button class="btn-hearty" @click="irAHearty">
                        💬 Hablar con Hearty
                    </button>
                </div>
                <div class="hero-image">
                    <img src="/images/hero-meditation.png" alt="Meditación" />
                </div>
            </div>
        </section>

        <!-- ── Técnicas ── -->
        <section class="tecnicas-section">
            <h2 class="section-title">TÉCNICAS</h2>
            <div class="tecnicas-grid">
                <button
                    v-for="tecnica in tecnicas"
                    :key="tecnica.id"
                    class="tecnica-card"
                    :style="{ backgroundColor: tecnica.color }"
                    @click="irATecnica(tecnica.ruta)"
                >
                    <span class="tecnica-icon">{{ tecnica.icon }}</span>
                    <span class="tecnica-nombre">{{ tecnica.nombre }}</span>
                </button>
            </div>
        </section>

        <!-- ── Sección bienestar ── -->
        <section class="bienestar-section">
            <h2 class="section-title">¿CÓMO TE SIENTES HOY?</h2>
            <p class="section-subtitle">Registra tu emoción diaria y descubre tus patrones emocionales</p>
            <div class="emociones-grid">
                <button class="emocion-btn" @click="registrarEmocion('alegría')">😊 Alegría</button>
                <button class="emocion-btn" @click="registrarEmocion('calma')">😌 Calma</button>
                <button class="emocion-btn" @click="registrarEmocion('ansiedad')">😰 Ansiedad</button>
                <button class="emocion-btn" @click="registrarEmocion('tristeza')">😢 Tristeza</button>
                <button class="emocion-btn" @click="registrarEmocion('enfado')">😠 Enfado</button>
                <button class="emocion-btn" @click="registrarEmocion('cansancio')">😴 Cansancio</button>
            </div>
        </section>

    </AppLayout>
</template>

<style scoped>
/* ── Hero ── */
.hero {
    background-color: #4ECDC4;
    padding: 3rem 1.5rem;
}

.hero-content {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
}

.hero-text h1 {
    font-size: 2.8rem;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.15;
    margin: 0 0 1rem;
}

.hero-text p {
    font-size: 1.1rem;
    color: #2D2D2D;
    margin: 0 0 1.5rem;
}

.btn-hearty {
    padding: 0.85rem 2rem;
    background: #fff;
    color: #3BAFA7;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.btn-hearty:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.hero-image img {
    width: 280px;
    height: auto;
}

/* ── Técnicas ── */
.tecnicas-section {
    max-width: 1100px;
    margin: 3rem auto;
    padding: 0 1.5rem;
}

.section-title {
    text-align: center;
    font-size: 1.3rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #2D2D2D;
    margin-bottom: 1.5rem;
}

.section-subtitle {
    text-align: center;
    color: #666;
    margin-top: -1rem;
    margin-bottom: 2rem;
    font-size: 0.95rem;
}

.tecnicas-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
}

.tecnica-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1.5rem 1rem;
    border: none;
    border-radius: 16px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.tecnica-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.tecnica-icon {
    font-size: 2rem;
}

.tecnica-nombre {
    font-size: 0.85rem;
    font-weight: 600;
    color: #2D2D2D;
    text-align: center;
}

/* ── Emociones ── */
.bienestar-section {
    background: #f9f9f9;
    padding: 3rem 1.5rem;
    text-align: center;
}

.emociones-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
    max-width: 700px;
    margin: 0 auto;
}

.emocion-btn {
    padding: 0.75rem 1.5rem;
    background: #fff;
    border: 2px solid #4ECDC4;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 600;
    color: #2D2D2D;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}

.emocion-btn:hover {
    background: #4ECDC4;
    color: #fff;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .hero-content {
        flex-direction: column;
        text-align: center;
    }

    .hero-text h1 {
        font-size: 2rem;
    }

    .hero-image img {
        width: 200px;
    }

    .tecnicas-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .tecnicas-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>