<script setup>
    import { ref, onMounted, watch } from 'vue'
    import axios from 'axios'

    const props = defineProps({
        refreshKey: {
            type: Number,
            default: 0,
        },
    })

    const transactions = ref([])
    const loading = ref(true)
    const currentPage = ref(1)
    const lastPage = ref(1)

    const loadTransactions = async (page = 1) => {
        loading.value = true

        try {
            const response = await axios.get('/api/transactions', {
                params: {
                    page: page,
                },
            })

            transactions.value = response.data.data
            currentPage.value = response.data.current_page
            lastPage.value = response.data.last_page
        } finally {
            loading.value = false
        }
    }

    onMounted(() => {
        loadTransactions()
    })

    watch(
        () => props.refreshKey,
        () => {
            loadTransactions()
        }
    )
</script>

<template>
    <table class="w-full border rounded-sm">
        <thead>
            <tr>
                <th class="p-2 border text-left bg-gray-50">ID transakcji</th>
                <th class="p-2 border text-left bg-gray-50">Numer konta</th>
                <th class="p-2 border text-left bg-gray-50">Data transakcji</th>
                <th class="p-2 border text-left bg-gray-50">Kwota</th>
                <th class="p-2 border text-left bg-gray-50">Waluta</th>
                <th class="p-2 border text-left bg-gray-50">Data dodania</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="6" class="p-5 text-center text-gray-500">
                    Ładowanie transakcji...
                </td>
            </tr>
            <tr v-else-if="transactions.length === 0">
                <td colspan="6" class="p-5 text-center text-gray-500" >
                    Brak transakcji do wyświetlenia.
                </td>
            </tr>
            <tr v-else v-for="item in transactions" :key="item.id">
                <td class="p-2 border">{{ item.transaction_id }}</td>
                <td class="p-2 border">{{ item.account_number }}</td>
                <td class="p-2 border">{{ new Date(item.transaction_date).toLocaleString('pl-PL') }}</td>
                <td class="p-2 border">{{ item.amount }}</td>
                <td class="p-2 border">{{ item.currency }}</td>
                <td class="p-2 border">{{ new Date(item.created_at).toLocaleString('pl-PL') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-6 flex items-center justify-between">
        <button type="button" @click="loadTransactions(currentPage - 1)" :disabled="currentPage === 1" class="rounded-lg border px-4 py-2 text-sm font-medium disabled:cursor-not-allowed disabled:opacity-40" >
            ← Poprzednia
        </button>

        <span class="text-sm text-gray-600">
            Strona {{ currentPage }} z {{ lastPage }}
        </span>

        <button type="button" @click="loadTransactions(currentPage + 1)" :disabled="currentPage === lastPage" class="rounded-lg border px-4 py-2 text-sm font-medium disabled:cursor-not-allowed disabled:opacity-40" >
            Następna →
        </button>
    </div>
</template>