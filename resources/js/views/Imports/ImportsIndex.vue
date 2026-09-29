<script setup>
    import { ref } from 'vue'

    import Navigation from '../../components/Navigation.vue'
    import ImportsList from '../../components/ImportsList.vue'
    import ImportUploadModal from '../../components/ImportUploadModal.vue'

    const showUploadModal = ref(false)
    const refreshKey = ref(0)

    const refreshImports = () => {
        refreshKey.value++
    }
</script>

<template>

    <Navigation />

    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6 flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Lista importów
                    </h1>
                    <button type="button" @click="showUploadModal = true"  class="rounded-lg bg-green-600 px-4 py-2 font-bold text-white transition hover:bg-green-700" >
                        Dodaj
                    </button>
                </div>
        
                <ImportsList :refresh-key="refreshKey" />

            </div>
        </div>
    </div>

    <ImportUploadModal :show="showUploadModal"  @close="showUploadModal = false" @imported="refreshImports" />

</template>
