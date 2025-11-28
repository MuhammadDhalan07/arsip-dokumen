<?php

namespace App\Filament\Resources\Pengaturan\QueueMonitors;

use App\Filament\Resources\Pengaturan\QueueMonitors\Pages\ListQueueMonitors;
use App\Filament\Resources\Pengaturan\QueueMonitors\Schemas\QueueMonitorForm;
use App\Filament\Resources\Pengaturan\QueueMonitors\Tables\QueueMonitorsTable;
use App\Filament\Resources\Pengaturan\QueueMonitors\Widgets\QueueStatsOverview;
use App\Models\QueueMonitor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QueueMonitorResource extends Resource
{
    protected static ?string $model = QueueMonitor::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $pluralModelLabel = 'Jobs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 12;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return QueueMonitorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QueueMonitorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            QueueStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQueueMonitors::route('/'),
        ];
    }
}
