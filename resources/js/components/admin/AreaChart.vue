<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, useId } from 'vue';
import { BULAN_PANJANG, BULAN_PENDEK, formatAngka, formatTon } from '@/lib/format';

type MonthlyPoint = {
    month: number;
    /** null untuk bulan yang belum berjalan */
    ton: number | null;
};

const props = defineProps<{
    points: MonthlyPoint[];
    peakMonth: number | null;
}>();

const uid = useId();
const gradientId = `area-${uid}`;

// Grafik digambar 1:1 dengan lebar kartu (satuan = piksel), jadi huruf tetap terbaca di HP.
const root = ref<HTMLElement | null>(null);
const width = ref(800);
let observer: ResizeObserver | null = null;

onMounted(() => {
    if (!root.value) {
        return;
    }

    width.value = Math.round(root.value.clientWidth) || 800;
    observer = new ResizeObserver(([entry]) => {
        width.value = Math.round(entry.contentRect.width) || width.value;
    });
    observer.observe(root.value);
});
onBeforeUnmount(() => observer?.disconnect());

const compact = computed(() => width.value < 520);
const W = computed(() => Math.max(width.value, 260));
const H = computed(() => (compact.value ? 260 : 330));
const pad = computed(() =>
    compact.value ? { left: 42, right: 14, top: 34, bottom: 34 } : { left: 56, right: 24, top: 40, bottom: 42 },
);
const plotW = computed(() => W.value - pad.value.left - pad.value.right);
const plotH = computed(() => H.value - pad.value.top - pad.value.bottom);

function niceStep(raw: number): number {
    if (raw <= 0) {
        return 1;
    }

    const pow = 10 ** Math.floor(Math.log10(raw));
    const f = raw / pow;
    const m = f <= 1 ? 1 : f <= 2 ? 2 : f <= 2.5 ? 2.5 : f <= 5 ? 5 : 10;

    return m * pow;
}

const max = computed(() => Math.max(...props.points.map((p) => p.ton ?? 0), 0));
const step = computed(() => niceStep(max.value / 4));
const top = computed(() => Math.max(step.value, Math.ceil(max.value / step.value) * step.value));
const ticks = computed(() => Array.from({ length: Math.round(top.value / step.value) + 1 }, (_, i) => i * step.value));

const x = (month: number) => pad.value.left + (plotW.value * (month - 1)) / 11;
const y = (ton: number) => pad.value.top + plotH.value * (1 - ton / top.value);
const baseline = computed(() => pad.value.top + plotH.value);

const filled = computed(() => props.points.filter((p): p is MonthlyPoint & { ton: number } => p.ton !== null));

const linePath = computed(() => filled.value.map((p, i) => `${i === 0 ? 'M' : 'L'}${x(p.month)},${y(p.ton)}`).join(' '));
const areaPath = computed(() => {
    const first = filled.value[0];
    const last = filled.value[filled.value.length - 1];

    return first && last ? `${linePath.value} L${x(last.month)},${baseline.value} L${x(first.month)},${baseline.value} Z` : '';
});

const hovered = ref<number | null>(null);
const hoveredPoint = computed(() => filled.value.find((p) => p.month === hovered.value) ?? null);

const tooltip = computed(() => {
    const point = hoveredPoint.value;

    if (!point) {
        return null;
    }

    const width = compact.value ? 92 : 104;
    const height = 46;
    const px = Math.min(Math.max(x(point.month) - width / 2, pad.value.left), W.value - pad.value.right - width);
    const above = y(point.ton) - height - 14;

    return { x: px, y: above < 4 ? y(point.ton) + 16 : above, width, height, month: BULAN_PANJANG[point.month - 1], ton: point.ton };
});

// Di layar sempit, label bulan ditampilkan selang-seling agar tidak berhimpitan.
const showMonthLabel = (month: number) => !compact.value || month % 2 === 1;
</script>

