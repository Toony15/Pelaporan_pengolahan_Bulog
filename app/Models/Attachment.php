<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = ['category', 'type', 'path', 'original_name', 'mime_type', 'size'];

    protected $appends = ['url'];

    public function workBook(): BelongsTo
    {
        return $this->belongsTo(WorkBook::class);
    }

    /** File disimpan privat; diakses lewat route yang memeriksa hak akses. */
    public function getUrlAttribute(): string
    {
        return route('attachments.show', $this->id, false);
    }
}
