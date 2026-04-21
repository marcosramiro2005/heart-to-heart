<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    email: String,
    token: String,
})

const mostrarPassword = ref(false)

const form = useForm({
    token:                 props.token,
    email:                 props.email,
    password:              '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Nueva contraseña" />

    <div class="auth-page">

        <div class="auth-deco reset-deco">
            <div class="deco-content">
                <div class="deco-logo">
                    <img src="/images/logo.png" alt="Heart to Heart" />
                    <span>HEART TO HEART</span>
                </div>
                <h2>Crea una nueva contraseña</h2>
                <p>Elige una contraseña segura que puedas recordar fácilmente.</p>
                <div class="deco-tips">
                    <p class="dt-titulo">Consejos para una buena contraseña:</p>
                    <div class="dt-item"><span>✓</span><span>Al menos 8 caracteres</span></div>
                    <div class="dt-item"><span>✓</span><span>Mezcla letras y números</span></div>
                    <div class="dt-item"><span>✓</span><span>Evita datos personales obvios</span></div>
                </div>
            </div>
            <div class="deco-circles">
                <div class="dc c1"></div>
                <div class="dc c2"></div>
            </div>
        </div>

        <div class="auth-form-side">
            <div class="auth-card">

                <div class="ac-header">
                    <div class="ac-icon">🔐</div>
                    <h1>Nueva contraseña</h1>
                    <p>Para: <strong>{{ email }}</strong></p>
                </div>

                <form @submit.prevent="submit" class="auth-form">

                    <div class="form-group">
                        <label>Nueva contraseña</label>
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

                    <!-- Fortaleza -->
                    <div v-if="form.password.length > 0" class="password-strength">
                        <div class="ps-barra">
                            <div class="ps-relleno" :style="{
                                width: `${Math.min(100, form.password.length * 10)}%`,
                                backgroundColor: form.password.length < 6 ? '#E63946' : form.password.length < 10 ? '#FF8C42' : '#4ECDC4'
                            }"></div>
                        </div>
                        <span class="ps-label" :style="{
                            color: form.password.length < 6 ? '#E63946' : form.password.length < 10 ? '#FF8C42' : '#4ECDC4'
                        }">
                            {{ form.password.length < 6 ? 'Débil' : form.password.length < 10 ? 'Moderada' : 'Fuerte' }}
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Confirmar contraseña</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input
                                v-model="form.password_confirmation"
                                :type="mostrarPassword ? 'text' : 'password'"
                                placeholder="Repite tu nueva contraseña"
                                autocomplete="new-password"
                                required
                            />
                        </div>
                        <span v-if="form.errors.password_confirmation" class="field-error">
                            {{ form.errors.password_confirmation }}
                        </span>
                    </div>

                    <button type="submit" class="btn-auth" :disabled="form.processing">
                        <span v-if="form.processing">⏳ Guardando...</span>
                        <span v-else>🔐 Guardar nueva contraseña</span>
                    </button>

                </form>

            </div>
        </div>
    </div>
</template>

<style scoped>
.auth-page { min-height: 100vh; display: flex; }

.auth-deco {
    flex: 1;
    background: linear-gradient(135deg, #4ECDC4 0%, #3BAFA7 50%, #9B8EC4 100%);
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

.auth-deco h2 { font-size: 1.8rem; font-weight: 900; color: white; margin: 0 0 0.75rem; }
.auth-deco > .deco-content > p { color: rgba(255,255,255,0.82); font-size: 1rem; line-height: 1.6; margin: 0 0 2rem; }

.deco-tips { display: flex; flex-direction: column; gap: 0.6rem; }
.dt-titulo { font-size: 0.85rem; color: rgba(255,255,255,0.7); font-weight: 600; margin: 0 0 0.4rem; }

.dt-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    font-size: 0.9rem;
}

.dt-item span:first-child { font-size: 0.8rem; color: #fff; opacity: 0.8; }

.deco-circles { position: absolute; inset: 0; pointer-events: none; }
.dc { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.08); }
.dc.c1 { width: 280px; height: 280px; top: -60px; right: -50px; }
.dc.c2 { width: 200px; height: 200px; bottom: 40px; right: 80px; }

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

.ac-icon { font-size: 2.5rem; text-align: center; }
.ac-header { display: flex; flex-direction: column; gap: 0.4rem; text-align: center; }
.ac-header h1 { font-size: 1.5rem; font-weight: 800; color: #1a1a1a; margin: 0; }
.ac-header p  { font-size: 0.88rem; color: #888; margin: 0; }
.ac-header strong { color: #2D2D2D; }

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

.password-strength { display: flex; align-items: center; gap: 0.75rem; }
.ps-barra { flex: 1; height: 4px; background: #f0f0f0; border-radius: 2px; overflow: hidden; }
.ps-relleno { height: 100%; border-radius: 2px; transition: width 0.3s, background-color 0.3s; }
.ps-label { font-size: 0.78rem; font-weight: 700; min-width: 55px; }

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

@media (max-width: 768px) {
    .auth-deco      { display: none; }
    .auth-form-side { width: 100%; background: linear-gradient(135deg, #f0fffe, #ffeef0); }
}
</style>