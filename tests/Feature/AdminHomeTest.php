<?php

use App\Enums\Role;
use App\Models\User;
use App\Models\WorkBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function makeReport(User $pic, array $overrides = []): WorkBook
{
    return $pic->workBooks()->create(array_merge([
        'pic_name' => $pic->name,
        'mitra_pengolahan' => 'UD Gerlong',
        'village' => 'Desa Makmur',
        'regency' => 'Seluma',
        'absorption_kg' => 5000,
        'absorption_date' => now()->toDateString(),
    ], $overrides));
}

test('admin and manager see the admin home', function (Role $role) {
    $viewer = User::factory()->create(['role' => $role]);

    $this->actingAs($viewer)->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Admin/Home'));
})->with([Role::Admin, Role::Manager]);

test('pic still sees the pic home', function () {
    $this->actingAs(User::factory()->create())->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('WorkBooks/Index'));
});

test('recent reports only include the last three days', function () {
    $pic = User::factory()->create();
    $old = makeReport($pic, ['mitra_pengolahan' => 'Lama']);
    WorkBook::query()->whereKey($old->id)->update(['created_at' => now()->subDays(5)]);
    makeReport($pic, ['mitra_pengolahan' => 'Baru']);

    $this->actingAs(User::factory()->create(['role' => Role::Admin]))->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Home')
            ->has('recent', 1)
            ->where('recent.0.mitra_pengolahan', 'Baru')
            ->where('recentTotal', 1));
});

test('summary counts pics, partners, regions and total tons', function () {
    $pic = User::factory()->create();
    User::factory()->create(['is_active' => false]);
    makeReport($pic, ['mitra_pengolahan' => 'UD A', 'regency' => 'Seluma', 'absorption_kg' => 5000]);
    makeReport($pic, ['mitra_pengolahan' => 'ud a ', 'regency' => 'Kaur', 'absorption_kg' => 2500]);
    makeReport($pic, ['mitra_pengolahan' => 'UD B', 'regency' => 'Kaur', 'absorption_kg' => 2500]);

    $this->actingAs(User::factory()->create(['role' => Role::Admin]))->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('total', fn ($total) => (float) $total === 10.0)
            ->where('pics.total', 2)
            ->where('pics.active', 1)
            ->where('pics.inactive', 1)
            ->where('partners.total', 2)
            ->where('partners.regions', 2));
});
