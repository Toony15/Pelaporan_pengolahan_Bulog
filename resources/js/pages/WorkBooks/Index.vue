<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Calendar,
    EllipsisVertical,
    Eye,
    FileText,
    Image as ImageIcon,
    MapPin,
    Pencil,
    Plus,
    Scale,
    Trash2,
    Video,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import WorkBookController from '@/actions/App/Http/Controllers/WorkBookController';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { formatAngka, formatTanggal } from '@/lib/format';
import { notify } from '@/lib/notify';
import type { WorkBookCard } from '@/types/work-book';

const props = defineProps<{
    workBooks: WorkBookCard[];
    canCreate: boolean;
}>();

const page = usePage();
const userName = computed(() => page.props.auth.user.name);

const sum = (pick: (wb: WorkBookCard) => number) =>
    props.workBooks.reduce((total, wb) => total + pick(wb), 0);

const stats = computed(() => [
    { label: 'Laporan', value: props.workBooks.length, icon: FileText, tile: 'bg-[#E6EEF9] text-[#3E5C94]' },
    { label: 'Video', value: sum((wb) => wb.video_count), icon: Video, tile: 'bg-[#FFF3DB] text-[#D9822B]' },
    { label: 'Foto', value: sum((wb) => wb.photo_count), icon: ImageIcon, tile: 'bg-[#E1F4EA] text-[#2F8F5B]' },
]);

const lokasi = (wb: WorkBookCard) => [wb.village, wb.regency].filter(Boolean).join(', ') || '-';
const jumlah = (kg: number) => `${formatAngka(kg, 2)} kg (${formatAngka(kg / 1000, 3)} ton)`;

const target = ref<WorkBookCard | null>(null);
const confirmOpen = ref(false);
const deleting = ref(false);

function askDelete(workBook: WorkBookCard) {
    target.value = workBook;
    confirmOpen.value = true;
}

function destroy() {
    if (!target.value) {
        return;
    }

    deleting.value = true;
    router.delete(WorkBookController.destroy.url(target.value.id), {
        preserveScroll: true,
        onError: () => notify('error', 'Data gagal dihapus.'),
        onFinish: () => {
            deleting.value = false;
            confirmOpen.value = false;
        },
    });
}
</script>

