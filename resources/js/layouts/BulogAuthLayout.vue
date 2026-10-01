<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';
</script>

<template>
    <!-- Halaman login/register selalu terang, mengikuti desain Figma -->
    <div
        class="flex min-h-svh flex-col bg-white font-[Inter,ui-sans-serif,system-ui,sans-serif] text-[#395E90]"
    >
        <header
            class="flex h-24 items-center border-b border-[#839DC1] px-6 md:h-[131px] md:px-10"
        >
            <Link :href="home()" aria-label="Bulog, beranda">
                <img
                    src="/images/bulog-logo.png"
                    alt="Bulog, mengantarkan kebaikan"
                    width="150"
                    height="50"
                    class="h-auto w-[130px] md:w-[150px]"
                />
            </Link>
        </header>

        <main class="flex flex-1 flex-col items-center justify-center px-4 py-10">
            <div class="w-full max-w-[567px]">
                <slot />
            </div>
        </main>
    </div>
</template>
