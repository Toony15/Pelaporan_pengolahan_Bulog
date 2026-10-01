<?php

use App\Http\Controllers\WorkBookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::resource('work-books', WorkBookController::class);
});

require __DIR__.'/settings.php';