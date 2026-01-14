<?php

namespace App\Enums;

enum JenisProject: string implements \Filament\Support\Contracts\HasLabel
{
    case WEBAPP = 'webapp';
    case BARANG = 'barang';
    case PERORANGAN = 'perorangan';

    public function getLabel(): string
    {
        return match ($this) {
            self::WEBAPP => 'WEBAPP (Jasa)',
            self::BARANG => 'BARANG',
            self::PERORANGAN => 'PERORANGAN',
        };
    }

    public function getPph(): int
    {
        return match ($this) {
            self::WEBAPP => 23,
            self::BARANG => 22,
            self::PERORANGAN => 21,
        };
    }

    public function hasPpn(): bool
    {
        return match ($this) {
            self::PERORANGAN => false,
            self::WEBAPP, self::BARANG => true,
        };
    }
}
