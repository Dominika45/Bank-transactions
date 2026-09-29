import { createRouter, createWebHistory } from 'vue-router';

import ImportsIndex from '../views/Imports/ImportsIndex.vue';
import ImportDetails from '../views/Imports/ImportDetails.vue';

const router = createRouter({
    history: createWebHistory(),

    routes: [
        {
            path: '/',
            redirect: '/imports',
        },
        {
            path: '/imports',
            name: 'imports.index',
            component: ImportsIndex,
        },
        {
            path: '/imports/:id',
            name: 'imports.show',
            component: ImportDetails,
        },
    ],
});

export default router;