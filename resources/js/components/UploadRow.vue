<script setup lang="ts">
import type { Component } from 'vue';
import { computed, onBeforeUnmount, ref } from 'vue';
import { formatUkuran } from '@/lib/format';
import type { AttachmentInfo } from '@/types/work-book';

const props = defineProps<{
    label: string;
    name: string;
    kind: 'video' | 'photo';
    icon: Component;
    error?: string;
    existing?: AttachmentInfo;
}>();

const emit = defineEmits<{ change: [hasFile: boolean] }>();

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
    emit('change', !!file || !!props.existing);
}

onBeforeUnmount(() => {
    if (preview.value) {
        URL.revokeObjectURL(preview.value);
    }
});

const state = computed<'empty' | 'picked' | 'saved'>(() =>
    picked.value ? 'picked' : props.existing ? 'saved' : 'empty',
);
const thumb = computed(
    () => preview.value ?? (props.kind === 'photo' ? (props.existing?.url ?? null) : null),
);
const subtitle = computed(() => {
    const file = picked.value ?? props.existing;
    const name = picked.value?.name ?? props.existing?.name;

    return file && name ? `${name} · ${formatUkuran(file.size)}` : 'Belum ada file';
});

const badges = {
    empty: { text: 'Kosong', class: 'bg-[#DCE3ED] text-[#4A5570]' },
    picked: { text: 'Terunggah', class: 'bg-[#17804F] text-white' },
    saved: { text: 'Tersimpan', class: 'bg-[#17804F] text-white' },
} as const;
</script>

<template>
    <div>
        <label
            class="flex cursor-pointer items-center gap-3 rounded-xl border p-3 transition-colors focus-within:ring-4 focus-within:ring-[#294161]/15"
            :class="[
                state === 'empty' ? 'border-dashed bg-[#F5F8FC] hover:bg-[#EDF2F9]' : 'bg-[#E1F4EA] hover:bg-[#D6EFE2]',
                error ? 'border-red-500' : state === 'empty' ? 'border-[#C9D3E3]' : 'border-[#95C8B0]',
            ]"
        >
            <input type="file" :name="name" :accept="accept" class="sr-only" @change="onChange" />

            <span
                class="flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-[#E0E6EF] bg-white text-[#294161]"
            >
                <img v-if="thumb" :src="thumb" alt="" class="size-full object-cover" />
                <component :is="icon" v-else class="size-5" aria-hidden="true" />
            </span>

            <span class="min-w-0 flex-1">
                <span class="block text-sm font-bold text-[#0F2038]">{{ label }}</span>
                <span class="block truncate text-xs text-[#6B7690]" :title="subtitle">{{ subtitle }}</span>
            </span>

            <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold" :class="badges[state].class">
                {{ badges[state].text }}
            </span>
        </label>
        <p v-if="error" class="mt-2 px-1 text-sm text-red-600" role="alert">{{ error }}</p>
    </div>
</template>
