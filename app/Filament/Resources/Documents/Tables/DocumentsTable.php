<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\Document;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->label('Project')
                    ->disabledClick()
                    ->formatStateUsing(function ($state, Document $record) {
                        $rincians = $record->rincians;

                        if ($rincians->isEmpty()) {
                            return new HtmlString(
                                '<div class="text-lg">' . e($state) . '</div>' .
                                '<span class="text-xs text-gray-400">Belum ada rincian</span>'
                            );
                        }

                        $componentMap = [
                            'panduan' => 'actions.panduan',
                            'laporan' => 'actions.laporan',
                            'rak' => 'actions.rak',
                        ];

                        $components = '';

                        foreach ($rincians as $rincian) {
                            $name = strtolower($rincian->name);

                            if (isset($componentMap[$name])) {
                                $components .= view(
                                    'components.' . $componentMap[$name],
                                    ['record' => $record, 'rincian' => $rincian]
                                )->render();
                            }
                        }

                        return new HtmlString(
                            Blade::render(<<<'BLADE'
                                <div class="space-y-2">
                                    <div class="text-lg font-medium">{{ $state }}</div>
                                    <div>
                                        <span> Petanggung jawab: {{ $record->pic?->name }}</span>
                                        <span class="ml-2"> Mulai: {{ $record->project?->start_date?->format('d F Y') }}</span>
                                        <span class="ml-2"> Berakhir: {{ $record->project?->end_date?->format('d F Y') }}</span>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        {!! $components !!}
                                    </div>
                                </div>
                            BLADE, [
                                'state' => $state,
                                'record' => $record,
                                'components' => $components,
                            ])
                        );
                    })
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('rincians.name')
                //     ->label('Rincian')
                //     ->badge()
                //     ->separator(',')
                //     ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('uploadPanduan')
                        ->label('Upload Panduan')
                        ->modalHeading('Upload Panduan')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('file')
                                ->collection('panduan')
                                ->openable()
                                ->downloadable()
                                ->hint('*Ukuran file maksimum: 10MB. Format yang diizinkan: JPG, JPEG, PNG.')
                                ->removeUploadedFileButtonPosition('right')
                                ->acceptedFileTypes([
                                    'image/jpeg',
                                    'image/png',
                                    'image/jpg',
                                ])
                                ->maxSize(10240),
                        ])
                        ->fillForm(fn ($record) => $record->attributesToArray())
                        ->action(function ($data, Document $record) {
                            $mediaCollectionName = 'panduan';
                            foreach (data_get($data, $mediaCollectionName, []) as $key => $file) {
                                $record->addMedia($file)->toMediaCollection($mediaCollectionName);
                            }
                        }),

                    Action::make('uploadLaporan')
                        ->label('Upload Laporan')
                        ->modalHeading('Upload Laporan')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('file')
                                ->collection('laporan')
                                ->openable()
                                ->downloadable()
                                ->hint('*Ukuran file maksimum: 10MB. Format yang diizinkan: JPG, JPEG, PNG.')
                                ->removeUploadedFileButtonPosition('right')
                                ->acceptedFileTypes([
                                    'image/jpeg',
                                    'image/png',
                                    'image/jpg',
                                ])
                                ->maxSize(10240),
                        ])
                        ->fillForm(fn ($record) => $record->attributesToArray())
                        ->action(function ($data, Document $record) {
                            //
                        }),

                    Action::make('uploadRak')
                        ->label('Upload RAK')
                        ->modalHeading('Upload RAK')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('file')
                                ->collection('rak')
                                ->openable()
                                ->downloadable()
                                ->hint('*Ukuran file maksimum: 10MB. Format yang diizinkan: JPG, JPEG, PNG.')
                                ->removeUploadedFileButtonPosition('right')
                                ->acceptedFileTypes([
                                    'image/jpeg',
                                    'image/png',
                                    'image/jpg',
                                ])
                                ->maxSize(10240),
                        ])
                        ->fillForm(fn ($record) => $record->attributesToArray())
                        ->action(function ($data, Document $record) {
                            //
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
