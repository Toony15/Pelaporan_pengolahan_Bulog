<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreWorkBookRequest;
use App\Http\Requests\UpdateWorkBookRequest;
use App\Models\WorkBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class WorkBookController extends Controller
{
    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $workBooks = $user->role === Role::Pic
            ? $user->workBooks()->latest('start_date')->get()
            : WorkBook::with('user:id,name')->latest('start_date')->get();

       return Inertia::render('WorkBooks/Index', [
            'workBooks' => $workBooks,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', WorkBook::class);

        return Inertia::render('WorkBooks/Create', [
            'defaultPicName' => Auth::user()->name,
        ]);
    }

    public function store(StoreWorkBookRequest $request): RedirectResponse
    {
        $workBook = Auth::user()->workBooks()->create($request->validated());

        return to_route('work-books.index')
            ->with('success', 'Buku kerja berhasil dibuat.');
    }

    public function show(WorkBook $workBook): Response
    {
        $this->authorize('view', $workBook);

        $workBook->load(['entries' => function ($query) {
            $query->latest('entry_date')->with('attachments');
        }, 'user:id,name']);

        return Inertia::render('WorkBooks/Show', [
            'workBook' => $workBook,
            'canManage' => Auth::user()->id === $workBook->user_id,
        ]);
    }

    public function edit(WorkBook $workBook): Response
    {
        $this->authorize('update', $workBook);

       return Inertia::render('WorkBooks/Edit', [
            'workBook' => $workBook,
        ]);
    }

    public function update(UpdateWorkBookRequest $request, WorkBook $workBook): RedirectResponse
    {
        $workBook->update($request->validated());

        return to_route('work-books.show', $workBook)
            ->with('success', 'Perubahan disimpan.');
    }

    public function destroy(WorkBook $workBook): RedirectResponse
    {
        $this->authorize('delete', $workBook);

        $workBook->delete();

        return to_route('work-books.index')
            ->with('success', 'Buku kerja dihapus.');
    }
}