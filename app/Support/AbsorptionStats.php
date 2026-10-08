<?php

namespace App\Support;

use App\Models\WorkBook;
use Illuminate\Support\Str;

/** Rekap penyerapan gabah (kg) per bulan dan per kota/kabupaten, dipakai Beranda dan Dashboard admin. */
class AbsorptionStats
{
    /**
     * @return array{months: array<int, float>, regions: array<string, array{name: string, kg: float}>}
     */
    public function forYear(int $year): array
    {
        $months = array_fill(1, 12, 0.0);
        $regions = [];

        WorkBook::query()
            ->whereBetween('absorption_date', ["{$year}-01-01", "{$year}-12-31"])
            ->get(['absorption_date', 'absorption_kg', 'regency'])
            ->each(function (WorkBook $workBook) use (&$months, &$regions) {
                $kg = (float) $workBook->absorption_kg;
                $months[$workBook->absorption_date->month] += $kg;

                $name = trim((string) $workBook->regency);
                $key = Str::lower($name);

                $regions[$key] ??= ['name' => $name === '' ? 'Tanpa wilayah' : Str::title($name), 'kg' => 0.0];
                $regions[$key]['kg'] += $kg;
            });

        uasort($regions, fn (array $a, array $b) => $b['kg'] <=> $a['kg']);

        return ['months' => $months, 'regions' => $regions];
    }

    /**
     * Tahun yang punya data, ditambah tahun berjalan, urut naik.
     *
     * @return list<int>
     */
    public function years(int $currentYear): array
    {
        $years = WorkBook::query()
            ->pluck('absorption_date')
            ->map(fn ($date) => $date->year)
            ->push($currentYear)
            ->unique()
            ->sort()
            ->values();

        return $years->all();
    }

    /** Jumlah kg dari Januari sampai bulan ke-$month. */
    public function sumUntil(array $months, int $month): float
    {
        $total = 0.0;

        for ($m = 1; $m <= $month; $m++) {
            $total += $months[$m] ?? 0.0;
        }

        return $total;
    }

    public static function ton(float $kg, int $precision = 3): float
    {
        return round($kg / 1000, $precision);
    }
}
