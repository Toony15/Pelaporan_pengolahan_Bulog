<script setup lang="ts">
import { CircleCheck, Info, TriangleAlert, X } from '@lucide/vue';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

const props = defineProps<{
    id: string;
    type: FlashToast['type'];
    message: string;
    title?: string;
}>();

// Warna mengikuti desain Figma (hijau = sukses, merah = gagal).
const styles = {
    success: { title: 'Sukses!', strip: '#6EEC0D', text: '#36F14C', icon: CircleCheck },
    error: { title: 'Gagal!', strip: '#EC0D0D', text: '#F13636', icon: X },
    info: { title: 'Info', strip: '#395E90', text: '#395E90', icon: Info },
    warning: { title: 'Perhatian', strip: '#AF7A1C', text: '#AF7A1C', icon: TriangleAlert },
} as const;

const style = computed(() => styles[props.type]);
</script>

<template>
    <div
        :role="type === 'error' ? 'alert' : 'status'"
        class="relative flex min-h-[88px] w-full overflow-hidden rounded-[10px] border border-[#395E90] bg-white font-[Inter,ui-sans-serif,system-ui,sans-serif] shadow-lg"
    >
        <div
            class="flex w-11 shrink-0 items-center justify-center"
            :style="{ backgroundColor: style.strip }"
        >
            <component :is="style.icon" class="size-6 text-white" :stroke-width="2" />
        </div>

        <div class="min-w-0 flex-1 px-4 py-3 pr-10">
            <p class="text-2xl leading-tight font-bold" :style="{ color: style.text }">
                {{ title ?? style.title }}
            </p>
            <p class="mt-1 text-sm font-bold text-[#395E90]">{{ message }}</p>
        </div>

        <button
            v-if="type !== 'success'"
            type="button"
            class="absolute top-2 right-2 rounded p-1 text-[#395E90] outline-none hover:bg-[#395E90]/10 focus-visible:ring-2 focus-visible:ring-[#395E90]/40"
            aria-label="Tutup notifikasi"
            @click="toast.dismiss(id)"
        >
            <X class="size-5" />
        </button>
    </div>
</template>
