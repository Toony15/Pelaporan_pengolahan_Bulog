<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\OverviewController;
use Illuminate\Http\Request;
use Inertia\Response;

/** Beranda: PIC melihat laporannya sendiri, Admin/Manager melihat ringkasan. */
class HomeController extends Controller
{
    public function __invoke(
        Request $request,
        WorkBookController $workBooks,
        OverviewController $overview,
    ): Response {
        return $request->user()->isPic()
            ? $workBooks->index()
            : $overview->home($request);
    }
}
