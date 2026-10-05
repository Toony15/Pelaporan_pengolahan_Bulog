<script setup lang="ts">
import { Eye, EyeOff } from '@lucide/vue';
import { computed, onMounted, ref, useTemplateRef } from 'vue';

defineOptions({ inheritAttrs: false });

const props = defineProps<{
    label: string;
    type?: string;
    error?: string;
    autofocus?: boolean;
    /** Tampilkan tombol mata untuk melihat password. */
    revealable?: boolean;
}>();

const input = useTemplateRef<HTMLInputElement>('input');
const reveal = ref(false);
const inputType = computed(() =>
    props.revealable && reveal.value ? 'text' : (props.type ?? 'text'),
);

onMounted(() => {
    if (props.autofocus) {
        input.value?.focus();
    }
});
</script>

<template>
    <div>
        <div class="relative">
            <input
                v-bind="$attrs"
                ref="input"
                :type="inputType"
                :placeholder="label"
                :aria-label="label"
                :aria-invalid="!!error"
                class="h-[61px] w-full rounded-xl border-0 bg-white px-5 text-base text-[#294161] outline-none placeholder:text-[#AF7A1C] focus-visible:ring-4 focus-visible:ring-[#F1B444]/70 aria-invalid:ring-2 aria-invalid:ring-[#FF8A8A]"
                :class="revealable ? 'pr-14' : ''"
            />
            <button
                v-if="revealable"
                type="button"
                class="absolute inset-y-0 right-3 flex items-center rounded-full px-3 text-black outline-none focus-visible:ring-4 focus-visible:ring-[#F1B444]/70"
                :aria-label="reveal ? 'Sembunyikan password' : 'Tampilkan password'"
                @click="reveal = !reveal"
            >
                <EyeOff v-if="reveal" class="size-6" />
                <Eye v-else class="size-6" />
            </button>
        </div>
        <p v-if="error" class="mt-1.5 px-1 text-sm font-bold text-[#FFC9C9]" role="alert">
            {{ error }}
        </p>
    </div>
</template>
