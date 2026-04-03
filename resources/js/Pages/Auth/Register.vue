<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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

            <h1 class="auth-title">BIENVENIDO A HEART TO HEART</h1>

            <form @submit.prevent="submit">

                <div class="field">
                    <label>NOMBRE:</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Tu nombre"
                        required
                        autofocus
                    />
                    <span v-if="form.errors.name" class="error">{{ form.errors.name }}</span>
                </div>

                <div class="field">
                    <label>EMAIL:</label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="tu@email.com"
                        required
                    />
                    <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
                </div>

                <div class="field">
                    <label>CONTRASEÑA:</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Mínimo 8 caracteres"
                        required
                    />
                    <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
                </div>

                <div class="field">
                    <label>CONFIRMAR CONTRASEÑA:</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="Repite tu contraseña"
                        required
                    />
                </div>

                <button type="submit" :disabled="form.processing" class="btn-primary">
                    {{ form.processing ? 'Creando cuenta...' : 'CREAR CUENTA' }}
                </button>

                <p class="switch-link">
                    ¿Ya tienes cuenta?
                    <Link :href="route('login')">Inicia sesión</Link>
                </p>

            </form>
        </div>
    </div>
</template>

<style scoped>
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
    color: var(--color-red-brand);
    font-weight: 700;
    font-size: 0.85rem;
    letter-spacing: 0.1em;
    margin: 0;
}

.auth-title {
    font-size: 1.1rem;
    font-weight: 700;
    text-align: center;
    margin: 0.5rem 0;
    color: var(--color-text);
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
    color: var(--color-text);
    letter-spacing: 0.05em;
}

.field input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 8px;
    background-color: var(--color-primary-light);
    color: var(--color-text);
    font-size: 0.95rem;
    outline: none;
    transition: box-shadow 0.2s;
    box-sizing: border-box;
}

.field input:focus {
    box-shadow: 0 0 0 2px var(--color-primary);
}

.field input::placeholder {
    color: var(--color-placeholder);
}

.btn-primary {
    margin-top: 0.5rem;
    padding: 0.85rem;
    background-color: var(--color-primary);
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
    background-color: var(--color-primary-dark);
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.switch-link {
    text-align: center;
    font-size: 0.85rem;
    color: var(--color-text);
    margin: 0;
}

.switch-link a {
    color: var(--color-primary-dark);
    font-weight: 600;
    text-decoration: none;
}

.switch-link a:hover {
    text-decoration: underline;
}

.error {
    font-size: 0.78rem;
    color: var(--color-red-brand);
}
</style>