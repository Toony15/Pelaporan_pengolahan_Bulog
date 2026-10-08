<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, ChartColumn, ChevronDown, ChevronUp, FileText, Scale, Store, Users } from '@lucide/vue';
import { computed } from 'vue';
import WorkBookController from '@/actions/App/Http/Controllers/WorkBookController';
import MiniBars from '@/components/admin/MiniBars.vue';
import { formatAngka, formatTon } from '@/lib/format';

type RecentReport = {
    id: number;
    mitra_pengolahan: string;
    pic_name: string;
    regency: string;
    ton: number;
    date: string;
};

const props = defineProps<{
    period: { year: number; month: number };
    total: number;
    change: number | null;
    monthly: number[];
    pics: { total: number; active: number; inactive: number };
    partners: { total: number; regions: number };
    recent: RecentReport[];
    recentTotal: number;
    recentDays: number;
}>();

const BULAN_PANJANG = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const BULAN_PENDEK = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

const page = usePage();
const firstName = computed(() => page.props.auth.user.name.trim().split(/\s+/)[0]);
const isAdmin = computed(() => page.props.auth.user.role === 'admin');

const subtitle = computed(() =>
    props.period.month === 1
        ? `Ringkasan penyerapan gabah Januari ${props.period.year}.`
        : `Ringkasan penyerapan gabah Januari – ${BULAN_PANJANG[props.period.month - 1]} ${props.period.year}.`,
);

const labels = computed(() => BULAN_PENDEK.slice(0, props.period.month));
const changeText = computed(() =>
    props.change === null ? '' : `${props.change > 0 ? '+' : ''}${formatAngka(props.change, 1)}%`,
);

/** "12 Okt" dari "2026-10-12" */
function tanggalPendek(iso: string): string {
    const [, month, day] = iso.slice(0, 10).split('-').map(Number);

    return `${day} ${BULAN_PENDEK[month - 1]}`;
}

function initials(name: string): string {
    return name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');
}

// Dashboard & Kelola akun menyusul; sementara tampil nonaktif.
const shortcuts = computed(() => [
    { label: 'Dashboard', note: 'Grafik per kota dan per bulan', icon: ChartColumn, tile: 'bg-[#E6EEF9] text-[#1F4E8C]' },
    ...(isAdmin.value
        ? [{ label: 'Kelola akun', note: 'Ubah atau hapus akun pengguna', icon: Users, tile: 'bg-[#FFF3DB] text-[#D9822B]' }]
        : []),
]);
</script>

