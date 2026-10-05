<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import WorkBookController from '@/actions/App/Http/Controllers/WorkBookController';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { dashboard } from '@/routes';
import { formatTanggal } from '@/lib/format';
import { notify } from '@/lib/notify';
import type { AttachmentCategory, WorkBookDetail } from '@/types/work-book';

const props = defineProps<{
    workBook: WorkBookDetail;
    canManage: boolean;
}>();

const confirmOpen = ref(false);
const deleting = ref(false);

const fields = [
    ['Nama Pic', props.workBook.pic_name],
    ['Nama Mitra pengolahan', props.workBook.mitra_pengolahan],
    ['Kancab', props.workBook.kancab],
    ['Kanwil', props.workBook.kanwil],
    ['Tanggal penyerapan', formatTanggal(props.workBook.absorption_date)],
] as const;

const photos: [AttachmentCategory, string][] = [
    ['foto_gabah', 'Foto Gabah'],
    ['foto_mitra', 'Foto bersama mitra'],
    ['foto_ktp', 'Foto ktp mitra'],
];

function destroy() {
    deleting.value = true;
    router.delete(WorkBookController.destroy.url(props.workBook.id), {
        onError: () => notify('error', 'Data gagal dihapus.'),
        onFinish: () => {
            deleting.value = false;
            confirmOpen.value = false;
        },
    });
}
</script>

<template>
    <Head :title="`Laporan ${workBook.mitra_pengolahan}`" />

    <Link :href="dashboard()" class="mb-4 inline-flex items-center gap-1.5 text-sm font-bold hover:underline">
        <ArrowLeft class="size-4" /> Kembali
    </Link>
    <h1 class="text-center text-lg font-bold text-[#AF7A1C] md:text-xl">Laporan kerja PIC</h1>

    <div class="mx-auto mt-8 max-w-[795px] rounded-[28px] border border-[#395E90] p-6 md:p-12">
        <dl class="flex flex-col gap-5">
            <div v-for="[label, value] in fields" :key="label">
                <dt class="mb-1.5 px-3 text-sm text-[#AF7A1C]">{{ label }}</dt>
                <dd class="rounded-xl border border-[#395E90] px-5 py-3.5 text-lg font-bold text-[#294161] md:text-2xl">
                    {{ value }}
                </dd>
            </div>
        </dl>

        <section class="mt-10">
            <h2 class="mb-2 text-center text-sm text-[#AF7A1C]">Video penyerapan</h2>
            <video
                v-if="workBook.attachments.video"
                :src="workBook.attachments.video.url"
                controls
                preload="metadata"
                class="w-full rounded-xl border border-[#395E90] bg-black"
            />
            <p v-else class="text-center text-sm">Belum diunggah</p>
        </section>

        <section class="mt-8 grid gap-6 sm:grid-cols-3">
            <figure v-for="[key, label] in photos" :key="key">
                <figcaption class="mb-2 text-center text-sm text-[#AF7A1C]">{{ label }}</figcaption>
                <a
                    v-if="workBook.attachments[key]"
                    :href="workBook.attachments[key]!.url"
                    target="_blank"
                    rel="noopener"
                    class="block overflow-hidden rounded-xl border border-[#395E90]"
                    :aria-label="`Buka ${label} ukuran penuh`"
                >
                    <img :src="workBook.attachments[key]!.url" :alt="label" loading="lazy" class="aspect-square w-full object-cover" />
                </a>
                <p v-else class="text-center text-sm">Belum diunggah</p>
            </figure>
        </section>

        <div v-if="canManage" class="mt-10 flex flex-wrap items-center gap-3">
            <Link
                :href="WorkBookController.edit(workBook.id)"
                class="inline-flex h-[46px] items-center gap-2 rounded-xl bg-[#294161] px-6 text-base font-medium text-white outline-none transition-colors hover:bg-[#1f3350] focus-visible:ring-4 focus-visible:ring-[#395E90]/40"
                data-test="edit-workbook"
            >
                <Pencil class="size-4" /> Edit
            </Link>
            <button
                type="button"
                class="inline-flex h-[46px] items-center gap-2 rounded-xl border border-red-600 px-6 text-base font-medium text-red-600 outline-none transition-colors hover:bg-red-50 focus-visible:ring-4 focus-visible:ring-red-600/25"
                data-test="delete-workbook"
                @click="confirmOpen = true"
            >
                <Trash2 class="size-4" /> Hapus
            </button>
        </div>
    </div>

    <ConfirmDialog
        v-model:open="confirmOpen"
        title="Yakin untuk menghapus logbook?"
        :description="`Mitra ${workBook.mitra_pengolahan} beserta foto dan videonya akan dihapus permanen.`"
        :loading="deleting"
        @confirm="destroy"
    />
</template>
