<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { CircleUser } from '@lucide/vue';
import { computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Toaster } from '@/components/ui/sonner';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { dashboard } from '@/routes';

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <div
        class="flex min-h-svh flex-col bg-white font-[Inter,ui-sans-serif,system-ui,sans-serif] text-[#395E90]"
    >
        <header
            class="flex h-24 items-center justify-between border-b border-[#839DC1] px-6 md:h-[131px] md:px-10"
        >
            <Link :href="dashboard()" aria-label="Bulog, beranda">
                <img
                    src="/images/bulog-logo.png"
                    alt="Bulog, mengantarkan kebaikan"
                    width="150"
                    height="50"
                    class="h-auto w-[130px] md:w-[150px]"
                />
            </Link>

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button
                        type="button"
                        class="rounded-full text-[#294161] outline-none focus-visible:ring-4 focus-visible:ring-[#395E90]/25"
                        aria-label="Menu akun"
                        data-test="user-menu"
                    >
                        <CircleUser class="size-10 md:size-12" :stroke-width="1.5" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="min-w-56">
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </header>

        <main class="mx-auto w-full max-w-[1272px] flex-1 px-4 py-10 md:px-8 md:py-14">
            <slot />
        </main>

        <footer class="h-24 bg-[#294161] md:h-[132px]" aria-hidden="true" />
        <Toaster position="top-center" :offset="24" />
    </div>
</template>
