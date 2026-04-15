<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <AppLayout>
        <div class="about-wrapper">

            <!-- Hero -->
            <div class="about-hero">
                <div class="hero-logo">💚</div>
                <h1>Heart to Heart</h1>
                <p class="hero-tagline">Un espacio seguro para cuidar tu bienestar emocional cada día</p>
            </div>

            <!-- Misión -->
            <div class="about-section">
                <h2>🎯 Nuestra misión</h2>
                <p>
                    Heart to Heart nació de una preocupación real: los problemas de salud mental están
                    creciendo, especialmente entre jóvenes y estudiantes, y las herramientas disponibles
                    son o de pago, o parciales, o difíciles de usar. Queríamos crear algo diferente:
                    una plataforma gratuita, integral y centrada en la persona.
                </p>
                <p>
                    Nuestra misión es hacer que el cuidado emocional sea accesible para cualquier
                    persona, independientemente de sus recursos o experiencia tecnológica.
                </p>
            </div>

            <!-- Qué ofrecemos -->
            <div class="about-section">
                <h2>✨ Qué ofrecemos</h2>
                <div class="features-grid">
                    <div class="feature-card" v-for="feature in features" :key="feature.titulo">
                        <span class="feature-emoji">{{ feature.emoji }}</span>
                        <h3>{{ feature.titulo }}</h3>
                        <p>{{ feature.descripcion }}</p>
                    </div>
                </div>
            </div>

            <!-- Valores -->
            <div class="about-section valores-section">
                <h2>💙 Nuestros valores</h2>
                <div class="valores-grid">
                    <div class="valor-item" v-for="valor in valores" :key="valor.titulo">
                        <span class="valor-icono">{{ valor.emoji }}</span>
                        <div>
                            <h4>{{ valor.titulo }}</h4>
                            <p>{{ valor.descripcion }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tecnología -->
            <div class="about-section">
                <h2>⚙️ Tecnología con propósito</h2>
                <p>
                    Heart to Heart está desarrollada con tecnologías modernas y de código abierto:
                    Laravel para el backend, Vue 3 para la interfaz, y Python con Flask para el
                    chatbot Hearty. Todo diseñado pensando en la privacidad y la seguridad de
                    los datos del usuario.
                </p>
                <div class="tech-chips">
                    <span v-for="tech in techs" :key="tech" class="tech-chip">{{ tech }}</span>
                </div>
            </div>

            <!-- Aviso importante -->
            <div class="aviso-section">
                <span class="aviso-icono">⚠️</span>
                <div>
                    <h3>Aviso importante</h3>
                    <p>
                        Heart to Heart es una herramienta de apoyo al bienestar personal y
                        <strong>no sustituye a la atención psicológica o psiquiátrica profesional</strong>.
                        Si estás atravesando una crisis emocional grave o sientes que necesitas
                        ayuda especializada, te animamos a contactar con un profesional de salud
                        mental o llamar a los servicios de emergencia de tu país.
                    </p>
                </div>
            </div>

            <!-- CTA -->
            <div class="about-cta">
                <h2>¿Listo para empezar?</h2>
                <p>Únete a nuestra comunidad y empieza a cuidarte hoy</p>
                <div class="cta-botones">
                    <Link href="/home" class="btn-cta-primary">Ir al inicio</Link>
                    <Link href="/hearty" class="btn-cta-secondary">Hablar con Hearty</Link>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script>
export default {
    data() {
        return {
            features: [
                { emoji: '💬', titulo: 'Chatbot Hearty',      descripcion: 'Un asistente emocional que te escucha, detecta tu estado de ánimo y te sugiere técnicas personalizadas.' },
                { emoji: '📊', titulo: 'Seguimiento emocional', descripcion: 'Registra tus emociones cada día y descubre patrones en tu bienestar con gráficas visuales.' },
                { emoji: '🫁', titulo: 'Técnicas de bienestar', descripcion: 'Respiración guiada, sonidos relajantes, diario de gratitud, infusiones y ejercicio físico.' },
                { emoji: '📰', titulo: 'Biblioteca y recursos',  descripcion: 'Artículos actualizados sobre salud mental y una biblioteca de recursos escritos por nuestro equipo.' },
                { emoji: '💬', titulo: 'Comunidad',           descripcion: 'Un foro de autoayuda donde compartir experiencias y apoyar a otros, con opción de anonimato.' },
                { emoji: '🏆', titulo: 'Sistema de logros',   descripcion: 'Gamificación positiva que te motiva a mantener hábitos saludables con badges y niveles.' },
            ],
            valores: [
                { emoji: '🔓', titulo: 'Gratuito y accesible',  descripcion: 'Todas las funcionalidades son completamente gratuitas y accesibles desde cualquier dispositivo.' },
                { emoji: '🔒', titulo: 'Privacidad primero',    descripcion: 'Tus datos emocionales son tuyos. Nunca los compartimos ni los vendemos a terceros.' },
                { emoji: '🤝', titulo: 'Comunidad de apoyo',    descripcion: 'Creemos en el poder de la comunidad y el apoyo mutuo para superar los momentos difíciles.' },
                { emoji: '🌱', titulo: 'Mejora continua',       descripcion: 'La aplicación evoluciona constantemente basándose en las necesidades reales de los usuarios.' },
            ],
            techs: [
                'Laravel', 'Vue 3', 'Python', 'Flask', 'MySQL',
                'Docker', 'Chart.js', 'Inertia.js', 'Tailwind CSS'
            ],
        }
    }
}
</script>

<style scoped>
.about-wrapper {
    max-width: 860px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

/* ── Hero ── */
.about-hero {
    text-align: center;
    padding: 3rem 2rem;
    background: #4ECDC4;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.hero-logo { font-size: 4rem; }

.about-hero h1 {
    font-size: 2.2rem;
    font-weight: 800;
    color: white;
    margin: 0;
}

.hero-tagline {
    font-size: 1.05rem;
    color: rgba(255,255,255,0.9);
    max-width: 500px;
    line-height: 1.6;
    margin: 0;
}

/* ── Secciones ── */
.about-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.about-section h2 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
}

.about-section p {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.8;
    margin: 0;
}

/* ── Features ── */
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.feature-card {
    background: #fafafa;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    border: 1px solid #f0f0f0;
}

.feature-emoji { font-size: 1.8rem; }

.feature-card h3 {
    font-size: 0.92rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0;
}

.feature-card p {
    font-size: 0.82rem;
    color: #666;
    line-height: 1.5;
    margin: 0;
}

/* ── Valores ── */
.valores-section { background: #E8FAF9; border-color: #c8e6e4; }

.valores-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.valor-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.valor-icono { font-size: 1.5rem; flex-shrink: 0; margin-top: 0.1rem; }

.valor-item h4 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0 0 0.2rem;
}

.valor-item p {
    font-size: 0.88rem;
    color: #555;
    margin: 0;
}

/* ── Tech chips ── */
.tech-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; }

.tech-chip {
    padding: 0.3rem 0.9rem;
    background: #E8FAF9;
    color: #3BAFA7;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 700;
    border: 1.5px solid #4ECDC4;
}

/* ── Aviso ── */
.aviso-section {
    display: flex;
    gap: 1rem;
    background: #fff9c4;
    border: 2px solid #f0d060;
    border-radius: 14px;
    padding: 1.5rem;
    align-items: flex-start;
}

.aviso-icono { font-size: 1.8rem; flex-shrink: 0; }

.aviso-section h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #2D2D2D;
    margin: 0 0 0.5rem;
}

.aviso-section p {
    font-size: 0.88rem;
    color: #555;
    line-height: 1.6;
    margin: 0;
}

/* ── CTA ── */
.about-cta {
    text-align: center;
    background: #4ECDC4;
    border-radius: 16px;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.about-cta h2 { font-size: 1.4rem; font-weight: 800; color: white; margin: 0; }
.about-cta p  { color: rgba(255,255,255,0.9); margin: 0; }

.cta-botones { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; }

.btn-cta-primary {
    padding: 0.85rem 2rem;
    background: white;
    color: #3BAFA7;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    transition: transform 0.2s;
}

.btn-cta-primary:hover { transform: translateY(-2px); }

.btn-cta-secondary {
    padding: 0.85rem 2rem;
    background: rgba(255,255,255,0.2);
    color: white;
    font-weight: 700;
    border-radius: 25px;
    text-decoration: none;
    border: 2px solid rgba(255,255,255,0.6);
    transition: background 0.2s;
}

.btn-cta-secondary:hover { background: rgba(255,255,255,0.3); }

@media (max-width: 700px) {
    .features-grid { grid-template-columns: 1fr; }
}
</style>