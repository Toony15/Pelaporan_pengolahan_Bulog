<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Calendar, ChartColumn, ChevronDown, House, Menu, Users, X } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Toaster } from '@/components/ui/sonner';
import UserMenuContent from '@/components/UserMenuContent.vue';
import AdminDashboardController from '@/actions/App/Http/Controllers/Admin/DashboardController';
import { dashboard } from '@/routes';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => user.value.role === 'admin');
const initial = computed(() => user.value.name.trim().charAt(0).toUpperCase());

const panelLabel = computed(() => (isAdmin.value ? 'Panel Admin' : 'Panel Manajer'));
const roleLabel = computed(() => (isAdmin.value ? 'Administrator' : 'Manajer'));

const titles: Record<string, string> = {
    'Admin/Home': 'Beranda',
    'Admin/Dashboard': 'Dashboard',
    'WorkBooks/Show': 'Detail laporan',
};
const title = computed(() => titles[page.component] ?? 'Beranda');

const today = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
}).format(new Date());

// Menu "Kelola akun" menyusul pada tahap berikutnya.
const nav = computed(() => [
    {
        label: 'Beranda',
        icon: House,
        href: dashboard(),
        active: page.component === 'Admin/Home' || page.component.startsWith('WorkBooks/'),
        soon: false,
    },
    {
        label: 'Dashboard',
        icon: ChartColumn,
        href: AdminDashboardController(),
        active: page.component === 'Admin/Dashboard',
        soon: false,
    },
    ...(isAdmin.value
        ? [{ label: 'Kelola akun', icon: Users, href: null, active: false, soon: true }]
        : []),
]);

// Menu geser (HP/tablet)
const open = ref(false);
watch(() => page.url, () => (open.value = false));
</script>

<template>
    <div
        class="min-h-svh bg-white font-[Inter,ui-sans-serif,system-ui,sans-serif] text-[#0F2038]"
        @keydown.esc="open = false"
    >
        <div
            v-if="open"
            class="fixed inset-0 z-30 bg-black/40 lg:hidden"
            aria-hidden="true"
            @click="open = false"
        />

        <aside
            id="admin-sidebar"
            class="fixed inset-y-0 left-0 z-40 flex w-[264px] flex-col bg-[#12305C] text-white transition-transform duration-200"
            :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            aria-label="Menu utama"
        >
            <div class="flex items-start justify-between px-6 pt-8 pb-7">
                <div>
                    <Link :href="dashboard()" aria-label="Bulog, beranda">
                        <img
                            src="/images/bulog-logo-light.png"
                            alt="Bulog, mengantarkan kebaikan"
                            width="150"
                            height="50"
                            class="h-11 w-auto"
                        />
                    </Link>
                    <p class="mt-1 text-[11px] font-bold tracking-[0.14em] text-[#F5A623] uppercase">
                        {{ panelLabel }}
                    </p>
                </div>
                <button
                    type="button"
                    class="rounded-lg p-1.5 text-[#C9D6EA] outline-none hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-white/60 lg:hidden"
                    aria-label="Tutup menu"
                    @click="open = false"
                >
                    <X class="size-5" />
                </button>
            </div>

            <p class="px-6 text-[11px] font-semibold tracking-[0.14em] text-[#8FA9D1] uppercase">Menu</p>

            <nav class="mt-3 flex flex-col gap-1.5 px-3.5" aria-label="Navigasi">
                <template v-for="item in nav" :key="item.label">
                    <Link
                        v-if="item.href"
                        :href="item.href"
                        class="relative flex items-center gap-3 rounded-xl px-3.5 py-3 text-[15px] font-medium outline-none transition-colors focus-visible:ring-2 focus-visible:ring-white/60"
                        :class="item.active ? 'bg-white/10 text-white' : 'text-[#C9D6EA] hover:bg-white/5 hover:text-white'"
                        :aria-current="item.active ? 'page' : undefined"
                    >
                        <span
                            v-if="item.active"
                            class="absolute top-1/2 -left-3.5 h-6 w-1 -translate-y-1/2 rounded-r bg-[#F5A623]"
                            aria-hidden="true"
                        />
                        <component :is="item.icon" class="size-5 shrink-0" aria-hidden="true" />
                        {{ item.label }}
                    </Link>
                    <div
                        v-else
                        class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3.5 py-3 text-[15px] font-medium text-[#C9D6EA]/50"
                        aria-disabled="true"
                    >
                        <component :is="item.icon" class="size-5 shrink-0" aria-hidden="true" />
                        {{ item.label }}
                        <span class="ml-auto rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase">
                            Segera
                        </span>
                    </div>
                </template>
            </nav>
        </aside>

        <div class="lg:pl-[264px]">
            <header class="sticky top-0 z-20 border-b border-[#E2E8F1] bg-white/95 backdrop-blur">
                <div class="flex h-16 items-center justify-between gap-3 px-4 sm:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            class="rounded-lg p-2 outline-none hover:bg-[#F5F8FC] focus-visible:ring-4 focus-visible:ring-[#1F4E8C]/20 lg:hidden"
                            aria-label="Buka menu"
                            aria-controls="admin-sidebar"
                            :aria-expanded="open"
                            @click="open = true"
                        >
                            <Menu class="size-5" />
                        </button>
                        <p class="truncate text-lg font-bold">{{ title }}</p>
                    </div>

                    <div class="flex shrink-0 items-center gap-4">
                        <p class="hidden items-center gap-2 text-sm text-[#5B6B82] md:flex">
                            <Calendar class="size-4" aria-hidden="true" /> {{ today }}
                        </p>

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <button
                                    type="button"
                                    class="flex items-center gap-2.5 rounded-full border border-[#E2E8F1] bg-white py-1 pr-3 pl-1 text-left shadow-sm outline-none transition-colors hover:bg-[#F5F8FC] focus-visible:ring-4 focus-visible:ring-[#1F4E8C]/20"
                                    aria-label="Menu akun"
                                    data-test="user-menu"
                                >
                                    <span
                                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#F5A623] text-sm font-bold text-[#0F2038]"
                                    >
                                        {{ initial }}
                                    </span>
                                    <span class="hidden min-w-0 leading-tight sm:block">
                                        <span class="block max-w-[140px] truncate text-[13px] font-bold">{{ user.name }}</span>
                                        <span class="block text-[11px] text-[#5B6B82]">{{ roleLabel }}</span>
                                    </span>
                                    <ChevronDown class="size-4 text-[#5B6B82]" aria-hidden="true" />
                                </button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="min-w-56">
                                <UserMenuContent :user="user" />
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-[1240px] px-4 py-8 sm:px-8">
                <slot />
            </main>
        </div>

        <Toaster position="top-center" :offset="24" />
    </div>
</template>
