<script setup lang="ts">
import { Eye, EyeOff } from '@lucide/vue';
import { computed, onMounted, ref, useTemplateRef } from 'vue';

defineOptions({ inheritAttrs: false });

const props = defineProps<{
    label: string;
    type?: string;
    error?: string;
    autofocus?: boolean;
}>();

const input = useTemplateRef<HTMLInputElement>('input');
const reveal = ref(false);
const isPassword = computed(() => props.type === 'password');
const inputType = computed(() =>
    isPassword.value && reveal.value ? 'text' : (props.type ?? 'text'),
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
                class="h-14 w-full rounded-full border border-[#395E90] bg-white px-6 text-base text-[#294161] outline-none placeholder:text-[#AF7A1C] focus-visible:ring-4 focus-visible:ring-[#395E90]/25 aria-invalid:border-red-600 md:h-[63px]"
                :class="isPassword ? 'pr-14' : ''"
            />
            <button
                v-if="isPassword"
                type="button"
                class="absolute inset-y-0 right-3 flex items-center rounded-full px-3 text-[#395E90] outline-none focus-visible:ring-4 focus-visible:ring-[#395E90]/25"
                :aria-label="reveal ? 'Sembunyikan password' : 'Tampilkan password'"
                @click="reveal = !reveal"
            >
                <EyeOff v-if="reveal" class="size-5" />
                <Eye v-else class="size-5" />
            </button>
        </div>
        <p v-if="error" class="mt-2 px-6 text-sm text-red-600" role="alert">
            {{ error }}
        </p>
    </div>
</template>
