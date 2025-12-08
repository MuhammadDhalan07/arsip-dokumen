<?php

namespace App\Filament\Resources\Rincian\Pages;

use App\Filament\Resources\Rincian\RincianResource;
use App\Models\Rincian;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageRincian extends ManageRecords
{
    protected static string $resource = RincianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function beforeSave($record, $data): void
    {
        $currentTotal = Rincian::where('is_active', true)
            ->where('id', '!=', $record->id ?? null)
            ->sum('bobot');

        $newTotal = $currentTotal + ($data['bobot'] ?? 0);

        if ($newTotal > 100) {
            Notification::make()
                ->title('Peringatan: Total Bobot Melebihi 100%')
                ->body("Total bobot akan menjadi {$newTotal}%. Pertimbangkan untuk menyesuaikan bobot rincian lainnya.")
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
