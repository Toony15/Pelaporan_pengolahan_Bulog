<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';

defineProps<{
    title: string;
    description?: string;
    loading?: boolean;
}>();

const open = defineModel<boolean>('open', { default: false });
const emit = defineEmits<{ confirm: [] }>();
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            :show-close-button="false"
            class="gap-3 rounded-[10px] border-[#395E90] bg-white p-6 text-center font-[Inter,ui-sans-serif,system-ui,sans-serif] text-[#395E90] sm:max-w-[447px]"
        >
            <DialogTitle class="text-center text-xl font-bold text-[#395E90]">
                {{ title }}
            </DialogTitle>
            <DialogDescription class="text-center text-sm font-bold text-[#395E90]/80">
                {{ description ?? 'Tindakan ini tidak dapat dibatalkan.' }}
            </DialogDescription>

            <div class="mt-2 flex justify-center gap-10">
                <button
                    type="button"
                    :disabled="loading"
                    class="h-9 min-w-[72px] rounded-lg border border-[#294161] bg-[#395E90] px-4 text-sm font-bold text-white outline-none transition-colors hover:bg-[#2f4f7c] focus-visible:ring-4 focus-visible:ring-[#395E90]/40 disabled:opacity-60"
                    data-test="confirm-no"
                    @click="open = false"
                >
                    Tidak
                </button>
                <button
                    type="button"
                    :disabled="loading"
                    class="inline-flex h-9 min-w-[72px] items-center justify-center gap-2 rounded-lg bg-[#EC0D0D] px-4 text-sm font-bold text-white outline-none transition-colors hover:bg-[#c90b0b] focus-visible:ring-4 focus-visible:ring-red-600/30 disabled:opacity-60"
                    data-test="confirm-yes"
                    @click="emit('confirm')"
                >
                    <Spinner v-if="loading" />
                    Ya
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>
