<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\WorkBookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [WorkBookController::class, 'index'])->name('dashboard');
    Route::resource('work-books', WorkBookController::class)->except('index');
    Route::get('attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
});

require __DIR__.'/settings.php';