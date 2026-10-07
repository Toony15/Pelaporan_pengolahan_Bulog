<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft } from '@lucide/vue';
import { dashboard } from '@/routes';

defineProps<{ current: string }>();
</script>

<template>
    <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-[#6B7690]">
        <Link
            :href="dashboard()"
            class="inline-flex items-center gap-1 font-bold text-[#1F4E8C] outline-none hover:underline focus-visible:underline"
        >
            <ChevronLeft class="size-3.5" aria-hidden="true" /> Beranda
        </Link>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ current }}</span>
    </nav>
</template>
