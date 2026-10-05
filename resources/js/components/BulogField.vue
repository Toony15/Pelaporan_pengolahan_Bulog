<script setup lang="ts">
import { Calendar } from '@lucide/vue';

defineOptions({ inheritAttrs: false });

defineProps<{
    label: string;
    name: string;
    type?: string;
    value?: string;
    error?: string;
}>();
</script>

<template>
    <div>
        <label :for="name" class="mb-1.5 block px-3 text-sm text-[#AF7A1C]">
            {{ label }}
        </label>
        <div class="relative">
            <Calendar
                v-if="type === 'date'"
                class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-[#AF7A1C]"
            />
            <input
                v-bind="$attrs"
                :id="name"
                :name="name"
                :type="type ?? 'text'"
                :value="value"
                :aria-invalid="!!error"
                class="h-14 w-full rounded-xl border border-[#395E90] bg-white px-5 text-lg font-bold text-[#294161] outline-none [color-scheme:light] focus-visible:ring-4 focus-visible:ring-[#395E90]/25 aria-invalid:border-red-600 md:h-[62px] md:text-2xl"
                :class="type === 'date' ? 'pl-12' : ''"
            />
        </div>
        <p v-if="error" class="mt-1.5 px-3 text-sm text-red-600" role="alert">
            {{ error }}
        </p>
    </div>
</template>
