<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Support\AbsorptionStats;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Dashboard penyerapan: kartu info, grafik per bulan, dan penyerapan per kota/kabupaten. */
class DashboardController extends Controller
{
    public function __invoke(Request $request, AbsorptionStats $stats): Response
    {
        abort_unless(in_array($request->user()->role, [Role::Admin, Role::Manager], true), 403);

        $now = now();
        $years = $stats->years($now->year);

        $year = $request->integer('year', $now->year);
        $year = in_array($year, $years, true) ? $year : $now->year;

        // Tahun berjalan dihitung sampai bulan ini saja; tahun-tahun lalu penuh 12 bulan.
        $monthsCounted = $year === $now->year ? $now->month : 12;

        $data = $stats->forYear($year);
        $totalKg = $stats->sumUntil($data['months'], $monthsCounted);

        $peakMonth = null;
        for ($m = 1; $m <= $monthsCounted; $m++) {
            if ($data['months'][$m] > 0 && ($peakMonth === null || $data['months'][$m] > $data['months'][$peakMonth])) {
                $peakMonth = $m;
            }
        }

        $regions = collect($data['regions'])
            ->filter(fn (array $region) => $region['kg'] > 0)
            ->map(fn (array $region) => [
                'name' => $region['name'],
                'ton' => AbsorptionStats::ton($region['kg'], 1),
                'percent' => $totalKg > 0 ? round($region['kg'] / $totalKg * 100, 1) : 0,
            ])
            ->values();

        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly[] = [
                'month' => $m,
                // null = bulan yang belum berjalan, tidak digambar di grafik.
                'ton' => $m <= $monthsCounted ? AbsorptionStats::ton($data['months'][$m], 1) : null,
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'year' => $year,
            'years' => $years,
            'monthsCounted' => $monthsCounted,
            'inProgress' => $year === $now->year,
            'stats' => [
                'totalTon' => AbsorptionStats::ton($totalKg, 1),
                'averageTon' => AbsorptionStats::ton($totalKg / $monthsCounted, 1),
                'peak' => $peakMonth === null ? null : [
                    'month' => $peakMonth,
                    'ton' => AbsorptionStats::ton($data['months'][$peakMonth], 1),
                ],
                'topRegion' => $regions->first(),
            ],
            'monthly' => $monthly,
            'regions' => $regions->all(),
        ]);
    }
}
