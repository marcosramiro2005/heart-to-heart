<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <div class="auth-wrapper">
        <div class="auth-card">

            <!-- Logo -->
            <div class="logo-area">
                <img src="/images/logo.png" alt="Heart to Heart" class="logo-img" />
                <p class="brand-name">HEART TO HEART</p>
            </div>

            <h1 class="auth-title">INICIA SESIÓN</h1>
            <p class="auth-subtitle">ACCEDE A TU ESPACIO SEGURO</p>

            <div v-if="status" class="status-msg">{{ status }}</div>

            <form @submit.prevent="submit">

                <div class="field">
                    <label>EMAIL:</label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="tu@email.com"
                        required
                        autofocus
                    />
                    <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
                </div>

                <div class="field">
                    <label>CONTRASEÑA:</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Tu contraseña"
                        required
                    />
                    <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
                </div>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="forgot-link"
                >
                    ¿Olvidaste tu contraseña?
                </Link>

                <button type="submit" :disabled="form.processing" class="btn-primary">
                    {{ form.processing ? 'Entrando...' : 'INICIAR SESIÓN' }}
                </button>

                <p class="switch-link">
                    ¿No tienes cuenta?
                    <Link :href="route('register')">Crear cuenta</Link>
                </p>

            </form>
        </div>
    </div>
</template>

<style scoped>
/* Mismo CSS que el Register — cópialo aquí también */
.auth-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #ffffff;
    padding: 2rem;
}

.auth-card {
    width: 100%;
    max-width: 420px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.logo-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
}

.logo-img {
    width: 90px;
    height: auto;
}

.brand-name {
    color: #E63946;
    font-weight: 700;
    font-size: 0.85rem;
    letter-spacing: 0.1em;
    margin: 0;
}

.auth-title {
    font-size: 1.2rem;
    font-weight: 700;
    text-align: center;
    margin: 0.5rem 0 0;
    color: #2D2D2D;
}

.auth-subtitle {
    font-size: 0.85rem;
    color: #666;
    margin: 0;
    text-align: center;
    letter-spacing: 0.05em;
}

.status-msg {
    background: #E8FAF9;
    color: #3BAFA7;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    width: 100%;
    text-align: center;
}

form {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.field label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #2D2D2D;
    letter-spacing: 0.05em;
}

.field input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 8px;
    background-color: #E8FAF9;
    color: #2D2D2D;
    font-size: 0.95rem;
    outline: none;
    transition: box-shadow 0.2s;
    box-sizing: border-box;
}

.field input:focus {
    box-shadow: 0 0 0 2px #4ECDC4;
}

.field input::placeholder {
    color: #A0AEC0;
}

.forgot-link {
    font-size: 0.82rem;
    color: #3BAFA7;
    text-decoration: none;
    text-align: left;
    margin-top: -0.5rem;
}

.forgot-link:hover {
    text-decoration: underline;
}

.btn-primary {
    margin-top: 0.5rem;
    padding: 0.85rem;
    background-color: #4ECDC4;
    color: white;
    font-weight: 700;
    font-size: 0.95rem;
    letter-spacing: 0.05em;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background-color 0.2s;
    width: 100%;
}

.btn-primary:hover {
    background-color: #3BAFA7;
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.switch-link {
    text-align: center;
    font-size: 0.85rem;
    color: #2D2D2D;
    margin: 0;
}

.switch-link a {
    color: #3BAFA7;
    font-weight: 600;
    text-decoration: none;
}

.switch-link a:hover {
    text-decoration: underline;
}

.error {
    font-size: 0.78rem;
    color: #E63946;
}
</style>