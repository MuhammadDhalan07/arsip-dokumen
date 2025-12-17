<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JenisOrganization: string implements HasLabel
{
    case COMPANY = 'company';
    case GOVERNMENT = 'government';
    case INDIVIDUAL = 'individual';

    public function getLabel(): string
    {
        return match ($this) {
            self::COMPANY => 'Company',
            self::GOVERNMENT => 'Government',
            self::INDIVIDUAL => 'Individual',
        };
    }
}
