<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestProjectsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->tahunAktif()
                    ->orderByRaw('
                        CASE
                            WHEN end_date < CURRENT_DATE THEN 1
                            ELSE 2
                        END
                    ')
                    ->orderBy('end_date', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->weight('medium')
                    ->grow(false),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tgl Mulai')
                    ->date('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tgl Selesai')
                    ->date('d F Y')
                    ->sortable()
                    ->color(fn ($record) => $record->end_date->isPast() && ! $record->is_complete ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('status_badge.label')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => $record->status_badge['color'])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'Terlambat' => '⚠️ Terlambat',
                        'Selesai' => '✅ Selesai',
                        'Berlangsung' => '🔄 Berlangsung',
                        default => $state,
                    }),
            ])
            ->striped();
    }
}
