<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import WorkBookController from '@/actions/App/Http/Controllers/WorkBookController';
import PageCrumb from '@/components/PageCrumb.vue';
import WorkBookForm from '@/components/WorkBookForm.vue';
import type { WorkBookDetail } from '@/types/work-book';

defineProps<{ workBook: WorkBookDetail }>();
</script>

<template>
    <Head title="Edit laporan kerja" />

    <PageCrumb current="Edit laporan" />
    <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Edit laporan kerja PIC</h1>

    <div class="mt-6">
        <WorkBookForm
            :form="WorkBookController.update.form(workBook.id)"
            :cancel-href="WorkBookController.show(workBook.id)"
            :work-book="workBook"
            submit-label="Simpan Perubahan"
            fail-message="Data gagal diperbarui. Periksa kembali isian Anda."
        />
    </div>
</template>
