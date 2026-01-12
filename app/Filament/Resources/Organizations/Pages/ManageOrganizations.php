<?php

namespace App\Filament\Resources\Organizations\Pages;

use App\Actions\Fetching\Skpd;
use App\Filament\Resources\Organizations\OrganizationResource;
use App\Models\Tahun;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRecords;

class ManageOrganizations extends ManageRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        $tahunOptions = Tahun::orderBy('year', 'desc')->pluck('year', 'year')->toArray();

        return [
            Action::make('fetch_skpd')
                ->label('Tarik Data SKPD')
                ->color('info')
                ->icon('heroicon-o-arrow-down-tray')
                ->schema([
                    Select::make('tahun')
                        ->label('Tahun')
                        ->options($tahunOptions)
                        ->default(function () {
                            $latestTahun = Tahun::orderBy('year', 'desc')->first();

                            return $latestTahun?->year ?? date('Y');
                        })
                        ->required(),
                    Select::make('daerah')
                        ->label('Kode Daerah')
                        ->options([
                            307 => 'Kota Bandung (307)',
                            320 => 'Jawa Barat (320)',
                            1 => 'Pusat (1)',
                        ])
                        ->default(307)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $skpd = new Skpd;
                    $imported = $skpd->fetch((int) $data['tahun'], (int) $data['daerah']);

                    if ($imported > 0) {
                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil')
                            ->body("{$imported} data SKPD berhasil diimport untuk tahun {$data['tahun']}.")
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal')
                            ->body('Tidak ada data yang diimport. Periksa koneksi atau parameter.')
                            ->danger()
                            ->send();
                    }

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            CreateAction::make(),
        ];
    }
}
