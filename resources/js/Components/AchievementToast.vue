<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const logrosNuevos = ref([])
const visible = ref(false)

const verificar = async () => {
    try {
        const res = await axios.post('/logros/verificar')
        if (res.data.nuevos_logros?.length > 0) {
            logrosNuevos.value = res.data.nuevos_logros
            visible.value = true
            setTimeout(() => {
                visible.value = false
                logrosNuevos.value = []
            }, 4000)
        }
    } catch (e) {}
}

onMounted(() => {
    // Verificar logros cada vez que se carga una página
    setTimeout(verificar, 1500)
})
</script>

<template>
    <Transition name="toast">
        <div v-if="visible" class="achievement-toast">
            <div
                v-for="logro in logrosNuevos"
                :key="logro.name"
                class="toast-item"
            >
                <span class="toast-emoji">{{ logro.emoji }}</span>
                <div class="toast-info">
                    <span class="toast-titulo">¡Logro desbloqueado!</span>
                    <span class="toast-nombre">{{ logro.name }}</span>
                    <span class="toast-puntos">+{{ logro.points }} puntos</span>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.achievement-toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.toast-item {
    background: white;
    border-left: 4px solid #4ECDC4;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    min-width: 280px;
}

.toast-emoji { font-size: 2rem; }

.toast-info {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}

.toast-titulo {
    font-size: 0.72rem;
    color: #4ECDC4;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.toast-nombre {
    font-size: 0.92rem;
    font-weight: 700;
    color: #2D2D2D;
}

.toast-puntos {
    font-size: 0.78rem;
    color: #888;
}

.toast-enter-active,
.toast-leave-active { transition: all 0.4s ease; }
.toast-enter-from   { opacity: 0; transform: translateX(100px); }
.toast-leave-to     { opacity: 0; transform: translateX(100px); }
</style>