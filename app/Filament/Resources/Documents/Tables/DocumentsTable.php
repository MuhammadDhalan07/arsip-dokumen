<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\Document;
use App\Models\Rincian;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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
            ->modifyQueryUsing(fn ( $query) =>$query->tahunAktif())
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

                        $components = '';
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
                TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->formatStateUsing(function ($state, Document $record) {
                        $progress = $record->progress_percentage;
                        $color = $record->progress_color;
                        // dd($record->progress_percentage, $color);

                        return new HtmlString("
                            <div class='flex items-center gap-2'>
                                <div class='flex-1 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700'>
                                    <div class='bg-{$color}-600 h-2.5 rounded-full transition-all' style='width: {$progress}%'></div>
                                </div>
                                <span class='text-sm font-medium text-{$color}-700'>{$progress}%</span>
                            </div>
                        ");
                    })
                    ->html(),
                TextColumn::make('progress_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (Document $record) => $record->progress_color),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ...self::getActionUpload($allRincian),
                    DeleteAction::make(),
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
                        ->multiple()
                        ->panelLayout('grid')
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
                    $mediaCollectionName = $collection;
                    foreach (data_get($data, $mediaCollectionName, []) as $key => $file) {
                        $record->addMedia($file)->toMediaCollection($mediaCollectionName);
                    }

                    $record->rincians()->updateExistingPivot($rincian->id, [
                        'is_completed' => true,
                        'completed_at' => now(),
                    ]);

                    $progress = $record->progress_percentage;

                    Notification::make()
                        ->title("{$rincian->name} berhasil diunggah")
                        ->body("Progress: {$progress}%")
                        ->success()
                        ->send();
                });
        }

        return $actions;
    }
}
