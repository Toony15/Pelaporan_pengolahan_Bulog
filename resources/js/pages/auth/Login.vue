<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import PillInput from '@/components/PillInput.vue';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';

defineProps<{
    status?: string;
    canResetPassword?: boolean;
}>();

const button =
    'inline-flex h-[43px] items-center justify-center gap-2 rounded-xl bg-[#294161] px-6 text-base font-medium text-white outline-none transition-colors hover:bg-[#1f3350] focus-visible:ring-4 focus-visible:ring-[#395E90]/40 disabled:opacity-60';
</script>

<template>
    <Head title="Login" />

    <h1 class="mb-2 text-center text-xl font-bold">Selamat Datang</h1>

    <div class="rounded-[28px] border border-[#94ADCF] p-7 md:pb-12">
        <p class="mb-4 px-3.5 text-sm font-bold">
            Sudah punya akun? silahkan masuk
        </p>

        <p v-if="status" class="mb-4 px-3.5 text-sm font-medium text-green-700">
            {{ status }}
        </p>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-7"
        >
            <PillInput
                name="username"
                label="Username"
                autocomplete="username"
                required
                autofocus
                :error="errors.username"
            />
            <PillInput
                name="password"
                type="password"
                label="Password"
                autocomplete="current-password"
                required
                :error="errors.password"
            />
            <div class="px-3.5">
                <button
                    type="submit"
                    :class="button"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    Login
                </button>
            </div>
        </Form>
    </div>

    <div class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-3 px-3.5">
        <p class="text-sm font-bold">Belum punya akun? silahkan register</p>
        <Link :href="register()" :class="button">Register</Link>
    </div>
</template>
