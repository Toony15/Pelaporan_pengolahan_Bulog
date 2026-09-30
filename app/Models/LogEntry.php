<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogEntry extends Model
{
    protected $fillable = [
        'entry_date', 'description', 'volume_kg', 'moisture_percent', 'issue',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'volume_kg' => 'decimal:2',
            'moisture_percent' => 'decimal:2',
        ];
    }

    public function workBook(): BelongsTo
    {
        return $this->belongsTo(WorkBook::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}