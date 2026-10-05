<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import WorkBookController from '@/actions/App/Http/Controllers/WorkBookController';
import WorkBookForm from '@/components/WorkBookForm.vue';
import type { WorkBookDetail } from '@/types/work-book';

defineProps<{ workBook: WorkBookDetail }>();
</script>

<template>
    <Head title="Edit laporan kerja" />

    <Link
        :href="WorkBookController.show(workBook.id)"
        class="mb-4 inline-flex items-center gap-1.5 text-sm font-bold hover:underline"
    >
        <ArrowLeft class="size-4" /> Kembali
    </Link>
    <h1 class="text-center text-lg font-bold text-[#AF7A1C] md:text-xl">Edit laporan kerja PIC</h1>

    <div class="mx-auto mt-8 max-w-[795px] rounded-[28px] border border-[#395E90] p-6 md:p-12">
        <p class="mb-6 px-3 text-sm">
            Unggah file baru hanya jika ingin mengganti yang lama. Kosongkan jika tidak ada perubahan.
        </p>
        <WorkBookForm
            :form="WorkBookController.update.form(workBook.id)"
            :work-book="workBook"
            submit-label="Simpan perubahan"
            fail-message="Data gagal diperbarui. Periksa kembali isian Anda."
        />
    </div>
</template>
