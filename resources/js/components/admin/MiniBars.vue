<script setup lang="ts">
import { computed } from 'vue';
import { formatTon } from '@/lib/format';

const props = defineProps<{
    /** Total penyerapan per bulan (ton), Januari sampai bulan berjalan. */
    values: number[];
    labels: string[];
}>();

const max = computed(() => Math.max(...props.values, 0));
const height = (value: number) => (max.value > 0 ? Math.max(8, (value / max.value) * 100) : 8);
</script>

<template>
    <div
        class="flex h-24 items-end gap-1.5"
        role="img"
        aria-label="Grafik batang total penyerapan per bulan"
    >
        <div
            v-for="(value, index) in values"
            :key="index"
            class="flex-1 rounded-t-[3px] transition-opacity hover:opacity-100"
            :class="index === values.length - 1 ? 'bg-[#F5A623]' : 'bg-[#F5A623]/50'"
            :style="{ height: `${height(value)}%` }"
            :title="`${labels[index]}: ${formatTon(value)} ton`"
        />
    </div>
</template>
