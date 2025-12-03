<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JenisRincian: string implements HasLabel
{
    case INTERNAL = 'internal';
    case EKSTERNAL = 'eksternal';

    public function getLabel(): string
    {
        return match($this) {
            self::INTERNAL => 'Internal',
            self::EKSTERNAL => 'Eksternal',
        };
    }
}
