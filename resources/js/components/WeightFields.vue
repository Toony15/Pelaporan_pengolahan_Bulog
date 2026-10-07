<script setup lang="ts">
import { Scale } from '@lucide/vue';
import { computed, ref } from 'vue';
import BulogField from '@/components/BulogField.vue';
import { formatAngka, parseAngka } from '@/lib/format';

const props = defineProps<{
    /** Jumlah penyerapan dalam Kg (nilai yang tersimpan). */
    value?: number | null;
    error?: string;
}>();

// Kg diisi PIC; Ton ("Setara") selalu dihitung dari Kg (1 Ton = 1.000 Kg).
const kg = ref<number | null>(props.value && props.value > 0 ? props.value : null);
const kgText = ref(kg.value === null ? '' : formatAngka(kg.value, 2));
const tonText = computed(() => (kg.value === null ? '' : formatAngka(kg.value / 1000, 3)));

function onKg(event: Event) {
    kgText.value = (event.target as HTMLInputElement).value;
    kg.value = parseAngka(kgText.value);
}

function tidyKg() {
    if (kg.value !== null) {
        kgText.value = formatAngka(kg.value, 2);
    }
}
</script>

<template>
    <div class="grid items-start gap-4 sm:grid-cols-2">
        <BulogField
            label="Jumlah penyerapan"
            :icon="Scale"
            suffix="Kg"
            :value="kgText"
            :error="error"
            required
            inputmode="decimal"
            autocomplete="off"
            placeholder="0"
            @input="onKg"
            @blur="tidyKg"
        />
        <BulogField
            label="Setara"
            :icon="Scale"
            suffix="Ton"
            :value="tonText"
            hint="Terhitung otomatis dari Kg."
            readonly
            tabindex="-1"
            placeholder="0"
        />
        <!-- Yang dikirim ke server hanya Kg -->
        <input type="hidden" name="absorption_kg" :value="kg ?? ''" />
    </div>
</template>
