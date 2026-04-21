<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
    email:    '',
    password: '',
    remember: false,
})

const mostrarPassword = ref(false)
const page = usePage()
const mensajeExito = page.props.flash?.success

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Iniciar sesión" />

    <div class="auth-page">
        <!-- Lado izquierdo — decorativo -->
        <div class="auth-deco">
            <div class="deco-content">
                <div class="deco-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </div>
                <h2>Tu bienestar emocional importa</h2>
                <p>Un espacio seguro para cuidarte cada día, con técnicas de bienestar y apoyo emocional cuando más lo necesitas.</p>
                <div class="deco-features">
                    <div class="df-item"><span>💬</span><span>Hearty, tu guía emocional</span></div>
                    <div class="df-item"><span>📊</span><span>Seguimiento emocional</span></div>
                    <div class="df-item"><span>🌿</span><span>15+ técnicas de bienestar</span></div>
                    <div class="df-item"><span>🏆</span><span>Sistema de logros</span></div>
                </div>
            </div>
            <div class="deco-circles">
                <div class="dc c1"></div>
                <div class="dc c2"></div>
                <div class="dc c3"></div>
            </div>
        </div>

        <!-- Lado derecho — formulario -->
        <div class="auth-form-side">
            <div class="auth-card">

                <div class="ac-header">
                    <h1>Bienvenido/a de nuevo</h1>
                    <p>Inicia sesión en tu cuenta</p>
                </div>

                <!-- Mensaje de éxito (verificación email) -->
                <div v-if="mensajeExito" class="auth-success">
                    <span>✅</span>
                    <span>{{ mensajeExito }}</span>
                </div>

                <!-- Error general -->
                <div v-if="form.errors.email" class="auth-error">
                    <span>⚠️</span>
                    <span>{{ form.errors.email }}</span>
                </div>

                <form @submit.prevent="submit" class="auth-form">

                    <div class="form-group">
                        <label>Correo electrónico</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✉️</span>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="tu@email.com"
                                autocomplete="email"
                                :class="{ error: form.errors.email }"
                                required
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="label-row">
                            <label>Contraseña</label>
                            <Link :href="route('password.request')" class="link-forgot">
                                ¿Olvidaste tu contraseña?
                            </Link>
                        </div>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input
                                v-model="form.password"
                                :type="mostrarPassword ? 'text' : 'password'"
                                placeholder="Tu contraseña"
                                autocomplete="current-password"
                                :class="{ error: form.errors.password }"
                                required
                            />
                            <button
                                type="button"
                                class="toggle-pass"
                                @click="mostrarPassword = !mostrarPassword"
                            >
                                {{ mostrarPassword ? '🙈' : '👁️' }}
                            </button>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="remember" v-model="form.remember" />
                        <label for="remember">Recordarme en este dispositivo</label>
                    </div>

                    <button
                        type="submit"
                        class="btn-auth"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="btn-loading">⏳</span>
                        <span v-else>Iniciar sesión →</span>
                    </button>

                </form>

                <div class="ac-footer">
                    <p>¿No tienes cuenta?
                        <Link :href="route('register')" class="link-auth">
                            Regístrate gratis
                        </Link>
                    </p>
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

/* ── Lado decorativo ── */
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
    margin-bottom: 2.5rem;
}

.deco-logo img  { width: 44px; filter: brightness(1.1); }
.deco-logo span { font-weight: 700; font-size: 0.9rem; color: white; letter-spacing: 0.1em; }

.auth-deco h2 {
    font-size: 2rem;
    font-weight: 900;
    color: white;
    line-height: 1.2;
    margin: 0 0 1rem;
}

.auth-deco p {
    font-size: 1rem;
    color: rgba(255,255,255,0.82);
    line-height: 1.6;
    margin: 0 0 2rem;
    max-width: 360px;
}

.deco-features {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.df-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.95rem;
    color: white;
    font-weight: 500;
}

.df-item span:first-child { font-size: 1.2rem; }

/* Círculos decorativos */
.deco-circles { position: absolute; inset: 0; pointer-events: none; }

.dc {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
}

.dc.c1 { width: 300px; height: 300px; top: -80px; right: -60px; }
.dc.c2 { width: 200px; height: 200px; bottom: 40px; right: 60px; }
.dc.c3 { width: 150px; height: 150px; bottom: -40px; left: -30px; }

/* ── Lado formulario ── */
.auth-form-side {
    width: 480px;
    flex-shrink: 0;
    background: #fafffe;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.auth-card {
    width: 100%;
    max-width: 380px;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.ac-header h1 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0 0 0.35rem;
}

.ac-header p {
    font-size: 0.92rem;
    color: #888;
    margin: 0;
}

/* Mensajes ── */
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

.auth-error {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: #fff5f5;
    border: 1.5px solid #E63946;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.88rem;
    color: #E63946;
    font-weight: 600;
}

/* Formulario ── */
.auth-form {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.form-group label {
    font-size: 0.85rem;
    font-weight: 700;
    color: #2D2D2D;
}

.label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.link-forgot {
    font-size: 0.8rem;
    color: #4ECDC4;
    text-decoration: none;
    font-weight: 600;
}

.link-forgot:hover { text-decoration: underline; }

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 0.85rem;
    font-size: 1rem;
    pointer-events: none;
    z-index: 1;
}

.input-wrapper input {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 2.6rem;
    border: 2px solid #e8f5f4;
    border-radius: 12px;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    background: white;
    transition: border-color 0.2s, box-shadow 0.2s;
    color: #2D2D2D;
}

.input-wrapper input:focus {
    border-color: #4ECDC4;
    box-shadow: 0 0 0 3px rgba(78,205,196,0.12);
}

.input-wrapper input.error { border-color: #E63946; }

.toggle-pass {
    position: absolute;
    right: 0.85rem;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    padding: 0;
    line-height: 1;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #555;
}

.form-check input { width: 16px; height: 16px; accent-color: #4ECDC4; cursor: pointer; }
.form-check label { cursor: pointer; }

.btn-auth {
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
    margin-top: 0.25rem;
}

.btn-auth:hover:not(:disabled) { background: #3BAFA7; transform: translateY(-1px); }
.btn-auth:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-loading { animation: spin 1s linear infinite; display: inline-block; }
@keyframes spin { to { transform: rotate(360deg); } }

.ac-footer {
    text-align: center;
    font-size: 0.88rem;
    color: #888;
}

.link-auth { color: #4ECDC4; font-weight: 700; text-decoration: none; }
.link-auth:hover { text-decoration: underline; }

/* Responsive ── */
@media (max-width: 768px) {
    .auth-deco      { display: none; }
    .auth-form-side { width: 100%; background: linear-gradient(135deg, #f0fffe, #ffeef0); }
}
</style>