<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
    name:                  '',
    email:                 '',
    password:              '',
    password_confirmation: '',
})

const mostrarPassword = ref(false)
const paso            = ref(1)

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}

const siguientePaso = () => {
    if (paso.value === 1 && form.name && form.email) paso.value = 2
}
</script>

<template>
    <Head title="Crear cuenta" />

    <div class="auth-page">

        <!-- Lado decorativo -->
        <div class="auth-deco register-deco">
            <div class="deco-content">
                <div class="deco-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </div>
                <h2>Empieza a cuidarte hoy</h2>
                <p>Únete a Heart to Heart. Es completamente gratuito y siempre lo será.</p>

                <div class="deco-pasos">
                    <div class="dp-item">
                        <span class="dp-num">01</span>
                        <div>
                            <strong>Crea tu cuenta</strong>
                            <p>Solo te llevará un minuto</p>
                        </div>
                    </div>
                    <div class="dp-item">
                        <span class="dp-num">02</span>
                        <div>
                            <strong>Verifica tu email</strong>
                            <p>Te enviamos un enlace de confirmación</p>
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

        <!-- Formulario -->
        <div class="auth-form-side">
            <div class="auth-card">

                <div class="ac-header">
                    <h1>Crear cuenta gratis</h1>
                    <p>Sin tarjeta de crédito · Sin compromisos</p>
                </div>

                <!-- Errores -->
                <div v-if="Object.keys(form.errors).length" class="auth-error">
                    <span>⚠️</span>
                    <span>{{ Object.values(form.errors)[0] }}</span>
                </div>

                <form @submit.prevent="submit" class="auth-form">

                    <div class="form-group">
                        <label>Nombre completo</label>
                        <div class="input-wrapper">
                            <span class="input-icon">👤</span>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Tu nombre"
                                autocomplete="name"
                                :class="{ error: form.errors.name }"
                                required
                            />
                        </div>
                        <span v-if="form.errors.name" class="field-error">{{ form.errors.name }}</span>
                    </div>

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

                    <div class="form-group">
                        <label>Contraseña</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input
                                v-model="form.password"
                                :type="mostrarPassword ? 'text' : 'password'"
                                placeholder="Mínimo 8 caracteres"
                                autocomplete="new-password"
                                :class="{ error: form.errors.password }"
                                required
                            />
                            <button type="button" class="toggle-pass" @click="mostrarPassword = !mostrarPassword">
                                {{ mostrarPassword ? '🙈' : '👁️' }}
                            </button>
                        </div>
                        <span v-if="form.errors.password" class="field-error">{{ form.errors.password }}</span>
                    </div>

                    <div class="form-group">
                        <label>Confirmar contraseña</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input
                                v-model="form.password_confirmation"
                                :type="mostrarPassword ? 'text' : 'password'"
                                placeholder="Repite tu contraseña"
                                autocomplete="new-password"
                                required
                            />
                        </div>
                    </div>

                    <!-- Indicador de fortaleza de contraseña -->
                    <div v-if="form.password.length > 0" class="password-strength">
                        <div class="ps-barra">
                            <div
                                class="ps-relleno"
                                :style="{
                                    width: `${Math.min(100, form.password.length * 10)}%`,
                                    backgroundColor: form.password.length < 6 ? '#E63946' : form.password.length < 10 ? '#FF8C42' : '#4ECDC4'
                                }"
                            ></div>
                        </div>
                        <span class="ps-label" :style="{
                            color: form.password.length < 6 ? '#E63946' : form.password.length < 10 ? '#FF8C42' : '#4ECDC4'
                        }">
                            {{ form.password.length < 6 ? 'Débil' : form.password.length < 10 ? 'Moderada' : 'Fuerte' }}
                        </span>
                    </div>

                    <p class="form-aviso">
                        Al registrarte aceptas que Heart to Heart es una herramienta de apoyo al bienestar y no sustituye atención psicológica profesional.
                    </p>

                    <button
                        type="submit"
                        class="btn-auth"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">⏳ Creando cuenta...</span>
                        <span v-else>💚 Crear mi cuenta gratis</span>
                    </button>

                </form>

                <div class="ac-footer">
                    <p>¿Ya tienes cuenta?
                        <Link :href="route('login')" class="link-auth">Iniciar sesión</Link>
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
}

.dp-num {
    font-size: 1.5rem;
    font-weight: 900;
    color: rgba(255,255,255,0.3);
    line-height: 1;
    min-width: 36px;
}

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

.ac-header h1 { font-size: 1.6rem; font-weight: 800; color: #1a1a1a; margin: 0 0 0.25rem; }
.ac-header p  { font-size: 0.88rem; color: #4ECDC4; margin: 0; font-weight: 600; }

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

.auth-form { display: flex; flex-direction: column; gap: 1rem; }

.form-group { display: flex; flex-direction: column; gap: 0.35rem; }
.form-group label { font-size: 0.85rem; font-weight: 700; color: #2D2D2D; }

.input-wrapper { position: relative; display: flex; align-items: center; }

.input-icon {
    position: absolute;
    left: 0.85rem;
    font-size: 1rem;
    pointer-events: none;
    z-index: 1;
}

.input-wrapper input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.6rem;
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

.field-error { font-size: 0.78rem; color: #E63946; font-weight: 600; }

.toggle-pass {
    position: absolute;
    right: 0.85rem;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    padding: 0;
}

/* Fortaleza contraseña */
.password-strength {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.ps-barra {
    flex: 1;
    height: 4px;
    background: #f0f0f0;
    border-radius: 2px;
    overflow: hidden;
}

.ps-relleno {
    height: 100%;
    border-radius: 2px;
    transition: width 0.3s, background-color 0.3s;
}

.ps-label { font-size: 0.78rem; font-weight: 700; min-width: 55px; }

.form-aviso {
    font-size: 0.75rem;
    color: #aaa;
    line-height: 1.5;
    margin: 0;
    text-align: center;
}

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
}

.btn-auth:hover:not(:disabled) { background: #3BAFA7; transform: translateY(-1px); }
.btn-auth:disabled { opacity: 0.6; cursor: not-allowed; }

.ac-footer { text-align: center; font-size: 0.88rem; color: #888; }
.link-auth { color: #4ECDC4; font-weight: 700; text-decoration: none; }
.link-auth:hover { text-decoration: underline; }

@media (max-width: 768px) {
    .auth-deco      { display: none; }
    .auth-form-side { width: 100%; background: linear-gradient(135deg, #f0fffe, #ffeef0); }
}
</style>