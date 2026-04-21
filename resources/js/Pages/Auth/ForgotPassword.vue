<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({ status: String })

const form = useForm({ email: '' })

const submit = () => form.post(route('password.email'))
</script>

<template>
    <Head title="Recuperar contraseña" />

    <div class="auth-page">

        <div class="auth-deco forgot-deco">
            <div class="deco-content">
                <div class="deco-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </div>
                <h2>¿Olvidaste tu contraseña?</h2>
                <p>No te preocupes. Te enviamos un enlace para restablecerla en tu correo.</p>
                <div class="deco-steps">
                    <div class="ds-item">
                        <span>📧</span>
                        <span>Introduce tu email registrado</span>
                    </div>
                    <div class="ds-item">
                        <span>🔗</span>
                        <span>Recibirás un enlace seguro</span>
                    </div>
                    <div class="ds-item">
                        <span>🔑</span>
                        <span>Crea una nueva contraseña</span>
                    </div>
                </div>
            </div>
            <div class="deco-circles">
                <div class="dc c1"></div>
                <div class="dc c2"></div>
            </div>
        </div>

        <div class="auth-form-side">
            <div class="auth-card">

                <div class="ac-back">
                    <Link :href="route('login')" class="back-link">
                        ← Volver al inicio de sesión
                    </Link>
                </div>

                <div class="ac-header">
                    <div class="ac-icon">🔑</div>
                    <h1>Recuperar contraseña</h1>
                    <p>Te enviaremos un enlace para restablecer tu contraseña.</p>
                </div>

                <div v-if="status" class="auth-success">
                    <span>✅</span>
                    <span>{{ status }}</span>
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
                        <span v-if="form.errors.email" class="field-error">{{ form.errors.email }}</span>
                    </div>

                    <button type="submit" class="btn-auth" :disabled="form.processing">
                        <span v-if="form.processing">⏳ Enviando...</span>
                        <span v-else>📧 Enviar enlace de recuperación</span>
                    </button>
                </form>

                <div class="ac-footer">
                    <p>¿Recuerdas tu contraseña?
                        <Link :href="route('login')" class="link-auth">Iniciar sesión</Link>
                    </p>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
.auth-page { min-height: 100vh; display: flex; }

.auth-deco {
    flex: 1;
    background: linear-gradient(135deg, #9B8EC4 0%, #7a6da8 60%, #5a4d88 100%);
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
    font-size: 1.8rem;
    font-weight: 900;
    color: white;
    margin: 0 0 0.75rem;
    line-height: 1.2;
}

.auth-deco p {
    color: rgba(255,255,255,0.82);
    font-size: 1rem;
    line-height: 1.6;
    margin: 0 0 2rem;
}

.deco-steps { display: flex; flex-direction: column; gap: 1rem; }

.ds-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: white;
    font-size: 0.95rem;
    font-weight: 500;
}

.ds-item span:first-child { font-size: 1.3rem; }

.deco-circles { position: absolute; inset: 0; pointer-events: none; }
.dc { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.08); }
.dc.c1 { width: 280px; height: 280px; top: -60px; right: -50px; }
.dc.c2 { width: 180px; height: 180px; bottom: 60px; left: -40px; }

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

.ac-back { }

.back-link {
    font-size: 0.85rem;
    color: #888;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
}

.back-link:hover { color: #4ECDC4; }

.ac-icon { font-size: 2.5rem; text-align: center; }

.ac-header { display: flex; flex-direction: column; gap: 0.4rem; text-align: center; }
.ac-header h1 { font-size: 1.5rem; font-weight: 800; color: #1a1a1a; margin: 0; }
.ac-header p  { font-size: 0.9rem; color: #888; margin: 0; line-height: 1.5; }

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

.auth-form { display: flex; flex-direction: column; gap: 1.1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-group label { font-size: 0.85rem; font-weight: 700; color: #2D2D2D; }

.input-wrapper { position: relative; display: flex; align-items: center; }

.input-icon {
    position: absolute;
    left: 0.85rem;
    font-size: 1rem;
    pointer-events: none;
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
    border-color: #9B8EC4;
    box-shadow: 0 0 0 3px rgba(155,142,196,0.12);
}

.input-wrapper input.error { border-color: #E63946; }
.field-error { font-size: 0.78rem; color: #E63946; font-weight: 600; }

.btn-auth {
    padding: 0.9rem;
    background: #9B8EC4;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
    font-family: inherit;
}

.btn-auth:hover:not(:disabled) { background: #7a6da8; transform: translateY(-1px); }
.btn-auth:disabled { opacity: 0.6; cursor: not-allowed; }

.ac-footer { text-align: center; font-size: 0.88rem; color: #888; }
.link-auth { color: #4ECDC4; font-weight: 700; text-decoration: none; }
.link-auth:hover { text-decoration: underline; }

@media (max-width: 768px) {
    .auth-deco      { display: none; }
    .auth-form-side { width: 100%; background: linear-gradient(135deg, #f5f0ff, #ffeef0); }
}
</style>