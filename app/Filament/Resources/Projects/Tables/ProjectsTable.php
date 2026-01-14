<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->tahunAktif()
                ->orderByRaw('
                    CASE 
                        WHEN end_date < CURRENT_DATE THEN 1
                        ELSE 2
                    END
                ')
                ->orderBy('end_date', 'asc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe Proyek')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('status_badge.label')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => $record->status_badge['color']),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
