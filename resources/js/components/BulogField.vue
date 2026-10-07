<script setup lang="ts">
import type { Component } from 'vue';
import { computed, useId } from 'vue';

defineOptions({ inheritAttrs: false });

const props = defineProps<{
    label: string;
    name?: string;
    type?: string;
    value?: string;
    error?: string;
    hint?: string;
    icon?: Component;
    /** Satuan di ujung kanan, misalnya "Kg". */
    suffix?: string;
    required?: boolean;
    readonly?: boolean;
}>();

const uid = useId();
const id = computed(() => props.name ?? uid);
const noteId = computed(() => `${id.value}-note`);
</script>

<template>
    <div>
        <label :for="id" class="mb-2 block text-sm font-bold text-[#0F2038]">
            {{ label }}<span v-if="required" class="text-red-600" aria-hidden="true"> *</span>
        </label>

        <div class="relative">
            <component
                :is="icon"
                v-if="icon"
                class="pointer-events-none absolute top-1/2 left-3.5 size-[18px] -translate-y-1/2 text-[#5B6784]"
                aria-hidden="true"
            />
            <input
                v-bind="$attrs"
                :id="id"
                :name="name"
                :type="type ?? 'text'"
                :value="value"
                :required="required"
                :readonly="readonly"
                :aria-invalid="!!error"
                :aria-describedby="hint || error ? noteId : undefined"
                class="h-12 w-full rounded-xl border bg-white text-base text-[#0F2038] outline-none transition-colors [color-scheme:light] placeholder:text-[#9AA3B5] read-only:cursor-default focus-visible:ring-4"
                :class="[
                    icon ? 'pl-11' : 'pl-4',
                    suffix ? 'pr-14' : 'pr-4',
                    error
                        ? 'border-red-500 focus-visible:ring-red-500/15'
                        : 'border-[#E0E6EF] focus-visible:border-[#294161] focus-visible:ring-[#294161]/15',
                ]"
            />
            <span
                v-if="suffix"
                class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-sm font-bold text-[#5B6784]"
            >
                {{ suffix }}
            </span>
        </div>

        <p v-if="hint" :id="noteId" class="mt-2 text-xs text-[#6B7690]">{{ hint }}</p>
        <p v-if="error" :id="hint ? undefined : noteId" class="mt-2 text-sm text-red-600" role="alert">
            {{ error }}
        </p>
    </div>
</template>