<template>
    <Head title="Beranda" />

    <h1 class="text-3xl font-extrabold tracking-tight sm:text-[40px] sm:leading-tight">
        Selamat datang, {{ firstName }}
    </h1>
    <p class="mt-1 text-sm text-[#5B6B82]">{{ subtitle }}</p>

    <!-- Kartu ringkasan -->
    <div class="mt-6 grid gap-4 lg:grid-cols-[1.5fr_1fr_1fr]">
        <section
            class="relative flex min-h-[216px] flex-col overflow-hidden rounded-2xl bg-gradient-to-br from-[#12305C] to-[#0D2850] p-5 text-white shadow-sm"
            aria-label="Total penyerapan"
        >
            <span class="flex size-11 items-center justify-center rounded-xl bg-[#F5A623] text-[#0F2038]">
                <Scale class="size-5" aria-hidden="true" />
            </span>
            <p class="mt-4 text-sm text-[#C9D6EA]">Total penyerapan</p>
            <p class="mt-1 flex items-baseline gap-2">
                <span class="text-5xl leading-none font-extrabold tracking-tight">{{ formatTon(total) }}</span>
                <span class="text-xl font-semibold text-[#C9D6EA]">ton</span>
            </p>

            <p class="relative z-10 mt-auto flex flex-wrap items-center gap-2 pt-4 text-xs text-[#C9D6EA]">
                <template v-if="change !== null">
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-white/10 px-2.5 py-1 font-bold"
                        :class="change >= 0 ? 'text-[#F5A623]' : 'text-[#FFB4B4]'"
                    >
                        <component :is="change >= 0 ? ChevronUp : ChevronDown" class="size-3.5" aria-hidden="true" />
                        {{ changeText }}
                    </span>
                    dibanding periode sama {{ period.year - 1 }}
                </template>
                <template v-else>Belum ada pembanding dari tahun {{ period.year - 1 }}</template>
            </p>

            <MiniBars
                :values="monthly"
                :labels="labels"
                class="pointer-events-auto absolute right-5 bottom-0 hidden w-[48%] sm:flex"
            />
        </section>

        <section class="flex flex-col rounded-2xl border border-[#E5E9F0] bg-white p-5 shadow-sm" aria-label="Jumlah PIC">
            <span class="flex size-11 items-center justify-center rounded-xl bg-[#E6EEF9] text-[#1F4E8C]">
                <Users class="size-5" aria-hidden="true" />
            </span>
            <p class="mt-4 text-sm text-[#5B6B82]">Jumlah PIC</p>
            <p class="mt-1 text-5xl leading-none font-extrabold tracking-tight">{{ pics.total }}</p>
            <p class="mt-auto flex items-center gap-2 pt-4 text-xs text-[#5B6B82]">
                <span class="size-2 rounded-full bg-[#17804F]" aria-hidden="true" />
                {{ pics.active }} aktif · {{ pics.inactive }} nonaktif
            </p>
        </section>

        <section class="flex flex-col rounded-2xl border border-[#E5E9F0] bg-white p-5 shadow-sm" aria-label="Jumlah mitra">
            <span class="flex size-11 items-center justify-center rounded-xl bg-[#FFF3DB] text-[#D9822B]">
                <Store class="size-5" aria-hidden="true" />
            </span>
            <p class="mt-4 text-sm text-[#5B6B82]">Jumlah mitra</p>
            <p class="mt-1 text-5xl leading-none font-extrabold tracking-tight">{{ partners.total }}</p>
            <p class="mt-auto pt-4 text-xs text-[#5B6B82]">Tersebar di {{ partners.regions }} kab/kota</p>
        </section>
    </div>

    <div class="mt-4 grid items-start gap-4 lg:grid-cols-[1.5fr_1fr]">
        <!-- Laporan terbaru -->
        <section class="rounded-2xl border border-[#E5E9F0] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Laporan terbaru</h2>
            <p class="mt-1 text-xs text-[#5B6B82]">Masuk dari PIC dalam {{ recentDays }} hari terakhir</p>

            <ul v-if="recent.length" class="mt-4 divide-y divide-[#EEF1F6]">
                <li v-for="report in recent" :key="report.id">
                    <Link
                        :href="WorkBookController.show(report.id)"
                        class="-mx-2 flex items-center gap-3 rounded-xl px-2 py-3 outline-none transition-colors hover:bg-[#F5F8FC] focus-visible:ring-4 focus-visible:ring-[#1F4E8C]/20"
                    >
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-full bg-[#E6EEF9] text-xs font-bold text-[#1F4E8C]"
                            aria-hidden="true"
                        >
                            {{ initials(report.pic_name) }}
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-bold">{{ report.mitra_pengolahan }}</span>
                            <span class="block truncate text-xs text-[#5B6B82]">
                                {{ report.pic_name }}<template v-if="report.regency"> · {{ report.regency }}</template>
                            </span>
                        </span>
                        <span class="shrink-0 text-right">
                            <span class="block text-sm font-bold">{{ formatTon(report.ton) }} ton</span>
                            <span class="block text-xs text-[#5B6B82]">{{ tanggalPendek(report.date) }}</span>
                        </span>
                    </Link>
                </li>
            </ul>
            <div v-else class="mt-4 flex flex-col items-center gap-2 rounded-xl border border-dashed border-[#C9D3E3] bg-[#F5F8FC] px-4 py-10 text-center">
                <FileText class="size-6 text-[#9AA3B5]" aria-hidden="true" />
                <p class="text-sm text-[#5B6B82]">Belum ada laporan masuk dalam {{ recentDays }} hari terakhir.</p>
            </div>
            <p v-if="recentTotal > recent.length" class="mt-3 text-xs text-[#5B6B82]">
                dan {{ recentTotal - recent.length }} laporan lainnya.
            </p>
        </section>

        <!-- Akses cepat -->
        <section class="rounded-2xl border border-[#E5E9F0] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Akses cepat</h2>
            <p class="mt-1 text-xs text-[#5B6B82]">Buka fitur yang sering dipakai</p>

            <ul class="mt-4 flex flex-col gap-3">
                <li
                    v-for="shortcut in shortcuts"
                    :key="shortcut.label"
                    class="flex cursor-not-allowed items-center gap-3 rounded-xl border border-[#E5E9F0] p-3 opacity-60"
                    aria-disabled="true"
                >
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg" :class="shortcut.tile">
                        <component :is="shortcut.icon" class="size-5" aria-hidden="true" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-[13px] font-bold">{{ shortcut.label }}</span>
                        <span class="block truncate text-xs text-[#5B6B82]">{{ shortcut.note }}</span>
                    </span>
                    <span class="rounded-full bg-[#EEF1F6] px-2 py-0.5 text-[10px] font-semibold tracking-wide text-[#5B6B82] uppercase">
                        Segera
                    </span>
                    <ArrowRight class="size-4 text-[#5B6B82]" aria-hidden="true" />
                </li>
            </ul>
        </section>
    </div>
</template>
