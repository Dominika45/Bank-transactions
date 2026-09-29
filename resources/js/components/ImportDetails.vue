<script setup>
    import { ref, onMounted } from 'vue'
    import { useRoute } from 'vue-router'
    import axios from 'axios'

    const route = useRoute()

    const importData = ref(null)

    onMounted(async () => {
        const response = await axios.get(`/api/imports/${route.params.id}`)
        importData.value = response.data
    })

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
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div  v-if="importData" class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="p-6">
                    <div class="mb-8 flex items-center justify-between">
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Szczegóły importu
                        </h1>
                        <RouterLink to="/imports" class="font-medium text-blue-600 hover:text-blue-800">
                            ← Powrót do listy
                        </RouterLink>
                    </div>

                    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"> 
                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-sm text-gray-500">
                                Plik
                            </p> 
                            <p class="mt-1 font-semibold text-gray-900">
                                {{ importData.file_name }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-sm text-gray-500">
                                Wszystkie rekordy
                            </p>
                            <p class="mt-1 text-xl font-semibold text-gray-900">
                                {{ importData.total_records }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-sm text-gray-500">
                                Pomyślne
                            </p>
                            <p class="mt-1 text-xl font-semibold text-green-600">
                                {{ importData.successful_records }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-sm text-gray-500">
                                Nieudane
                            </p>
                            <p class="mt-1 text-xl font-semibold text-red-600">
                                {{ importData.failed_records }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <p class="text-sm text-gray-500">
                            Status importu
                        </p>
                        <p class="mt-1 font-semibold text-gray-900">
                            {{ statusLabel(importData.status) }}
                        </p>
                    </div>

                    <div>
                        <h2 class="mb-4 text-xl font-semibold text-gray-900">
                            Logi błędów
                        </h2>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border p-3 text-left">
                                            ID transakcji
                                        </th>
                                        <th class="border p-3 text-left">
                                            Komunikat błędu
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="log in importData.import_log" :key="log.id">
                                        <td class="border p-3">
                                            {{ log.transaction_id }}
                                        </td>
                                        <td class="border p-3">
                                            {{ log.error_message }}
                                        </td>
                                    </tr>
                                    <tr v-if="!importData.import_log || importData.import_log.length === 0">
                                        <td colspan="2" class="border p-6 text-center text-gray-500" >
                                            Brak błędów dla tego importu.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="rounded-lg bg-white p-6 text-center shadow-sm">
                Ładowanie...
            </div>
        </div>
    </div>
</template>
