<script setup>
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const visible   = ref(false)
const logro     = ref(null)
const page      = usePage()

const mostrar = (achievement) => {
    logro.value   = achievement
    visible.value = true
    setTimeout(() => { visible.value = false }, 5000)
}

onMounted(() => {
    if (page.props.flash?.achievement) {
        mostrar(page.props.flash.achievement)
    }
})
</script>

<template>
    <Transition name="toast">
        <div v-if="visible && logro" class="achievement-toast">
            <div class="at-icono">🏆</div>
            <div class="at-contenido">
                <span class="at-titulo">¡Logro desbloqueado!</span>
                <span class="at-nombre">{{ logro.nombre ?? logro }}</span>
            </div>
            <button class="at-cerrar" @click="visible = false">✕</button>
        </div>
    </Transition>
</template>

<style scoped>
.achievement-toast {
    position: fixed;
    top: 1.5rem;
    right: 1.5rem;
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    color: white;
    border-radius: 16px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.25);
    z-index: 1000;
    border: 1px solid rgba(78,205,196,0.3);
    max-width: 320px;
}

.at-icono { font-size: 1.8rem; flex-shrink: 0; animation: bounce-in 0.4s ease; }

@keyframes bounce-in {
    0%   { transform: scale(0); }
    60%  { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.at-contenido { flex: 1; display: flex; flex-direction: column; gap: 0.1rem; }
.at-titulo    { font-size: 0.72rem; color: #4ECDC4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
.at-nombre    { font-size: 0.92rem; font-weight: 600; color: white; }

.at-cerrar {
    background: none;
    border: none;
    color: rgba(255,255,255,0.4);
    cursor: pointer;
    font-size: 0.85rem;
    padding: 0;
    flex-shrink: 0;
    transition: color 0.2s;
}

.at-cerrar:hover { color: white; }

.toast-enter-active { animation: slideInRight 0.35s ease; }
.toast-leave-active { transition: opacity 0.3s, transform 0.3s; }
.toast-leave-to     { opacity: 0; transform: translateX(100%); }

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(100%); }
    to   { opacity: 1; transform: translateX(0); }
}
</style>