<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { Calendar, IdCard, Image as ImageIcon, MapPin, Store, User, Video } from '@lucide/vue';
import { computed, reactive } from 'vue';
import BulogField from '@/components/BulogField.vue';
import { Spinner } from '@/components/ui/spinner';
import UploadRow from '@/components/UploadRow.vue';
import WeightFields from '@/components/WeightFields.vue';
import { hariIni } from '@/lib/format';
import { notify } from '@/lib/notify';
import { useUploadProgressToast } from '@/lib/uploadToast';
import type { AttachmentCategory, WorkBookDetail } from '@/types/work-book';

const props = defineProps<{
    // hasil WorkBookController.store.form() / update.form(id)
    form: { action: string; method: 'get' | 'post' | 'put' | 'patch' | 'delete' };
    cancelHref: string | { url: string };
    workBook?: WorkBookDetail;
    defaultPicName?: string;
    submitLabel?: string;
    failMessage?: string;
}>();

// Toast persentase muncul otomatis selama file diunggah.
useUploadProgressToast();

const slots = [
    { key: 'video', label: 'Video penyerapan', kind: 'video', icon: Video },
    { key: 'foto_gabah', label: 'Foto gabah', kind: 'photo', icon: ImageIcon },
    { key: 'foto_mitra', label: 'Foto bersama mitra', kind: 'photo', icon: ImageIcon },
    { key: 'foto_ktp', label: 'Foto KTP mitra', kind: 'photo', icon: IdCard },
] as const;

// Melacak kotak dokumentasi mana yang sudah terisi (file baru atau yang sudah tersimpan).
const filled = reactive<Record<AttachmentCategory, boolean>>({
    video: !!props.workBook?.attachments.video,
    foto_gabah: !!props.workBook?.attachments.foto_gabah,
    foto_mitra: !!props.workBook?.attachments.foto_mitra,
    foto_ktp: !!props.workBook?.attachments.foto_ktp,
});
const filledCount = computed(() => Object.values(filled).filter(Boolean).length);
</script>

<template>
    <Form
        v-bind="form"
        v-slot="{ errors, processing }"
        @error="notify('error', failMessage ?? 'Data gagal disimpan. Periksa kembali isian Anda.')"
    >
        <div class="grid items-start gap-5 lg:grid-cols-[1.25fr_1fr]">
            <!-- Data penyerapan -->
            <section class="rounded-2xl border border-[#E0E6EF] bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-[#0F2038]">Data penyerapan</h2>
                <p class="mt-1 text-sm text-[#6B7690]">
                    Semua kolom bertanda <span class="text-red-600">*</span> wajib diisi.
                </p>

                <div class="mt-5 flex flex-col gap-5">
                    <BulogField
                        label="Nama PIC"
                        name="pic_name"
                        :icon="User"
                        :value="workBook?.pic_name ?? defaultPicName"
                        :error="errors.pic_name"
                        autocomplete="name"
                        readonly
                    />
                    <BulogField
                        label="Nama mitra pengolahan"
                        name="mitra_pengolahan"
                        :icon="Store"
                        :value="workBook?.mitra_pengolahan"
                        :error="errors.mitra_pengolahan"
                        placeholder="Contoh: UD Tani Makmur"
                        autocomplete="off"
                        required
                    />
                    <div class="grid gap-5 sm:grid-cols-2">
                        <BulogField
                            label="Desa / Kelurahan"
                            name="village"
                            :icon="MapPin"
                            :value="workBook?.village"
                            :error="errors.village"
                            placeholder="Nama desa"
                            autocomplete="off"
                            required
                        />
                        <BulogField
                            label="Kota / Kabupaten"
                            name="regency"
                            :icon="MapPin"
                            :value="workBook?.regency"
                            :error="errors.regency"
                            placeholder="Nama kabupaten"
                            autocomplete="off"
                            required
                        />
                    </div>
                    <WeightFields :value="workBook?.absorption_kg" :error="errors.absorption_kg" />
                    <BulogField
                        label="Tanggal penyerapan"
                        name="absorption_date"
                        type="date"
                        :icon="Calendar"
                        :value="workBook?.absorption_date ?? hariIni()"
                        :error="errors.absorption_date"
                        required
                    />
                </div>
            </section>

            <!-- Dokumentasi penyerapan -->
            <section class="rounded-2xl border border-[#E0E6EF] bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-[#0F2038]">Dokumentasi penyerapan</h2>
                <p class="mt-1 text-sm text-[#6B7690]">
                    Ketuk untuk mengambil foto atau memilih file.
                    <template v-if="workBook">Pilih file baru hanya jika ingin mengganti yang lama.</template>
                </p>

                <div class="mt-5 flex items-center gap-4">
                    <div
                        class="h-2 flex-1 overflow-hidden rounded-full bg-[#DCE3ED]"
                        role="progressbar"
                        aria-label="Kelengkapan dokumentasi"
                        aria-valuemin="0"
                        aria-valuemax="4"
                        :aria-valuenow="filledCount"
                    >
                        <div
                            class="h-full rounded-full bg-[#17804F] transition-all duration-300"
                            :style="{ width: `${(filledCount / 4) * 100}%` }"
                        />
                    </div>
                    <p class="shrink-0 text-xs font-bold text-[#0F2038]">{{ filledCount }} dari 4 terunggah</p>
                </div>

                <div class="mt-4 flex flex-col gap-3">
                    <UploadRow
                        v-for="slot in slots"
                        :key="slot.key"
                        :label="slot.label"
                        :name="slot.key"
                        :kind="slot.kind"
                        :icon="slot.icon"
                        :existing="workBook?.attachments[slot.key]"
                        :error="errors[slot.key]"
                        @change="filled[slot.key] = $event"
                    />
                </div>
            </section>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button
                type="submit"
                :disabled="processing"
                class="inline-flex h-12 min-w-[160px] items-center justify-center gap-2 rounded-xl bg-[#F5A623] px-6 text-base font-bold text-[#0F2038] shadow-md outline-none transition-colors hover:bg-[#E89A18] focus-visible:ring-4 focus-visible:ring-[#F5A623]/40 disabled:opacity-60"
                data-test="submit-workbook"
            >
                <Spinner v-if="processing" />
                {{ submitLabel ?? 'Buat Laporan' }}
            </button>
            <Link
                :href="cancelHref"
                class="inline-flex h-12 min-w-[120px] items-center justify-center rounded-xl border border-[#E0E6EF] bg-white px-6 text-base font-bold text-[#0F2038] outline-none transition-colors hover:bg-[#F5F8FC] focus-visible:ring-4 focus-visible:ring-[#294161]/15"
                data-test="cancel-workbook"
            >
                Batal
            </Link>
        </div>
    </Form>
</template>
