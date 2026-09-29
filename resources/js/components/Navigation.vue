<script setup>
    import { RouterLink, useRoute } from 'vue-router'

    const route = useRoute()

    const links = [
        { to: '/imports', label: 'Lista importów' },
    ]
</script>

<template>
    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center space-x-8">
                <RouterLink
                    v-for="link in links"
                    :key="link.to"
                    :to="link.to"
                    class="text-lg font-medium"
                    :class="route.path.startsWith(link.to) ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700'" >
                    {{ link.label }}
                </RouterLink>
            </div>
        </div>
    </nav>
</template>