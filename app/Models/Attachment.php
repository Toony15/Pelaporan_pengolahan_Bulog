<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = ['type', 'path', 'original_name', 'mime_type', 'size'];

    public function logEntry(): BelongsTo
    {
        return $this->belongsTo(LogEntry::class);
    }
}