<template>
    <Head title="Beranda" />

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <h1 class="truncate text-3xl font-extrabold tracking-tight sm:text-4xl">Halo, {{ userName }}</h1>
            <p class="mt-1 text-sm text-[#6B7690]">
                {{ canCreate ? 'PIC Pengolahan Bulog' : 'Pantau laporan PIC Pengolahan Bulog' }}
            </p>
        </div>
    </div>

    <dl class="mt-6 grid grid-cols-3 gap-2 sm:gap-4">
        <div
            v-for="stat in stats"
            :key="stat.label"
            class="flex flex-col gap-3 rounded-2xl border border-[#E5E9F0] bg-white p-3 shadow-sm sm:flex-row sm:items-center sm:gap-3 sm:p-4"
        >
            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl" :class="stat.tile">
                <component :is="stat.icon" class="size-5" aria-hidden="true" />
            </span>
            <div class="flex flex-col-reverse">
                <dt class="text-xs text-[#6B7690] sm:text-sm">{{ stat.label }}</dt>
                <dd class="text-2xl leading-tight font-extrabold sm:text-3xl">{{ stat.value }}</dd>
            </div>
        </div>
    </dl>

    <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-bold">{{ workBooks.length }} laporan kerja</h2>

        <Link
            v-if="canCreate"
            :href="WorkBookController.create()"
            class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#F5A623] px-5 text-sm font-bold text-[#0F2038] shadow-md outline-none transition-colors hover:bg-[#E89A18] focus-visible:ring-4 focus-visible:ring-[#F5A623]/40"
            data-test="create-workbook"
        >
            <Plus class="size-4" :stroke-width="2.5" /> Buat laporan kerja
        </Link>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="wb in workBooks" :key="wb.id" class="relative">
            <Link
                :href="WorkBookController.show(wb.id)"
                class="flex h-full flex-col overflow-hidden rounded-2xl border border-[#E5E9F0] bg-white shadow-sm outline-none transition-shadow hover:shadow-md focus-visible:ring-4 focus-visible:ring-[#1F4E8C]/20"
            >
                <div class="relative h-32 shrink-0 bg-[#E6EEF9]">
                    <img
                        v-if="wb.cover_url"
                        :src="wb.cover_url"
                        :alt="`Foto bersama ${wb.mitra_pengolahan}`"
                        loading="lazy"
                        class="size-full object-cover"
                    />
                    <span v-else class="flex size-full items-center justify-center text-[#9AA3B5]">
                        <ImageIcon class="size-8" aria-hidden="true" />
                    </span>
                    <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-[#0F2038]/70 to-transparent" />
                    <span
                        class="absolute bottom-2.5 left-3 inline-flex items-center gap-1.5 text-xs font-semibold text-white"
                    >
                        <Calendar class="size-3.5" aria-hidden="true" />
                        {{ formatTanggal(wb.absorption_date) }}
                    </span>
                </div>

                <div class="flex flex-1 flex-col gap-2 p-4">
                    <h3 class="truncate text-lg font-bold">{{ wb.mitra_pengolahan }}</h3>
                    <p class="flex items-center gap-2 text-sm text-[#5B6784]">
                        <MapPin class="size-4 shrink-0" aria-hidden="true" />
                        <span class="truncate">{{ lokasi(wb) }}</span>
                    </p>
                    <p class="flex items-center gap-2 text-sm text-[#5B6784]">
                        <Scale class="size-4 shrink-0" aria-hidden="true" />
                        {{ jumlah(wb.absorption_kg) }}
                    </p>
                    <p v-if="!canCreate" class="truncate text-xs text-[#6B7690]">PIC: {{ wb.pic_name }}</p>

                    <div class="mt-auto flex flex-wrap gap-2 pt-1">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3DB] px-2.5 py-1 text-xs font-semibold text-[#B7731A]"
                        >
                            <Video class="size-3.5" aria-hidden="true" /> {{ wb.video_count }} video
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-[#E6EEF9] px-2.5 py-1 text-xs font-semibold text-[#1F4E8C]"
                        >
                            <ImageIcon class="size-3.5" aria-hidden="true" /> {{ wb.photo_count }} foto
                        </span>
                    </div>
                </div>
            </Link>

            <DropdownMenu :modal="false">
                <DropdownMenuTrigger as-child>
                    <button
                        type="button"
                        class="absolute top-3 right-3 z-10 flex size-8 items-center justify-center rounded-full bg-white/95 text-[#0F2038] shadow outline-none transition-colors hover:bg-white focus-visible:ring-4 focus-visible:ring-[#1F4E8C]/30"
                        :aria-label="`Menu laporan ${wb.mitra_pengolahan}`"
                        data-test="card-menu"
                    >
                        <EllipsisVertical class="size-4" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-44">
                    <DropdownMenuItem as-child>
                        <Link :href="WorkBookController.show(wb.id)"><Eye /> Lihat detail</Link>
                    </DropdownMenuItem>
                    <template v-if="canCreate">
                        <DropdownMenuItem as-child>
                            <Link :href="WorkBookController.edit(wb.id)"><Pencil /> Edit</Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem variant="destructive" data-test="card-delete" @select="askDelete(wb)">
                            <Trash2 /> Hapus
                        </DropdownMenuItem>
                    </template>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>

        <Link
            v-if="canCreate"
            :href="WorkBookController.create()"
            class="flex min-h-[260px] flex-col items-center justify-center gap-1 rounded-2xl border-2 border-dashed border-[#BAC6D8] p-6 text-center outline-none transition-colors hover:border-[#F5A623] hover:bg-[#FFF3DB] focus-visible:border-[#F5A623] focus-visible:bg-[#FFF3DB] focus-visible:ring-4 focus-visible:ring-[#F5A623]/30"
            data-test="create-card"
        >
            <span class="mb-3 flex size-11 items-center justify-center rounded-full bg-[#F5A623] text-[#0F2038]">
                <Plus class="size-5" :stroke-width="2.5" />
            </span>
            <span class="text-sm font-bold text-[#1F4E8C]">Buat laporan baru</span>
            <span class="text-xs text-[#6B7690]">Catat penyerapan hari ini</span>
        </Link>
    </div>

    <p
        v-if="!canCreate && !workBooks.length"
        class="mt-4 rounded-2xl border border-dashed border-[#BAC6D8] p-10 text-center text-sm text-[#6B7690]"
    >
        Belum ada laporan kerja.
    </p>

    <ConfirmDialog
        v-model:open="confirmOpen"
        title="Yakin untuk menghapus logbook?"
        :description="target ? `Mitra ${target.mitra_pengolahan} beserta foto dan videonya akan dihapus permanen.` : undefined"
        :loading="deleting"
        @confirm="destroy"
    />
</template>
