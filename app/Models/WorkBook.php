<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $pic_name
 * @property string $mitra_pengolahan
 * @property string $village
 * @property string $regency
 * @property string $absorption_kg
 * @property Carbon $absorption_date
 */
class WorkBook extends Model
{
    protected $fillable = [
        'pic_name', 'mitra_pengolahan', 'village', 'regency', 'absorption_kg', 'absorption_date',
    ];

    protected function casts(): array
    {
        return [
            'absorption_kg' => 'decimal:2',
            'absorption_date' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}