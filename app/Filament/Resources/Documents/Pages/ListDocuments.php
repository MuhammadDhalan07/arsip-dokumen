<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Exports\DocumentExport;
use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use App\Models\Tahun;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        $tahunOptions = Tahun::orderBy('year', 'desc')
            ->pluck('year', 'year')
            ->toArray();

        return [
            Action::make('export')
                ->label('Export Dokumen')
                ->color('success')
                ->icon('fileicon-microsoft-excel')
                ->schema([
                    Select::make('year')
                        ->label('Pilih Tahun')
                        ->options([
                            'all' => 'Semua Tahun',
                            ...$tahunOptions,
                        ])
                        ->native(false)
                        ->default('all')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $tahunRaw = $data['year'] ?? null;

                    if ($tahunRaw === null || $tahunRaw === '') {
                        \Filament\Notifications\Notification::make()
                            ->title('Error')
                            ->body('Silakan pilih tahun terlebih dahulu.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $query = Document::query();

                    if ($tahunRaw !== 'all') {
                        $query->where('year', (int) $tahunRaw);
                    }

                    $documents = $query->get();

                    if ($documents->isEmpty()) {
                        $message = $tahunRaw === 'all'
                            ? 'Tidak ada dokumen sama sekali.'
                            : 'Tidak ada dokumen untuk tahun '.(int) $tahunRaw.'.';

                        \Filament\Notifications\Notification::make()
                            ->title('Tidak ada data')
                            ->body($message)
                            ->warning()
                            ->send();

                        return;
                    }

                    return DocumentExport::make($documents);
                }),
            CreateAction::make(),
        ];
    }
}
