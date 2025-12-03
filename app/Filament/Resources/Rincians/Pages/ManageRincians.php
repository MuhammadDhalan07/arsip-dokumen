<?php

namespace App\Filament\Resources\Rincians\Pages;

use App\Filament\Resources\Rincians\RincianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageRincians extends ManageRecords
{
    protected static string $resource = RincianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
