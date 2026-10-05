<?php

namespace App\Http\Controllers;

use App\Enums\AttachmentCategory;
use App\Http\Requests\StoreWorkBookRequest;
use App\Http\Requests\UpdateWorkBookRequest;
use App\Models\User;
use App\Models\WorkBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class WorkBookController extends Controller
{
    private const FIELDS = ['pic_name', 'mitra_pengolahan', 'kancab', 'kanwil', 'absorption_date'];

    /** Beranda: PIC melihat miliknya, Admin/Manager melihat semua. */
    public function index(): Response
    {
        $this->authorize('viewAny', WorkBook::class);

        $user = $this->currentUser();
        $query = $user->isPic() ? $user->workBooks() : WorkBook::query();

        $workBooks = $query
            ->with(['attachments:id,work_book_id,category,type'])
            ->latest('absorption_date')
            ->latest('id')
            ->get()
            ->map(fn (WorkBook $wb) => [
                'id' => $wb->id,
                'pic_name' => $wb->pic_name,
                'mitra_pengolahan' => $wb->mitra_pengolahan,
                'absorption_date' => $wb->absorption_date->toDateString(),
                'video_count' => $wb->attachments->where('type', 'video')->count(),
                'photo_count' => $wb->attachments->where('type', 'photo')->count(),
                'cover_url' => ($cover = $wb->attachments->firstWhere('category', AttachmentCategory::FotoMitra->value))
                    ? route('attachments.show', $cover->id, false)
                    : null,
            ]);

        return Inertia::render('WorkBooks/Index', [
            'workBooks' => $workBooks,
            'canCreate' => $user->can('create', WorkBook::class),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', WorkBook::class);

        return Inertia::render('WorkBooks/Create', [
            'defaultPicName' => $this->currentUser()->name,
        ]);
    }

    public function store(StoreWorkBookRequest $request): RedirectResponse
    {
        $workBook = DB::transaction(function () use ($request) {
            $workBook = $this->currentUser()->workBooks()->create($request->safe()->only(self::FIELDS));
            $this->saveFiles($workBook, $request);

            return $workBook;
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Data berhasil ditambahkan.',
        ]);

        return to_route('dashboard');
    }

    public function show(WorkBook $workBook): Response
    {
        $this->authorize('view', $workBook);

        return Inertia::render('WorkBooks/Show', [
            'workBook' => $this->present($workBook),
            'canManage' => $this->currentUser()->can('update', $workBook),
        ]);
    }

    public function edit(WorkBook $workBook): Response
    {
        $this->authorize('update', $workBook);

        return Inertia::render('WorkBooks/Edit', [
            'workBook' => $this->present($workBook),
        ]);
    }

    public function update(UpdateWorkBookRequest $request, WorkBook $workBook): RedirectResponse
    {
        DB::transaction(function () use ($request, $workBook) {
            $workBook->update($request->safe()->only(self::FIELDS));
            $this->saveFiles($workBook, $request);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Data berhasil diperbarui.']);

        return to_route('work-books.show', $workBook);
    }

    public function destroy(WorkBook $workBook): RedirectResponse
    {
        $this->authorize('delete', $workBook);

        $workBook->delete();
        Storage::disk('local')->deleteDirectory("work-books/{$workBook->id}");

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Data berhasil dihapus.']);

        return to_route('dashboard');
    }

    /** Pengguna yang sedang login (diberi tipe User agar dikenali editor). */
    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    /** Menyimpan file yang diunggah; file lama pada kategori yang sama diganti. */
    private function saveFiles(WorkBook $workBook, Request $request): void
    {
        foreach (AttachmentCategory::cases() as $category) {
            $file = $request->file($category->value);

            if (! $file) {
                continue;
            }

            $old = $workBook->attachments()->where('category', $category->value)->first();
            $path = $file->store("work-books/{$workBook->id}", 'local');

            $workBook->attachments()->updateOrCreate(
                ['category' => $category->value],
                [
                    'type' => $category->type(),
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                    'size' => $file->getSize(),
                ],
            );

            if ($old) {
                Storage::disk('local')->delete($old->path);
            }
        }
    }

    /** Data buku kerja untuk halaman detail dan edit. */
    private function present(WorkBook $workBook): array
    {
        $workBook->load('attachments');

        return [
            'id' => $workBook->id,
            'pic_name' => $workBook->pic_name,
            'mitra_pengolahan' => $workBook->mitra_pengolahan,
            'kancab' => $workBook->kancab,
            'kanwil' => $workBook->kanwil,
            'absorption_date' => $workBook->absorption_date->toDateString(),
            'attachments' => $workBook->attachments->mapWithKeys(fn ($a) => [
                $a->category => [
                    'url' => $a->url,
                    'name' => $a->original_name,
                    'size' => $a->size,
                    'type' => $a->type,
                ],
            ]),
        ];
    }
}