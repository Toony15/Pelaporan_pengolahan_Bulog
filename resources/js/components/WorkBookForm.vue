<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import BulogField from '@/components/BulogField.vue';
import UploadBox from '@/components/UploadBox.vue';
import { Spinner } from '@/components/ui/spinner';
import { hariIni } from '@/lib/format';
import { notify } from '@/lib/notify';
import type { WorkBookDetail } from '@/types/work-book';

defineProps<{
    // hasil WorkBookController.store.form() / update.form(id)
    form: { action: string; method: 'get' | 'post' | 'put' | 'patch' | 'delete' };
    workBook?: WorkBookDetail;
    defaultPicName?: string;
    submitLabel?: string;
    failMessage?: string;
}>();
</script>

<template>
    <Form
        v-bind="form"
        v-slot="{ errors, processing, progress }"
        class="flex flex-col gap-5"
        @error="notify('error', failMessage ?? 'Data gagal disimpan. Periksa kembali isian Anda.')"
    >
        <BulogField
            label="Nama Pic"
            name="pic_name"
            :value="workBook?.pic_name ?? defaultPicName"
            :error="errors.pic_name"
            autocomplete="name"
            required
        />
        <BulogField
            label="Nama Mitra pengolahan"
            name="mitra_pengolahan"
            :value="workBook?.mitra_pengolahan"
            :error="errors.mitra_pengolahan"
            autocomplete="off"
            required
        />
        <BulogField
            label="Kancab"
            name="kancab"
            :value="workBook?.kancab"
            :error="errors.kancab"
            autocomplete="off"
            required
        />
        <BulogField
            label="Kanwil"
            name="kanwil"
            :value="workBook?.kanwil"
            :error="errors.kanwil"
            autocomplete="off"
            required
        />
        <BulogField
            label="Tanggal penyerapan"
            name="absorption_date"
            type="date"
            :value="workBook?.absorption_date ?? hariIni()"
            :error="errors.absorption_date"
            required
        />

        <div class="mt-8 flex flex-col gap-8">
            <UploadBox
                label="Upload Video penyerapan"
                name="video"
                kind="video"
                :existing="workBook?.attachments.video"
                :error="errors.video"
            />
            <UploadBox
                label="Upload Foto Gabah"
                name="foto_gabah"
                kind="photo"
                :existing="workBook?.attachments.foto_gabah"
                :error="errors.foto_gabah"
            />
            <UploadBox
                label="Upload Foto bersama mitra"
                name="foto_mitra"
                kind="photo"
                :existing="workBook?.attachments.foto_mitra"
                :error="errors.foto_mitra"
            />
            <UploadBox
                label="Upload Foto ktp mitra"
                name="foto_ktp"
                kind="photo"
                :existing="workBook?.attachments.foto_ktp"
                :error="errors.foto_ktp"
            />
        </div>

        <div class="mt-6 flex items-center gap-4">
            <button
                type="submit"
                :disabled="processing"
                class="inline-flex h-[52px] items-center justify-center gap-2 rounded-xl bg-[#294161] px-7 text-base font-medium text-white outline-none transition-colors hover:bg-[#1f3350] focus-visible:ring-4 focus-visible:ring-[#395E90]/40 disabled:opacity-60"
                data-test="submit-workbook"
            >
                <Spinner v-if="processing" />
                {{ submitLabel ?? 'Submit' }}
            </button>
            <span v-if="processing && progress" class="text-sm font-bold" role="status">
                Mengunggah… {{ progress.percentage }}%
            </span>
        </div>
    </Form>
</template>
