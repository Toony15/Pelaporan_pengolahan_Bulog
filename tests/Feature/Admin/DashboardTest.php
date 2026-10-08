<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function dashboardUser(Role $role): User
{
    return User::factory()->create(['role' => $role]);
}

function dashboardReport(User $pic, string $date, float $kg, string $regency = 'Seluma'): void
{
    $pic->workBooks()->create([
        'pic_name' => $pic->name,
        'mitra_pengolahan' => 'UD Gerlong',
        'village' => 'Desa Makmur',
        'regency' => $regency,
        'absorption_kg' => $kg,
        'absorption_date' => $date,
    ]);
}

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-10-07 10:00:00'));
});

test('admin and manager can open the dashboard', function (Role $role) {
    $this->actingAs(dashboardUser($role))->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Admin/Dashboard'));
})->with([Role::Admin, Role::Manager]);

test('pic cannot open the dashboard', function () {
    $this->actingAs(dashboardUser(Role::Pic))->get(route('admin.dashboard'))->assertForbidden();
});

test('guests are redirected to login', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('info cards and chart data are calculated for the current year', function () {
    $pic = dashboardUser(Role::Pic);

    dashboardReport($pic, '2026-05-10', 30000, 'Seluma');
    dashboardReport($pic, '2026-02-10', 10000, 'seluma ');
    dashboardReport($pic, '2026-09-10', 10000, 'Kaur');
    dashboardReport($pic, '2025-06-10', 5000, 'Lebong');

    $this->actingAs(dashboardUser(Role::Admin))->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('year', 2026)
            ->where('years', [2025, 2026])
            ->where('monthsCounted', 10)
            ->where('inProgress', true)
            ->where('stats.totalTon', 50)
            ->where('stats.averageTon', 5)
            ->where('stats.peak.month', 5)
            ->where('stats.peak.ton', 30)
            // "Seluma" dan "seluma " dianggap satu wilayah.
            ->where('stats.topRegion.name', 'Seluma')
            ->where('stats.topRegion.ton', 40)
            ->where('stats.topRegion.percent', 80)
            ->has('regions', 2)
            ->has('monthly', 12)
            ->where('monthly.4.ton', 30)
            // Bulan yang belum berjalan tidak punya nilai.
            ->where('monthly.9.ton', 0)
            ->where('monthly.10.ton', null)
            ->where('monthly.11.ton', null));
});

test('a previous year is shown in full and can be selected', function () {
    $pic = dashboardUser(Role::Pic);
    dashboardReport($pic, '2026-05-10', 30000);
    dashboardReport($pic, '2025-06-10', 6000, 'Lebong');

    $this->actingAs(dashboardUser(Role::Manager))->get(route('admin.dashboard', ['year' => 2025]))
        ->assertInertia(fn (Assert $page) => $page
            ->where('year', 2025)
            ->where('monthsCounted', 12)
            ->where('inProgress', false)
            ->where('stats.totalTon', 6)
            ->where('stats.averageTon', 0.5)
            ->where('stats.peak.month', 6)
            ->where('stats.topRegion.name', 'Lebong')
            ->where('monthly.11.ton', 0));
});

test('an unknown year falls back to the current year', function () {
    $this->actingAs(dashboardUser(Role::Admin))->get(route('admin.dashboard', ['year' => 1999]))
        ->assertInertia(fn (Assert $page) => $page->where('year', 2026));
});

test('the dashboard works without any reports', function () {
    $this->actingAs(dashboardUser(Role::Admin))->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('stats.totalTon', 0)
            ->where('stats.averageTon', 0)
            ->where('stats.peak', null)
            ->where('stats.topRegion', null)
            ->has('regions', 0));
});

test('regions are ordered from highest to lowest', function () {
    $pic = dashboardUser(Role::Pic);
    dashboardReport($pic, '2026-01-10', 1000, 'Kaur');
    dashboardReport($pic, '2026-02-10', 3000, 'Mukomuko');
    dashboardReport($pic, '2026-03-10', 2000, 'Lebong');

    $this->actingAs(dashboardUser(Role::Admin))->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('regions', fn ($regions) => collect($regions)->pluck('name')->all() === ['Mukomuko', 'Lebong', 'Kaur']));
});
