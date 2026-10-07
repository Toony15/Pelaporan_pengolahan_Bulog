<script setup lang="ts">
import { LoaderCircle } from '@lucide/vue';
import { computed } from 'vue';
import { uploadPercent } from '@/lib/uploadProgress';

const percent = computed(() => Math.min(100, Math.max(0, Math.round(uploadPercent.value))));
const saving = computed(() => percent.value >= 100);
</script>

<template>
    <div
        role="status"
        aria-live="polite"
        class="flex w-full overflow-hidden rounded-[10px] border border-[#395E90] bg-white font-[Inter,ui-sans-serif,system-ui,sans-serif] shadow-lg"
    >
        <div class="flex w-11 shrink-0 items-center justify-center bg-[#1F4E8C]">
            <LoaderCircle class="size-6 animate-spin text-white" aria-hidden="true" />
        </div>

        <div class="min-w-0 flex-1 px-4 py-3">
            <div class="flex items-baseline justify-between gap-3">
                <p class="text-lg leading-tight font-bold text-[#1F4E8C]">
                    {{ saving ? 'Menyimpan laporan…' : 'Mengunggah…' }}
                </p>
                <p class="text-lg font-extrabold text-[#0F2038] tabular-nums">{{ percent }}%</p>
            </div>

            <div
                class="mt-2 h-2 overflow-hidden rounded-full bg-[#DCE3ED]"
                role="progressbar"
                aria-label="Kemajuan unggahan"
                aria-valuemin="0"
                aria-valuemax="100"
                :aria-valuenow="percent"
            >
                <div
                    class="h-full rounded-full bg-[#17804F] transition-all duration-200"
                    :style="{ width: `${percent}%` }"
                />
            </div>

            <p class="mt-1.5 text-xs font-bold text-[#395E90]">Jangan tutup halaman ini sampai selesai.</p>
        </div>
    </div>
</template>
