<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\Document;
use App\Models\Rincian;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use PhpParser\Comment\Doc;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        $allRincian = Rincian::all();

        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->label('Project')
                    ->disabledClick()
                    ->formatStateUsing(function ($state, Document $record) {
                        $rincians = $record->rincians;

                        if ($rincians->isEmpty()) {
                            return new HtmlString(
                                '<div class="text-lg">'.e($state).'</div>'.
                                '<span class="text-xs text-gray-400">Belum ada rincian</span>'
                            );
                        }

                        $componentMap = [
                            'panduan' => 'actions.panduan',
                            'laporan' => 'actions.laporan',
                            'kak' => 'actions.kak',
                            'penawaran' => 'actions.penawaran',
                            'permohonan pemeriksaan dan serah terima' => 'actions.permohonan-pemeriksaan-serah-terima',
                            'permohonan pembayaran' => 'actions.permohonan-pembayaran',
                            'rab' => 'actions.rab',
                        ];

                        $components = '';

                        // foreach ($rincians as $rincian) {
                        //     $name = strtolower($rincian->name);

                        //     if (isset($componentMap[$name])) {
                        //         $components .= view(
                        //             'components.'.$componentMap[$name],
                        //             ['record' => $record, 'rincian' => $rincian]
                        //         )->render();
                        //     }
                        // }
                        foreach ($rincians as $rincian) {
                            $components .= view(
                                'components.actions.rincian-button',
                                compact('record', 'rincian')
                            )->render();
                        }

                        return new HtmlString(
                            Blade::render(<<<'BLADE'
                                <div class="space-y-2">
                                    <div class="text-lg font-medium">{{ $state }}</div>
                                    <div>
                                        <span class="mr-2"> Petanggung jawab: {{ $record->pic?->name ?? '-' }}</span>
                                        <span class="ml-2"> Mulai: {{ $record->project?->start_date?->format('d F Y') ?? '-' }}</span>
                                        <span class="ml-2"> Berakhir: {{ $record->project?->end_date?->format('d F Y') ?? '-' }}</span>
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
                    ...self::getActionUpload($allRincian),
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

    public static function getActionUpload($rincians): array
    {
        // $record = Document::class::first();
        $actions = [];

        foreach ($rincians as $rincian) {
            $collection = Str::snake($rincian->name);
            $slug = Str::slug($rincian->name, '_');

            $actions[] = Action::make("upload_{$slug}")
                ->label($rincian->name)
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn (Document $record) =>
                    $record->rincians->contains('id', $rincian->id)
                )
                ->fillForm(fn ($record) => $record->attributesToArray())
                ->schema([
                    SpatieMediaLibraryFileUpload::make('file')
                        ->collection($collection)
                        ->openable()
                        ->downloadable()
                        ->hint('*Ukuran file maksimum: 10MB. Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG.')
                        ->maxSize(10240)
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ])
                        ->required(),
                ])
                ->action(function (array $data, Document $record) use ($collection, $rincian) {

                    // $record->addMedia($data['file'])
                    //     ->toMediaCollection($collection);

                    Notification::make()
                        ->title("{$rincian->name} berhasil diunggah")
                        ->success()
                        ->send();
                });
        }

        return $actions;
    }
}
