<?php

namespace App\Enums;

enum AttachmentCategory: string
{
    case Video = 'video';
    case FotoGabah = 'foto_gabah';
    case FotoMitra = 'foto_mitra';
    case FotoKtp = 'foto_ktp';

    public function type(): string
    {
        return $this === self::Video ? 'video' : 'photo';
    }

    public function label(): string
    {
        return match ($this) {
            self::Video => 'Video penyerapan',
            self::FotoGabah => 'Foto gabah',
            self::FotoMitra => 'Foto bersama mitra',
            self::FotoKtp => 'Foto KTP mitra',
        };
    }
}
