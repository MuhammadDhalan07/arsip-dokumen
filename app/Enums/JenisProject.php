<?php

namespace App\Enums;

enum JenisProject: string implements \Filament\Support\Contracts\HasLabel
{
    case WEBAPP = 'webapp';
    case BARANG = 'barang';
    case PERORANGAN = 'perorangan';

    public function getLabel(): string
    {
        return match($this) {
            self::WEBAPP => 'WEBAPP',
            self::BARANG => 'BARANG',
            self::PERORANGAN => 'PERORANGAN'
        };
    }
}
