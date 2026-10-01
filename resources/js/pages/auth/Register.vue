<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import PillInput from '@/components/PillInput.vue';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

const button =
    'inline-flex h-[43px] items-center justify-center gap-2 rounded-xl bg-[#294161] px-6 text-base font-medium text-white outline-none transition-colors hover:bg-[#1f3350] focus-visible:ring-4 focus-visible:ring-[#395E90]/40 disabled:opacity-60';
</script>

<template>
    <Head title="Register" />

    <h1 class="mb-2 text-center text-xl font-bold">Selamat Datang</h1>

    <div class="rounded-[28px] border border-[#94ADCF] p-7 md:pb-12">
        <p class="mb-4 px-3.5 text-sm font-bold">
            Silahkan registrasi untuk membuat akun
        </p>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-7"
        >
            <PillInput
                name="email"
                type="email"
                label="Email"
                autocomplete="email"
                required
                autofocus
                :error="errors.email"
            />
            <PillInput
                name="phone"
                type="tel"
                label="No Hp"
                inputmode="tel"
                autocomplete="tel"
                required
                :error="errors.phone"
            />
            <PillInput
                name="username"
                label="Username"
                autocomplete="username"
                required
                :error="errors.username"
            />
            <PillInput
                name="password"
                type="password"
                label="Password"
                autocomplete="new-password"
                :passwordrules="passwordRules"
                required
                :error="errors.password"
            />
            <div class="px-3.5">
                <button
                    type="submit"
                    :class="button"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    Register
                </button>
            </div>
        </Form>
    </div>

    <div class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-3 px-3.5">
        <p class="text-sm font-bold">Sudah punya akun? silahkan masuk</p>
        <Link :href="login()" :class="button">Login</Link>
    </div>
</template>
