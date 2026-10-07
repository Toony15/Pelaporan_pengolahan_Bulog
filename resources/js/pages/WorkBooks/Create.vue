<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import WorkBookController from '@/actions/App/Http/Controllers/WorkBookController';
import PageCrumb from '@/components/PageCrumb.vue';
import WorkBookForm from '@/components/WorkBookForm.vue';
import { dashboard } from '@/routes';

defineProps<{ defaultPicName: string }>();
</script>

<template>
    <Head title="Buat laporan kerja" />

    <PageCrumb current="Laporan baru" />
    <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Buat laporan kerja PIC</h1>

    <div class="mt-6">
        <WorkBookForm
            :form="WorkBookController.store.form()"
            :cancel-href="dashboard()"
            :default-pic-name="defaultPicName"
            submit-label="Buat Laporan"
            fail-message="Data gagal ditambahkan. Periksa kembali isian Anda."
        />
    </div>
</template>
