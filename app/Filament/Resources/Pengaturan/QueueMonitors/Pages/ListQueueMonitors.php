<?php

namespace App\Filament\Resources\Pengaturan\QueueMonitors\Pages;

use App\Filament\Resources\Pengaturan\QueueMonitors;
use App\Filament\Resources\Pengaturan\QueueMonitors\QueueMonitorResource;
use App\Models\QueueMonitor;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListQueueMonitors extends ListRecords
{
    protected static string $resource = QueueMonitorResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            QueueMonitors\Widgets\QueueStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        $queueOptions = array_unique(Arr::flatten(Arr::pluck(config('horizon.defaults'), 'queue')));

        return [
            Actions\ActionGroup::make([
                Actions\Action::make('restart-queue-service')
                    ->label('Restart Service')
                    ->color('gray')
                    ->icon('heroicon-o-bolt')
                    ->requiresConfirmation()
                    ->action(function () {

                        Artisan::call('queue:restart');
                        Artisan::call('horizon:terminate');

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Berhasil Menjalankan Ulang Antrian')
                            ->success()
                            ->send();
                    }),
                Actions\Action::make('retry-queue')
                    ->label('Retry Queue')
                    ->color('gray')
                    ->icon('heroicon-o-queue-list')
                    ->modalWidth(Width::Medium)
                    ->schema([
                        Select::make('queue')
                            ->options(array_combine($queueOptions, $queueOptions))
                            ->default($queueOptions)
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $listQueue = $data['queue'];

                        Artisan::call('queue:retry', ['--queue' => implode(',', $listQueue)]);

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Sedang Mengulang Antrian')
                            ->success()
                            ->send();
                    }),
            ])
                ->button()
                ->color('gray')
                ->icon('heroicon-o-arrow-path')
                ->label('Queue'),
            Actions\Action::make('truncate')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action(function () {
                    QueueMonitor::truncate();

                    Schema::disableForeignKeyConstraints();
                    DB::table('failed_jobs')->truncate();
                    Schema::enableForeignKeyConstraints();

                    Notification::make()
                        ->title('Berhasil')
                        ->body('Berhasil Truncate Jobs')
                        ->success()
                        ->send();
                }),
        ];
    }
}
