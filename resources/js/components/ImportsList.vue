<script setup>
    import { ref, onMounted, watch } from 'vue'
    import axios from 'axios'

    const props = defineProps({
        refreshKey: {
            type: Number,
            default: 0,
        },
    })

    const imports = ref([])
    const loading = ref(false)
    const currentPage = ref(1)
    const lastPage = ref(1)
    const error = ref(null)

    const loadImports = async (page = 1) => {
        loading.value = true
        error.value = null

        try {
            const response = await axios.get('/api/imports', {
                params: {
                    page,
                },
            })

            imports.value = response.data.data
            currentPage.value = response.data.current_page
            lastPage.value = response.data.last_page

        } catch (err) {
            console.error('BŁĄD API:', err)

            error.value =
                err.response?.data?.message ||
                err.message ||
                'Nie udało się pobrać importów.'

            imports.value = []
        } finally {
            loading.value = false
        }
    }

    onMounted(() => {
        loadImports(1)
    })

    watch(
        () => props.refreshKey,
        () => {
            loadImports(1)
        }
    )

    const statusLabel = (status) => {
        const labels = {
            success: 'Sukces',
            partial: 'Import częściowy',
            failed: 'Import nieudany',
        }

        return labels[status] ?? status
    }
</script>

<template>
    <table class="w-full border rounded-sm">
        <thead>
            <tr>
                <th class="p-2 border text-left bg-gray-50">Plik</th>
                <th class="p-2 border text-left bg-gray-50">Rekordy</th>
                <th class="p-2 border text-left bg-gray-50">Sukces</th>
                <th class="p-2 border text-left bg-gray-50">Niepowodzenie</th>
                <th class="p-2 border text-left bg-gray-50">Status</th>
                <th class="p-2 border text-left bg-gray-50">Akcje</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="7" class="p-5 text-center text-gray-500">
                    Ładowanie importów...
                </td>
            </tr>
            <tr v-else-if="imports.length === 0">
                <td colspan="7" class="p-5 text-center text-gray-500" >
                    Brak importów do wyświetlenia.
                </td>
            </tr>
            <tr v-else v-for="item in imports" :key="item.id">
                <td class="p-2 border">{{ item.file_name }}</td>
                <td class="p-2 border">{{ item.total_records }}</td>
                <td class="p-2 border">{{ item.successful_records }}</td>
                <td class="p-2 border">{{ item.failed_records }}</td>
                <td class="p-2 border">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium" :class="{
                        'bg-green-100 text-green-700': item.status === 'success',
                        'bg-yellow-100 text-yellow-700': item.status === 'partial',
                        'bg-red-100 text-red-700': item.status === 'failed',}">
                        {{ statusLabel(item.status) }}
                    </span>
                </td>
                <td class="p-2 border">
                    <RouterLink :to="`/imports/${item.id}`" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        Szczegóły →
                    </RouterLink>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="mt-6 flex items-center justify-between">
        <button type="button" @click="loadImports(currentPage - 1)" :disabled="currentPage === 1" class="rounded-lg border px-4 py-2 text-sm font-medium disabled:cursor-not-allowed disabled:opacity-40" >
            ← Poprzednia
        </button>

        <span class="text-sm text-gray-600">
            Strona {{ currentPage }} z {{ lastPage }}
        </span>

        <button type="button" @click="loadImports(currentPage + 1)" :disabled="currentPage === lastPage" class="rounded-lg border px-4 py-2 text-sm font-medium disabled:cursor-not-allowed disabled:opacity-40" >
            Następna →
        </button>
    </div>
</template>