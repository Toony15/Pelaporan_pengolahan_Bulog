<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    /** Menampilkan file bukti hanya untuk pemilik (PIC) atau Admin/Manager. */
    public function show(Attachment $attachment): BinaryFileResponse
    {
        $this->authorize('view', $attachment->workBook);

        $path = Storage::disk('local')->path($attachment->path);
        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => $attachment->mime_type,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
