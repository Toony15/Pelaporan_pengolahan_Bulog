<?php

use App\Enums\Role;
use App\Models\Attachment;
use App\Models\User;
use App\Models\WorkBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function logbookPayload(array $overrides = []): array
{
    return array_merge([
        'pic_name' => 'Fathoni',
        'mitra_pengolahan' => 'Mira',
        'village' => 'Desa Makmur',
        'regency' => 'Bengkulu Utara',
        'absorption_kg' => 10000,
        'absorption_date' => '2026-09-25',
        'video' => UploadedFile::fake()->create('penyerapan.mp4', 1024, 'video/mp4'),
        'foto_gabah' => UploadedFile::fake()->create('gabah.jpg', 100, 'image/jpeg'),
        'foto_mitra' => UploadedFile::fake()->create('mitra.jpg', 100, 'image/jpeg'),
        'foto_ktp' => UploadedFile::fake()->create('ktp.jpg', 100, 'image/jpeg'),
    ], $overrides);
}

function createLogbook(User $pic): WorkBook
{
    test()->actingAs($pic)->post(route('work-books.store'), logbookPayload())->assertRedirect();

    return WorkBook::latest('id')->first();
}

beforeEach(fn () => Storage::fake('local'));

test('pic can create a logbook with evidence', function () {
    $pic = User::factory()->create();

    $this->actingAs($pic)->post(route('work-books.store'), logbookPayload())->assertRedirect();

    $workBook = WorkBook::first();
    expect($workBook->user_id)->toBe($pic->id)
        ->and($workBook->attachments)->toHaveCount(4)
        ->and($workBook->attachments->where('type', 'video'))->toHaveCount(1)
        ->and($workBook->attachments->where('type', 'photo'))->toHaveCount(3);

    Storage::disk('local')->assertExists($workBook->attachments->first()->path);
});

test('all four evidence files are required on create', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('work-books.store'), [
            'pic_name' => 'Fathoni', 'mitra_pengolahan' => 'Mira', 'village' => 'A', 'regency' => 'B', 'absorption_kg' => 100, 'absorption_date' => '2026-09-25',
        ])
        ->assertSessionHasErrors(['video', 'foto_gabah', 'foto_mitra', 'foto_ktp']);
});

test('pic can edit a logbook and replace a file', function () {
    $pic = User::factory()->create();
    $workBook = createLogbook($pic);
    $oldPath = $workBook->attachments()->where('category', 'foto_gabah')->value('path');

    $this->actingAs($pic)->put(route('work-books.update', $workBook), [
        'pic_name' => 'Fathoni', 'mitra_pengolahan' => 'Mitra Baru', 'village' => 'Desa Makmur',
        'regency' => 'Bengkulu Utara', 'absorption_kg' => 2500.5, 'absorption_date' => '2026-09-26',
        'foto_gabah' => UploadedFile::fake()->create('baru.jpg', 100, 'image/jpeg'),
    ])->assertRedirect(route('work-books.show', $workBook));

    expect($workBook->fresh()->mitra_pengolahan)->toBe('Mitra Baru')
        ->and($workBook->attachments()->count())->toBe(4);
    Storage::disk('local')->assertMissing($oldPath);
});

test('pic cannot access another pic logbook', function () {
    $workBook = createLogbook(User::factory()->create());
    $other = User::factory()->create();

    $this->actingAs($other)->get(route('work-books.show', $workBook))->assertForbidden();
    $this->actingAs($other)->get(route('work-books.edit', $workBook))->assertForbidden();
    $this->actingAs($other)->delete(route('work-books.destroy', $workBook))->assertForbidden();
    $this->actingAs($other)->get(route('attachments.show', $workBook->attachments->first()))->assertForbidden();
});

test('admin and manager can view but not change logbooks', function (Role $role) {
    $workBook = createLogbook(User::factory()->create());
    $viewer = User::factory()->create(['role' => $role]);

    $this->actingAs($viewer)->get(route('dashboard'))->assertOk();
    $this->actingAs($viewer)->get(route('work-books.show', $workBook))->assertOk();
    $this->actingAs($viewer)->get(route('attachments.show', $workBook->attachments->first()))->assertOk();
    $this->actingAs($viewer)->get(route('work-books.create'))->assertForbidden();
    $this->actingAs($viewer)->delete(route('work-books.destroy', $workBook))->assertForbidden();
})->with([Role::Admin, Role::Manager]);

test('pic can delete a logbook and its files', function () {
    $pic = User::factory()->create();
    $workBook = createLogbook($pic);
    $path = $workBook->attachments->first()->path;

    $this->actingAs($pic)->delete(route('work-books.destroy', $workBook))->assertRedirect(route('dashboard'));

    expect(WorkBook::count())->toBe(0)->and(Attachment::count())->toBe(0);
    Storage::disk('local')->assertMissing($path);
});

test('guests are redirected to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});
