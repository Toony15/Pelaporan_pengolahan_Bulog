<?php

namespace App\Http\Requests;

class UpdateWorkBookRequest extends StoreWorkBookRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('work_book'));
    }

    /** Saat mengedit, file hanya diunggah bila ingin mengganti yang lama. */
    protected function filePresenceRule(): string
    {
        return 'nullable';
    }
}
