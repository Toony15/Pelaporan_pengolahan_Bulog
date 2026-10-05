<?php

namespace App\Http\Requests;

use App\Enums\AttachmentCategory;
use App\Models\WorkBook;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', WorkBook::class);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'pic_name' => ['required', 'string', 'max:255'],
            'mitra_pengolahan' => ['required', 'string', 'max:255'],
            'kancab' => ['required', 'string', 'max:255'],
            'kanwil' => ['required', 'string', 'max:255'],
            'absorption_date' => ['required', 'date'],
            ...$this->fileRules(),
        ];
    }

    /** Saat membuat baru, keempat bukti wajib diunggah. */
    protected function fileRules(): array
    {
        $presence = $this->filePresenceRule();

        return [
            'video' => [$presence, 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo,video/x-matroska,video/3gpp', 'max:204800'],
            'foto_gabah' => [$presence, 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'foto_mitra' => [$presence, 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'foto_ktp' => [$presence, 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    protected function filePresenceRule(): string
    {
        return 'required';
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        $messages = [];

        foreach (['pic_name' => 'Nama PIC', 'mitra_pengolahan' => 'Nama mitra pengolahan', 'kancab' => 'Kancab', 'kanwil' => 'Kanwil', 'absorption_date' => 'Tanggal penyerapan'] as $field => $label) {
            $messages["$field.required"] = "$label wajib diisi.";
            $messages["$field.max"] = "$label maksimal 255 karakter.";
        }
        $messages['absorption_date.date'] = 'Tanggal penyerapan tidak valid.';

        foreach (AttachmentCategory::cases() as $category) {
            $label = $category->label();
            $field = $category->value;
            $messages["$field.required"] = "$label wajib diunggah.";
            $messages["$field.uploaded"] = "$label gagal diunggah. Ukuran file kemungkinan melebihi batas server.";
            $messages["$field.image"] = "$label harus berupa gambar.";
            $messages["$field.mimes"] = "$label harus berformat JPG, PNG, atau WEBP.";
            $messages["$field.mimetypes"] = "$label harus berformat video (MP4, MOV, WEBM, AVI, MKV).";
            $messages["$field.max"] = $category->type() === 'video'
                ? "$label maksimal 200 MB."
                : "$label maksimal 10 MB.";
        }

        return $messages;
    }
}
