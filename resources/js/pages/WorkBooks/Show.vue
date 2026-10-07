<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import WorkBookController from '@/actions/App/Http/Controllers/WorkBookController';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import PageCrumb from '@/components/PageCrumb.vue';
import { formatAngka, formatTanggal } from '@/lib/format';
import { notify } from '@/lib/notify';
import type { AttachmentCategory, WorkBookDetail } from '@/types/work-book';

const props = defineProps<{
    workBook: WorkBookDetail;
    canManage: boolean;
}>();

const confirmOpen = ref(false);
const deleting = ref(false);

const rows = computed(() => [
    ['Nama PIC', props.workBook.pic_name],
    ['Nama mitra pengolahan', props.workBook.mitra_pengolahan],
    ['Desa / Kelurahan', props.workBook.village || '-'],
    ['Kota / Kabupaten', props.workBook.regency || '-'],
    [
        'Jumlah penyerapan',
        `${formatAngka(props.workBook.absorption_kg, 2)} kg (${formatAngka(props.workBook.absorption_kg / 1000, 3)} ton)`,
    ],
    ['Tanggal penyerapan', formatTanggal(props.workBook.absorption_date)],
]);

const photos: [AttachmentCategory, string][] = [
    ['foto_gabah', 'Foto gabah'],
    ['foto_mitra', 'Foto bersama mitra'],
    ['foto_ktp', 'Foto KTP mitra'],
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

    <PageCrumb current="Detail laporan" />

    <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <h1 class="truncate text-3xl font-extrabold tracking-tight sm:text-4xl">
                {{ workBook.mitra_pengolahan }}
            </h1>
            <p class="mt-1 text-sm text-[#6B7690]">
                {{ formatTanggal(workBook.absorption_date) }} · PIC {{ workBook.pic_name }}
            </p>
        </div>

        <div v-if="canManage" class="flex items-center gap-3">
            <Link
                :href="WorkBookController.edit(workBook.id)"
                class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#F5A623] px-5 text-sm font-bold text-[#0F2038] shadow-md outline-none transition-colors hover:bg-[#E89A18] focus-visible:ring-4 focus-visible:ring-[#F5A623]/40"
                data-test="edit-workbook"
            >
                <Pencil class="size-4" /> Edit
            </Link>
            <button
                type="button"
                class="inline-flex h-11 items-center gap-2 rounded-xl border border-red-200 bg-white px-5 text-sm font-bold text-red-600 outline-none transition-colors hover:bg-red-50 focus-visible:ring-4 focus-visible:ring-red-600/20"
                data-test="delete-workbook"
                @click="confirmOpen = true"
            >
                <Trash2 class="size-4" /> Hapus
            </button>
        </div>
    </div>

    <div class="mt-6 grid items-start gap-5 lg:grid-cols-[1.25fr_1fr]">
        <section class="rounded-2xl border border-[#E0E6EF] bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold">Data penyerapan</h2>
            <dl class="mt-3 divide-y divide-[#EEF1F6]">
                <div v-for="[label, value] in rows" :key="label" class="grid gap-1 py-3 sm:grid-cols-[170px_1fr]">
                    <dt class="text-sm text-[#6B7690]">{{ label }}</dt>
                    <dd class="text-sm font-bold break-words">{{ value }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-[#E0E6EF] bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold">Dokumentasi penyerapan</h2>

            <div class="mt-4">
                <p class="mb-2 text-sm font-bold">Video penyerapan</p>
                <video
                    v-if="workBook.attachments.video"
                    :src="workBook.attachments.video.url"
                    controls
                    preload="metadata"
                    class="w-full rounded-xl border border-[#E0E6EF] bg-black"
                />
                <p v-else class="rounded-xl border border-dashed border-[#C9D3E3] bg-[#F5F8FC] p-4 text-center text-sm text-[#6B7690]">
                    Belum ada video
                </p>
            </div>

            <div class="mt-5 grid grid-cols-3 gap-3">
                <figure v-for="[key, label] in photos" :key="key">
                    <a
                        v-if="workBook.attachments[key]"
                        :href="workBook.attachments[key]!.url"
                        target="_blank"
                        rel="noopener"
                        class="block overflow-hidden rounded-xl border border-[#E0E6EF] outline-none focus-visible:ring-4 focus-visible:ring-[#1F4E8C]/20"
                        :aria-label="`Buka ${label} ukuran penuh`"
                    >
                        <img
                            :src="workBook.attachments[key]!.url"
                            :alt="label"
                            loading="lazy"
                            class="aspect-square w-full object-cover"
                        />
                    </a>
                    <div
                        v-else
                        class="flex aspect-square items-center justify-center rounded-xl border border-dashed border-[#C9D3E3] bg-[#F5F8FC] p-2 text-center text-xs text-[#6B7690]"
                    >
                        Belum ada
                    </div>
                    <figcaption class="mt-1.5 text-center text-xs text-[#6B7690]">{{ label }}</figcaption>
                </figure>
            </div>
        </section>
    </div>

    <ConfirmDialog
        v-model:open="confirmOpen"
        title="Yakin untuk menghapus logbook?"
        :description="`Mitra ${workBook.mitra_pengolahan} beserta foto dan videonya akan dihapus permanen.`"
        :loading="deleting"
        @confirm="destroy"
    />
</template>
