<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import AuthInput from '@/components/AuthInput.vue';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';

defineProps<{
    status?: string;
    canResetPassword?: boolean;
}>();
</script>

<template>
    <Head title="Login" />

    <h1 class="mb-2 text-center text-xl font-bold">Selamat Datang</h1>

    <div class="rounded-[20px] bg-[#395E90] px-5 pt-8 pb-8 md:px-[30px]">
        <p class="mb-5 px-1 text-sm font-bold text-white">Masuk dengan akun anda</p>

        <p v-if="status" class="mb-4 px-1 text-sm font-bold text-[#C8F7A8]">
            {{ status }}
        </p>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <AuthInput
                name="username"
                label="Username"
                autocomplete="username"
                required
                autofocus
                :error="errors.username"
            />
            <AuthInput
                name="password"
                type="password"
                label="Password"
                autocomplete="current-password"
                revealable
                required
                :error="errors.password"
            />
            <button
                type="submit"
                :disabled="processing"
                class="mx-auto mt-2 inline-flex h-[50px] w-full items-center justify-center gap-2 rounded-[10px] border border-[#D69A2B] bg-[#F1B444] text-[15px] font-medium tracking-wide text-white uppercase outline-none transition-colors hover:bg-[#E6A530] focus-visible:ring-4 focus-visible:ring-white/60 disabled:opacity-60 md:w-[87%]"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Login
            </button>
        </Form>
    </div>

    <p class="mt-5 px-5 text-sm font-bold">
        Belum punya akun?
        <Link
            :href="register()"
            class="text-[#E99E18] uppercase underline-offset-4 outline-none hover:underline focus-visible:underline"
        >
            Silahkan daftar disini
        </Link>
    </p>
</template>
