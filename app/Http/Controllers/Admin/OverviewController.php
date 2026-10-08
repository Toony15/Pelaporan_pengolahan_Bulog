<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkBook;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OverviewController extends Controller
{
    /** Jumlah hari ke belakang untuk daftar "Laporan terbaru". */
    private const RECENT_DAYS = 3;

    /** Maksimal baris yang ditampilkan pada "Laporan terbaru". */
    private const RECENT_LIMIT = 5;

    public function home(Request $request): Response
    {
        abort_unless(in_array($request->user()->role, [Role::Admin, Role::Manager], true), 403);

        $now = now();
        $month = $now->month;

        $current = $this->monthlyTons($now->year);
        $previous = $this->monthlyTons($now->year - 1);

        $total = array_sum(array_slice($current, 0, $month));
        $previousTotal = array_sum(array_slice($previous, 0, $month));
        $change = $previousTotal > 0 ? round((($total - $previousTotal) / $previousTotal) * 100, 1) : null;

        $pics = User::query()->where('role', Role::Pic->value);
        $picTotal = (clone $pics)->count();
        $picActive = (clone $pics)->where('is_active', true)->count();

        $reports = WorkBook::query()->get(['mitra_pengolahan', 'regency']);

        $recentQuery = WorkBook::query()->where('created_at', '>=', $now->copy()->subDays(self::RECENT_DAYS));
        $recentTotal = (clone $recentQuery)->count();
        $recent = $recentQuery->latest()->latest('id')->limit(self::RECENT_LIMIT)->get()
            ->map(fn (WorkBook $wb) => [
                'id' => $wb->id,
                'mitra_pengolahan' => $wb->mitra_pengolahan,
                'pic_name' => $wb->pic_name,
                'regency' => $wb->regency,
                'ton' => round((float) $wb->absorption_kg / 1000, 3),
                'date' => $wb->created_at->toDateString(),
            ]);

        return Inertia::render('Admin/Home', [
            'period' => ['year' => $now->year, 'month' => $month],
            'total' => round($total, 3),
            'change' => $change,
            'monthly' => array_slice($current, 0, $month),
            'pics' => [
                'total' => $picTotal,
                'active' => $picActive,
                'inactive' => $picTotal - $picActive,
            ],
            'partners' => [
                'total' => $this->distinct($reports->pluck('mitra_pengolahan')),
                'regions' => $this->distinct($reports->pluck('regency')),
            ],
            'recent' => $recent,
            'recentTotal' => $recentTotal,
            'recentDays' => self::RECENT_DAYS,
        ]);
    }

    /**
     * Total penyerapan per bulan (ton) untuk satu tahun, 12 nilai Januari-Desember.
     *
     * @return array<int, float>
     */
    private function monthlyTons(int $year): array
    {
        $months = array_fill(0, 12, 0.0);

        WorkBook::query()
            ->whereYear('absorption_date', $year)
            ->get(['absorption_date', 'absorption_kg'])
            ->each(function (WorkBook $wb) use (&$months) {
                $months[$wb->absorption_date->month - 1] += (float) $wb->absorption_kg / 1000;
            });

        return array_map(fn (float $value) => round($value, 3), $months);
    }

    /** Jumlah nilai unik (tanpa membedakan huruf besar/kecil dan spasi tepi). */
    private function distinct(Collection $values): int
    {
        return $values
            ->map(fn ($value) => Str::lower(trim((string) $value)))
            ->filter()
            ->unique()
            ->count();
    }
}
