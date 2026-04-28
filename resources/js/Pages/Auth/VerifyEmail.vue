<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
    status: { type: String },
})

const page = usePage()
const userEmail = computed(() => page.props.auth?.user?.email ?? '')

const form = useForm({})

const submit = () => {
    form.post(route('verification.send'))
}

const verificationLinkSent = computed(() => props.status === 'verification-link-sent')
</script>

<template>
    <Head title="Verifica tu correo" />

    <div class="auth-page">

        <!-- Lado decorativo -->
        <div class="auth-deco">
            <div class="deco-content">
                <div class="deco-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </div>
                <h2>Casi lo tienes</h2>
                <p>Solo falta un pequeño paso para empezar a cuidarte con nosotros.</p>

                <div class="deco-pasos">
                    <div class="dp-item done">
                        <span class="dp-num">✓</span>
                        <div>
                            <strong>Cuenta creada</strong>
                            <p>Ya formas parte de Heart to Heart</p>
                        </div>
                    </div>
                    <div class="dp-item active">
                        <span class="dp-num">02</span>
                        <div>
                            <strong>Verifica tu email</strong>
                            <p>Revisa tu bandeja de entrada</p>
                        </div>
                    </div>
                    <div class="dp-item">
                        <span class="dp-num">03</span>
                        <div>
                            <strong>¡Empieza!</strong>
                            <p>Hearty te estará esperando</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="deco-circles">
                <div class="dc c1"></div>
                <div class="dc c2"></div>
                <div class="dc c3"></div>
            </div>
        </div>

        <!-- Lado formulario -->
        <div class="auth-form-side">
            <div class="auth-card">

                <div class="email-icon">💌</div>

                <div class="ac-header">
                    <h1>Revisa tu correo</h1>
                    <p>Te hemos enviado un enlace de verificación</p>
                </div>

                <div class="email-badge" v-if="userEmail">
                    <span class="badge-icon">✉️</span>
                    <span class="badge-text">{{ userEmail }}</span>
                </div>

                <p class="info-text">
                    Haz clic en el enlace que te hemos enviado a ese correo para activar tu cuenta. Si no lo encuentras, revisa la carpeta de spam.
                </p>

                <div class="auth-success" v-if="verificationLinkSent">
                    <span>✅</span>
                    <span>¡Enlace reenviado! Revisa tu bandeja de entrada.</span>
                </div>

                <form @submit.prevent="submit">
                    <button
                        type="submit"
                        class="btn-auth"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">⏳ Enviando...</span>
                        <span v-else>🔄 Reenviar correo de verificación</span>
                    </button>
                </form>

                <div class="ac-footer">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="link-logout"
                    >
                        Cerrar sesión
                    </Link>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
.auth-page {
    min-height: 100vh;
    display: flex;
}

.auth-deco {
    flex: 1;
    background: linear-gradient(135deg, #4ECDC4 0%, #3BAFA7 60%, #2d9990 100%);
    padding: 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.deco-content { position: relative; z-index: 1; }

.deco-logo {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 2rem;
}

.deco-logo img  { width: 44px; }
.deco-logo span { font-weight: 700; font-size: 0.9rem; color: white; letter-spacing: 0.1em; }

.auth-deco h2 {
    font-size: 2rem;
    font-weight: 900;
    color: white;
    margin: 0 0 0.75rem;
    line-height: 1.2;
}

.auth-deco > .deco-content > p {
    color: rgba(255,255,255,0.82);
    font-size: 1rem;
    line-height: 1.6;
    margin: 0 0 2rem;
}

.deco-pasos {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.dp-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    opacity: 0.6;
    transition: opacity 0.2s;
}

.dp-item.done,
.dp-item.active { opacity: 1; }

.dp-num {
    font-size: 1.5rem;
    font-weight: 900;
    color: rgba(255,255,255,0.5);
    line-height: 1;
    min-width: 36px;
}

.dp-item.active .dp-num { color: white; }
.dp-item.done  .dp-num  { color: rgba(255,255,255,0.9); font-size: 1.3rem; }

.dp-item strong { display: block; color: white; font-size: 0.95rem; margin-bottom: 0.1rem; }
.dp-item p      { color: rgba(255,255,255,0.7); font-size: 0.82rem; margin: 0; }

.deco-circles { position: absolute; inset: 0; pointer-events: none; }
.dc { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.08); }
.dc.c1 { width: 300px; height: 300px; top: -80px; right: -60px; }
.dc.c2 { width: 200px; height: 200px; bottom: 40px; right: 60px; }
.dc.c3 { width: 150px; height: 150px; bottom: -40px; left: -30px; }

.auth-form-side {
    width: 500px;
    flex-shrink: 0;
    background: #fafffe;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.auth-card {
    width: 100%;
    max-width: 400px;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.email-icon {
    font-size: 3.5rem;
    text-align: center;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-8px); }
}

.ac-header { text-align: center; }
.ac-header h1 { font-size: 1.6rem; font-weight: 800; color: #1a1a1a; margin: 0 0 0.25rem; }
.ac-header p  { font-size: 0.9rem; color: #888; margin: 0; }

.email-badge {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: #E8FAF9;
    border: 1.5px solid #4ECDC4;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #2d9990;
    word-break: break-all;
}

.badge-icon { font-size: 1.1rem; flex-shrink: 0; }
.badge-text { flex: 1; }

.info-text {
    font-size: 0.88rem;
    color: #666;
    line-height: 1.6;
    margin: 0;
    text-align: center;
}

.auth-success {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: #E8FAF9;
    border: 1.5px solid #4ECDC4;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.88rem;
    color: #3BAFA7;
    font-weight: 600;
}

.btn-auth {
    width: 100%;
    padding: 0.9rem;
    background: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
    font-family: inherit;
}

.btn-auth:hover:not(:disabled) { background: #3BAFA7; transform: translateY(-1px); }
.btn-auth:disabled { opacity: 0.6; cursor: not-allowed; }

.ac-footer { text-align: center; }

.link-logout {
    font-size: 0.85rem;
    color: #aaa;
    background: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    text-decoration: underline;
    transition: color 0.2s;
}

.link-logout:hover { color: #666; }

@media (max-width: 768px) {
    .auth-deco      { display: none; }
    .auth-form-side { width: 100%; background: linear-gradient(135deg, #f0fffe, #ffeef0); }
}
</style>
