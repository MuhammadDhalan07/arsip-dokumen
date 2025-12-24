<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Exports\DocumentExport;
use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Dokumen')
                ->color('success')
                ->icon('fileicon-microsoft-excel')
                ->action(function () {
                    $document = Document::all();

                    return DocumentExport::make($document);
                }),
            CreateAction::make(),
        ];
    }
}
