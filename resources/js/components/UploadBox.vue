<script setup lang="ts">
import { Clapperboard, Image as ImageIcon } from '@lucide/vue';
import { computed, onBeforeUnmount, ref } from 'vue';
import { formatUkuran } from '@/lib/format';
import type { AttachmentInfo } from '@/types/work-book';

const props = defineProps<{
    label: string;
    name: string;
    kind: 'video' | 'photo';
    error?: string;
    existing?: AttachmentInfo;
}>();

const picked = ref<File | null>(null);
const preview = ref<string | null>(null);
const accept = props.kind === 'video' ? 'video/*' : 'image/jpeg,image/png,image/webp';

function onChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;

    if (preview.value) {
        URL.revokeObjectURL(preview.value);
    }

    picked.value = file;
    preview.value = file && props.kind === 'photo' ? URL.createObjectURL(file) : null;
}

onBeforeUnmount(() => {
    if (preview.value) {
        URL.revokeObjectURL(preview.value);
    }
});

const imageSrc = computed(
    () => preview.value ?? (props.kind === 'photo' ? (props.existing?.url ?? null) : null),
);
const fileName = computed(() => picked.value?.name ?? props.existing?.name ?? null);
const fileSize = computed(() => picked.value?.size ?? props.existing?.size ?? null);
</script>

<template>
    <div class="mx-auto w-full max-w-[365px]">
        <p class="mb-2 text-center text-sm text-[#AF7A1C]">{{ label }}</p>

        <label
            class="relative flex h-28 cursor-pointer items-center justify-center overflow-hidden rounded-xl border bg-white transition-colors hover:bg-[#395E90]/5 focus-within:ring-4 focus-within:ring-[#395E90]/25"
            :class="error ? 'border-red-600' : 'border-[#395E90]'"
        >
            <input type="file" :name="name" :accept="accept" class="sr-only" @change="onChange" />

            <img
                v-if="imageSrc"
                :src="imageSrc"
                :alt="label"
                class="absolute inset-0 size-full object-cover"
            />
            <div v-else-if="fileName" class="flex max-w-full flex-col items-center gap-1 px-4 text-center">
                <Clapperboard class="size-7 text-[#AF7A1C]" />
                <span class="max-w-full truncate text-sm font-bold text-[#294161]">{{ fileName }}</span>
                <span v-if="fileSize" class="text-xs">{{ formatUkuran(fileSize) }}</span>
            </div>
            <component
                :is="kind === 'video' ? Clapperboard : ImageIcon"
                v-else
                class="size-8 text-[#AF7A1C]"
            />
        </label>

        <p v-if="fileName" class="mt-1.5 truncate text-center text-xs">
            {{ imageSrc ? `${fileName} · ` : '' }}klik kotak untuk mengganti
        </p>
        <p v-if="error" class="mt-1.5 text-center text-sm text-red-600" role="alert">{{ error }}</p>
    </div>
</template>
