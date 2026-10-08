<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import PicLayout from '@/layouts/PicLayout.vue';

// Halaman buku kerja dipakai bersama: PIC memakai tampilan PIC, Admin/Manager memakai panel.
const page = usePage();
const layout = computed(() =>
    ['admin', 'manager'].includes(String(page.props.auth.user.role)) ? AdminLayout : PicLayout,
);
</script>

<template>
    <component :is="layout"><slot /></component>
</template>