<template>
    <div ref="root" class="w-full">
        <svg :viewBox="`0 0 ${W} ${H}`" :width="W" :height="H" class="block max-w-full" role="img" aria-label="Grafik garis penyerapan gabah per bulan">
            <defs>
                <linearGradient :id="gradientId" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#1F4E8C" stop-opacity="0.28" />
                    <stop offset="100%" stop-color="#1F4E8C" stop-opacity="0" />
                </linearGradient>
            </defs>

            <g>
                <line
                    v-for="tick in ticks"
                    :key="tick"
                    :x1="pad.left"
                    :x2="W - pad.right"
                    :y1="y(tick)"
                    :y2="y(tick)"
                    stroke="#DCE3EE"
                    stroke-dasharray="4 5"
                />
                <text
                    v-for="tick in ticks"
                    :key="`t-${tick}`"
                    :x="pad.left - 12"
                    :y="y(tick) + 5"
                    text-anchor="end"
                    class="fill-[#5B6B82]" :class="compact ? 'text-[11px]' : 'text-[13px]'"
                >
                    {{ formatAngka(tick, 1) }}
                </text>
            </g>

            <path v-if="areaPath" :d="areaPath" :fill="`url(#${gradientId})`" />
            <path v-if="linePath" :d="linePath" fill="none" stroke="#1F4E8C" stroke-width="3.5" stroke-linejoin="round" stroke-linecap="round" />

            <line
                v-if="hoveredPoint"
                :x1="x(hoveredPoint.month)"
                :x2="x(hoveredPoint.month)"
                :y1="pad.top"
                :y2="baseline"
                stroke="#1F4E8C"
                stroke-opacity="0.25"
                stroke-dasharray="3 4"
            />

            <g v-for="point in filled" :key="point.month">
                <circle
                    v-if="point.month === peakMonth"
                    :cx="x(point.month)"
                    :cy="y(point.ton)"
                    :r="hovered === point.month ? 8.5 : 7"
                    fill="#F5A623"
                    stroke="#fff"
                    stroke-width="2.5"
                />
                <circle
                    v-else
                    :cx="x(point.month)"
                    :cy="y(point.ton)"
                    :r="hovered === point.month ? 7 : 5.5"
                    fill="#fff"
                    stroke="#1F4E8C"
                    stroke-width="3"
                />
                <text
                    v-if="point.month === peakMonth && hovered !== point.month"
                    :x="x(point.month)"
                    :y="y(point.ton) - 16"
                    text-anchor="middle"
                    class="fill-[#9A6200] font-bold" :class="compact ? 'text-[12px]' : 'text-[15px]'"
                >
                    {{ formatTon(point.ton) }}
                </text>
            </g>

            <template v-for="point in points" :key="`m-${point.month}`">
                <text
                    v-if="showMonthLabel(point.month)"
                    :x="x(point.month)"
                    :y="H - (compact ? 10 : 12)"
                    text-anchor="middle"
                    :class="[compact ? 'text-[11px]' : 'text-[14px]', point.ton === null ? 'fill-[#B5BDCD]' : 'fill-[#3F4F69]']"
                >
                    {{ BULAN_PENDEK[point.month - 1] }}
                </text>
            </template>

            <!-- Area sentuh/hover per bulan -->
            <rect
                v-for="point in filled"
                :key="`h-${point.month}`"
                :x="x(point.month) - plotW / 22"
                :y="pad.top - 10"
                :width="plotW / 11"
                :height="plotH + 10"
                fill="transparent"
                @pointerenter="hovered = point.month"
                @pointerdown="hovered = point.month"
                @pointerleave="hovered = null"
            />

            <g v-if="tooltip" pointer-events="none">
                <rect :x="tooltip.x" :y="tooltip.y" :width="tooltip.width" :height="tooltip.height" rx="10" fill="#12305C" />
                <text :x="tooltip.x + tooltip.width / 2" :y="tooltip.y + 19" text-anchor="middle" class="fill-white/70 text-[12px]">
                    {{ tooltip.month }}
                </text>
                <text :x="tooltip.x + tooltip.width / 2" :y="tooltip.y + 37" text-anchor="middle" class="fill-white text-[15px] font-bold">
                    {{ formatTon(tooltip.ton) }} ton
                </text>
            </g>
        </svg>

        <table class="sr-only">
            <caption>
                Penyerapan gabah per bulan (ton)
            </caption>
            <thead>
                <tr>
                    <th scope="col">Bulan</th>
                    <th scope="col">Penyerapan (ton)</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="point in filled" :key="point.month">
                    <th scope="row">{{ BULAN_PANJANG[point.month - 1] }}</th>
                    <td>{{ formatTon(point.ton) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
