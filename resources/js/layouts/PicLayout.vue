<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { EllipsisVertical } from '@lucide/vue';
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
const initial = computed(() => user.value.name.trim().charAt(0).toUpperCase());
</script>

<template>
    <div
        class="flex min-h-svh flex-col bg-white font-[Inter,ui-sans-serif,system-ui,sans-serif] text-[#0F2038]"
    >
        <header class="border-b border-[#F2F4F8] bg-white">
            <div class="mx-auto flex h-14 max-w-5xl items-center justify-between px-4 sm:px-6">
                <Link :href="dashboard()" aria-label="Bulog, beranda">
                    <img
                        src="/images/bulog-logo.png"
                        alt="Bulog, mengantarkan kebaikan"
                        width="150"
                        height="50"
                        class="h-9 w-auto"
                    />
                </Link>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button
                            type="button"
                            class="flex items-center gap-2.5 rounded-full border border-[#E5E9F0] bg-white py-1 pr-3 pl-1 text-sm font-bold shadow-sm outline-none transition-colors hover:bg-[#F5F8FC] focus-visible:ring-4 focus-visible:ring-[#1F4E8C]/20"
                            aria-label="Menu akun"
                            data-test="user-menu"
                        >
                            <span
                                class="flex size-8 items-center justify-center rounded-full bg-[#1F4E8C] text-xs font-bold text-white"
                            >
                                {{ initial }}
                            </span>
                            <span class="max-w-[140px] truncate">{{ user.name }}</span>
                            <EllipsisVertical class="size-4 text-[#5B6784]" />
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="min-w-56">
                        <UserMenuContent :user="user" />
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </header>

        <main class="mx-auto w-full max-w-5xl flex-1 px-4 py-6 sm:px-6 sm:py-8">
            <slot />
        </main>

        <Toaster position="top-center" :offset="24" />
    </div>
</template>
