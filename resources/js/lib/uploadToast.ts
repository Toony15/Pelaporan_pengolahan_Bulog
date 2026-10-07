import { router } from '@inertiajs/vue3';
import { markRaw, onBeforeUnmount } from 'vue';
import { toast } from 'vue-sonner';
import UploadProgressToast from '@/components/UploadProgressToast.vue';
import { uploadPercent } from '@/lib/uploadProgress';

const TOAST_ID = 'upload-progress';
const component = markRaw(UploadProgressToast);
let visible = false;

function show(percent: number) {
    uploadPercent.value = percent;

    if (!visible) {
        visible = true;
        toast.custom(component, { id: TOAST_ID, duration: Infinity, dismissible: false });
    }
}

function hide() {
    if (visible) {
        toast.dismiss(TOAST_ID);
        visible = false;
    }

    uploadPercent.value = 0;
}

/**
 * Panggil di setup() komponen form: menampilkan toast persentase
 * selama ada file yang sedang diunggah, dan menutupnya saat selesai.
 */
export function useUploadProgressToast(): void {
    const offProgress = router.on('progress', (event) => {
        const percentage = event.detail.progress?.percentage;

        if (typeof percentage === 'number') {
            show(percentage);
        }
    });
    const offFinish = router.on('finish', hide);

    onBeforeUnmount(() => {
        offProgress();
        offFinish();
        hide();
    });
}
