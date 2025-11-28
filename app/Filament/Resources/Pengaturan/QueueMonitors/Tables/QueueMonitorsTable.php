<?php

namespace App\Filament\Resources\Pengaturan\QueueMonitors\Tables;

use App\Models\QueueMonitor;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;

class QueueMonitorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        default => $state,
                        'succeeded' => 'Succeeded',
                        'failed' => 'Failed',
                        'running' => 'Running',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'running' => 'primary',
                        'succeeded' => 'success',
                        'failed' => 'danger',
                    }),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('queue')
                    ->label('Queue')
                    ->sortable(),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->formatStateUsing(fn (string $state) => "{$state}%")
                    ->sortable(),
                TextColumn::make('started_at')
                    ->label('Started at')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'running' => 'Running',
                        'succeeded' => 'Succeeded',
                        'failed' => 'Failed',
                    ])
                    ->multiple()
                    ->query(function (Builder $query, $state) {
                        $values = $state['values'] ?? [];

                        $query->when(count($values), function ($query) use ($values) {
                            if (in_array('succeeded', $values)) {
                                $query->orWhere(function ($query) {
                                    $query->whereNotNull('finished_at')
                                        ->where('failed', false);
                                });
                            }

                            if (in_array('running', $values)) {
                                $query->orWhere(function ($query) {
                                    $query->whereNull('finished_at')
                                        ->where('failed', false);
                                });
                            }

                            if (in_array('failed', $values)) {
                                $query->orWhere('failed', true);
                            }
                        });
                    }),
            ])
            ->recordActions([

            ])
            ->toolbarActions([
                BulkAction::make('retry')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->label('Retry')
                    ->action(function (Collection $records) {
                        try {
                            $ids = $records->pluck('job_id')->toArray();

                            Artisan::call('queue:retry', ['id' => $ids]);
                            QueueMonitor::whereIn('job_id', $ids)->delete();

                            Notification::make()
                                ->title('Success')
                                ->body('Jobs retried successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation(),
                DeleteBulkAction::make(),
            ]);
    }
}
