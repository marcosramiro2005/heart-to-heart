<script setup>
import { useForm } from '@inertiajs/vue3'

const emit = defineEmits(['cerrar'])

const categorias = [
    { id: 'ansiedad', label: '😰 Ansiedad' },
    { id: 'depresion', label: '😢 Depresión' },
    { id: 'relaciones', label: '💙 Relaciones' },
    { id: 'autoestima', label: '💪 Autoestima' },
    { id: 'sueno', label: '😴 Sueño' },
    { id: 'general', label: '💬 General' },
]

const form = useForm({
    title: '',
    content: '',
    category: 'general',
    is_anonymous: false,
})

const submit = () => {
    form.post('/comunidad', {
        onSuccess: () => emit('cerrar'),
    })
}
</script>

<template>
    <div class="nuevo-post-card">
        <div class="np-header">
            <h2>Compartir experiencia</h2>
            <button class="btn-cerrar" @click="emit('cerrar')">✕</button>
        </div>

        <form @submit.prevent="submit">
            <div class="field">
                <label>Título</label>
                <input
                    v-model="form.title"
                    type="text"
                    placeholder="¿De qué quieres hablar?"
                    maxlength="150"
                    required
                />
                <span class="char-count">{{ form.title.length }}/150</span>
                <span v-if="form.errors.title" class="error">{{ form.errors.title }}</span>
            </div>

            <div class="field">
                <label>Categoría</label>
                <div class="cat-selector">
                    <button
                        v-for="cat in categorias"
                        :key="cat.id"
                        type="button"
                        class="cat-option"
                        :class="{ selected: form.category === cat.id }"
                        @click="form.category = cat.id"
                    >
                        {{ cat.label }}
                    </button>
                </div>
            </div>

            <div class="field">
                <label>Tu experiencia</label>
                <textarea
                    v-model="form.content"
                    placeholder="Comparte lo que sientes, lo que te ha ayudado o simplemente desahógate. Este es un espacio seguro 💚"
                    rows="6"
                    maxlength="2000"
                    required
                ></textarea>
                <span class="char-count">{{ form.content.length }}/2000</span>
                <span v-if="form.errors.content" class="error">{{ form.errors.content }}</span>
            </div>

            <div class="anon-toggle">
                <label class="toggle-label">
                    <input type="checkbox" v-model="form.is_anonymous" />
                    <span class="toggle-track">
                        <span class="toggle-thumb"></span>
                    </span>
                    Publicar de forma anónima
                </label>
                <p class="anon-hint">Tu nombre no será visible para otros usuarios</p>
            </div>

            <div class="np-actions">
                <button type="button" class="btn-cancelar" @click="emit('cerrar')">
                    Cancelar
                </button>
                <button type="submit" class="btn-publicar" :disabled="form.processing">
                    {{ form.processing ? 'Publicando...' : '💚 Publicar' }}
                </button>
            </div>
        </form>
    </div>
</template>

<style scoped>
.nuevo-post-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    width: 100%;
    max-width: 580px;
    max-height: 90vh;
    overflow-y: auto;
}

.np-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.np-header h2 {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    color: #2D2D2D;
}

.btn-cerrar {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #999;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
}

.btn-cerrar:hover { background: #f0f0f0; }

.field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    margin-bottom: 1.2rem;
    position: relative;
}

.field label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #2D2D2D;
}

.field input, .field textarea {
    padding: 0.75rem 1rem;
    border: 2px solid #e8f5f4;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    transition: border-color 0.2s;
    background: #fafafa;
    resize: vertical;
}

.field input:focus, .field textarea:focus {
    border-color: #4ECDC4;
}

.char-count {
    font-size: 0.75rem;
    color: #aaa;
    text-align: right;
}

.cat-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.cat-option {
    padding: 0.4rem 0.9rem;
    border: 2px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    color: #2D2D2D;
}

.cat-option.selected {
    background: #4ECDC4;
    border-color: #4ECDC4;
    color: white;
}

.anon-toggle {
    margin-bottom: 1.5rem;
}

.toggle-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
}

.toggle-label input {
    display: none;
}

.toggle-track {
    position: relative;
    width: 40px;
    height: 22px;
    background: #ddd;
    border-radius: 11px;
    transition: background 0.2s;
    flex-shrink: 0;
}

.toggle-label input:checked + .toggle-track {
    background: #4ECDC4;
}

.toggle-thumb {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 16px;
    height: 16px;
    background: white;
    border-radius: 50%;
    transition: transform 0.2s;
}

.toggle-label input:checked + .toggle-track .toggle-thumb {
    transform: translateX(18px);
}

.anon-hint {
    font-size: 0.78rem;
    color: #aaa;
    margin: 0.3rem 0 0 52px;
}

.np-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.btn-cancelar {
    padding: 0.75rem 1.5rem;
    background: #f5f5f5;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    color: #666;
}

.btn-publicar {
    padding: 0.75rem 1.5rem;
    background: #4ECDC4;
    color: white;
    border: none;
    border-radius: 25px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-publicar:hover { background: #3BAFA7; }
.btn-publicar:disabled { opacity: 0.6; cursor: not-allowed; }

.error {
    font-size: 0.78rem;
    color: #E63946;
}
</style>