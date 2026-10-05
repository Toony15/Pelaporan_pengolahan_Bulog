<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Clapperboard, Image as ImageIcon, Plus, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import WorkBookController from '@/actions/App/Http/Controllers/WorkBookController';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { formatTanggal } from '@/lib/format';
import { notify } from '@/lib/notify';
import type { WorkBookCard } from '@/types/work-book';

const props = defineProps<{
    workBooks: WorkBookCard[];
    canCreate: boolean;
}>();

const page = usePage();
const userName = computed(() => page.props.auth.user.name);

const stats = computed(() => [
    { label: 'Laporan', value: props.workBooks.length },
    { label: 'Video', value: props.workBooks.reduce((n, wb) => n + wb.video_count, 0) },
    { label: 'Foto', value: props.workBooks.reduce((n, wb) => n + wb.photo_count, 0) },
]);

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

    <header class="text-center text-[#294161]">
        <h1 class="text-3xl font-bold md:text-4xl">Selamat Datang {{ userName }}</h1>
        <p class="mt-1 text-lg font-bold md:text-xl">
            {{ canCreate ? 'PIC Pengolahan Bulog' : 'Pantau Laporan PIC Pengolahan Bulog' }}
        </p>
    </header>

    <dl class="mt-8 grid grid-cols-3 gap-0.5 overflow-hidden rounded-md">
        <div
            v-for="stat in stats"
            :key="stat.label"
            class="flex flex-col-reverse items-center justify-center bg-[#2157A1] px-2 py-4 text-white"
        >
            <dt class="text-sm font-bold md:text-base">{{ stat.label }}</dt>
            <dd class="text-3xl leading-tight font-bold md:text-4xl">{{ stat.value }}</dd>
        </div>
    </dl>

    <Link
        v-if="canCreate"
        :href="WorkBookController.create()"
        class="mt-6 inline-flex h-[49px] items-center gap-4 rounded-[10px] border border-[#395475] bg-[#FFBF4C] pr-8 pl-3 text-[15px] font-bold text-white outline-none transition-colors hover:bg-[#F5B13A] focus-visible:ring-4 focus-visible:ring-[#395E90]/40"
        data-test="create-workbook"
    >
        <span class="flex size-7 items-center justify-center rounded-full bg-white text-[#FFBF4C]">
            <Plus class="size-5" :stroke-width="3" />
        </span>
        Buat laporan kerja PIC
    </Link>

    <section v-if="workBooks.length" class="mt-10">
        <h2 class="mb-4 text-lg font-bold text-[#E99E18]">
            {{ workBooks.length }} Laporan Kerja terdeteksi
        </h2>

        <div class="grid gap-8 rounded-[28px] border border-[#395E90] p-6 sm:grid-cols-2 md:p-10 lg:grid-cols-3">
            <div v-for="wb in workBooks" :key="wb.id" class="relative flex">
            <Link
                :href="WorkBookController.show(wb.id)"
                class="flex w-full flex-col overflow-hidden rounded-[22px] border border-[#AF7A1C] bg-white outline-none transition-shadow hover:shadow-lg focus-visible:ring-4 focus-visible:ring-[#AF7A1C]/30"
            >
                <div class="h-40 bg-[#AF7A1C]/5">
                    <img
                        v-if="wb.cover_url"
                        :src="wb.cover_url"
                        :alt="`Foto bersama ${wb.mitra_pengolahan}`"
                        loading="lazy"
                        class="size-full object-cover"
                    />
                    <span
                        v-else
                        class="flex size-full items-center justify-center text-xs font-bold text-[#AF7A1C]"
                    >
                        Gambar foto bersama mitra
                    </span>
                </div>

                <div
                    class="relative -mt-5 flex flex-1 flex-col rounded-t-[22px] border-t border-[#AF7A1C] bg-white px-5 pt-5 pb-4"
                >
                    <p class="truncate text-lg font-bold text-[#395E90]">{{ wb.mitra_pengolahan }}</p>
                    <p class="mt-1 text-base font-bold text-[#395E90]">{{ formatTanggal(wb.absorption_date) }}</p>
                    <p v-if="!canCreate" class="mt-1 truncate text-sm text-[#AF7A1C]">PIC: {{ wb.pic_name }}</p>

                    <div class="mt-auto flex items-center gap-5 pt-6 text-xs font-bold text-[#294161]">
                        <span class="inline-flex items-center gap-1.5">
                            {{ wb.video_count }} video <Clapperboard class="size-4" />
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            {{ wb.photo_count }} Foto <ImageIcon class="size-4" />
                        </span>
                    </div>
                </div>
            </Link>
            <button
                v-if="canCreate"
                type="button"
                class="absolute top-3 right-3 z-10 flex size-9 items-center justify-center rounded-full bg-white/95 text-[#EC0D0D] shadow outline-none transition-colors hover:bg-red-50 focus-visible:ring-4 focus-visible:ring-red-600/25"
                :aria-label="`Hapus laporan ${wb.mitra_pengolahan}`"
                data-test="delete-card"
                @click="askDelete(wb)"
            >
                <Trash2 class="size-4" />
            </button>
            </div>
        </div>
    </section>

    <section
        v-else
        class="mt-12 flex min-h-[313px] items-center justify-center rounded-[22px] border border-[#395E90] p-8"
    >
        <p class="text-center text-2xl font-bold text-[#E99E18] md:text-3xl">Belum ada laporan kerja</p>
    </section>

    <ConfirmDialog
        v-model:open="confirmOpen"
        title="Yakin untuk menghapus logbook?"
        :description="target ? `Mitra ${target.mitra_pengolahan} beserta foto dan videonya akan dihapus permanen.` : undefined"
        :loading="deleting"
        @confirm="destroy"
    />
</template>
