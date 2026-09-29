<script setup>
    import { ref } from 'vue'
    import axios from 'axios'

    const props = defineProps({
        show: Boolean,
    })

    const emit = defineEmits(['close', 'imported'])

    const file = ref(null)
    const loading = ref(false)
    const error = ref('')

    const handleFileChange = (event) => {
        file.value = event.target.files[0] ?? null
        error.value = ''
    }

    const uploadFile = async () => {
        if (!file.value) {
            error.value = 'Wybierz plik.'
            return
        }

        loading.value = true
        error.value = ''

        const formData = new FormData()
        formData.append('import_file', file.value)

        try {
            await axios.post('/api/imports', formData)

            emit('imported')
            emit('close')

            file.value = null
        } catch (err) {
            error.value =
                err.response?.data?.errors?.import_file?.[0] ??
                'Wystąpił błąd podczas importu.'
        } finally {
            loading.value = false
        }
    }
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" @click.self="emit('close')">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">
                    Dodaj import
                </h2>
                <button type="button" @click="emit('close')" class="text-2xl text-gray-400 hover:text-gray-600">
                    ×
                </button>
            </div>
            <p class="mt-2 text-sm text-gray-500">
                Wybierz plik CSV, JSON lub XML.
            </p>
            <div class="mt-6">
                <input type="file" accept=".csv,.json,.xml" @change="handleFileChange" class="block w-full rounded-lg border border-gray-300 p-2 text-sm">
            </div>
            <p v-if="file" class="mt-3 text-sm text-gray-600" >
                Wybrany plik:
                <strong>{{ file.name }}</strong>
            </p>
            <p v-if="error" class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700" >
                {{ error }}
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="emit('close')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" >
                    Anuluj
                </button>
                <button
                    type="button"
                    @click="uploadFile"
                    :disabled="loading"
                    class="rounded-lg bg-green-600 px-4 py-2 text-sm font-bold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50">
                    {{ loading ? 'Importowanie...' : 'Importuj' }}
                </button>
            </div>
        </div>
    </div>
</template>
