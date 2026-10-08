<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import DashboardController from '@/actions/App/Http/Controllers/Admin/DashboardController';
import AreaChart from '@/components/admin/AreaChart.vue';
import { BULAN_PANJANG, BULAN_PENDEK, formatAngka, formatTon } from '@/lib/format';

type RegionTotal = { name: string; ton: number; percent: number };
type MonthlyPoint = {
    month: number;
    /** null untuk bulan yang belum berjalan */
    ton: number | null;
};

const props = defineProps<{
    year: number;
    years: number[];
    monthsCounted: number;
    inProgress: boolean;
    stats: {
        totalTon: number;
        averageTon: number;
        peak: { month: number; ton: number } | null;
        topRegion: RegionTotal | null;
    };
    monthly: MonthlyPoint[];
    regions: RegionTotal[];
}>();

const hasData = computed(() => props.stats.totalTon > 0);

const lastMonth = computed(() => BULAN_PANJANG[props.monthsCounted - 1]);
const chartPeriod = computed(() => {
    const range =
        props.monthsCounted === 1 ? `${lastMonth.value} ${props.year}` : `Januari – ${lastMonth.value} ${props.year}`;

    return props.inProgress ? `${range} (${lastMonth.value} masih berjalan)` : range;
});
const regionPeriod = computed(() => {
    const range = props.monthsCounted === 1 ? BULAN_PENDEK[0] : `Jan – ${BULAN_PENDEK[props.monthsCounted - 1]}`;

    return `Total ${formatTon(props.stats.totalTon)} ton sepanjang ${range} ${props.year}`;
});

const cards = computed(() => [
    { label: 'Total penyerapan', value: `${formatTon(props.stats.totalTon)} ton`, note: `Tahun ${props.year}` },
    {
        label: 'Rata-rata per bulan',
        value: `${formatTon(props.stats.averageTon)} ton`,
        note: `dari ${props.monthsCounted} bulan data`,
    },
    {
        label: 'Bulan tertinggi',
        value: props.stats.peak ? BULAN_PANJANG[props.stats.peak.month - 1] : '-',
        note: props.stats.peak ? `${formatTon(props.stats.peak.ton)} ton` : 'Belum ada data',
    },
    {
        label: 'Wilayah teratas',
        value: props.stats.topRegion?.name ?? '-',
        note: props.stats.topRegion
            ? `${formatTon(props.stats.topRegion.ton)} ton · ${formatAngka(props.stats.topRegion.percent, 0)}%`
            : 'Belum ada data',
    },
]);

const maxRegionTon = computed(() => Math.max(...props.regions.map((region) => region.ton), 1));
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-[28px] sm:leading-tight">Dashboard penyerapan</h1>
            <p class="mt-1 text-sm text-[#5B6B82]">Pantau penyerapan gabah berdasarkan wilayah dan waktu.</p>
        </div>

        <nav class="flex gap-1 rounded-xl border border-[#E5E9F0] bg-[#F5F8FC] p-1" aria-label="Pilih tahun">
            <Link
                v-for="item in years"
                :key="item"
                :href="DashboardController.url({ query: { year: item } })"
                preserve-scroll
                :aria-current="item === year ? 'page' : undefined"
                class="rounded-lg px-4 py-1.5 text-sm font-bold outline-none transition-colors focus-visible:ring-4 focus-visible:ring-[#1F4E8C]/20"
                :class="item === year ? 'bg-white text-[#0F2038] shadow-sm' : 'text-[#5B6B82] hover:text-[#0F2038]'"
            >
                {{ item }}
            </Link>
        </nav>
    </div>

    <dl class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div v-for="card in cards" :key="card.label" class="rounded-xl border border-[#E5E9F0] bg-[#F7F9FC] px-4 py-3.5">
            <dt class="text-xs text-[#5B6B82]">{{ card.label }}</dt>
            <dd class="mt-1 truncate text-xl font-extrabold tracking-tight sm:text-2xl">{{ card.value }}</dd>
            <dd class="mt-0.5 text-xs text-[#5B6B82]">{{ card.note }}</dd>
        </div>
    </dl>

    <div
        v-if="!hasData"
        class="mt-4 rounded-2xl border border-dashed border-[#C9D3E3] bg-[#F5F8FC] px-6 py-14 text-center text-sm text-[#5B6B82]"
    >
        Belum ada laporan penyerapan pada tahun {{ year }}. Grafik muncul setelah PIC mengirim laporan.
    </div>

    <template v-else>
        <section class="mt-4 rounded-2xl border border-[#E5E9F0] bg-white p-6 shadow-sm" aria-labelledby="per-bulan">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 id="per-bulan" class="text-lg font-bold">Penyerapan per bulan</h2>
                    <p class="mt-1 text-xs text-[#5B6B82]">{{ chartPeriod }}</p>
                </div>
                <ul class="flex items-center gap-4 text-xs text-[#5B6B82]">
                    <li class="flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-[#1F4E8C]" aria-hidden="true" />
                        Total penyerapan (ton)
                    </li>
                    <li class="flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-[#F5A623]" aria-hidden="true" />
                        Bulan tertinggi
                    </li>
                </ul>
            </div>

            <div class="mt-4">
                <AreaChart :points="monthly" :peak-month="stats.peak?.month ?? null" />
            </div>
        </section>

        <section class="mt-4 rounded-2xl border border-[#E5E9F0] bg-white p-6 shadow-sm" aria-labelledby="per-wilayah">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 id="per-wilayah" class="text-lg font-bold">Penyerapan per kota/kabupaten</h2>
                    <p class="mt-1 text-xs text-[#5B6B82]">{{ regionPeriod }}</p>
                </div>
                <ul class="flex items-center gap-4 text-xs text-[#5B6B82]">
                    <li class="flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-[#F5A623]" aria-hidden="true" />
                        Tertinggi
                    </li>
                    <li class="flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-[#1F4E8C]" aria-hidden="true" />
                        Lainnya
                    </li>
                </ul>
            </div>

            <ul class="mt-5 flex flex-col gap-3.5 sm:gap-3">
                <li
                    v-for="(region, index) in regions"
                    :key="region.name"
                    class="grid grid-cols-[1fr_auto] items-center gap-x-4 text-[13px] sm:grid-cols-[9rem_1fr_7.5rem]"
                >
                    <span class="truncate font-semibold">{{ region.name }}</span>
                    <span class="text-right sm:col-start-3 sm:row-start-1">
                        <span class="font-bold">{{ formatTon(region.ton) }} ton</span>
                        <span class="ml-1.5 text-[11px] text-[#5B6B82]">{{ formatAngka(region.percent, 0) }}%</span>
                    </span>
                    <span
                        class="col-span-2 row-start-2 mt-1.5 h-2.5 overflow-hidden rounded-full bg-[#EEF2F8] sm:col-span-1 sm:col-start-2 sm:row-start-1 sm:mt-0"
                        aria-hidden="true"
                    >
                        <span
                            class="block h-full rounded-full"
                            :class="index === 0 ? 'bg-[#F5A623]' : 'bg-[#1F4E8C]'"
                            :style="{ width: `${Math.max(2, (region.ton / maxRegionTon) * 100)}%` }"
                        />
                    </span>
                </li>
            </ul>
        </section>
    </template>
</template>
