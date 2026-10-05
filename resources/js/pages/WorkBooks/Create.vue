<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import WorkBookController from '@/actions/App/Http/Controllers/WorkBookController';
import WorkBookForm from '@/components/WorkBookForm.vue';
import { dashboard } from '@/routes';

defineProps<{ defaultPicName: string }>();
</script>

<template>
    <Head title="Buat laporan kerja" />

    <Link :href="dashboard()" class="mb-4 inline-flex items-center gap-1.5 text-sm font-bold hover:underline">
        <ArrowLeft class="size-4" /> Kembali
    </Link>
    <h1 class="text-center text-lg font-bold text-[#AF7A1C] md:text-xl">Buku laporan kerja PIC</h1>

    <div class="mx-auto mt-8 max-w-[795px] rounded-[28px] border border-[#395E90] p-6 md:p-12">
        <WorkBookForm
            :form="WorkBookController.store.form()"
            :default-pic-name="defaultPicName"
            fail-message="Data gagal ditambahkan. Periksa kembali isian Anda."
        />
    </div>
</template>
