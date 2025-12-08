<?php

namespace App\Filament\Resources\Tahun\Pages;

use App\Filament\Resources\Tahun\TahunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTahun extends ManageRecords
{
    protected static string $resource = TahunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
