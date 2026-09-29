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

    const loadImports = async () => {
        const response = await axios.get('/api/imports')
        imports.value = response.data
    }

    onMounted(loadImports)

    watch(
        () => props.refreshKey,
        () => {
            loadImports()
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
                <th class="p-2 border text-left bg-gray-50">Data dodania</th>
                <th class="p-2 border text-left bg-gray-50">Akcje</th>
            </tr>
        </thead>
        <tbody>
        <tr v-for="item in imports" :key="item.id">
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
                <td class="p-2 border">{{ new Date(item.created_at).toLocaleString('pl-PL') }}</td>
                <td class="p-2 border">
                    <RouterLink :to="`/imports/${item.id}`" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        Szczegóły →
                    </RouterLink>
                </td>
            </tr>
        </tbody>
    </table>
</template>