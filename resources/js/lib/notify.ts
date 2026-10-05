import { markRaw } from 'vue';
import { toast } from 'vue-sonner';
import BulogToast from '@/components/BulogToast.vue';
import type { FlashToast } from '@/types/ui';

const component = markRaw(BulogToast);

/** Menampilkan toast bergaya Bulog (sukses / gagal / info / perhatian). */
export function notify(type: FlashToast['type'], message: string, title?: string): void {
    const id = `${Date.now()}-${Math.random().toString(36).slice(2)}`;

    toast.custom(component, {
        id,
        componentProps: { id, type, message, title },
        duration: type === 'error' ? 8000 : 4500,
    });
}
